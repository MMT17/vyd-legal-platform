<?php

namespace App\Services;

use App\Models\Convenio;
use App\Models\ImportacionHistorial;
use App\Models\Querella;
use DateTimeImmutable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use InvalidArgumentException;
use SplFileObject;
use Throwable;

class ChilquintaCsvImporter
{
    public const TYPE_CONVENIO = 'convenio';

    public const TYPE_QUERELLA = 'querella';

    private const COMMON_FIELDS = [
        'caso',
        'nis',
        'energia_ventana',
        'energia_fv',
        'energia_total',
        'monto_ventana',
        'monto_fv',
        'monto_total',
        'meses_ventana',
        'meses_fv',
        'meses_total',
        'tipo_cnr',
        'tipo_irregularidad',
        'nombre',
        'direccion',
        'comuna',
        'telefono',
    ];

    private const QUERELLA_FIELDS = [
        'caso',
        'ruc',
        'ruc_dv',
        'rit',
        'juzgado',
        'fecha_presentacion',
        'nis',
        'energia_ventana',
        'energia_fv',
        'energia_total',
        'monto_ventana',
        'monto_fv',
        'monto_total',
        'meses_ventana',
        'meses_fv',
        'meses_total',
        'tipo_cnr',
        'tipo_irregularidad',
        'nombre',
        'direccion',
        'comuna',
        'telefono',
    ];

    /**
     * @return array<string, mixed>
     */
    public function analyze(string $path, string $type): array
    {
        $this->assertReadableCsvFile($path);

        $delimiter = $this->detectDelimiter($path);
        $encoding = $this->detectEncoding($path);
        $headers = [];
        $normalizedHeaders = [];
        $preview = [];
        $totalRows = 0;
        $validRows = 0;
        $warningRows = 0;
        $invalidRows = 0;
        $newRows = 0;
        $existingRows = 0;
        $skippedRows = 0;
        $incidents = [];
        $seenCases = [];

        foreach ($this->rows($path, $delimiter, $encoding) as $rowNumber => $row) {
            if ($rowNumber === 1) {
                $headers = $row;
                $normalizedHeaders = array_map([self::class, 'canonicalHeading'], $row);

                continue;
            }

            if ($this->isEmptyRow($row)) {
                continue;
            }

            $totalRows++;
            $data = $this->mapRow($headers, $row, $type);
            $errors = $this->validate($data, $type);
            $case = $data['caso'] === null ? null : (string) $data['caso'];

            if ($errors === []) {
                if ($case !== null && isset($seenCases[$case])) {
                    $skippedRows++;

                    $incidents[] = $this->userIncident(
                        $rowNumber,
                        'caso',
                        $data['caso'],
                        "Caso repetido en este archivo. Ya aparece en la fila {$seenCases[$case]}.",
                        'Deje una sola fila por caso antes de importar.'
                    );

                    if (count($preview) < 5) {
                        $preview[] = ['__row' => $rowNumber, '__status' => 'skipped'] + $data;
                    }

                    continue;
                }

                if ($case !== null) {
                    $seenCases[$case] = $rowNumber;
                }

                $validRows++;

                $model = $type === self::TYPE_QUERELLA ? Querella::class : Convenio::class;
                $status = $model::query()->where('caso', $case)->exists() ? 'updated' : 'created';

                if ($status === 'updated') {
                    $existingRows++;
                } else {
                    $newRows++;
                }

                if (count($preview) < 5) {
                    $preview[] = ['__row' => $rowNumber, '__status' => $status] + $data;
                }

                continue;
            }

            $invalidRows++;
            foreach ($errors as $error) {
                $incidents[] = $this->userIncident(
                    $rowNumber,
                    $error[0],
                    $error[1],
                    $error[2],
                    $this->correctionFor($error[0])
                );
            }

            if (count($preview) < 5) {
                $preview[] = ['__row' => $rowNumber, '__status' => 'error'] + $data;
            }
        }

        $expected = $this->expectedFields($type);
        $missing = array_values(array_diff($expected, $normalizedHeaders));
        $unknown = array_values(array_diff($normalizedHeaders, $expected));

        return [
            'delimiter' => $delimiter,
            'encoding' => $encoding,
            'filename' => basename($path),
            'size' => filesize($path) ?: 0,
            'physical_lines' => $this->countPhysicalLines($path),
            'total_rows' => $totalRows,
            'columns' => $normalizedHeaders,
            'missing_columns' => $missing,
            'unknown_columns' => $unknown,
            'required_present' => array_values(array_intersect($this->requiredFields($type), $normalizedHeaders)),
            'can_import' => array_intersect($this->requiredFields($type), $missing) === [],
            'preview' => $preview,
            'incidents' => $incidents,
            'valid_rows' => $validRows,
            'ready_rows' => $validRows,
            'warning_rows' => $warningRows,
            'invalid_rows' => $invalidRows,
            'skipped_rows' => $skippedRows,
            'new_rows' => $newRows,
            'existing_rows' => $existingRows,
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public function import(
        string $path,
        string $type,
        ?int $userId = null,
        ?string $originalFilename = null,
        ?string $storedFilename = null,
    ): array {
        $this->assertReadableCsvFile($path);

        $delimiter = $this->detectDelimiter($path);
        $encoding = $this->detectEncoding($path);
        $fileSize = filesize($path) ?: 0;
        $physicalLines = $this->countPhysicalLines($path);
        $historial = ImportacionHistorial::create([
            'user_id' => $userId,
            'tipo_importacion' => $type === self::TYPE_QUERELLA ? 'querellas' : 'convenios',
            'archivo_original' => $originalFilename ?: basename($path),
            'archivo_almacenado' => $storedFilename,
            'estado' => 'procesando',
            'iniciado_at' => now(),
            'detalles' => [
                'separador' => $delimiter,
                'encoding' => $encoding,
                'ruta_real' => $path,
                'tamano_bytes' => $fileSize,
                'lineas_fisicas' => $physicalLines,
            ],
        ]);

        $headers = [];
        $created = 0;
        $updated = 0;
        $skipped = 0;
        $processed = 0;
        $errorRows = 0;
        $errors = [];
        $seenCases = [];

        Log::info('Importacion CSV Chilquinta iniciada', [
            'tipo' => $type,
            'archivo_original' => $historial->archivo_original,
            'archivo_almacenado' => $storedFilename,
            'ruta_real' => $path,
            'tamano_bytes' => $fileSize,
            'lineas_fisicas' => $physicalLines,
        ]);

        foreach ($this->rows($path, $delimiter, $encoding) as $rowNumber => $row) {
            if ($rowNumber === 1) {
                $headers = $row;

                continue;
            }

            if ($this->isEmptyRow($row)) {
                continue;
            }

            $processed++;
            $data = $this->mapRow($headers, $row, $type);
            $rowErrors = $this->validate($data, $type);
            $case = $data['caso'] === null ? null : (string) $data['caso'];

            if ($case !== null && isset($seenCases[$case])) {
                $errors[] = $this->errorReportRow(
                    $rowNumber,
                    'skipped',
                    'caso',
                    $data['caso'],
                    "Caso duplicado en el archivo. Primera aparicion en fila {$seenCases[$case]}."
                );
                $skipped++;

                continue;
            }

            if ($rowErrors !== []) {
                $errors[] = $this->errorReportRowFromValidation($rowNumber, $rowErrors);
                $errorRows++;

                continue;
            }

            $seenCases[(string) $case] = $rowNumber;

            try {
                DB::transaction(function () use ($type, $data, &$created, &$updated): void {
                    $record = $type === self::TYPE_QUERELLA
                        ? $this->upsertQuerella($data)
                        : $this->upsertConvenio($data);

                    $record->wasRecentlyCreated ? $created++ : $updated++;
                });
            } catch (Throwable $e) {
                Log::warning('Fila CSV Chilquinta no procesada', [
                    'tipo' => $type,
                    'fila' => $rowNumber,
                    'message' => $e->getMessage(),
                ]);

                $errors[] = $this->errorReportRow(
                    $rowNumber,
                    'error',
                    null,
                    null,
                    'No se pudo procesar esta fila. Revise que los datos correspondan al formato esperado.'
                );
                $errorRows++;
            }
        }

        $errorReportPath = $errors === []
            ? null
            : $this->writeErrorReport($historial->id, $errors);

        $historial->update([
            'total_registros' => $processed,
            'registros_validos' => $created + $updated,
            'creados' => $created,
            'actualizados' => $updated,
            'duplicados' => $skipped,
            'errores' => $errorRows,
            'reporte_errores' => $errorReportPath,
            'estado' => $errorRows === 0 ? 'completado' : 'con_errores',
            'completado_at' => now(),
            'detalles' => [
                'omitidos' => $skipped,
                'errores' => $errors,
                'error_report_path' => $errorReportPath,
                'separador' => $delimiter,
                'encoding' => $encoding,
                'ruta_real' => $path,
                'archivo_almacenado' => $storedFilename,
                'tamano_bytes' => $fileSize,
                'lineas_fisicas' => $physicalLines,
                'filas_csv_detectadas' => $processed,
            ],
        ]);

        activity('importacion')
            ->causedBy(auth()->user())
            ->performedOn($historial)
            ->event('imported')
            ->withProperties([
                'tipo_importacion' => $historial->tipo_importacion,
                'archivo' => $historial->archivo_original,
                'creados' => $created,
                'actualizados' => $updated,
                'omitidos' => $skipped,
                'errores' => $errorRows,
                'procesados' => $processed,
            ])
            ->log("Importacion de {$historial->tipo_importacion} ejecutada");

        Log::info('Importacion CSV Chilquinta finalizada', [
            'tipo' => $type,
            'historial_id' => $historial->id,
            'procesados' => $processed,
            'creados' => $created,
            'actualizados' => $updated,
            'omitidos' => $skipped,
            'errores' => $errorRows,
        ]);

        return [
            'historial' => $historial->refresh(),
            'created' => $created,
            'updated' => $updated,
            'skipped' => $skipped,
            'errors' => $errorRows,
            'total' => $processed,
            'error_report_path' => $errorReportPath,
        ];
    }

    public static function normalizeHeading(string $heading): string
    {
        $heading = preg_replace('/^\xEF\xBB\xBF/', '', $heading) ?? $heading;

        $normalized = Str::of($heading)
            ->ascii()
            ->lower()
            ->replaceMatches('/[^a-z0-9]+/', '_')
            ->trim('_')
            ->toString();

        return match ($normalized) {
            'telefonos' => 'telefono',
            default => $normalized,
        };
    }

    public static function canonicalHeading(string $heading): string
    {
        return self::normalizeHeading($heading);
    }

    public static function nullableText(mixed $value): ?string
    {
        if ($value === null) {
            return null;
        }

        $value = trim((string) $value);

        return $value === '' ? null : $value;
    }

    public static function nullableDecimal(mixed $value): ?string
    {
        $value = self::nullableText($value);

        if ($value === null) {
            return null;
        }

        $normalized = str_replace(['$', ' '], '', $value);

        if (str_contains($normalized, ',') && ! str_contains($normalized, '.')) {
            $normalized = str_replace(',', '.', $normalized);
        } else {
            $normalized = str_replace(',', '', $normalized);
        }

        if (! is_numeric($normalized)) {
            throw new InvalidArgumentException('Debe ser numerico.');
        }

        return number_format((float) $normalized, 2, '.', '');
    }

    public static function nullableInteger(mixed $value): ?int
    {
        $value = self::nullableText($value);

        if ($value === null) {
            return null;
        }

        if (! preg_match('/^-?\d+$/', $value)) {
            throw new InvalidArgumentException('Debe ser entero.');
        }

        return (int) $value;
    }

    public static function parseFlexibleDate(mixed $value): ?string
    {
        $value = self::nullableText($value);

        if ($value === null) {
            return null;
        }

        $value = preg_replace('/\s+/', ' ', $value) ?? $value;

        foreach ([
            'd/m/Y H:i:s',
            'd/m/Y H:i',
            'd/m/Y H',
            'd/m/Y',
            'Y-m-d H:i:s',
            'Y-m-d H:i',
            'Y-m-d H',
            'Y-m-d',
            'd-m-Y H:i:s',
            'd-m-Y H:i',
            'd-m-Y H',
            'd-m-Y',
        ] as $format) {
            $date = DateTimeImmutable::createFromFormat('!'.$format, $value);
            $errors = DateTimeImmutable::getLastErrors();

            if (
                $date instanceof DateTimeImmutable
                && ($errors === false || ($errors['warning_count'] === 0 && $errors['error_count'] === 0))
                && $date->format($format) === $value
            ) {
                return $date->format('Y-m-d H:i:s');
            }
        }

        throw new InvalidArgumentException('Formato de fecha invalido.');
    }

    /**
     * @return array<int, string>
     */
    public function expectedFields(string $type): array
    {
        return $type === self::TYPE_QUERELLA ? self::QUERELLA_FIELDS : self::COMMON_FIELDS;
    }

    public static function fieldLabel(string $field): string
    {
        return match ($field) {
            'caso' => 'Caso',
            'nis' => 'NIS',
            'energia_ventana' => 'Energia ventana',
            'energia_fv' => 'Energia FV',
            'energia_total' => 'Energia total',
            'monto_ventana' => 'Monto ventana',
            'monto_fv' => 'Monto FV',
            'monto_total' => 'Monto total',
            'meses_ventana' => 'Meses ventana',
            'meses_fv' => 'Meses FV',
            'meses_total' => 'Meses total',
            'tipo_cnr' => 'Tipo CNR',
            'tipo_irregularidad' => 'Tipo irregularidad',
            'nombre' => 'Nombre',
            'direccion' => 'Direccion',
            'comuna' => 'Comuna',
            'telefono' => 'Telefono',
            'ruc' => 'RUC',
            'ruc_dv' => 'RUC DV',
            'rit' => 'RIT',
            'juzgado' => 'Juzgado',
            'fecha_presentacion' => 'Fecha presentacion',
            default => Str::of($field)->replace('_', ' ')->title()->toString(),
        };
    }

    /**
     * @return array<int, string>
     */
    private function requiredFields(string $type): array
    {
        return ['caso', 'nis'];
    }

    /**
     * @return iterable<int, array<int, string|null>>
     */
    private function rows(string $path, string $delimiter, string $encoding): iterable
    {
        $file = new SplFileObject($path);
        $file->setFlags(SplFileObject::READ_CSV | SplFileObject::SKIP_EMPTY);
        $file->setCsvControl($delimiter);

        foreach ($file as $index => $row) {
            if ($row === [null] || $row === false) {
                continue;
            }

            yield $index + 1 => array_map(
                fn ($value) => is_string($value) ? $this->toUtf8($value, $encoding) : $value,
                $row,
            );
        }
    }

    private function detectDelimiter(string $path): string
    {
        $sample = $this->readSample($path);
        $comma = substr_count($sample, ',');
        $semicolon = substr_count($sample, ';');

        return $semicolon > $comma ? ';' : ',';
    }

    private function detectEncoding(string $path): string
    {
        $sample = $this->readSample($path);

        if (str_starts_with($sample, "\xEF\xBB\xBF")) {
            return 'UTF-8 BOM';
        }

        foreach (['UTF-8', 'Windows-1252', 'ISO-8859-1'] as $encoding) {
            if (mb_check_encoding($sample, $encoding)) {
                return $encoding;
            }
        }

        return 'UTF-8';
    }

    private function readSample(string $path): string
    {
        $handle = fopen($path, 'rb');

        if ($handle === false) {
            throw new InvalidArgumentException('No se pudo abrir el archivo CSV.');
        }

        $sample = fread($handle, 8192);
        fclose($handle);

        return $sample === false ? '' : $sample;
    }

    private function toUtf8(string $value, string $encoding): string
    {
        $encoding = $encoding === 'UTF-8 BOM' ? 'UTF-8' : $encoding;
        $converted = mb_convert_encoding($value, 'UTF-8', $encoding);

        return preg_replace('/^\xEF\xBB\xBF/', '', $converted) ?? $converted;
    }

    /**
     * @param  array<int, string|null>  $headers
     * @param  array<int, string|null>  $row
     * @return array<string, mixed>
     */
    private function mapRow(array $headers, array $row, string $type): array
    {
        $data = array_fill_keys($this->expectedFields($type), null);

        foreach ($headers as $index => $heading) {
            $field = self::canonicalHeading((string) $heading);

            if (array_key_exists($field, $data)) {
                $data[$field] = self::nullableText($row[$index] ?? null);
            }
        }

        return $data;
    }

    /**
     * @param  array<string, mixed>  $data
     * @return array<int, array{0:string,1:mixed,2:string}>
     */
    private function validate(array $data, string $type): array
    {
        $errors = [];

        foreach ($this->requiredFields($type) as $field) {
            if (self::nullableText($data[$field] ?? null) === null) {
                $errors[] = [$field, $data[$field] ?? null, 'Campo requerido.'];
            }
        }

        foreach (['energia_ventana', 'energia_fv', 'energia_total', 'monto_ventana', 'monto_fv', 'monto_total'] as $field) {
            try {
                self::nullableDecimal($data[$field] ?? null);
            } catch (Throwable) {
                $errors[] = [$field, $data[$field] ?? null, 'Debe ser numerico.'];
            }
        }

        foreach (['meses_ventana', 'meses_fv', 'meses_total'] as $field) {
            try {
                self::nullableInteger($data[$field] ?? null);
            } catch (Throwable) {
                $errors[] = [$field, $data[$field] ?? null, 'Debe ser entero.'];
            }
        }

        if ($type === self::TYPE_QUERELLA && ($data['fecha_presentacion'] ?? null) !== null) {
            try {
                self::parseFlexibleDate($data['fecha_presentacion']);
            } catch (Throwable) {
                $errors[] = ['fecha_presentacion', $data['fecha_presentacion'], 'Formato de fecha invalido.'];
            }
        }

        return $errors;
    }

    /**
     * @param  array<int, array{0:string,1:mixed,2:string}>  $rowErrors
     * @return array<string, mixed>
     */
    private function errorReportRowFromValidation(int $rowNumber, array $rowErrors): array
    {
        [$field, $value] = $rowErrors[0];
        $message = collect($rowErrors)
            ->map(fn (array $error): string => self::fieldLabel($error[0]).": {$error[2]}")
            ->implode(' | ');

        return $this->errorReportRow($rowNumber, 'error', $field, $value, $message);
    }

    /**
     * @return array<string, mixed>
     */
    private function errorReportRow(int $rowNumber, string $status, ?string $field, mixed $value, string $message): array
    {
        return [
            'fila' => $rowNumber,
            'estado' => $status,
            'campo' => $field ? self::fieldLabel($field) : null,
            'valor' => is_scalar($value) || $value === null ? $value : 'Valor no legible',
            'mensaje' => $message,
        ];
    }

    /**
     * @return array{row:int,field:string,value:string,message:string,correction:string}
     */
    private function userIncident(int $rowNumber, string $field, mixed $value, string $message, string $correction): array
    {
        return [
            'row' => $rowNumber,
            'field' => self::fieldLabel($field),
            'value' => is_scalar($value) || $value === null ? (string) $value : 'Valor no legible',
            'message' => $message,
            'correction' => $correction,
        ];
    }

    private function correctionFor(string $field): string
    {
        return match ($field) {
            'caso', 'nis' => 'Complete este dato con un numero entero.',
            'meses_ventana', 'meses_fv', 'meses_total' => 'Use solo numeros enteros, sin puntos ni texto.',
            'fecha_presentacion' => 'Use yyyy-mm-dd, dd-mm-yyyy o dd/mm/yyyy.',
            'energia_ventana', 'energia_fv', 'energia_total', 'monto_ventana', 'monto_fv', 'monto_total' => 'Use un valor numerico.',
            default => 'Revise el dato y vuelva a cargar el archivo.',
        };
    }

    /**
     * @param  array<string, mixed>  $data
     */
    private function upsertConvenio(array $data): Model
    {
        $attributes = [
            'numero' => $data['caso'],
            'caso' => $data['caso'],
            'nis' => $data['nis'],
            'nis_convenio' => $data['nis'],
            'ciudad' => $data['comuna'],
            'energia_ventana' => self::nullableDecimal($data['energia_ventana']),
            'energia_fv' => self::nullableDecimal($data['energia_fv']),
            'energia_total' => self::nullableDecimal($data['energia_total']),
            'monto_ventana' => self::nullableDecimal($data['monto_ventana']),
            'monto_fv' => self::nullableDecimal($data['monto_fv']),
            'monto_total' => self::nullableDecimal($data['monto_total']),
            'meses_ventana' => self::nullableInteger($data['meses_ventana']),
            'meses_fv' => self::nullableInteger($data['meses_fv']),
            'meses_total' => self::nullableInteger($data['meses_total']),
            'tipo_cnr' => $data['tipo_cnr'],
            'tipo_irregularidad' => $data['tipo_irregularidad'],
            'nombre' => $data['nombre'],
            'direccion' => $data['direccion'],
            'comuna' => $data['comuna'],
            'telefono' => $data['telefono'],
        ];

        $convenio = Convenio::query()->firstOrNew(['caso' => (string) $data['caso']]);
        $convenio->fill($attributes);
        $convenio->save();
        $this->syncContacto($convenio, $data);

        return $convenio;
    }

    /**
     * @param  array<string, mixed>  $data
     */
    private function upsertQuerella(array $data): Model
    {
        $fecha = self::parseFlexibleDate($data['fecha_presentacion']);
        $querella = Querella::query()->firstOrNew(['caso' => (string) $data['caso']]);
        $querella->fill([
            'numero' => $data['caso'],
            'caso' => $data['caso'],
            'ruc' => $data['ruc'],
            'ruc_dv' => $data['ruc_dv'],
            'rit' => $data['rit'],
            'juzgado' => $data['juzgado'],
            'tribunal' => $data['juzgado'],
            'fecha_presentacion' => $fecha,
            'fecha' => $fecha,
            'nis' => $data['nis'],
            'ciudad' => $data['comuna'],
            'energia_ventana' => self::nullableDecimal($data['energia_ventana']),
            'energia_fv' => self::nullableDecimal($data['energia_fv']),
            'energia_total' => self::nullableDecimal($data['energia_total']),
            'monto_ventana' => self::nullableDecimal($data['monto_ventana']),
            'monto_fv' => self::nullableDecimal($data['monto_fv']),
            'monto_total' => self::nullableDecimal($data['monto_total']),
            'meses_ventana' => self::nullableInteger($data['meses_ventana']),
            'meses_fv' => self::nullableInteger($data['meses_fv']),
            'meses_total' => self::nullableInteger($data['meses_total']),
            'tipo_cnr' => $data['tipo_cnr'],
            'tipo_irregularidad' => $data['tipo_irregularidad'],
            'tipo_proceso' => $data['tipo_irregularidad'],
            'nombre' => $data['nombre'],
            'direccion' => $data['direccion'],
            'comuna' => $data['comuna'],
            'telefono' => $data['telefono'],
        ]);
        $querella->save();

        return $querella;
    }

    /**
     * @param  array<string, mixed>  $data
     */
    private function syncContacto(Convenio $convenio, array $data): void
    {
        if (! array_filter([$data['nombre'], $data['direccion'], $data['comuna'], $data['telefono']])) {
            return;
        }

        $comentario = implode(PHP_EOL, array_filter([
            'Datos de contacto importados desde CSV Chilquinta.',
            $data['nombre'] ? "Nombre: {$data['nombre']}" : null,
            $data['direccion'] ? "Direccion: {$data['direccion']}" : null,
            $data['comuna'] ? "Comuna: {$data['comuna']}" : null,
            $data['telefono'] ? "Telefono: {$data['telefono']}" : null,
        ]));

        $contacto = $convenio->contactos()
            ->where('comentario', 'like', 'Datos de contacto importados desde CSV Chilquinta.%')
            ->first();

        if ($contacto) {
            $contacto->update([
                'fecha' => now(),
                'comentario' => $comentario,
                'importante' => false,
                'user_id' => auth()->id(),
            ]);

            return;
        }

        $convenio->contactos()->create([
            'fecha' => now(),
            'comentario' => $comentario,
            'importante' => false,
            'user_id' => auth()->id(),
        ]);
    }

    /**
     * @param  array<int, array<string, mixed>>  $errors
     */
    private function writeErrorReport(int $batchId, array $errors): string
    {
        $path = "imports/errors/importacion_{$batchId}_errores.csv";
        $handle = fopen('php://temp', 'r+');

        fputcsv($handle, ['fila', 'estado', 'campo', 'valor', 'mensaje']);

        foreach ($errors as $error) {
            fputcsv($handle, [$error['fila'], $error['estado'], $error['campo'], $error['valor'], $error['mensaje']]);
        }

        rewind($handle);
        Storage::disk('local')->put($path, stream_get_contents($handle));
        fclose($handle);

        return $path;
    }

    /**
     * @param  array<int, string|null>  $row
     */
    private function isEmptyRow(array $row): bool
    {
        return array_filter($row, fn ($value) => self::nullableText($value) !== null) === [];
    }

    private function assertReadableCsvFile(string $path): void
    {
        if (! file_exists($path)) {
            throw new InvalidArgumentException('El archivo CSV no existe.');
        }

        if (! is_file($path)) {
            throw new InvalidArgumentException('La ruta seleccionada no corresponde a un archivo.');
        }

        if (! is_readable($path)) {
            throw new InvalidArgumentException('El archivo CSV no se puede leer.');
        }
    }

    private function countPhysicalLines(string $path): int
    {
        $file = new SplFileObject($path, 'rb');
        $lines = 0;

        while (! $file->eof()) {
            $file->fgets();
            $lines++;
        }

        return max(0, $lines - 1);
    }
}

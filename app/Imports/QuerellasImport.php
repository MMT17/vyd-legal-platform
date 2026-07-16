<?php

namespace App\Imports;

use App\Imports\Concerns\HandlesChilquintaCsvRows;
use App\Models\ImportacionHistorial;
use App\Models\Querella;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Throwable;

class QuerellasImport implements ToCollection, WithHeadingRow
{
    use HandlesChilquintaCsvRows;

    private ImportacionHistorial $historial;

    private int $totalRegistros = 0;

    private int $creados = 0;

    private int $actualizados = 0;

    private int $omitidos = 0;

    private int $errores = 0;

    /**
     * @var array<int, array<string, mixed>>
     */
    private array $erroresDetalle = [];

    /**
     * @var array<string, int>
     */
    private array $casosProcesados = [];

    public function __construct(?int $userId = null, ?string $archivoOriginal = null)
    {
        $this->historial = ImportacionHistorial::create([
            'user_id' => $userId,
            'tipo_importacion' => 'querellas',
            'archivo_original' => $archivoOriginal,
            'estado' => 'procesando',
        ]);
    }

    public function collection(Collection $rows): void
    {
        foreach ($rows as $index => $row) {
            $this->totalRegistros++;

            $rowNumber = $index + 2;
            $data = $this->normalizeChilquintaRow($row->toArray(), $this->aliases());
            $errors = $this->validateRow($data);

            if ($errors !== []) {
                $this->registerErrors($rowNumber, $errors);
                $this->omitidos++;

                continue;
            }

            $caso = (string) $data['caso'];

            if (isset($this->casosProcesados[$caso])) {
                $this->registerError($rowNumber, 'caso', $data['caso'], "Caso duplicado en el archivo. Primera aparicion en fila {$this->casosProcesados[$caso]}.");
                $this->omitidos++;

                continue;
            }

            $this->casosProcesados[$caso] = $rowNumber;

            try {
                $fechaPresentacion = $this->parseFlexibleDate($data['fecha_presentacion']);
                $querella = Querella::query()->where('caso', $caso)->first();

                $attributes = [
                    'numero' => $caso,
                    'caso' => $caso,
                    'ruc' => $data['ruc'],
                    'ruc_dv' => $data['ruc_dv'],
                    'rit' => $data['rit'],
                    'juzgado' => $data['juzgado'],
                    'tribunal' => $data['juzgado'],
                    'fecha_presentacion' => $fechaPresentacion,
                    'fecha' => $fechaPresentacion,
                    'nis' => (string) $data['nis'],
                    'ciudad' => $data['comuna'],
                    'energia_ventana' => $this->nullableDecimal($data['energia_ventana']),
                    'energia_fv' => $this->nullableDecimal($data['energia_fv']),
                    'energia_total' => $this->nullableDecimal($data['energia_total']),
                    'monto_ventana' => $this->nullableDecimal($data['monto_ventana']),
                    'monto_fv' => $this->nullableDecimal($data['monto_fv']),
                    'monto_total' => $this->nullableDecimal($data['monto_total']),
                    'meses_ventana' => $this->nullableInteger($data['meses_ventana']),
                    'meses_fv' => $this->nullableInteger($data['meses_fv']),
                    'meses_total' => $this->nullableInteger($data['meses_total']),
                    'tipo_cnr' => $data['tipo_cnr'],
                    'tipo_irregularidad' => $data['tipo_irregularidad'],
                    'tipo_proceso' => $data['tipo_irregularidad'],
                    'nombre' => $data['nombre'],
                    'direccion' => $data['direccion'],
                    'comuna' => $data['comuna'],
                    'telefono' => $data['telefono'],
                ];

                if ($querella) {
                    $querella->update($attributes);
                    $this->actualizados++;
                } else {
                    Querella::create($attributes);
                    $this->creados++;
                }
            } catch (Throwable $e) {
                $this->registerError($rowNumber, null, null, $e->getMessage());
                $this->omitidos++;
            }
        }

        $this->finish();
    }

    public function getHistorial(): ImportacionHistorial
    {
        return $this->historial->refresh();
    }

    /**
     * @return array<string, array<int, string>>
     */
    private function aliases(): array
    {
        return [
            'caso' => ['caso'],
            'ruc' => ['ruc'],
            'ruc_dv' => ['ruc_dv', 'ruc dv'],
            'rit' => ['rit'],
            'juzgado' => ['juzgado'],
            'fecha_presentacion' => ['fecha_presentacion', 'fecha_presentación'],
            'nis' => ['nis'],
            'energia_ventana' => ['energia_ventana', 'energía_ventana'],
            'energia_fv' => ['energia_fv', 'energía_fv', 'energia_FV', 'energía_FV'],
            'energia_total' => ['energia_total', 'energía_total'],
            'monto_ventana' => ['monto_ventana'],
            'monto_fv' => ['monto_fv', 'monto_FV'],
            'monto_total' => ['monto_total'],
            'meses_ventana' => ['meses_ventana'],
            'meses_fv' => ['meses_fv', 'meses_FV'],
            'meses_total' => ['meses_total'],
            'tipo_cnr' => ['tipo_cnr', 'tipo_CNR'],
            'tipo_irregularidad' => ['tipo_irregularidad'],
            'nombre' => ['nombre'],
            'direccion' => ['direccion', 'dirección'],
            'comuna' => ['comuna'],
            'telefono' => ['telefono', 'teléfono', 'telefonos', 'teléfonos'],
        ];
    }

    /**
     * @param  array<string, mixed>  $data
     * @return array<int, array<string, mixed>>
     */
    private function validateRow(array $data): array
    {
        $errors = [];

        foreach (['caso', 'nis'] as $field) {
            if ($data[$field] === null || $data[$field] === '') {
                $errors[] = [$field, $data[$field], 'Campo requerido.'];
            }
        }

        foreach ([
            'energia_ventana',
            'energia_fv',
            'energia_total',
            'monto_ventana',
            'monto_fv',
            'monto_total',
        ] as $field) {
            try {
                $this->nullableDecimal($data[$field]);
            } catch (Throwable) {
                $errors[] = [$field, $data[$field], 'Debe ser numerico o venir vacio.'];
            }
        }

        foreach ([
            'meses_ventana',
            'meses_fv',
            'meses_total',
        ] as $field) {
            try {
                $this->nullableInteger($data[$field]);
            } catch (Throwable) {
                $errors[] = [$field, $data[$field], 'Debe ser entero o venir vacio.'];
            }
        }

        if ($data['fecha_presentacion'] !== null) {
            try {
                $this->parseFlexibleDate($data['fecha_presentacion']);
            } catch (Throwable) {
                $errors[] = ['fecha_presentacion', $data['fecha_presentacion'], 'Formato de fecha invalido. Use yyyy-mm-dd, dd-mm-yyyy o dd/mm/yyyy.'];
            }
        }

        return $errors;
    }

    /**
     * @param  array<int, array<string, mixed>>  $errors
     */
    private function registerErrors(int $rowNumber, array $errors): void
    {
        foreach ($errors as [$column, $value, $message]) {
            $this->registerError($rowNumber, $column, $value, $message);
        }
    }

    private function registerError(int $rowNumber, ?string $column, mixed $value, string $message): void
    {
        $this->errores++;
        $this->erroresDetalle[] = [
            'fila' => $rowNumber,
            'columna' => $column,
            'valor' => $this->valueForReport($value),
            'mensaje' => $message,
        ];
    }

    private function finish(): void
    {
        $this->historial->update([
            'total_registros' => $this->totalRegistros,
            'creados' => $this->creados,
            'actualizados' => $this->actualizados,
            'duplicados' => $this->omitidos,
            'errores' => $this->errores,
            'estado' => $this->errores > 0 ? 'con_errores' : 'completado',
            'detalles' => [
                'omitidos' => $this->omitidos,
                'errores' => $this->erroresDetalle,
            ],
        ]);

        activity('importacion')
            ->causedBy(auth()->user())
            ->performedOn($this->historial)
            ->event('imported')
            ->withProperties([
                'tipo_importacion' => 'querellas',
                'total_registros' => $this->totalRegistros,
                'creados' => $this->creados,
                'actualizados' => $this->actualizados,
                'omitidos' => $this->omitidos,
                'errores' => $this->errores,
            ])
            ->log('Importacion de querellas ejecutada');
    }
}

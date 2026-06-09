<?php

namespace App\Imports;

use App\Models\Convenio;
use App\Models\ImportacionHistorial;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class ConveniosImport implements ToCollection, WithHeadingRow
{
    private ImportacionHistorial $historial;

    private int $totalRegistros = 0;

    private int $creados = 0;

    private int $actualizados = 0;

    private int $duplicados = 0;

    private int $errores = 0;

    /**
     * @var array<int, array<string, mixed>>
     */
    private array $erroresDetalle = [];

    /**
     * @var array<string, int>
     */
    private array $numerosProcesados = [];

    public function __construct(?int $userId = null, ?string $archivoOriginal = null)
    {
        $this->historial = ImportacionHistorial::create([
            'user_id' => $userId,
            'tipo_importacion' => 'convenios',
            'archivo_original' => $archivoOriginal,
            'estado' => 'procesando',
        ]);
    }

    public function collection(Collection $rows): void
    {
        foreach ($rows as $index => $row) {
            $this->totalRegistros++;

            $data = $this->normalizeRow($row->toArray());
            $rowNumber = $index + 2;

            $validator = Validator::make($data, [
                'numero' => ['required'],
                'estado' => ['nullable', 'in:firmado,en_negociacion,frustrado'],
                'fecha' => ['nullable', 'date'],
                'fecha_cierre' => ['nullable', 'date'],
                'deuda_total' => ['nullable', 'numeric'],
                'cuotas' => ['nullable', 'integer'],
            ]);

            if ($validator->fails()) {
                $this->registerError($rowNumber, $validator->errors()->all());

                continue;
            }

            if (isset($this->numerosProcesados[$data['numero']])) {
                $this->duplicados++;
                $this->registerError($rowNumber, ["Número duplicado en archivo: {$data['numero']}"]);

                continue;
            }

            $this->numerosProcesados[$data['numero']] = $rowNumber;

            $convenio = Convenio::query()
                ->where('numero', $data['numero'])
                ->first();

            $attributes = [
                'numero' => $data['numero'],
                'nis_convenio' => $data['nis_convenio'],
                'ciudad' => $data['ciudad'],
                'estado' => $data['estado'],
                'fecha' => $this->parseDate($data['fecha']),
                'fecha_cierre' => $this->parseDateTime($data['fecha_cierre']),
                'caso' => $data['caso'],
                'nombre_abogado' => $data['nombre_abogado'],
                'abogado_responsable' => $data['abogado_responsable'],
                'documentacion_estado' => $data['documentacion_estado'],
                'cnr_12_meses' => $this->parseDecimal($data['cnr_12_meses']),
                'cnr_fuera_ventana' => $this->parseDecimal($data['cnr_fuera_ventana']),
                'deuda_total' => $this->parseDecimal($data['deuda_total']),
                'acuerdo_extrajudicial' => $this->parseDecimal($data['acuerdo_extrajudicial']),
                'cuotas' => $data['cuotas'] === null ? null : (int) $data['cuotas'],
                'cnr_pagado_anterior' => $this->parseDecimal($data['cnr_pagado_anterior']),
                'observacion' => $data['observacion'],
            ];

            if ($convenio) {
                $convenio->update($attributes);
                $this->actualizados++;

                continue;
            }

            Convenio::create($attributes);
            $this->creados++;
        }

        $this->historial->update([
            'total_registros' => $this->totalRegistros,
            'creados' => $this->creados,
            'actualizados' => $this->actualizados,
            'duplicados' => $this->duplicados,
            'errores' => $this->errores,
            'estado' => $this->errores > 0 ? 'con_errores' : 'completado',
            'detalles' => [
                'errores' => $this->erroresDetalle,
            ],
        ]);

        activity('importacion')
            ->causedBy(auth()->user())
            ->performedOn($this->historial)
            ->event('imported')
            ->withProperties([
                'tipo_importacion' => 'convenios',
                'total_registros' => $this->totalRegistros,
                'creados' => $this->creados,
                'actualizados' => $this->actualizados,
                'duplicados' => $this->duplicados,
                'errores' => $this->errores,
            ])
            ->log('Importación de convenios ejecutada');
    }

    public function getHistorial(): ImportacionHistorial
    {
        return $this->historial->refresh();
    }

    /**
     * @param  array<string, mixed>  $row
     * @return array<string, mixed>
     */
    private function normalizeRow(array $row): array
    {
        $fields = [
            'numero',
            'nis_convenio',
            'ciudad',
            'estado',
            'fecha',
            'fecha_cierre',
            'caso',
            'nombre_abogado',
            'abogado_responsable',
            'documentacion_estado',
            'cnr_12_meses',
            'cnr_fuera_ventana',
            'deuda_total',
            'acuerdo_extrajudicial',
            'cuotas',
            'cnr_pagado_anterior',
            'observacion',
        ];

        $data = [];

        foreach ($fields as $field) {
            $value = $row[$field] ?? null;
            $value = is_string($value) ? trim($value) : $value;
            $data[$field] = $value === '' ? null : $value;
        }

        return $data;
    }

    /**
     * @param  array<int, string>  $motivos
     */
    private function registerError(int $rowNumber, array $motivos): void
    {
        $this->errores++;
        $this->erroresDetalle[] = [
            'fila' => $rowNumber,
            'motivo' => implode(' ', $motivos),
        ];
    }

    private function parseDate(mixed $value): ?string
    {
        return $value ? Carbon::parse($value)->toDateString() : null;
    }

    private function parseDateTime(mixed $value): ?string
    {
        return $value ? Carbon::parse($value)->toDateTimeString() : null;
    }

    private function parseDecimal(mixed $value): ?string
    {
        if ($value === null) {
            return null;
        }

        return (string) str_replace(',', '.', (string) $value);
    }
}

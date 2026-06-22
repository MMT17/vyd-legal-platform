<?php

namespace App\Exports;

use App\Models\Querella;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class QuerellasExport implements FromCollection, WithHeadings, WithMapping
{
    public function collection(): Collection
    {
        return Querella::query()
            ->orderBy('numero')
            ->get();
    }

    /**
     * @return array<int, string>
     */
    public function headings(): array
    {
        return [
            'numero',
            'id_querella',
            'ciudad',
            'estado',
            'fecha',
            'fecha_cierre',
            'tribunal',
            'tipo_proceso',
            'abogado_responsable',
            'documentacion_estado',
            'cnr_12_meses',
            'cnr_fuera_ventana',
            'deuda_total',
            'acuerdo_extrajudicial',
            'cuotas',
            'cnr_pagado_anterior',
            'observacion',
            'created_at',
        ];
    }

    /**
     * @return array<int, mixed>
     */
    public function map($row): array
    {
        return [
            $row->numero,
            $row->id_querella,
            $row->ciudad,
            $row->estado,
            $row->fecha?->format('Y-m-d'),
            $row->fecha_cierre?->format('Y-m-d H:i:s'),
            $row->tribunal,
            $row->tipo_proceso,
            $row->abogado_responsable,
            $row->documentacion_estado,
            $row->cnr_12_meses,
            $row->cnr_fuera_ventana,
            $row->deuda_total,
            $row->acuerdo_extrajudicial,
            $row->cuotas,
            $row->cnr_pagado_anterior,
            $row->observacion,
            $row->created_at?->format('Y-m-d H:i:s'),
        ];
    }
}

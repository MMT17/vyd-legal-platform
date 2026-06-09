<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Models\Activity;
use Spatie\Activitylog\Traits\LogsActivity;

#[Fillable([
    'numero',
    'nis',
    'id_querella',
    'ciudad',
    'estado',
    'fecha',
    'fecha_cierre',
    'documentacion_estado',
    'cnr_12_meses',
    'cnr_fuera_ventana',
    'deuda_total',
    'acuerdo_extrajudicial',
    'cuotas',
    'cnr_pagado_anterior',
    'abogado_responsable',
    'tribunal',
    'tipo_proceso',
    'user_id',
    'observacion',
    'wordpress_id',
])]
class Querella extends Model
{
    use LogsActivity, SoftDeletes;

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->useLogName('querella')
            ->logFillable()
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }

    public function getDescriptionForEvent(string $eventName): string
    {
        return match ($eventName) {
            'created' => 'Querella creada',
            'updated' => 'Querella actualizada',
            'deleted' => 'Querella eliminada',
            default => "Querella {$eventName}",
        };
    }

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'fecha' => 'date',
            'fecha_cierre' => 'datetime',
            'cnr_12_meses' => 'decimal:2',
            'cnr_fuera_ventana' => 'decimal:2',
            'deuda_total' => 'decimal:2',
            'acuerdo_extrajudicial' => 'decimal:2',
            'cuotas' => 'integer',
            'cnr_pagado_anterior' => 'decimal:2',
            'user_id' => 'integer',
            'wordpress_id' => 'integer',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function procesos(): MorphMany
    {
        return $this->morphMany(Proceso::class, 'procesable');
    }

    public function documentos(): MorphMany
    {
        return $this->morphMany(Documento::class, 'documentable');
    }

    public function activities(): MorphMany
    {
        return $this->morphMany(Activity::class, 'subject');
    }
}

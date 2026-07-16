<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Models\Activity;
use Spatie\Activitylog\Traits\LogsActivity;

#[Fillable([
    'numero',
    'nis',
    'nis_convenio',
    'id_usuario',
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
    'caso',
    'nombre_abogado',
    'abogado_responsable',
    'user_id',
    'observacion',
    'wordpress_id',
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
])]
class Convenio extends Model
{
    use LogsActivity, SoftDeletes;

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->useLogName('convenio')
            ->logFillable()
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }

    public function getDescriptionForEvent(string $eventName): string
    {
        return match ($eventName) {
            'created' => 'Convenio creado',
            'updated' => 'Convenio actualizado',
            'deleted' => 'Convenio eliminado',
            default => "Convenio {$eventName}",
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
            'energia_ventana' => 'decimal:2',
            'energia_fv' => 'decimal:2',
            'energia_total' => 'decimal:2',
            'monto_ventana' => 'decimal:2',
            'monto_fv' => 'decimal:2',
            'monto_total' => 'decimal:2',
            'meses_ventana' => 'integer',
            'meses_fv' => 'integer',
            'meses_total' => 'integer',
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

    public function contactos(): HasMany
    {
        return $this->hasMany(Contacto::class);
    }

    public function activities(): MorphMany
    {
        return $this->morphMany(Activity::class, 'subject');
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

#[Fillable([
    'user_id',
    'tipo_importacion',
    'archivo_original',
    'archivo_almacenado',
    'total_registros',
    'registros_validos',
    'creados',
    'actualizados',
    'duplicados',
    'errores',
    'reporte_errores',
    'estado',
    'detalles',
    'iniciado_at',
    'completado_at',
])]
class ImportacionHistorial extends Model
{
    use LogsActivity;

    protected $table = 'importacion_historial';

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->useLogName('importacion')
            ->logFillable()
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }

    public function getDescriptionForEvent(string $eventName): string
    {
        return match ($eventName) {
            'created' => 'Importación creada',
            'updated' => 'Importación actualizada',
            'deleted' => 'Importación eliminada',
            default => "Importación {$eventName}",
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
            'detalles' => 'array',
            'user_id' => 'integer',
            'total_registros' => 'integer',
            'registros_validos' => 'integer',
            'creados' => 'integer',
            'actualizados' => 'integer',
            'duplicados' => 'integer',
            'errores' => 'integer',
            'iniciado_at' => 'datetime',
            'completado_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}

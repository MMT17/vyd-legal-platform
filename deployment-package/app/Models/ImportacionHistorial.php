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
    'total_registros',
    'creados',
    'actualizados',
    'duplicados',
    'errores',
    'estado',
    'detalles',
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
            'creados' => 'integer',
            'actualizados' => 'integer',
            'duplicados' => 'integer',
            'errores' => 'integer',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}

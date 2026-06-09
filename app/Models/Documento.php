<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Support\Facades\Storage;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Models\Activity;
use Spatie\Activitylog\Traits\LogsActivity;

#[Fillable([
    'documentable_type',
    'documentable_id',
    'nombre',
    'descripcion',
    'fecha',
    'archivo_path',
    'tipo_documento',
    'checksum_archivo',
    'tamano_archivo',
    'mime_type',
    'nombre_original',
    'wordpress_attachment_id',
    'wordpress_id',
])]
class Documento extends Model
{
    use LogsActivity;

    protected static function booted(): void
    {
        static::saving(function (Documento $documento): void {
            $documento->syncFileMetadata();
        });
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->useLogName('documento')
            ->logFillable()
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }

    public function getDescriptionForEvent(string $eventName): string
    {
        return match ($eventName) {
            'created' => 'Documento creado',
            'updated' => 'Documento actualizado',
            'deleted' => 'Documento eliminado',
            default => "Documento {$eventName}",
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
            'fecha' => 'datetime',
            'documentable_id' => 'integer',
            'tamano_archivo' => 'integer',
            'wordpress_attachment_id' => 'integer',
            'wordpress_id' => 'integer',
        ];
    }

    public function documentable(): MorphTo
    {
        return $this->morphTo();
    }

    public function activities(): MorphMany
    {
        return $this->morphMany(Activity::class, 'subject');
    }

    private function syncFileMetadata(): void
    {
        if (! $this->archivo_path) {
            return;
        }

        $disk = Storage::disk('public');

        if (! $disk->exists($this->archivo_path)) {
            return;
        }

        $path = $disk->path($this->archivo_path);

        $this->nombre_original ??= basename($this->archivo_path);
        $this->mime_type = $disk->mimeType($this->archivo_path) ?: $this->mime_type;
        $this->tamano_archivo = $disk->size($this->archivo_path) ?: $this->tamano_archivo;
        $this->checksum_archivo = is_file($path) ? hash_file('sha256', $path) : $this->checksum_archivo;
    }
}

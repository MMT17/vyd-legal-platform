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
    'procesable_type',
    'procesable_id',
    'nombre',
    'tipo',
    'fecha',
    'descripcion',
    'archivo_path',
    'checksum_archivo',
    'wordpress_id',
])]
class Proceso extends Model
{
    use LogsActivity;

    protected static function booted(): void
    {
        static::saving(function (Proceso $proceso): void {
            $proceso->syncFileMetadata();
        });
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->useLogName('proceso')
            ->logFillable()
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }

    public function getDescriptionForEvent(string $eventName): string
    {
        return match ($eventName) {
            'created' => 'Proceso creado',
            'updated' => 'Proceso actualizado',
            'deleted' => 'Proceso eliminado',
            default => "Proceso {$eventName}",
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
            'procesable_id' => 'integer',
            'wordpress_id' => 'integer',
        ];
    }

    public function procesable(): MorphTo
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

        $this->checksum_archivo = is_file($path) ? hash_file('sha256', $path) : $this->checksum_archivo;
    }
}

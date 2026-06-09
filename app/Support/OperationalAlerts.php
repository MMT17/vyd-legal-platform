<?php

namespace App\Support;

use App\Filament\Resources\ContactoResource;
use App\Filament\Resources\ConvenioResource;
use App\Filament\Resources\ProcesoResource;
use App\Filament\Resources\QuerellaResource;
use App\Models\Contacto;
use App\Models\Convenio;
use App\Models\Proceso;
use App\Models\Querella;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Spatie\Activitylog\Models\Activity;

class OperationalAlerts
{
    public static function canView(): bool
    {
        return auth()->user()?->can('reportes.ver') ?? false;
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    public static function dashboardAlerts(): array
    {
        return Cache::remember('operational-alerts.dashboard', 60, fn (): array => [
            [
                'type' => 'Convenios sin actividad',
                'count' => self::conveniosWithoutActivityQuery()->count(),
                'description' => 'Sin movimientos hace más de 30 días.',
                'color' => 'danger',
            ],
            [
                'type' => 'Querellas sin actividad',
                'count' => self::querellasWithoutActivityQuery()->count(),
                'description' => 'Sin movimientos hace más de 30 días.',
                'color' => 'danger',
            ],
            [
                'type' => 'Convenios sin documentos',
                'count' => self::conveniosWithoutDocumentsQuery()->count(),
                'description' => 'Casos sin respaldo documental.',
                'color' => 'warning',
            ],
            [
                'type' => 'Querellas sin documentos',
                'count' => self::querellasWithoutDocumentsQuery()->count(),
                'description' => 'Casos sin respaldo documental.',
                'color' => 'warning',
            ],
            [
                'type' => 'Convenios sin abogado',
                'count' => self::conveniosWithoutLawyerQuery()->count(),
                'description' => 'Sin abogado responsable o nombre abogado.',
                'color' => 'danger',
            ],
            [
                'type' => 'Querellas sin abogado',
                'count' => self::querellasWithoutLawyerQuery()->count(),
                'description' => 'Sin abogado responsable asignado.',
                'color' => 'danger',
            ],
            [
                'type' => 'Procesos sin fecha',
                'count' => self::procesosWithoutDateQuery()->count(),
                'description' => 'Procesos pendientes de calendarización.',
                'color' => 'warning',
            ],
            [
                'type' => 'Contactos importantes pendientes',
                'count' => self::pendingImportantContactsQuery()->count(),
                'description' => 'Contactos críticos futuros o sin fecha.',
                'color' => 'info',
            ],
        ]);
    }

    /**
     * @return array<string, Collection<int, array<string, mixed>>>
     */
    public static function sections(int $limitPerAlert = 25): array
    {
        return Cache::remember('operational-alerts.sections.' . auth()->id() . '.' . $limitPerAlert, 60, fn (): array => [
            'Convenios' => collect()
                ->merge(self::conveniosWithoutActivity($limitPerAlert))
                ->merge(self::conveniosWithoutDocuments($limitPerAlert))
                ->merge(self::conveniosWithoutLawyer($limitPerAlert))
                ->values(),
            'Querellas' => collect()
                ->merge(self::querellasWithoutActivity($limitPerAlert))
                ->merge(self::querellasWithoutDocuments($limitPerAlert))
                ->merge(self::querellasWithoutLawyer($limitPerAlert))
                ->values(),
            'Procesos' => self::procesosWithoutDate($limitPerAlert),
            'Contactos' => self::pendingImportantContacts($limitPerAlert),
        ]);
    }

    private static function conveniosWithoutActivity(int $limit): Collection
    {
        return self::conveniosWithoutActivityQuery()
            ->limit($limit)
            ->get()
            ->map(fn (Convenio $convenio): array => self::row(
                'Convenio sin actividad hace 30 días',
                'Convenio ' . ($convenio->numero ?: "#{$convenio->id}"),
                'No registra actividad reciente en auditoría.',
                $convenio->last_activity_at ?: $convenio->created_at,
                self::convenioUrl($convenio),
                'danger',
            ));
    }

    private static function querellasWithoutActivity(int $limit): Collection
    {
        return self::querellasWithoutActivityQuery()
            ->limit($limit)
            ->get()
            ->map(fn (Querella $querella): array => self::row(
                'Querella sin actividad hace 30 días',
                'Querella ' . ($querella->numero ?: "#{$querella->id}"),
                'No registra actividad reciente en auditoría.',
                $querella->last_activity_at ?: $querella->created_at,
                self::querellaUrl($querella),
                'danger',
            ));
    }

    private static function conveniosWithoutDocuments(int $limit): Collection
    {
        return self::conveniosWithoutDocumentsQuery()
            ->limit($limit)
            ->get()
            ->map(fn (Convenio $convenio): array => self::row(
                'Convenio sin documentos',
                'Convenio ' . ($convenio->numero ?: "#{$convenio->id}"),
                'No tiene documentos relacionados.',
                $convenio->created_at,
                self::convenioUrl($convenio),
                'warning',
            ));
    }

    private static function querellasWithoutDocuments(int $limit): Collection
    {
        return self::querellasWithoutDocumentsQuery()
            ->limit($limit)
            ->get()
            ->map(fn (Querella $querella): array => self::row(
                'Querella sin documentos',
                'Querella ' . ($querella->numero ?: "#{$querella->id}"),
                'No tiene documentos relacionados.',
                $querella->created_at,
                self::querellaUrl($querella),
                'warning',
            ));
    }

    private static function conveniosWithoutLawyer(int $limit): Collection
    {
        return self::conveniosWithoutLawyerQuery()
            ->limit($limit)
            ->get()
            ->map(fn (Convenio $convenio): array => self::row(
                'Convenio sin abogado responsable',
                'Convenio ' . ($convenio->numero ?: "#{$convenio->id}"),
                'Falta abogado responsable y nombre abogado.',
                $convenio->created_at,
                self::convenioUrl($convenio),
                'danger',
            ));
    }

    private static function querellasWithoutLawyer(int $limit): Collection
    {
        return self::querellasWithoutLawyerQuery()
            ->limit($limit)
            ->get()
            ->map(fn (Querella $querella): array => self::row(
                'Querella sin abogado responsable',
                'Querella ' . ($querella->numero ?: "#{$querella->id}"),
                'Falta abogado responsable.',
                $querella->created_at,
                self::querellaUrl($querella),
                'danger',
            ));
    }

    private static function procesosWithoutDate(int $limit): Collection
    {
        return self::procesosWithoutDateQuery()
            ->with('procesable')
            ->limit($limit)
            ->get()
            ->map(fn (Proceso $proceso): array => self::row(
                'Proceso sin fecha',
                $proceso->nombre ?: ($proceso->tipo ?: "Proceso #{$proceso->id}"),
                'El proceso no tiene fecha registrada.',
                $proceso->created_at,
                (auth()->user()?->can('procesos.editar') ?? false) ? ProcesoResource::getUrl('edit', ['record' => $proceso]) : null,
                'warning',
            ));
    }

    private static function pendingImportantContacts(int $limit): Collection
    {
        return self::pendingImportantContactsQuery()
            ->with('convenio:id,numero')
            ->limit($limit)
            ->get()
            ->map(fn (Contacto $contacto): array => self::row(
                'Contacto importante pendiente',
                $contacto->convenio?->numero ? "Convenio {$contacto->convenio->numero}" : "Contacto #{$contacto->id}",
                $contacto->comentario ? str($contacto->comentario)->limit(100)->toString() : 'Contacto marcado como importante.',
                $contacto->fecha ?: $contacto->created_at,
                (auth()->user()?->can('contactos.editar') ?? false) ? ContactoResource::getUrl('edit', ['record' => $contacto]) : null,
                'info',
            ));
    }

    private static function conveniosWithoutActivityQuery(): Builder
    {
        $cutoff = now()->subDays(30);

        return Convenio::query()
            ->select('convenios.*')
            ->addSelect('last_activities.last_activity_at')
            ->leftJoinSub(self::lastActivitySubquery(app(Convenio::class)->getMorphClass()), 'last_activities', function ($join): void {
                $join->on('convenios.id', '=', 'last_activities.subject_id');
            })
            ->where(function (Builder $query) use ($cutoff): void {
                $query
                    ->where('last_activities.last_activity_at', '<', $cutoff)
                    ->orWhere(fn (Builder $query) => $query
                        ->whereNull('last_activities.last_activity_at')
                        ->where('convenios.created_at', '<', $cutoff));
            })
            ->orderByRaw('coalesce(last_activities.last_activity_at, convenios.created_at) asc');
    }

    private static function querellasWithoutActivityQuery(): Builder
    {
        $cutoff = now()->subDays(30);

        return Querella::query()
            ->select('querellas.*')
            ->addSelect('last_activities.last_activity_at')
            ->leftJoinSub(self::lastActivitySubquery(app(Querella::class)->getMorphClass()), 'last_activities', function ($join): void {
                $join->on('querellas.id', '=', 'last_activities.subject_id');
            })
            ->where(function (Builder $query) use ($cutoff): void {
                $query
                    ->where('last_activities.last_activity_at', '<', $cutoff)
                    ->orWhere(fn (Builder $query) => $query
                        ->whereNull('last_activities.last_activity_at')
                        ->where('querellas.created_at', '<', $cutoff));
            })
            ->orderByRaw('coalesce(last_activities.last_activity_at, querellas.created_at) asc');
    }

    private static function conveniosWithoutDocumentsQuery(): Builder
    {
        return Convenio::query()
            ->doesntHave('documentos')
            ->orderBy('created_at');
    }

    private static function querellasWithoutDocumentsQuery(): Builder
    {
        return Querella::query()
            ->doesntHave('documentos')
            ->orderBy('created_at');
    }

    private static function conveniosWithoutLawyerQuery(): Builder
    {
        return Convenio::query()
            ->where(fn (Builder $query) => $query
                ->whereNull('abogado_responsable')
                ->orWhere('abogado_responsable', ''))
            ->where(fn (Builder $query) => $query
                ->whereNull('nombre_abogado')
                ->orWhere('nombre_abogado', ''))
            ->orderBy('created_at');
    }

    private static function querellasWithoutLawyerQuery(): Builder
    {
        return Querella::query()
            ->where(fn (Builder $query) => $query
                ->whereNull('abogado_responsable')
                ->orWhere('abogado_responsable', ''))
            ->orderBy('created_at');
    }

    private static function procesosWithoutDateQuery(): Builder
    {
        return Proceso::query()
            ->whereNull('fecha')
            ->orderBy('created_at');
    }

    private static function pendingImportantContactsQuery(): Builder
    {
        return Contacto::query()
            ->where('importante', true)
            ->where(fn (Builder $query) => $query
                ->whereNull('fecha')
                ->orWhere('fecha', '>=', today()))
            ->orderByRaw('fecha is null')
            ->orderBy('fecha');
    }

    private static function lastActivitySubquery(string $subjectType): Builder
    {
        return Activity::query()
            ->select('subject_id')
            ->selectRaw('max(created_at) as last_activity_at')
            ->where('subject_type', $subjectType)
            ->groupBy('subject_id');
    }

    private static function row(string $type, string $record, string $description, Carbon|string|null $date, ?string $url, string $color): array
    {
        return [
            'type' => $type,
            'record' => $record,
            'description' => $description,
            'date' => $date ? Carbon::parse($date) : null,
            'url' => $url,
            'color' => $color,
        ];
    }

    private static function convenioUrl(Convenio $convenio): ?string
    {
        return (auth()->user()?->can('convenios.ver') ?? false)
            ? ConvenioResource::getUrl('view', ['record' => $convenio])
            : null;
    }

    private static function querellaUrl(Querella $querella): ?string
    {
        return (auth()->user()?->can('querellas.ver') ?? false)
            ? QuerellaResource::getUrl('view', ['record' => $querella])
            : null;
    }
}

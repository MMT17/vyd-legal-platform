<?php

namespace App\Filament\Pages;

use App\Support\OperationalAlerts as OperationalAlertsData;
use BackedEnum;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;
use Illuminate\Support\Collection;

class OperationalAlerts extends Page
{
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedBellAlert;

    protected static string|\UnitEnum|null $navigationGroup = 'Gestión Legal';

    protected static ?string $navigationLabel = 'Alertas Operativas';

    protected static ?string $title = 'Alertas Operativas';

    protected static ?int $navigationSort = 6;

    protected string $view = 'filament.pages.operational-alerts';

    public string $activeFilter = 'all';

    public static function canAccess(): bool
    {
        return OperationalAlertsData::canView();
    }

    public function setFilter(string $filter): void
    {
        $this->activeFilter = in_array($filter, array_column($this->filters(), 'key'), true)
            ? $filter
            : 'all';
    }

    /**
     * @return array<string, mixed>
     */
    protected function getViewData(): array
    {
        $allRows = OperationalAlertsData::operationalInbox();
        $rows = $this->filteredRowsForDisplay($allRows);

        return [
            'summaryCards' => $this->summaryCardsFor($allRows),
            'filters' => $this->filters(),
            'rows' => $rows,
            'allRowsCount' => $allRows->count(),
        ];
    }

    /**
     * @return array<int, array{key: string, label: string}>
     */
    public function filters(): array
    {
        return [
            ['key' => 'all', 'label' => 'Todos'],
            ['key' => 'convenios', 'label' => 'Convenios'],
            ['key' => 'querellas', 'label' => 'Querellas'],
            ['key' => 'procesos', 'label' => 'Procesos'],
            ['key' => 'contactos', 'label' => 'Contactos'],
            ['key' => 'without_lawyer', 'label' => 'Sin abogado'],
            ['key' => 'without_documents', 'label' => 'Sin documentos'],
            ['key' => 'without_activity', 'label' => 'Sin actividad'],
            ['key' => 'without_date', 'label' => 'Sin fecha'],
            ['key' => 'pending_contacts', 'label' => 'Contactos pendientes'],
        ];
    }

    /**
     * @param  array<string, Collection<int, array<string, mixed>>|array<int, array<string, mixed>>>  $sections
     * @return Collection<int, array<string, mixed>>
     */
    public function rowsForDisplay(array $sections): Collection
    {
        return collect($sections)
            ->flatMap(fn (mixed $records, string $section): Collection => $this->recordsCollection($records)
                ->filter(fn (mixed $record): bool => $this->isValidRecord($record))
                ->map(fn (array $record): array => [
                    ...$record,
                    'section' => $section,
                    'module' => $this->moduleForSection($section),
                    'record_id' => $this->recordIdFor($record),
                    'identifier' => (string) $record['record'],
                    'alert_types' => $this->alertTypesFor((string) $record['type']),
                    'alerts' => collect($this->alertTypesFor((string) $record['type']))
                        ->map(fn (string $type): array => [
                            'type' => $type,
                            'label' => $this->alertLabelFor($type),
                        ])
                        ->all(),
                    'alert_names' => [(string) $record['type']],
                    'descriptions' => [(string) $record['description']],
                    'priority' => $this->priorityForAlertTypes($this->alertTypesFor((string) $record['type'])),
                    'relevant_date' => $record['date'] ?? null,
                ]))
            ->groupBy(fn (array $row): string => implode('|', [
                $row['module'],
                $row['identifier'],
                $row['url'] ?? '',
            ]))
            ->map(function (Collection $group): array {
                $first = $group->first();
                $alertTypes = $group
                    ->flatMap(fn (array $row): array => $row['alert_types'])
                    ->unique()
                    ->values()
                    ->all();

                return [
                    ...$first,
                    'alert_types' => $alertTypes,
                    'alerts' => collect($alertTypes)
                        ->map(fn (string $type): array => [
                            'type' => $type,
                            'label' => $this->alertLabelFor($type),
                        ])
                        ->all(),
                    'priority' => $this->priorityForAlertTypes($alertTypes),
                    'alert_names' => $group
                        ->flatMap(fn (array $row): array => $row['alert_names'])
                        ->unique()
                        ->values()
                        ->all(),
                    'descriptions' => $group
                        ->flatMap(fn (array $row): array => $row['descriptions'])
                        ->unique()
                        ->values()
                        ->all(),
                ];
            })
            ->values();
    }

    public function recordsCollection(mixed $records): Collection
    {
        if ($records instanceof Collection) {
            return $records;
        }

        if (is_array($records)) {
            return collect($records);
        }

        return collect();
    }

    public function isValidRecord(mixed $record): bool
    {
        return is_array($record)
            && array_key_exists('type', $record)
            && array_key_exists('record', $record)
            && array_key_exists('description', $record);
    }

    /**
     * @param  Collection<int, array<string, mixed>>  $rows
     * @return Collection<int, array<string, mixed>>
     */
    public function filteredRowsForDisplay(Collection $rows): Collection
    {
        if ($this->activeFilter === 'all') {
            return $rows->values();
        }

        return $rows
            ->filter(fn (array $row): bool => $this->rowMatchesFilter($row, $this->activeFilter))
            ->values();
    }

    /**
     * @param  array<string, mixed>  $row
     */
    public function rowMatchesFilter(array $row, string $filter): bool
    {
        return match ($filter) {
            'convenios' => $row['module'] === 'convenio',
            'querellas' => $row['module'] === 'querella',
            'procesos' => $row['module'] === 'proceso',
            'contactos' => $row['module'] === 'contacto',
            'without_lawyer' => in_array('without_lawyer', $row['alert_types'], true),
            'without_documents' => in_array('without_documents', $row['alert_types'], true),
            'without_activity' => in_array('without_activity', $row['alert_types'], true),
            'without_date' => in_array('without_date', $row['alert_types'], true),
            'pending_contacts' => in_array('pending_contact', $row['alert_types'], true),
            default => true,
        };
    }

    /**
     * @return array<int, string>
     */
    public function alertTypesFor(string $type): array
    {
        $text = str($type)->lower()->toString();

        return array_values(array_filter([
            str_contains($text, 'abogado') ? 'without_lawyer' : null,
            str_contains($text, 'documentos') ? 'without_documents' : null,
            str_contains($text, 'actividad') ? 'without_activity' : null,
            str_contains($text, 'fecha') ? 'without_date' : null,
            str_contains($text, 'contacto') ? 'pending_contact' : null,
        ]));
    }

    public function moduleForSection(string $section): string
    {
        return match ($section) {
            'Convenios' => 'convenio',
            'Querellas' => 'querella',
            'Procesos' => 'proceso',
            'Contactos' => 'contacto',
            default => str($section)->lower()->singular()->toString(),
        };
    }

    /**
     * @param  array<string, mixed>  $record
     */
    public function recordIdFor(array $record): ?string
    {
        if (isset($record['record_id'])) {
            return (string) $record['record_id'];
        }

        $url = (string) ($record['url'] ?? '');

        if (preg_match('/\/(\d+)(?:\/|$)/', $url, $matches) === 1) {
            return $matches[1];
        }

        return null;
    }

    /**
     * @param  array<int, string>  $alertTypes
     */
    public function priorityForAlertTypes(array $alertTypes): string
    {
        return collect($alertTypes)
            ->intersect(['without_lawyer', 'without_date', 'pending_contact'])
            ->isNotEmpty() ? 'action' : 'review';
    }

    public function alertLabelFor(string $type): string
    {
        return match ($type) {
            'without_lawyer' => 'Sin abogado',
            'without_documents' => 'Sin documentos',
            'without_activity' => 'Sin actividad',
            'without_date' => 'Sin fecha',
            'pending_contact' => 'Contacto pendiente',
            default => $type,
        };
    }

    /**
     * @param  Collection<int, array<string, mixed>>  $rows
     * @return array<int, array<string, mixed>>
     */
    public function summaryCardsFor(Collection $rows): array
    {
        $countMatching = fn (array $types): int => $rows
            ->filter(fn (array $row): bool => collect($row['alert_types'] ?? [])->intersect($types)->isNotEmpty())
            ->count();

        return [
            [
                'title' => 'Asuntos sin responsable',
                'count' => $countMatching(['without_lawyer']),
                'description' => 'Convenios o querellas sin abogado asignado.',
                'icon' => 'heroicon-o-user-minus',
                'tone' => 'action',
            ],
            [
                'title' => 'Asuntos sin documentos',
                'count' => $countMatching(['without_documents']),
                'description' => 'Registros sin respaldo documental asociado.',
                'icon' => 'heroicon-o-document-minus',
                'tone' => 'review',
            ],
            [
                'title' => 'Asuntos sin actividad',
                'count' => $countMatching(['without_activity']),
                'description' => 'Casos sin movimientos recientes registrados.',
                'icon' => 'heroicon-o-clock',
                'tone' => 'review',
            ],
            [
                'title' => 'Procesos y contactos pendientes',
                'count' => $countMatching(['without_date', 'pending_contact']),
                'description' => 'Procesos sin fecha o contactos importantes por revisar.',
                'icon' => 'heroicon-o-calendar-days',
                'tone' => 'action',
            ],
        ];
    }
}

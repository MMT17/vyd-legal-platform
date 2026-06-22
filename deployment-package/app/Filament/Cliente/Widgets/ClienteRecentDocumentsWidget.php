<?php

namespace App\Filament\Cliente\Widgets;

use App\Models\Documento;
use Filament\Widgets\Widget;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;

class ClienteRecentDocumentsWidget extends Widget
{
    protected string $view = 'filament.cliente.widgets.cliente-recent-documents-widget';

    protected int|string|array $columnSpan = [
        'default' => 'full',
        'xl' => 1,
    ];

    /**
     * @return array<string, mixed>
     */
    protected function getViewData(): array
    {
        return [
            'documents' => Cache::remember('portal.widgets.recent-documents', 60, fn (): Collection => Documento::query()
                ->select(['id', 'nombre', 'tipo_documento', 'fecha', 'archivo_path', 'nombre_original', 'created_at'])
                ->latest()
                ->limit(5)
                ->get()),
        ];
    }
}

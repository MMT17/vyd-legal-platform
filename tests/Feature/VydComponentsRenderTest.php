<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\Blade;
use Tests\TestCase;

class VydComponentsRenderTest extends TestCase
{
    public function test_vyd_components_render_basic_markup(): void
    {
        $html = Blade::render(<<<'BLADE'
            <x-vyd.metric-card label="Creados" value="3" tone="success" icon="heroicon-o-check-circle" />
            <x-vyd.alert title="Validacion completa" description="Todas las columnas requeridas estan presentes." tone="success" />
            <x-vyd.stepper :steps="['Seleccionar', 'Validar', 'Confirmar', 'Resultado']" :current="2" :completed="[1]" />
            <x-vyd.preview-table :columns="['status' => 'Estado', 'caso' => 'Caso']" :rows="[['status' => 'created', 'caso' => '100']]" status-column="status" />
            <x-vyd.file-upload-card model="archivo" />
            <x-vyd.alert-summary-card title="Asuntos sin responsable" count="4" description="Convenios o querellas sin abogado asignado." icon="heroicon-o-user-minus" tone="action" />
            <x-vyd.priority-badge priority="action" />
            <x-vyd.alert-badge type="documents" />
            <x-vyd.alert-row
                priority="review"
                record-type="Convenio"
                identifier="Convenio CV-2026-0008"
                secondary="Sin documentos asociados."
                :alert-types="['documents', 'activity']"
                responsible="Por revisar"
                date="21-07-2026"
                suggested-action="Revisar documentos"
                action-url="/admin/convenios/1"
                action-label="Abrir convenio"
            />
        BLADE);

        $this->assertStringContainsString('Creados', $html);
        $this->assertStringContainsString('Validacion completa', $html);
        $this->assertStringContainsString('Seleccionar', $html);
        $this->assertStringContainsString('100', $html);
        $this->assertStringContainsString('vyd-metric__value', $html);
        $this->assertStringContainsString('vyd-preview-table-wrap', $html);
        $this->assertStringContainsString('vyd-preview-table__cell--caso', $html);
        $this->assertStringContainsString('vyd-upload__input', $html);
        $this->assertStringContainsString('vyd-upload__button', $html);
        $this->assertStringContainsString('Asuntos sin responsable', $html);
        $this->assertStringContainsString('rounded-full', $html);
        $this->assertStringContainsString('Sin documentos', $html);
        $this->assertStringContainsString('Convenio CV-2026-0008', $html);
        $this->assertStringContainsString('lg:grid-cols-[180px_minmax(0,1fr)_auto]', $html);
        $this->assertStringNotContainsString('etapa ac', $html);
        $this->assertStringNotContainsString('Ningún archivo seleccionado', $html);
        $this->assertStringNotContainsString('Ningun archivo seleccionado', $html);
    }
}

<?php

namespace Tests\Feature;

use App\Filament\Resources\ConvenioResource;
use App\Filament\Resources\ConvenioResource\Pages\ImportConvenios;
use App\Filament\Resources\ImportacionHistorialResource;
use App\Filament\Resources\QuerellaResource;
use App\Filament\Resources\QuerellaResource\Pages\ImportQuerellas;
use App\Models\Convenio;
use App\Models\ImportacionHistorial;
use App\Models\User;
use App\Services\ChilquintaCsvImporter;
use Database\Seeders\RolesAndPermissionsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Livewire\Livewire;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class ChilquintaImportFlowTest extends TestCase
{
    use RefreshDatabase;

    public function test_authorized_user_can_open_import_pages(): void
    {
        $admin = $this->adminUser();
        $this->actingAs($admin);

        $this->get(ConvenioResource::getUrl('import'))
            ->assertOk()
            ->assertSee('Importar Convenios')
            ->assertSee('Arrastra tu archivo CSV aquí')
            ->assertSee('Seleccionar archivo')
            ->assertSee('Solo archivos CSV')
            ->assertSee('Revisión')
            ->assertSee('Confirmación')
            ->assertDontSee('Ningún archivo seleccionado')
            ->assertDontSee('Ningun archivo seleccionado');

        $this->get(QuerellaResource::getUrl('import'))
            ->assertOk()
            ->assertSee('Importar Querellas')
            ->assertSee('Arrastra tu archivo CSV aquí')
            ->assertSee('Seleccionar archivo')
            ->assertDontSee('Ningún archivo seleccionado')
            ->assertDontSee('Ningun archivo seleccionado');
    }

    public function test_unauthorized_user_cannot_open_import_page(): void
    {
        $this->seed(RolesAndPermissionsSeeder::class);
        $this->actingAs(User::factory()->create());

        $this->get(ConvenioResource::getUrl('import'))->assertForbidden();
    }

    public function test_selected_file_stage_shows_replace_action_without_native_file_text(): void
    {
        $this->actingAs($this->adminUser());

        Livewire::test(ImportConvenios::class)
            ->set('archivo', UploadedFile::fake()->createWithContent(
                'convenios.csv',
                file_get_contents(base_path('tests/Fixtures/convenios_ok.csv')),
            ))
            ->assertSet('stage', 'selected')
            ->assertSee('Reemplazar archivo')
            ->assertSee('Quitar archivo')
            ->assertSee('Revisar archivo')
            ->assertSee('vyd-file-action', false)
            ->assertSee('vyd-native-file', false)
            ->assertDontSee('Ningún archivo seleccionado')
            ->assertDontSee('Ningun archivo seleccionado');
    }

    public function test_validation_preview_does_not_modify_records(): void
    {
        $this->actingAs($this->adminUser());

        Livewire::test(ImportConvenios::class)
            ->set('archivo', UploadedFile::fake()->createWithContent(
                'convenios.csv',
                file_get_contents(base_path('tests/Fixtures/convenios_ok.csv')),
            ))
            ->call('reviewFile')
            ->assertSet('stage', 'review')
            ->assertSee('Filas listas para importar')
            ->assertSee('Se creará')
            ->assertSee('Continuar a incidencias')
            ->assertSee('vyd-metrics-grid--review', false)
            ->assertSee('vyd-preview-table__cell--nis', false)
            ->assertSee('vyd-preview-table__cell--nombre', false)
            ->assertSee('vyd-preview-table__cell--comuna', false)
            ->assertSee('vyd-preview-table__cell--monto-total', false)
            ->assertDontSee('Confirmar importación')
            ->assertDontSee('Ningún archivo seleccionado')
            ->assertDontSee('encoding')
            ->assertDontSee('separador');

        $this->assertSame(0, Convenio::count());
        $this->assertSame(0, ImportacionHistorial::count());
    }

    public function test_querellas_preview_uses_spaced_table_columns(): void
    {
        $this->actingAs($this->adminUser());

        Livewire::test(ImportQuerellas::class)
            ->set('archivo', UploadedFile::fake()->createWithContent(
                'querellas.csv',
                file_get_contents(base_path('tests/Fixtures/querellas_valid.csv')),
            ))
            ->call('reviewFile')
            ->assertSet('stage', 'review')
            ->assertSee('vyd-preview-table__cell--ruc', false)
            ->assertSee('vyd-preview-table__cell--rit', false)
            ->assertSee('vyd-preview-table__cell--juzgado', false)
            ->assertSee('vyd-preview-table__cell--fecha-presentacion', false)
            ->assertSee('vyd-metrics-grid--review', false);
    }

    public function test_invalid_extension_is_rejected_before_processing(): void
    {
        $this->actingAs($this->adminUser());

        Livewire::test(ImportConvenios::class)
            ->set('archivo', UploadedFile::fake()->createWithContent('convenios.txt', 'caso,nis'.PHP_EOL.'1,2'))
            ->assertSet('stage', 'validation_error')
            ->assertSee('extension .csv');

        $this->assertSame(0, ImportacionHistorial::count());
    }

    public function test_missing_required_headings_blocks_import(): void
    {
        $this->actingAs($this->adminUser());

        Livewire::test(ImportConvenios::class)
            ->set('archivo', UploadedFile::fake()->createWithContent('convenios.csv', 'nombre,comuna'.PHP_EOL.'Persona,Vina del Mar'))
            ->call('reviewFile')
            ->assertSet('stage', 'validation_error')
            ->assertSee('Faltan columnas obligatorias')
            ->call('importRecords');

        $this->assertSame(0, ImportacionHistorial::count());
    }

    public function test_duplicate_case_inside_same_csv_is_presented_as_omitted(): void
    {
        $this->actingAs($this->adminUser());
        $path = storage_path('app/testing/convenios_duplicados.csv');
        @mkdir(dirname($path), 0777, true);
        file_put_contents($path, 'caso,nis,nombre'.PHP_EOL.'90001,123,Uno'.PHP_EOL.'90001,123,Dos');

        $analysis = app(ChilquintaCsvImporter::class)->analyze($path, ChilquintaCsvImporter::TYPE_CONVENIO);

        $this->assertSame(1, $analysis['ready_rows']);
        $this->assertSame(1, $analysis['skipped_rows']);
        $this->assertSame('skipped', $analysis['preview'][1]['__status']);
        $this->assertSame('Caso', $analysis['incidents'][0]['field']);
    }

    public function test_import_requires_explicit_confirmation_stage(): void
    {
        $this->actingAs($this->adminUser());

        Livewire::test(ImportConvenios::class)
            ->set('archivo', UploadedFile::fake()->createWithContent(
                'convenios.csv',
                file_get_contents(base_path('tests/Fixtures/convenios_ok.csv')),
            ))
            ->call('reviewFile')
            ->call('importRecords');

        $this->assertSame(0, ImportacionHistorial::count());
    }

    public function test_import_stages_render_exclusively_and_keep_preview_when_returning(): void
    {
        $this->actingAs($this->adminUser());

        Livewire::test(ImportConvenios::class)
            ->set('archivo', UploadedFile::fake()->createWithContent(
                'convenios.csv',
                file_get_contents(base_path('tests/Fixtures/convenios_ok.csv')),
            ))
            ->call('reviewFile')
            ->assertSet('stage', 'review')
            ->assertSee('Vista previa')
            ->assertSee('Continuar a incidencias')
            ->assertDontSee('Confirmar importación')
            ->call('goToIncidents')
            ->assertSet('stage', 'incidents')
            ->assertSee('Incidencias')
            ->assertSee('Sin incidencias')
            ->assertSee('Continuar a confirmación')
            ->assertDontSee('Vista previa')
            ->assertDontSee('Confirmar importación')
            ->call('goToConfirmation')
            ->assertSet('stage', 'confirmation')
            ->assertSee('Confirmar importación')
            ->assertSee('Importar registros')
            ->assertSee('vyd-summary-grid', false)
            ->assertDontSee('Vista previa')
            ->assertDontSee('Sin incidencias')
            ->assertDontSee('Ningún archivo seleccionado')
            ->call('backToReview')
            ->assertSet('stage', 'review')
            ->assertSee('Vista previa')
            ->assertSee('Se creará')
            ->assertDontSee('Confirmar importación');
    }

    public function test_result_stage_keeps_final_actions_and_summary_visible(): void
    {
        $this->actingAs($this->adminUser());

        Livewire::test(ImportConvenios::class)
            ->set('archivo', UploadedFile::fake()->createWithContent(
                'convenios.csv',
                file_get_contents(base_path('tests/Fixtures/convenios_ok.csv')),
            ))
            ->call('reviewFile')
            ->call('goToIncidents')
            ->call('goToConfirmation')
            ->call('importRecords')
            ->assertSet('stage', 'success')
            ->assertSee('Importación completada')
            ->assertSee('Realizar otra importación')
            ->assertSee('Ver registros importados')
            ->assertSee('Revisar historial')
            ->assertSee('vyd-metrics-grid--result', false)
            ->assertSee('vyd-summary-grid--compact', false)
            ->assertDontSee('Ningún archivo seleccionado');
    }

    public function test_history_is_limited_by_import_permission(): void
    {
        $this->seed(RolesAndPermissionsSeeder::class);
        ImportacionHistorial::create(['tipo_importacion' => 'convenios', 'archivo_original' => 'convenios.csv']);
        ImportacionHistorial::create(['tipo_importacion' => 'querellas', 'archivo_original' => 'querellas.csv']);

        $user = User::factory()->create();
        $user->givePermissionTo('convenios.importar');
        $this->actingAs($user);

        $this->assertTrue(ImportacionHistorialResource::canViewAny());
        $this->assertSame(['convenios'], ImportacionHistorialResource::getEloquentQuery()->pluck('tipo_importacion')->all());
    }

    public function test_history_detail_is_user_friendly_and_hides_raw_technical_details(): void
    {
        $admin = $this->adminUser();
        $this->actingAs($admin);

        app(ChilquintaCsvImporter::class)->import(
            path: base_path('tests/Fixtures/convenios_with_invalid_row.csv'),
            type: ChilquintaCsvImporter::TYPE_CONVENIO,
            userId: $admin->id,
            originalFilename: 'convenios_with_invalid_row.csv',
        );

        $record = ImportacionHistorial::query()->firstOrFail();

        $this->get(ImportacionHistorialResource::getUrl('view', ['record' => $record]))
            ->assertOk()
            ->assertSee('Contexto de la importacion')
            ->assertSee('Incidencias')
            ->assertSee('Requiere correccion')
            ->assertDontSee('ruta_real')
            ->assertDontSee('encoding')
            ->assertDontSee('separador');
    }

    private function adminUser(): User
    {
        $this->seed(RolesAndPermissionsSeeder::class);

        $user = User::factory()->create();
        $user->assignRole(Role::findByName('administrador'));

        return $user;
    }
}

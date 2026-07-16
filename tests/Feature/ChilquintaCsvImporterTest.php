<?php

namespace Tests\Feature;

use App\Filament\Resources\ConvenioResource\Pages\ListConvenios;
use App\Models\Convenio;
use App\Models\ImportacionHistorial;
use App\Models\Querella;
use App\Models\User;
use App\Services\ChilquintaCsvImporter;
use Database\Seeders\RolesAndPermissionsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class ChilquintaCsvImporterTest extends TestCase
{
    use RefreshDatabase;

    public function test_normalizes_known_chilquinta_headings(): void
    {
        $this->assertSame('energia_ventana', ChilquintaCsvImporter::normalizeHeading(' energía-ventana '));
        $this->assertSame('energia_fv', ChilquintaCsvImporter::normalizeHeading('energía_FV'));
        $this->assertSame('tipo_cnr', ChilquintaCsvImporter::normalizeHeading('tipo_CNR'));
        $this->assertSame('direccion', ChilquintaCsvImporter::normalizeHeading('dirección'));
        $this->assertSame('telefono', ChilquintaCsvImporter::normalizeHeading('teléfonos'));
        $this->assertSame('fecha_presentacion', ChilquintaCsvImporter::normalizeHeading('fecha_presentación'));
    }

    public function test_imports_convenios_with_bom_decimals_and_multiple_phones(): void
    {
        $this->seed(RolesAndPermissionsSeeder::class);
        $user = User::factory()->create();
        $this->actingAs($user);

        $path = $this->fixturePath('convenios_bom.csv');

        $result = app(ChilquintaCsvImporter::class)->import(
            path: $path,
            type: ChilquintaCsvImporter::TYPE_CONVENIO,
            userId: $user->id,
            originalFilename: 'convenios_bom.csv',
        );

        $this->assertSame(2, $result['total']);
        $this->assertSame(2, $result['created']);
        $this->assertSame(0, $result['updated']);
        $this->assertSame(0, $result['skipped']);
        $this->assertSame(0, $result['errors']);
        $this->assertDatabaseHas('convenios', [
            'caso' => '10001',
            'energia_ventana' => '120.50',
            'monto_total' => '117000.75',
            'telefono' => '+56912345678 / 32-2223333',
        ]);
    }

    public function test_imports_querella_dv_k_rit_and_datetime(): void
    {
        $this->seed(RolesAndPermissionsSeeder::class);
        $user = User::factory()->create();
        $this->actingAs($user);

        $result = app(ChilquintaCsvImporter::class)->import(
            path: $this->fixturePath('querellas_valid.csv'),
            type: ChilquintaCsvImporter::TYPE_QUERELLA,
            userId: $user->id,
            originalFilename: 'querellas_valid.csv',
        );

        $this->assertSame(2, $result['total']);
        $this->assertSame(2, $result['created']);
        $this->assertSame(0, $result['errors']);

        $querella = Querella::query()->where('caso', '20001')->firstOrFail();

        $this->assertSame('K', $querella->ruc_dv);
        $this->assertSame('O-2215-2026', $querella->rit);
        $this->assertSame('2026-06-26 10:50:05', $querella->fecha_presentacion->format('Y-m-d H:i:s'));
    }

    public function test_updates_by_caso_and_invalid_row_does_not_stop_next_rows(): void
    {
        $this->seed(RolesAndPermissionsSeeder::class);
        Convenio::create([
            'numero' => '10001',
            'caso' => '10001',
            'nis' => 'old',
            'nis_convenio' => 'old',
            'monto_total' => '1.00',
        ]);

        $user = User::factory()->create();
        $this->actingAs($user);

        $result = app(ChilquintaCsvImporter::class)->import(
            path: $this->fixturePath('convenios_with_invalid_row.csv'),
            type: ChilquintaCsvImporter::TYPE_CONVENIO,
            userId: $user->id,
            originalFilename: 'convenios_with_invalid_row.csv',
        );

        $this->assertSame(1, $result['created']);
        $this->assertSame(1, $result['updated']);
        $this->assertSame(0, $result['skipped']);
        $this->assertSame(1, $result['errors']);
        $this->assertNotNull($result['error_report_path']);
        Storage::disk('local')->assertExists($result['error_report_path']);

        $this->assertDatabaseHas('convenios', [
            'caso' => '10001',
            'monto_total' => '2500.50',
        ]);
        $this->assertDatabaseHas('convenios', [
            'caso' => '10003',
            'nis' => '777',
        ]);
        $this->assertSame(1, ImportacionHistorial::query()->where('estado', 'con_errores')->count());
    }

    public function test_convenios_ok_three_rows_have_isolated_counters(): void
    {
        $this->seed(RolesAndPermissionsSeeder::class);
        $user = User::factory()->create();
        $this->actingAs($user);

        $result = app(ChilquintaCsvImporter::class)->import(
            path: $this->fixturePath('convenios_ok.csv'),
            type: ChilquintaCsvImporter::TYPE_CONVENIO,
            userId: $user->id,
            originalFilename: 'convenios_ok.csv',
            storedFilename: 'imports/convenio/batch/convenios_ok.csv',
        );

        $this->assertSame(3, $result['total']);
        $this->assertSame(3, $result['created']);
        $this->assertSame(0, $result['updated']);
        $this->assertSame(0, $result['skipped']);
        $this->assertSame(0, $result['errors']);
        $this->assertSame(3, Convenio::count());
    }

    public function test_convenios_error_four_invalid_rows_are_errors_not_skipped(): void
    {
        $this->seed(RolesAndPermissionsSeeder::class);
        $user = User::factory()->create();
        $this->actingAs($user);

        $result = app(ChilquintaCsvImporter::class)->import(
            path: $this->fixturePath('convenios_error.csv'),
            type: ChilquintaCsvImporter::TYPE_CONVENIO,
            userId: $user->id,
            originalFilename: 'convenios_error.csv',
        );

        $this->assertSame(4, $result['total']);
        $this->assertSame(0, $result['created']);
        $this->assertSame(0, $result['updated']);
        $this->assertSame(0, $result['skipped']);
        $this->assertSame(4, $result['errors']);
        $this->assertSame(0, Convenio::count());
    }

    public function test_second_import_updates_by_case_without_duplicate(): void
    {
        $this->seed(RolesAndPermissionsSeeder::class);
        $user = User::factory()->create();
        $this->actingAs($user);

        app(ChilquintaCsvImporter::class)->import(
            path: $this->fixturePath('convenios_ok.csv'),
            type: ChilquintaCsvImporter::TYPE_CONVENIO,
            userId: $user->id,
            originalFilename: 'convenios_ok.csv',
        );

        $result = app(ChilquintaCsvImporter::class)->import(
            path: $this->fixturePath('convenios_update.csv'),
            type: ChilquintaCsvImporter::TYPE_CONVENIO,
            userId: $user->id,
            originalFilename: 'convenios_update.csv',
        );

        $this->assertSame(1, $result['total']);
        $this->assertSame(0, $result['created']);
        $this->assertSame(1, $result['updated']);
        $this->assertSame(0, $result['errors']);
        $this->assertSame(3, Convenio::count());
        $this->assertDatabaseHas('convenios', [
            'caso' => '30001',
            'monto_total' => '9999.99',
        ]);
    }

    public function test_two_files_in_same_folder_do_not_mix_or_accumulate_counters(): void
    {
        $this->seed(RolesAndPermissionsSeeder::class);
        $user = User::factory()->create();
        $this->actingAs($user);

        $first = app(ChilquintaCsvImporter::class)->import(
            path: $this->fixturePath('same_folder_a.csv'),
            type: ChilquintaCsvImporter::TYPE_CONVENIO,
            userId: $user->id,
            originalFilename: 'same_folder_a.csv',
        );
        $second = app(ChilquintaCsvImporter::class)->import(
            path: $this->fixturePath('same_folder_b.csv'),
            type: ChilquintaCsvImporter::TYPE_CONVENIO,
            userId: $user->id,
            originalFilename: 'same_folder_b.csv',
        );

        $this->assertSame(1, $first['total']);
        $this->assertSame(1, $first['created']);
        $this->assertSame(1, $second['total']);
        $this->assertSame(1, $second['created']);
        $this->assertSame(2, Convenio::count());
    }

    public function test_preview_analysis_does_not_create_or_update_records(): void
    {
        $this->seed(RolesAndPermissionsSeeder::class);

        $analysis = app(ChilquintaCsvImporter::class)->analyze(
            path: $this->fixturePath('convenios_ok.csv'),
            type: ChilquintaCsvImporter::TYPE_CONVENIO,
        );

        $this->assertSame(3, $analysis['total_rows']);
        $this->assertSame(3, $analysis['valid_rows']);
        $this->assertSame(0, Convenio::count());
        $this->assertSame(0, ImportacionHistorial::count());
    }

    public function test_import_permission_visibility_uses_existing_roles(): void
    {
        $this->seed(RolesAndPermissionsSeeder::class);

        $admin = User::factory()->create();
        $admin->assignRole(Role::findByName('administrador'));

        $reader = User::factory()->create();
        $reader->assignRole(Role::findByName('cliente_chilquinta_lectura'));

        $this->actingAs($admin);
        $this->assertTrue((bool) $admin->can('convenios.importar'));
        $this->assertTrue((bool) $admin->can('querellas.importar'));
        $this->assertTrue(ListConvenios::getResource()::canViewAny());

        $this->actingAs($reader);
        $this->assertFalse((bool) $reader->can('convenios.importar'));
        $this->assertFalse((bool) $reader->can('querellas.importar'));
    }

    private function fixturePath(string $filename): string
    {
        return base_path("tests/Fixtures/{$filename}");
    }
}

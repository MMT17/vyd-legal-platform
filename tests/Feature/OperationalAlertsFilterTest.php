<?php

namespace Tests\Feature;

use App\Filament\Pages\OperationalAlerts;
use App\Models\Contacto;
use App\Models\Convenio;
use App\Models\Documento;
use App\Models\Proceso;
use App\Models\Querella;
use App\Models\User;
use App\Support\OperationalAlerts as OperationalAlertsData;
use Database\Seeders\RolesAndPermissionsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;
use Livewire\Livewire;
use ReflectionClass;
use ReflectionProperty;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class OperationalAlertsFilterTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Cache::flush();
    }

    public function test_operational_alerts_page_renders_without_server_error(): void
    {
        $this->actingAs($this->adminUser());

        $this->get(OperationalAlerts::getUrl())
            ->assertOk()
            ->assertSee('Asuntos que requieren atención')
            ->assertSee('Todos')
            ->assertSee('asuntos visibles');
    }

    public function test_operational_alert_filters_group_and_filter_visible_rows(): void
    {
        $this->actingAs($this->adminUser());
        $rows = $this->seedOperationalInboxDataset();
        $page = app(OperationalAlerts::class);

        $this->assertCount(5, $rows);
        $this->assertCount(5, OperationalAlertsData::operationalInbox());

        $page->setFilter('all');
        $this->assertSame('all', $page->activeFilter);
        $this->assertCount(5, $page->filteredRowsForDisplay($rows));

        $page->setFilter('convenios');
        $this->assertCount(2, $page->filteredRowsForDisplay($rows));
        $this->assertTrue($page->filteredRowsForDisplay($rows)->every(fn (array $row): bool => $row['module'] === 'convenio'));

        $page->setFilter('querellas');
        $this->assertCount(1, $page->filteredRowsForDisplay($rows));
        $this->assertTrue($page->filteredRowsForDisplay($rows)->every(fn (array $row): bool => $row['module'] === 'querella'));

        $page->setFilter('procesos');
        $this->assertCount(1, $page->filteredRowsForDisplay($rows));
        $this->assertSame('proceso', $page->filteredRowsForDisplay($rows)->first()['module']);

        $page->setFilter('contactos');
        $this->assertCount(1, $page->filteredRowsForDisplay($rows));
        $this->assertSame('contacto', $page->filteredRowsForDisplay($rows)->first()['module']);

        $page->setFilter('without_lawyer');
        $withoutLawyer = $page->filteredRowsForDisplay($rows);
        $this->assertCount(1, $withoutLawyer);
        $this->assertSame('Convenio CV-2026-0002', $withoutLawyer->first()['identifier']);

        $page->setFilter('without_documents');
        $this->assertCount(2, $page->filteredRowsForDisplay($rows));

        $page->setFilter('without_activity');
        $this->assertCount(1, $page->filteredRowsForDisplay($rows));

        $page->setFilter('without_date');
        $this->assertCount(1, $page->filteredRowsForDisplay($rows));
        $this->assertSame('Proceso audiencia', $page->filteredRowsForDisplay($rows)->first()['identifier']);

        $page->setFilter('pending_contacts');
        $this->assertCount(1, $page->filteredRowsForDisplay($rows));
        $this->assertStringStartsWith('Convenio CV-2026-0004', $page->filteredRowsForDisplay($rows)->first()['identifier']);

        $groupedConvenio = $rows->firstWhere('identifier', 'Convenio CV-2026-0002');
        $this->assertNotNull($groupedConvenio);
        $this->assertEqualsCanonicalizing(['without_activity', 'without_lawyer'], $groupedConvenio['alert_types']);
        $this->assertCount(2, $groupedConvenio['alerts']);
        $this->assertSame('convenio', $groupedConvenio['module']);
        $this->assertSame('Convenio CV-2026-0002', $groupedConvenio['identifier']);

        $summaryCards = collect($page->summaryCardsFor($rows))->keyBy('title');
        $this->assertSame(2, $summaryCards->get('Asuntos sin documentos')['count']);
        $page->setFilter('without_documents');
        $this->assertSame(
            $summaryCards->get('Asuntos sin documentos')['count'],
            $page->filteredRowsForDisplay($rows)->count(),
        );

        $page->setFilter('without_date');
        $this->assertCount(0, $page->filteredRowsForDisplay($rows->where('module', 'convenio')->values()));

        $page->setFilter('Filtro inexistente');
        $this->assertSame('all', $page->activeFilter);
    }

    public function test_livewire_filter_updates_do_not_fail(): void
    {
        $this->actingAs($this->adminUser());
        $this->seedOperationalInboxDataset();

        $component = Livewire::test(OperationalAlerts::class)
            ->assertSet('activeFilter', 'all')
            ->assertSee('Convenio CV-2026-0001')
            ->assertSee('Querella Q-2026-0001')
            ->assertSee('Proceso audiencia')
            ->assertSee('Convenio CV-2026-0004');

        foreach ([
            'convenios',
            'querellas',
            'procesos',
            'contactos',
            'without_lawyer',
            'without_documents',
            'without_activity',
            'without_date',
            'pending_contacts',
            'all',
        ] as $filter) {
            $component
                ->set('activeFilter', $filter)
                ->assertSet('activeFilter', $filter)
                ->assertSee('asuntos visibles');
        }
    }

    public function test_incomplete_cached_inbox_is_rebuilt_without_livewire_error(): void
    {
        $this->actingAs($admin = $this->adminUser());
        $this->seedOperationalInboxDataset();

        Cache::put('operational-alerts.inbox.' . $admin->id, unserialize('O:12:"MissingClass":0:{}'), 60);

        Livewire::test(OperationalAlerts::class)
            ->set('activeFilter', 'without_documents')
            ->assertSet('activeFilter', 'without_documents')
            ->assertSee('Convenio CV-2026-0001')
            ->assertSee('Querella Q-2026-0001')
            ->assertDontSee('Error al cargar la página');
    }

    public function test_livewire_filter_actions_update_visible_rows(): void
    {
        $this->actingAs($this->adminUser());
        $this->seedOperationalInboxDataset();

        Livewire::test(OperationalAlerts::class)
            ->call('setFilter', 'convenios')
            ->assertSet('activeFilter', 'convenios')
            ->assertSee('Convenio CV-2026-0001')
            ->assertSee('Convenio CV-2026-0002')
            ->assertDontSee('Querella Q-2026-0001')
            ->call('setFilter', 'querellas')
            ->assertSet('activeFilter', 'querellas')
            ->assertSee('Querella Q-2026-0001')
            ->assertDontSee('Convenio CV-2026-0001')
            ->call('setFilter', 'without_lawyer')
            ->assertSet('activeFilter', 'without_lawyer')
            ->assertSee('Convenio CV-2026-0002')
            ->assertSee('Sin actividad')
            ->assertSee('Sin abogado')
            ->assertDontSee('Convenio CV-2026-0001');
    }

    public function test_alert_rows_render_unique_wire_keys(): void
    {
        $this->actingAs($this->adminUser());
        $this->seedOperationalInboxDataset();

        Livewire::test(OperationalAlerts::class)
            ->call('setFilter', 'all')
            ->assertSeeHtml('wire:key="operational-alert-convenio:')
            ->assertSeeHtml('wire:key="operational-alert-querella:')
            ->assertSeeHtml('wire:key="operational-alert-proceso:')
            ->assertSeeHtml('wire:key="operational-alert-contacto:');
    }

    public function test_livewire_empty_filter_result_shows_empty_state(): void
    {
        $this->actingAs($this->adminUser());
        $this->seedOperationalInboxDataset();

        Livewire::test(OperationalAlerts::class)
            ->set('activeFilter', 'without_date')
            ->assertSet('activeFilter', 'without_date')
            ->assertSee('Proceso audiencia')
            ->set('activeFilter', 'pending_contacts')
            ->assertSet('activeFilter', 'pending_contacts')
            ->assertSee('Convenio CV-2026-0004')
            ->set('activeFilter', 'without_lawyer')
            ->assertSet('activeFilter', 'without_lawyer')
            ->assertSee('Convenio CV-2026-0002')
            ->set('activeFilter', 'contactos')
            ->assertSet('activeFilter', 'contactos')
            ->assertSee('Convenio CV-2026-0004');
    }

    public function test_livewire_empty_filter_result_shows_filter_empty_state(): void
    {
        $this->actingAs($this->adminUser());

        $convenio = Convenio::query()->create([
            'numero' => 'CV-2026-0099',
            'abogado_responsable' => 'Abogada Responsable',
            'nombre_abogado' => 'Abogada Responsable',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        Documento::query()->create([
            'documentable_type' => $convenio->getMorphClass(),
            'documentable_id' => $convenio->id,
            'nombre' => 'Contrato',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        Proceso::query()->create([
            'procesable_type' => $convenio->getMorphClass(),
            'procesable_id' => $convenio->id,
            'nombre' => 'Proceso sin fecha',
            'fecha' => null,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        Cache::flush();

        Livewire::test(OperationalAlerts::class)
            ->set('activeFilter', 'convenios')
            ->assertSet('activeFilter', 'convenios')
            ->assertSee('No hay asuntos para este filtro')
            ->assertSee('Prueba seleccionar otro filtro o volver a Todos.');
    }

    public function test_empty_inbox_does_not_break_livewire_updates(): void
    {
        $this->actingAs($this->adminUser());

        Livewire::test(OperationalAlerts::class)
            ->set('activeFilter', 'convenios')
            ->assertSet('activeFilter', 'convenios')
            ->assertSee('No hay asuntos que requieran atención');
    }

    public function test_operational_alerts_only_stores_simple_public_state(): void
    {
        $properties = collect((new ReflectionClass(OperationalAlerts::class))
            ->getProperties(ReflectionProperty::IS_PUBLIC))
            ->filter(fn (ReflectionProperty $property): bool => $property->getDeclaringClass()->getName() === OperationalAlerts::class)
            ->mapWithKeys(fn (ReflectionProperty $property): array => [$property->getName() => (string) $property->getType()])
            ->all();

        $this->assertSame(['activeFilter' => 'string'], $properties);
    }

    private function adminUser(): User
    {
        $this->seed(RolesAndPermissionsSeeder::class);

        $user = User::factory()->create();
        $user->assignRole(Role::findByName('administrador'));

        return $user;
    }

    /**
     * @return \Illuminate\Support\Collection<int, array<string, mixed>>
     */
    private function seedOperationalInboxDataset(): \Illuminate\Support\Collection
    {
        $now = now();
        $old = now()->subDays(45);

        Convenio::query()->create([
            'numero' => 'CV-2026-0001',
            'abogado_responsable' => 'Abogada Responsable',
            'nombre_abogado' => 'Abogada Responsable',
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        $convenioWithoutLawyerAndActivity = Convenio::query()->create([
            'numero' => 'CV-2026-0002',
            'abogado_responsable' => null,
            'nombre_abogado' => null,
        ]);
        $convenioWithoutLawyerAndActivity->forceFill([
            'created_at' => $old,
            'updated_at' => $old,
        ])->saveQuietly();

        Documento::query()->create([
            'documentable_type' => $convenioWithoutLawyerAndActivity->getMorphClass(),
            'documentable_id' => $convenioWithoutLawyerAndActivity->id,
            'nombre' => 'Contrato',
            'created_at' => $old,
            'updated_at' => $old,
        ]);

        Querella::query()->create([
            'numero' => 'Q-2026-0001',
            'abogado_responsable' => 'Abogada Responsable',
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        $procesoParent = Convenio::query()->create([
            'numero' => 'CV-2026-0003',
            'abogado_responsable' => 'Abogada Responsable',
            'nombre_abogado' => 'Abogada Responsable',
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        Documento::query()->create([
            'documentable_type' => $procesoParent->getMorphClass(),
            'documentable_id' => $procesoParent->id,
            'nombre' => 'Contrato',
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        Proceso::query()->create([
            'procesable_type' => $procesoParent->getMorphClass(),
            'procesable_id' => $procesoParent->id,
            'nombre' => 'Proceso audiencia',
            'fecha' => null,
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        $contactoParent = Convenio::query()->create([
            'numero' => 'CV-2026-0004',
            'abogado_responsable' => 'Abogada Responsable',
            'nombre_abogado' => 'Abogada Responsable',
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        Documento::query()->create([
            'documentable_type' => $contactoParent->getMorphClass(),
            'documentable_id' => $contactoParent->id,
            'nombre' => 'Contrato',
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        Contacto::query()->create([
            'convenio_id' => $contactoParent->id,
            'comentario' => 'Contacto marcado como importante.',
            'importante' => true,
            'fecha' => null,
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        \Spatie\Activitylog\Models\Activity::query()->delete();
        Cache::flush();

        return OperationalAlertsData::operationalInbox();
    }

    /**
     * @return array<string, array<int, array<string, mixed>>>
     */
    private function sections(): array
    {
        $date = Carbon::parse('2026-07-21 10:00:00');

        return [
            'Convenios' => [
                [
                    'type' => 'Convenio sin documentos',
                    'record' => 'Convenio CV-2026-0001',
                    'description' => 'No tiene documentos relacionados.',
                    'date' => $date,
                    'url' => '/admin/convenios/1',
                    'color' => 'warning',
                ],
                [
                    'type' => 'Convenio sin abogado responsable',
                    'record' => 'Convenio CV-2026-0001',
                    'description' => 'Falta abogado responsable.',
                    'date' => $date,
                    'url' => '/admin/convenios/1',
                    'color' => 'danger',
                ],
                [
                    'type' => 'Convenio sin actividad hace 30 días',
                    'record' => 'Convenio CV-2026-0002',
                    'description' => 'No registra actividad reciente.',
                    'date' => $date,
                    'url' => '/admin/convenios/2',
                    'color' => 'danger',
                ],
            ],
            'Querellas' => [
                [
                    'type' => 'Querella sin documentos',
                    'record' => 'Querella Q-2026-0001',
                    'description' => 'No tiene documentos relacionados.',
                    'date' => $date,
                    'url' => '/admin/querellas/1',
                    'color' => 'warning',
                ],
            ],
            'Procesos' => [
                [
                    'type' => 'Proceso sin fecha',
                    'record' => 'Proceso audiencia',
                    'description' => 'El proceso no tiene fecha registrada.',
                    'date' => $date,
                    'url' => '/admin/procesos/1',
                    'color' => 'warning',
                ],
            ],
            'Contactos' => [
                [
                    'type' => 'Contacto importante pendiente',
                    'record' => 'Contacto #8',
                    'description' => 'Contacto marcado como importante.',
                    'date' => $date,
                    'url' => '/admin/contactos/8',
                    'color' => 'info',
                ],
            ],
        ];
    }

    /**
     * @return array<string, array<int, array<string, mixed>>>
     */
    private function sectionsWithoutConvenios(): array
    {
        $sections = $this->sections();
        $sections['Convenios'] = [];

        return $sections;
    }
}

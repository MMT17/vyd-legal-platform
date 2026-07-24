<?php

namespace Tests\Feature;

use App\Models\Cms\PracticeArea;
use App\Models\Cms\TeamMember;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PublicSiteInteractionTest extends TestCase
{
    use RefreshDatabase;

    public function test_team_cards_render_summaries_and_detail_templates(): void
    {
        TeamMember::query()->create([
            'name' => 'Mauricio Dominguez Delgado',
            'slug' => 'mauricio-dominguez',
            'position' => 'Socio',
            'specialties' => ['Derecho Civil'],
            'education' => ['Abogado'],
            'experience' => ['Experiencia profesional'],
            'activities' => ['Trabajo anterior'],
            'email' => 'mauricio@vydabogados.cl',
            'is_partner' => true,
            'is_active' => true,
            'sort_order' => 1,
        ]);

        TeamMember::query()->create([
            'name' => 'Cristian Vicencio Verdugo',
            'slug' => 'cristian-vicencio',
            'position' => 'Socio',
            'is_partner' => true,
            'is_active' => true,
            'sort_order' => 2,
        ]);

        TeamMember::query()->create([
            'name' => 'Esteban Carrasco',
            'slug' => 'esteban-carrasco',
            'position' => 'Abogado',
            'is_partner' => false,
            'is_active' => true,
            'sort_order' => 3,
        ]);

        $response = $this->get(route('public.team'));

        $response
            ->assertOk()
            ->assertSeeInOrder([
                'Mauricio Dominguez Delgado',
                'Cristian Vicencio Verdugo',
                'Esteban Carrasco',
            ])
            ->assertSee('data-team-toggle', false)
            ->assertSee('data-team-template', false)
            ->assertSee('profile-detail-panel', false)
            ->assertSee('scroll-margin-top: 104px', false)
            ->assertSee('scrollIntoView', false)
            ->assertSee('mailto:mauricio@vydabogados.cl', false)
            ->assertSee('Educaci&oacute;n', false)
            ->assertSee('Experiencia', false)
            ->assertSee('Trabajos anteriores', false);
    }

    public function test_practice_areas_render_overlay_without_extra_buttons_or_links(): void
    {
        PracticeArea::query()->create([
            'title' => 'Derecho Civil',
            'slug' => 'derecho-civil',
            'excerpt' => 'Asesoria y representacion en materias civiles y patrimoniales con una mirada practica.',
            'content' => '<p>Contenido largo para el detalle interno.</p>',
            'is_active' => true,
            'sort_order' => 1,
        ]);

        $response = $this->get(route('public.practice-areas.index'));

        $response
            ->assertOk()
            ->assertSee('data-area-card', false)
            ->assertSee('area-card__overlay', false)
            ->assertSee('area-card__description', false)
            ->assertSee('Derecho Civil')
            ->assertSee('Asesoria y representacion en materias civiles', false)
            ->assertDontSee('area-card__placeholder">&Aacute;rea', false)
            ->assertDontSee('pagination', false)
            ->assertDontSee('Showing', false)
            ->assertDontSee('Conocer m&aacute;s', false)
            ->assertDontSee('Ver m&aacute;s', false);
    }
}

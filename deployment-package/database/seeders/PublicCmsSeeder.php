<?php

namespace Database\Seeders;

use App\Models\Cms\Page;
use App\Models\Cms\PracticeArea;
use Illuminate\Database\Seeder;

class PublicCmsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $home = Page::firstOrCreate(
            ['slug' => 'home'],
            [
                'title' => 'VYD Abogados',
                'meta_title' => 'VYD Abogados',
                'meta_description' => 'Estudio juridico VYD Abogados.',
                'sort_order' => 1,
                'is_active' => true,
            ],
        );

        $home->sections()->updateOrCreate(
            ['key' => 'intro'],
            [
                'title' => 'Quienes somos',
                'subtitle' => 'Un estudio juridico orientado a entregar soluciones claras y oportunas.',
                'content' => '<p>Acompanamos a personas y empresas en la gestion de sus asuntos legales con seriedad, experiencia y compromiso.</p>',
                'sort_order' => 1,
                'is_active' => true,
            ],
        );

        $about = Page::firstOrCreate(
            ['slug' => 'nosotros'],
            [
                'title' => 'Nosotros',
                'meta_title' => 'Nosotros | VYD Abogados',
                'meta_description' => 'Informacion institucional de VYD Abogados.',
                'sort_order' => 2,
                'is_active' => true,
            ],
        );

        $about->sections()->updateOrCreate(
            ['key' => 'historia'],
            [
                'title' => 'Nuestro estudio',
                'subtitle' => 'Contenido institucional editable.',
                'content' => '<p>Esta seccion queda lista para reemplazar el contenido publico actual.</p>',
                'sort_order' => 1,
                'is_active' => true,
            ],
        );

        foreach ([
            [
                'title' => 'Derecho Civil',
                'slug' => 'derecho-civil',
                'excerpt' => 'Asesoria y representacion en materias civiles y patrimoniales.',
                'content' => '<p>Apoyamos la resolucion de conflictos civiles con una mirada practica, seria y orientada a resultados.</p>',
            ],
            [
                'title' => 'Derecho Laboral',
                'slug' => 'derecho-laboral',
                'excerpt' => 'Acompanamiento en relaciones laborales, contratos y conflictos del trabajo.',
                'content' => '<p>Prestamos asesoria laboral preventiva y representacion en controversias vinculadas al trabajo.</p>',
            ],
            [
                'title' => 'Derecho de Familia',
                'slug' => 'derecho-de-familia',
                'excerpt' => 'Orientacion juridica en asuntos familiares sensibles y relevantes.',
                'content' => '<p>Abordamos materias de familia con cercania, reserva y responsabilidad profesional.</p>',
            ],
            [
                'title' => 'Derecho Comercial',
                'slug' => 'derecho-comercial',
                'excerpt' => 'Soporte legal para empresas, contratos y actividad comercial.',
                'content' => '<p>Asesoramos a empresas y emprendedores en decisiones legales vinculadas a su operacion comercial.</p>',
            ],
        ] as $index => $area) {
            PracticeArea::firstOrCreate(
                ['slug' => $area['slug']],
                [
                    'title' => $area['title'],
                    'excerpt' => $area['excerpt'],
                    'content' => $area['content'],
                    'sort_order' => $index + 1,
                    'is_active' => true,
                ],
            );
        }
    }
}

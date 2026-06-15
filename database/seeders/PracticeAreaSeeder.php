<?php

namespace Database\Seeders;

use App\Models\Cms\PracticeArea;
use Illuminate\Database\Seeder;

class PracticeAreaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
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
            PracticeArea::updateOrCreate(
                ['slug' => $area['slug']],
                [
                    'title' => $area['title'],
                    'excerpt' => $area['excerpt'],
                    'content' => $area['content'],
                    'is_active' => true,
                    'sort_order' => $index + 1,
                ],
            );
        }
    }
}

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
                'meta_description' => 'Estudio jurídico VYD Abogados.',
                'sort_order' => 1,
                'is_active' => true,
            ],
        );

        $home->sections()->updateOrCreate(
            ['key' => 'intro'],
            [
                'title' => 'Asesoría jurídica estratégica',
                'subtitle' => 'Base de contenido para migrar desde WordPress.',
                'content' => '<p>Administra este bloque desde Filament en Sitio Público > Páginas.</p>',
                'sort_order' => 1,
                'is_active' => true,
            ],
        );

        $about = Page::firstOrCreate(
            ['slug' => 'nosotros'],
            [
                'title' => 'Nosotros',
                'meta_title' => 'Nosotros | VYD Abogados',
                'meta_description' => 'Información institucional de VYD Abogados.',
                'sort_order' => 2,
                'is_active' => true,
            ],
        );

        $about->sections()->updateOrCreate(
            ['key' => 'historia'],
            [
                'title' => 'Nuestro estudio',
                'subtitle' => 'Contenido institucional editable.',
                'content' => '<p>Esta sección queda lista para reemplazar el contenido público actual.</p>',
                'sort_order' => 1,
                'is_active' => true,
            ],
        );

        foreach ([
            ['title' => 'Litigios civiles', 'slug' => 'litigios-civiles', 'icon' => 'Litigios'],
            ['title' => 'Derecho corporativo', 'slug' => 'derecho-corporativo', 'icon' => 'Empresa'],
            ['title' => 'Gestión legal', 'slug' => 'gestion-legal', 'icon' => 'Gestión'],
        ] as $index => $area) {
            PracticeArea::firstOrCreate(
                ['slug' => $area['slug']],
                [
                    'title' => $area['title'],
                    'excerpt' => 'Área de práctica demo para validar el CMS público.',
                    'content' => '<p>Contenido editable desde Filament.</p>',
                    'icon' => $area['icon'],
                    'sort_order' => $index + 1,
                    'is_active' => true,
                ],
            );
        }
    }
}

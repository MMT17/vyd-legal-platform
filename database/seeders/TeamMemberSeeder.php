<?php

namespace Database\Seeders;

use App\Models\Cms\TeamMember;
use Illuminate\Database\Seeder;

class TeamMemberSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        foreach ($this->members() as $member) {
            TeamMember::updateOrCreate(
                ['slug' => $member['slug']],
                $member,
            );
        }
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    private function members(): array
    {
        return [
            [
                'name' => 'Mauricio Domínguez',
                'slug' => 'mauricio-dominguez',
                'position' => 'Socio',
                'short_description' => null,
                'bio' => null,
                'photo_path' => null,
                'email' => null,
                'phone' => null,
                'linkedin_url' => null,
                'specialties' => [
                    'Derecho Administrativo',
                    'Derecho Corporativo',
                    'Derecho Civil',
                    'Litigación',
                    'Propiedad Intelectual',
                    'Compras Públicas',
                ],
                'education' => [
                    'Abogado (2019)',
                    'Diplomado, “Cumplimiento Normativo y Gestión de riesgos de la Empresa”, Pontificia Universidad Católica de Valparaíso.',
                    'Diplomado, “Derecho Administrativo”, Pontificia Universidad Católica de Valparaíso.',
                    'Acreditación en Compras Públicas, Mercado Público.',
                    'Curso “Inducción General de la Administración del Estado”, Contraloría General de la República.',
                ],
                'experience' => null,
                'activities' => null,
                'is_partner' => true,
                'is_active' => true,
                'sort_order' => 1,
            ],
            [
                'name' => 'Cristián Vicencio',
                'slug' => 'cristian-vicencio',
                'position' => 'Socio',
                'short_description' => null,
                'bio' => null,
                'photo_path' => null,
                'email' => null,
                'phone' => null,
                'linkedin_url' => null,
                'specialties' => [
                    'Derecho Civil',
                    'Derecho del Trabajo',
                    'Regulación Eléctrica',
                    'Litigación',
                    'Derecho Administrativo',
                    'Negociaciones',
                ],
                'education' => [
                    'Abogado',
                    'Diplomado, “Tributación Aplicada a la Empresa”, Universidad Adolfo Ibáñez.',
                    'Diplomado, “Regulación Eléctrica”, Universidad de Chile.',
                    'Diplomado, “Compras Públicas”, Universidad de Viña del Mar.',
                ],
                'experience' => null,
                'activities' => null,
                'is_partner' => true,
                'is_active' => true,
                'sort_order' => 2,
            ],
            [
                'name' => 'Esteban Carrasco',
                'slug' => 'esteban-carrasco',
                'position' => 'Abogado',
                'short_description' => null,
                'bio' => null,
                'photo_path' => null,
                'email' => null,
                'phone' => null,
                'linkedin_url' => null,
                'specialties' => [
                    'Derecho Civil',
                    'Derecho Penal',
                    'Derecho Concursal',
                ],
                'education' => [
                    'Abogado (2023)',
                    'Diplomado en Compliance y Derecho Penal Económico - Actualización Ley N° 21.595, Ley de Delitos Económicos y Medioambientales – Universidad Adolfo Ibáñez.',
                ],
                'experience' => null,
                'activities' => null,
                'is_partner' => false,
                'is_active' => true,
                'sort_order' => 3,
            ],
            [
                'name' => 'Aldo Honorato Soto',
                'slug' => 'aldo-honorato-soto',
                'position' => 'Abogado',
                'short_description' => null,
                'bio' => null,
                'photo_path' => null,
                'email' => null,
                'phone' => null,
                'linkedin_url' => null,
                'specialties' => [
                    'Derecho Civil',
                    'Derecho Penal',
                    'Derecho Laboral',
                    'Derecho de Familia',
                    'Juzgados de Policía Local',
                    'Litigación',
                ],
                'education' => [
                    'Abogado (2021)',
                ],
                'experience' => null,
                'activities' => null,
                'is_partner' => false,
                'is_active' => true,
                'sort_order' => 4,
            ],
            [
                'name' => 'Sebastián Rojas',
                'slug' => 'sebastian-rojas',
                'position' => 'Abogado',
                'short_description' => null,
                'bio' => null,
                'photo_path' => null,
                'email' => null,
                'phone' => null,
                'linkedin_url' => null,
                'specialties' => [
                    'Derecho Laboral',
                    'Derecho Aduanero',
                    'Derecho Regulación Eléctrica',
                    'Derecho Civil',
                    'Litigación',
                ],
                'education' => [
                    'Abogado (2014)',
                    'Diplomado, “Derecho Aduanero”, Universidad Andrés Bello.',
                    'Diplomado, “Derecho Laboral de la Empresa”, Universidad de Los Andes.',
                    'Magíster en “Derecho del Trabajo”, Universidad de Los Andes.',
                    'Inglés jurídico, Universidad de Los Andes.',
                    'Máster en Derecho y Gestión Aduanera, Universidad de Barcelona.',
                    'Certified Shortsea Logistics, Escuela Europea.',
                    'Diplomado, “Regulación del Sector Eléctrico”, Universidad de Chile.',
                ],
                'experience' => null,
                'activities' => null,
                'is_partner' => false,
                'is_active' => true,
                'sort_order' => 5,
            ],
        ];
    }
}

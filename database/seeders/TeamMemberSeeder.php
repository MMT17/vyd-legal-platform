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
                ['name' => $member['name']],
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
                'position' => 'Socio',
                'short_description' => 'Profesional con experiencia en asuntos legales complejos, comprometido con soluciones efectivas y confidenciales.',
                'bio' => null,
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
                'experience' => [
                    'Hospital Carlos Van Buren.',
                    'Servicio de Salud Valparaíso-San Antonio.',
                ],
                'activities' => [
                    'Relator en capacitación “Traspaso del personal de Educación Municipal a los Servicios Locales de Educación”.',
                ],
                'is_partner' => true,
                'is_active' => true,
                'sort_order' => 1,
            ],
            [
                'name' => 'Cristián Vicencio',
                'position' => 'Socio',
                'short_description' => 'Abogado con experiencia en derecho civil, derecho del trabajo, regulación eléctrica, litigación y asuntos administrativos.',
                'bio' => null,
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
                'experience' => [
                    'Estudio Jurídico, Navia & Torres y Cía.',
                    'Estudio Jurídico, Vasseur Abogados.',
                    '1º Juzgado Civil de Quilpué.',
                    'Estudio Jurídico, Vicencio y Castro Abogados Limitada.',
                    'Estudio Jurídico, Vicencio y Domínguez Abogados Limitada.',
                ],
                'activities' => null,
                'is_partner' => true,
                'is_active' => true,
                'sort_order' => 2,
            ],
            [
                'name' => 'Esteban Carrasco',
                'position' => 'Abogado',
                'short_description' => 'Abogado orientado a la litigación y asesoría en materias civiles, penales y concursales.',
                'bio' => null,
                'specialties' => [
                    'Derecho Civil',
                    'Derecho Penal',
                    'Derecho Concursal',
                ],
                'education' => [
                    'Abogado (2023)',
                    'Diplomado en Compliance y Derecho Penal Económico - Actualización Ley N° 21.595, Ley de Delitos Económicos y Medioambientales – Universidad Adolfo Ibáñez.',
                ],
                'experience' => [
                    'Salazar e Hidalgo Abogados.',
                    'Estudio Jurídico Lena y Cía.',
                ],
                'activities' => null,
                'is_partner' => false,
                'is_active' => true,
                'sort_order' => 3,
            ],
            [
                'name' => 'Aldo Honorato Soto',
                'position' => 'Abogado',
                'short_description' => 'Abogado con práctica en materias civiles, penales, laborales, familia, policía local y litigación.',
                'bio' => null,
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
                'experience' => [
                    'Estudio Jurídico, Demaría Varas.',
                    'Ejercicio libre de la profesión.',
                ],
                'activities' => null,
                'is_partner' => false,
                'is_active' => true,
                'sort_order' => 4,
            ],
        ];
    }
}

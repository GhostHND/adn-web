<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AdnWebInitialContentSeeder extends Seeder
{
    public function run(): void
    {
        /*
        |--------------------------------------------------------------------------
        | CONFIGURACIÓN GENERAL
        |--------------------------------------------------------------------------
        */

        $settings = [
            [
                'group' => 'business',
                'key' => 'business_name',
                'value' => 'ADN Publicidad',
                'type' => 'text',
                'is_public' => true,
                'sort_order' => 10,
            ],

            [
                'group' => 'business',
                'key' => 'business_tagline',
                'value' => 'Diseño · Publicidad · Tecnología',
                'type' => 'text',
                'is_public' => true,
                'sort_order' => 20,
            ],

            [
                'group' => 'contact',
                'key' => 'phone',
                'value' => null,
                'type' => 'text',
                'is_public' => true,
                'sort_order' => 30,
            ],

            [
                'group' => 'contact',
                'key' => 'whatsapp',
                'value' => null,
                'type' => 'text',
                'is_public' => true,
                'sort_order' => 40,
            ],

            [
                'group' => 'contact',
                'key' => 'email',
                'value' => null,
                'type' => 'email',
                'is_public' => true,
                'sort_order' => 50,
            ],

            [
                'group' => 'contact',
                'key' => 'address',
                'value' => null,
                'type' => 'textarea',
                'is_public' => true,
                'sort_order' => 60,
            ],

            [
                'group' => 'social',
                'key' => 'facebook',
                'value' => null,
                'type' => 'url',
                'is_public' => true,
                'sort_order' => 70,
            ],

            [
                'group' => 'social',
                'key' => 'instagram',
                'value' => null,
                'type' => 'url',
                'is_public' => true,
                'sort_order' => 80,
            ],

            [
                'group' => 'seo',
                'key' => 'default_meta_title',
                'value' => 'ADN Publicidad',
                'type' => 'text',
                'is_public' => false,
                'sort_order' => 90,
            ],

            [
                'group' => 'seo',
                'key' => 'default_meta_description',
                'value' => 'Diseño, publicidad, impresión, personalización y soluciones tecnológicas de ADN Publicidad.',
                'type' => 'textarea',
                'is_public' => false,
                'sort_order' => 100,
            ],
        ];

        foreach ($settings as $setting) {
            DB::table('settings')->updateOrInsert(
                [
                    'key' => $setting['key'],
                ],
                [
                    ...$setting,
                    'updated_at' => now(),
                    'created_at' => now(),
                ]
            );
        }

        /*
        |--------------------------------------------------------------------------
        | PÁGINAS PRINCIPALES
        |--------------------------------------------------------------------------
        */

        $pages = [
            [
                'slug' => 'inicio',
                'name' => 'Inicio',
                'title' => 'ADN Publicidad',
                'summary' => 'Página principal del sitio web.',
                'sort_order' => 10,
            ],

            [
                'slug' => 'sobre-nosotros',
                'name' => 'Sobre nosotros',
                'title' => 'Sobre nosotros',
                'summary' => 'Información institucional de ADN Publicidad.',
                'sort_order' => 20,
            ],

            [
                'slug' => 'servicios',
                'name' => 'Servicios',
                'title' => 'Servicios',
                'summary' => 'Servicios ofrecidos por ADN Publicidad.',
                'sort_order' => 30,
            ],

            [
                'slug' => 'catalogo',
                'name' => 'Catálogo',
                'title' => 'Catálogo',
                'summary' => 'Productos disponibles para solicitar cotización.',
                'sort_order' => 40,
            ],

            [
                'slug' => 'portafolio',
                'name' => 'Portafolio',
                'title' => 'Portafolio',
                'summary' => 'Proyectos y trabajos realizados por ADN Publicidad.',
                'sort_order' => 50,
            ],

            [
                'slug' => 'contacto',
                'name' => 'Contacto',
                'title' => 'Contacto',
                'summary' => 'Información y formulario de contacto.',
                'sort_order' => 60,
            ],
        ];

        foreach ($pages as $page) {
            DB::table('pages')->updateOrInsert(
                [
                    'slug' => $page['slug'],
                ],
                [
                    ...$page,
                    'status' => 'published',
                    'published_at' => now(),
                    'updated_at' => now(),
                    'created_at' => now(),
                ]
            );
        }

        /*
        |--------------------------------------------------------------------------
        | SECCIONES INICIALES DE HOME
        |--------------------------------------------------------------------------
        */

        $homeId =
            DB::table('pages')
                ->where('slug', 'inicio')
                ->value('id');

        if ($homeId) {
            $homeSections = [
                [
                    'section_key' => 'hero',
                    'section_type' => 'hero',
                    'title' => 'Tu marca merece hacerse notar.',
                    'subtitle' => 'Ideas que se convierten en realidad',
                    'body' => 'Diseño, impresión, publicidad, personalización y soluciones tecnológicas para convertir tus ideas en proyectos que generan impacto.',
                    'sort_order' => 10,
                ],

                [
                    'section_key' => 'services',
                    'section_type' => 'services',
                    'title' => 'Soluciones para comunicar, producir y hacer crecer tu marca.',
                    'subtitle' => 'Lo que hacemos',
                    'body' => null,
                    'sort_order' => 20,
                ],

                [
                    'section_key' => 'catalog',
                    'section_type' => 'catalog',
                    'title' => 'Productos y soluciones para tu proyecto.',
                    'subtitle' => 'Catálogo',
                    'body' => null,
                    'sort_order' => 30,
                ],

                [
                    'section_key' => 'portfolio',
                    'section_type' => 'portfolio',
                    'title' => 'Nuestro trabajo habla por nosotros.',
                    'subtitle' => 'Portafolio',
                    'body' => null,
                    'sort_order' => 40,
                ],

                [
                    'section_key' => 'cta',
                    'section_type' => 'cta',
                    'title' => '¿Tienes un proyecto en mente?',
                    'subtitle' => 'Hablemos',
                    'body' => 'Cuéntanos lo que necesitas y preparemos juntos la solución.',
                    'sort_order' => 50,
                ],
            ];

            foreach ($homeSections as $section) {
                DB::table('page_sections')->updateOrInsert(
                    [
                        'page_id' => $homeId,
                        'section_key' => $section['section_key'],
                    ],
                    [
                        ...$section,
                        'page_id' => $homeId,
                        'active' => true,
                        'updated_at' => now(),
                        'created_at' => now(),
                    ]
                );
            }
        }

        $this->command?->newLine();

        $this->command?->info(
            'Contenido inicial de ADN Web cargado correctamente.'
        );

        $this->command?->line(
            'Páginas: ' .
            DB::table('pages')->count()
        );

        $this->command?->line(
            'Configuraciones: ' .
            DB::table('settings')->count()
        );

        $this->command?->line(
            'Secciones: ' .
            DB::table('page_sections')->count()
        );
    }
}
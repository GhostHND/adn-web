<?php

namespace App\Console\Commands;

use App\Models\Page;
use Illuminate\Console\Command;

class SeedWebsitePageSections extends Command
{
    protected $signature =
        'adn:seed-website-pages';

    protected $description =
        'Crea las secciones base faltantes de las páginas institucionales.';

    public function handle(): int
    {
        $this->seedAbout();
        $this->seedServices();

        $this->newLine();

        $this->info(
            'Secciones institucionales verificadas correctamente.'
        );

        return self::SUCCESS;
    }

    private function seedAbout(): void
    {
        $page =
            Page::query()
                ->where(
                    'slug',
                    'sobre-nosotros'
                )
                ->first();

        if (
            !$page
        ) {
            $this->warn(
                'No existe sobre-nosotros.'
            );

            return;
        }

        $this->createMissingSections(
            $page,
            [
                [
                    'section_key' =>
                        'hero',

                    'section_type' =>
                        'hero',

                    'title' =>
                        'Ideas que toman forma. Marcas que se hacen notar.',

                    'subtitle' =>
                        'Sobre ADN Publicidad',

                    'body' =>
                        'Combinamos creatividad, producción y tecnología para desarrollar soluciones publicitarias pensadas para las necesidades reales de cada cliente.',

                    'content' =>
                        null,

                    'settings' => [
                        'cta_label' =>
                            'Explorar catálogo',

                        'cta_url' =>
                            '/catalogo',
                    ],

                    'sort_order' =>
                        10,
                ],

                [
                    'section_key' =>
                        'story',

                    'section_type' =>
                        'story',

                    'title' =>
                        'Más que producir publicidad, construimos soluciones.',

                    'subtitle' =>
                        'Nuestra forma de trabajar',

                    'body' =>
                        'ADN Publicidad parte de una idea sencilla: escuchar lo que el cliente necesita, encontrar una solución viable y convertirla en un resultado que pueda verse, utilizarse y generar impacto.',

                    'content' => [
                        'items' => [
                            [
                                'label' =>
                                    'Enfoque',

                                'title' =>
                                    'Soluciones a medida',

                                'text' =>
                                    'Adaptamos materiales, formatos y procesos a las necesidades reales de cada proyecto.',

                                'icon' =>
                                    'sparkles',
                            ],

                            [
                                'label' =>
                                    'Proceso',

                                'title' =>
                                    'De la idea a la entrega',

                                'text' =>
                                    'Acompañamos el trabajo desde su planteamiento hasta su producción, instalación o entrega.',

                                'icon' =>
                                    'layers',
                            ],
                        ],
                    ],

                    'settings' =>
                        null,

                    'sort_order' =>
                        20,
                ],

                [
                    'section_key' =>
                        'values',

                    'section_type' =>
                        'values',

                    'title' =>
                        'Principios presentes en cada proyecto.',

                    'subtitle' =>
                        'Nuestra manera de hacer las cosas',

                    'body' =>
                        null,

                    'content' => [
                        'items' => [
                            [
                                'title' =>
                                    'Creatividad útil',

                                'text' =>
                                    'Diseñamos para comunicar y resolver, no únicamente para decorar.',

                                'icon' =>
                                    'palette',
                            ],

                            [
                                'title' =>
                                    'Atención cercana',

                                'text' =>
                                    'Entender correctamente el proyecto es parte fundamental del resultado.',

                                'icon' =>
                                    'message',
                            ],

                            [
                                'title' =>
                                    'Calidad',

                                'text' =>
                                    'Cuidamos materiales, presentación, acabados y ejecución.',

                                'icon' =>
                                    'shield',
                            ],

                            [
                                'title' =>
                                    'Evolución',

                                'text' =>
                                    'Incorporamos nuevas herramientas y tecnología para ampliar nuestras soluciones.',

                                'icon' =>
                                    'cpu',
                            ],
                        ],
                    ],

                    'settings' =>
                        null,

                    'sort_order' =>
                        30,
                ],

                [
                    'section_key' =>
                        'process',

                    'section_type' =>
                        'process',

                    'title' =>
                        'Un proceso claro desde el primer contacto.',

                    'subtitle' =>
                        'Cómo trabajamos',

                    'body' =>
                        null,

                    'content' => [
                        'items' => [
                            [
                                'label' =>
                                    '01',

                                'title' =>
                                    'Escuchamos',

                                'text' =>
                                    'Conocemos la necesidad, medidas, objetivo e idea del proyecto.',
                            ],

                            [
                                'label' =>
                                    '02',

                                'title' =>
                                    'Proponemos',

                                'text' =>
                                    'Definimos materiales, formato, diseño y solución adecuada.',
                            ],

                            [
                                'label' =>
                                    '03',

                                'title' =>
                                    'Producimos',

                                'text' =>
                                    'Ejecutamos el trabajo cuidando cada etapa del proceso.',
                            ],

                            [
                                'label' =>
                                    '04',

                                'title' =>
                                    'Entregamos',

                                'text' =>
                                    'Finalizamos el proyecto listo para cumplir su propósito.',
                            ],
                        ],
                    ],

                    'settings' =>
                        null,

                    'sort_order' =>
                        40,
                ],

                [
                    'section_key' =>
                        'cta',

                    'section_type' =>
                        'cta',

                    'title' =>
                        '¿Tienes una idea que quieres convertir en realidad?',

                    'subtitle' =>
                        'Hablemos de tu proyecto',

                    'body' =>
                        'Explora nuestro catálogo o cuéntanos lo que necesitas.',

                    'content' =>
                        null,

                    'settings' => [
                        'cta_label' =>
                            'Ver catálogo',

                        'cta_url' =>
                            '/catalogo',
                    ],

                    'sort_order' =>
                        50,
                ],
            ]
        );

        $this->line(
            'Sobre nosotros: listo.'
        );
    }

    private function seedServices(): void
    {
        $page =
            Page::query()
                ->where(
                    'slug',
                    'servicios'
                )
                ->first();

        if (
            !$page
        ) {
            $this->warn(
                'No existe servicios.'
            );

            return;
        }

        $this->createMissingSections(
            $page,
            [
                [
                    'section_key' =>
                        'hero',

                    'section_type' =>
                        'hero',

                    'title' =>
                        'Soluciones para comunicar, producir y hacer visible tu marca.',

                    'subtitle' =>
                        'Servicios ADN',

                    'body' =>
                        'Desde una pieza gráfica hasta una solución publicitaria completa: desarrollamos cada proyecto según sus necesidades reales.',

                    'content' =>
                        null,

                    'settings' => [
                        'cta_label' =>
                            'Explorar catálogo',

                        'cta_url' =>
                            '/catalogo',
                    ],

                    'sort_order' =>
                        10,
                ],

                [
                    'section_key' =>
                        'services',

                    'section_type' =>
                        'services_grid',

                    'title' =>
                        'Todo lo que necesitas para llevar una idea a producción.',

                    'subtitle' =>
                        'Lo que hacemos',

                    'body' =>
                        'Nuestros servicios pueden contratarse individualmente o combinarse según el alcance del proyecto.',

                    'content' => [
                        'items' => [
                            [
                                'title' =>
                                    'Diseño gráfico',

                                'label' =>
                                    'Creatividad',

                                'text' =>
                                    'Piezas visuales, artes publicitarios y propuestas adaptadas al formato final.',

                                'icon' =>
                                    'palette',
                            ],

                            [
                                'title' =>
                                    'Impresión publicitaria',

                                'label' =>
                                    'Producción',

                                'text' =>
                                    'Banners, stickers, material promocional y diferentes aplicaciones comerciales.',

                                'icon' =>
                                    'printer',
                            ],

                            [
                                'title' =>
                                    'Rotulación',

                                'label' =>
                                    'Visibilidad',

                                'text' =>
                                    'Elementos visuales para negocios, espacios, señalización y aplicaciones de marca.',

                                'icon' =>
                                    'sign',
                            ],

                            [
                                'title' =>
                                    'Personalización',

                                'label' =>
                                    'Tu marca',

                                'text' =>
                                    'Aplicamos identidad y conceptos visuales a diferentes productos y soportes.',

                                'icon' =>
                                    'sparkles',
                            ],

                            [
                                'title' =>
                                    'Cámaras de seguridad',

                                'label' =>
                                    'Seguridad',

                                'text' =>
                                    'Soluciones de videovigilancia para hogares, comercios y empresas.',

                                'icon' =>
                                    'camera',
                            ],

                            [
                                'title' =>
                                    'Soluciones tecnológicas',

                                'label' =>
                                    'Tecnología',

                                'text' =>
                                    'Integramos herramientas digitales cuando el proyecto requiere algo más que producción gráfica.',

                                'icon' =>
                                    'cpu',
                            ],
                        ],
                    ],

                    'settings' =>
                        null,

                    'sort_order' =>
                        20,
                ],

                [
                    'section_key' =>
                        'process',

                    'section_type' =>
                        'process',

                    'title' =>
                        'Una solución comienza entendiendo bien lo que necesitas.',

                    'subtitle' =>
                        'Del requerimiento al resultado',

                    'body' =>
                        null,

                    'content' => [
                        'items' => [
                            [
                                'label' =>
                                    '01',

                                'title' =>
                                    'Cuéntanos tu idea',

                                'text' =>
                                    'Indícanos qué necesitas, para qué lo utilizarás y sus medidas o cantidades.',
                            ],

                            [
                                'label' =>
                                    '02',

                                'title' =>
                                    'Definimos la solución',

                                'text' =>
                                    'Seleccionamos producto, material, proceso y características.',
                            ],

                            [
                                'label' =>
                                    '03',

                                'title' =>
                                    'Cotizamos',

                                'text' =>
                                    'Preparamos la solicitud con las características específicas del proyecto.',
                            ],

                            [
                                'label' =>
                                    '04',

                                'title' =>
                                    'Producimos',

                                'text' =>
                                    'Con la propuesta aprobada, el proyecto entra al proceso correspondiente.',
                            ],
                        ],
                    ],

                    'settings' =>
                        null,

                    'sort_order' =>
                        30,
                ],

                [
                    'section_key' =>
                        'cta',

                    'section_type' =>
                        'cta',

                    'title' =>
                        'Encuentra el producto que necesitas y cotízalo en línea.',

                    'subtitle' =>
                        'Catálogo ADN',

                    'body' =>
                        'Consulta opciones y envíanos directamente las características de tu proyecto.',

                    'content' =>
                        null,

                    'settings' => [
                        'cta_label' =>
                            'Ir al catálogo',

                        'cta_url' =>
                            '/catalogo',
                    ],

                    'sort_order' =>
                        40,
                ],
            ]
        );

        $this->line(
            'Servicios: listo.'
        );
    }

    private function createMissingSections(
        Page $page,
        array $sections
    ): void {
        foreach (
            $sections
            as
            $section
        ) {
            if (
                $page
                    ->sections()
                    ->where(
                        'section_key',
                        $section[
                            'section_key'
                        ]
                    )
                    ->exists()
            ) {
                continue;
            }

            $page
                ->sections()
                ->create([
                    ...$section,

                    'active' =>
                        true,
                ]);
        }
    }
}
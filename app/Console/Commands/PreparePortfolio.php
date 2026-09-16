<?php

namespace App\Console\Commands;

use App\Models\Page;
use App\Models\PortfolioCategory;
use Illuminate\Console\Command;

class PreparePortfolio extends Command
{
    protected $signature =
        'adn:prepare-portfolio';

    protected $description =
        'Prepara las categorías y contenido base del portafolio de ADN Web.';

    public function handle(): int
    {
        $this->prepareCategories();
        $this->preparePage();

        $this->newLine();

        $this->info(
            'Portafolio preparado correctamente.'
        );

        return self::SUCCESS;
    }

    private function prepareCategories(): void
    {
        $categories = [
            [
                'slug' =>
                    'diseno-grafico',

                'name' =>
                    'Diseño gráfico',

                'description' =>
                    'Diseño visual, identidad, piezas gráficas y comunicación.',

                'sort_order' =>
                    10,
            ],

            [
                'slug' =>
                    'impresion',

                'name' =>
                    'Impresión',

                'description' =>
                    'Banners, stickers y producción publicitaria impresa.',

                'sort_order' =>
                    20,
            ],

            [
                'slug' =>
                    'rotulacion',

                'name' =>
                    'Rotulación',

                'description' =>
                    'Rotulación comercial, señalización y aplicaciones de marca.',

                'sort_order' =>
                    30,
            ],

            [
                'slug' =>
                    'personalizacion',

                'name' =>
                    'Personalización',

                'description' =>
                    'Productos y piezas personalizadas para marcas y eventos.',

                'sort_order' =>
                    40,
            ],

            [
                'slug' =>
                    'seguridad',

                'name' =>
                    'Cámaras de seguridad',

                'description' =>
                    'Proyectos visuales relacionados con soluciones de videovigilancia.',

                'sort_order' =>
                    50,
            ],

            [
                'slug' =>
                    'tecnologia',

                'name' =>
                    'Soluciones tecnológicas',

                'description' =>
                    'Proyectos que integran tecnología y soluciones digitales.',

                'sort_order' =>
                    60,
            ],
        ];

        foreach (
            $categories
            as
            $category
        ) {
            PortfolioCategory::query()
                ->firstOrCreate(
                    [
                        'slug' =>
                            $category[
                                'slug'
                            ],
                    ],
                    [
                        ...$category,

                        'active' =>
                            true,
                    ]
                );
        }

        $this->line(
            'Categorías: listas.'
        );
    }

    private function preparePage(): void
    {
        $page =
            Page::query()
                ->where(
                    'slug',
                    'portafolio'
                )
                ->first();

        if (
            !$page
        ) {
            $this->warn(
                'La página portafolio no existe.'
            );

            return;
        }

        if (
            !$page
                ->sections()
                ->where(
                    'section_key',
                    'hero'
                )
                ->exists()
        ) {
            $page
                ->sections()
                ->create([
                    'section_key' =>
                        'hero',

                    'section_type' =>
                        'hero',

                    'subtitle' =>
                        'Nuestro trabajo',

                    'title' =>
                        'Ideas que se convierten en resultados visibles.',

                    'body' =>
                        'Explora algunos de los proyectos que hemos desarrollado en diseño, producción publicitaria, personalización y tecnología.',

                    'settings' =>
                        null,

                    'sort_order' =>
                        10,

                    'active' =>
                        true,
                ]);
        }

        if (
            !$page
                ->sections()
                ->where(
                    'section_key',
                    'cta'
                )
                ->exists()
        ) {
            $page
                ->sections()
                ->create([
                    'section_key' =>
                        'cta',

                    'section_type' =>
                        'cta',

                    'subtitle' =>
                        'Tu proyecto puede ser el siguiente',

                    'title' =>
                        '¿Tienes una idea que quieres hacer realidad?',

                    'body' =>
                        'Cuéntanos qué necesitas y trabajemos juntos en la solución.',

                    'settings' => [
                        'cta_label' =>
                            'Explorar catálogo',

                        'cta_url' =>
                            '/catalogo',
                    ],

                    'sort_order' =>
                        90,

                    'active' =>
                        true,
                ]);
        }

        $this->line(
            'Página pública: lista.'
        );
    }
}
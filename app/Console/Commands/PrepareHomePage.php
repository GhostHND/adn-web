<?php

namespace App\Console\Commands;

use App\Models\Page;
use App\Models\PageSection;
use Illuminate\Console\Command;

class PrepareHomePage extends Command
{
    protected $signature =
        'adn:prepare-home-page';

    protected $description =
        'Completa la configuración editable de la portada de ADN Web sin sobrescribir contenido existente.';

    public function handle(): int
    {
        $page =
            Page::query()
                ->where(
                    'slug',
                    'inicio'
                )
                ->first();

        if (
            !$page
        ) {
            $this->error(
                'No existe la página inicio.'
            );

            return self::FAILURE;
        }

        $pageUpdates = [];

        if (
            !$page->eyebrow
        ) {
            $pageUpdates[
                'eyebrow'
            ] =
                'ADN Publicidad';
        }

        if (
            !$page->meta_title
        ) {
            $pageUpdates[
                'meta_title'
            ] =
                'ADN Publicidad | Diseño, impresión y soluciones publicitarias';
        }

        if (
            !$page->meta_description
        ) {
            $pageUpdates[
                'meta_description'
            ] =
                'Diseño, impresión, rotulación, personalización, cámaras de seguridad y soluciones publicitarias para hacer visible tu marca.';
        }

        if (
            count(
                $pageUpdates
            ) > 0
        ) {
            $page->update(
                $pageUpdates
            );
        }

        $this->prepareHero(
            $page
        );

        $this->prepareServices(
            $page
        );

        $this->prepareCatalog(
            $page
        );

        $this->preparePortfolio(
            $page
        );

        $this->prepareCta(
            $page
        );

        $this->newLine();

        $this->info(
            'Inicio preparado correctamente.'
        );

        $this->line(
            'No se sobreescribió contenido existente.'
        );

        return self::SUCCESS;
    }

    private function prepareHero(
        Page $page
    ): void {
        $section =
            $this->section(
                $page,
                'hero'
            );

        if (
            !$section
        ) {
            return;
        }

        $settings =
            $section->settings
            ?? [];

        if (
            empty(
                $settings[
                    'cta_label'
                ]
            )
        ) {
            $settings[
                'cta_label'
            ] =
                'Explorar catálogo';
        }

        if (
            empty(
                $settings[
                    'cta_url'
                ]
            )
        ) {
            $settings[
                'cta_url'
            ] =
                '/catalogo';
        }

        $section->update([
            'settings' =>
                $settings,
        ]);
    }

    private function prepareServices(
        Page $page
    ): void {
        $section =
            $this->section(
                $page,
                'services'
            );

        if (
            !$section
        ) {
            return;
        }

        $content =
            $section->content
            ?? [];

        $items =
            $content[
                'items'
            ]
            ?? [];

        if (
            count(
                $items
            ) ===
            0
        ) {
            $content[
                'items'
            ] = [
                [
                    'label' =>
                        'Creatividad',

                    'title' =>
                        'Diseño gráfico',

                    'text' =>
                        'Piezas visuales y artes publicitarios preparados para comunicar y producir.',

                    'icon' =>
                        'palette',
                ],

                [
                    'label' =>
                        'Producción',

                    'title' =>
                        'Impresión publicitaria',

                    'text' =>
                        'Banners, stickers y diferentes soluciones impresas para negocios y proyectos.',

                    'icon' =>
                        'printer',
                ],

                [
                    'label' =>
                        'Visibilidad',

                    'title' =>
                        'Rotulación',

                    'text' =>
                        'Soluciones visuales para espacios, negocios, señalización y aplicaciones de marca.',

                    'icon' =>
                        'sign',
                ],

                [
                    'label' =>
                        'Personalización',

                    'title' =>
                        'Productos con identidad',

                    'text' =>
                        'Aplicamos diseños y marcas a diferentes productos y soportes publicitarios.',

                    'icon' =>
                        'sparkles',
                ],

                [
                    'label' =>
                        'Seguridad',

                    'title' =>
                        'Cámaras de seguridad',

                    'text' =>
                        'Videovigilancia para hogares, comercios y empresas adaptada a cada espacio.',

                    'icon' =>
                        'camera',
                ],

                [
                    'label' =>
                        'Tecnología',

                    'title' =>
                        'Soluciones tecnológicas',

                    'text' =>
                        'Integramos herramientas digitales y tecnología cuando el proyecto lo requiere.',

                    'icon' =>
                        'cpu',
                ],
            ];
        }

        $settings =
            $section->settings
            ?? [];

        if (
            empty(
                $settings[
                    'cta_label'
                ]
            )
        ) {
            $settings[
                'cta_label'
            ] =
                'Conocer servicios';
        }

        if (
            empty(
                $settings[
                    'cta_url'
                ]
            )
        ) {
            $settings[
                'cta_url'
            ] =
                '/servicios';
        }

        $section->update([
            'content' =>
                $content,

            'settings' =>
                $settings,
        ]);
    }

    private function prepareCatalog(
        Page $page
    ): void {
        $section =
            $this->section(
                $page,
                'catalog'
            );

        if (
            !$section
        ) {
            return;
        }

        $settings =
            $section->settings
            ?? [];

        if (
            empty(
                $settings[
                    'cta_label'
                ]
            )
        ) {
            $settings[
                'cta_label'
            ] =
                'Ver catálogo completo';
        }

        if (
            empty(
                $settings[
                    'cta_url'
                ]
            )
        ) {
            $settings[
                'cta_url'
            ] =
                '/catalogo';
        }

        $section->update([
            'settings' =>
                $settings,
        ]);
    }

    private function preparePortfolio(
        Page $page
    ): void {
        $section =
            $this->section(
                $page,
                'portfolio'
            );

        if (
            !$section
        ) {
            return;
        }

        if (
            !$section->body
        ) {
            $section->update([
                'body' =>
                    'Una pequeña muestra visual de ideas que han pasado del diseño a la producción.',
            ]);
        }
    }

    private function prepareCta(
        Page $page
    ): void {
        $section =
            $this->section(
                $page,
                'cta'
            );

        if (
            !$section
        ) {
            return;
        }

        $settings =
            $section->settings
            ?? [];

        if (
            empty(
                $settings[
                    'cta_label'
                ]
            )
        ) {
            $settings[
                'cta_label'
            ] =
                'Comenzar a cotizar';
        }

        if (
            empty(
                $settings[
                    'cta_url'
                ]
            )
        ) {
            $settings[
                'cta_url'
            ] =
                '/catalogo';
        }

        $section->update([
            'settings' =>
                $settings,
        ]);
    }

    private function section(
        Page $page,
        string $key
    ): ?PageSection {
        return $page
            ->sections()
            ->where(
                'section_key',
                $key
            )
            ->first();
    }
}
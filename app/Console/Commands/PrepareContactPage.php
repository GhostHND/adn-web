<?php

namespace App\Console\Commands;

use App\Models\Page;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class PrepareContactPage extends Command
{
    protected $signature =
        'adn:prepare-contact-page';

    protected $description =
        'Prepara el contenido base y configuraciones públicas de Contacto sin sobrescribir datos existentes.';

    public function handle(): int
    {
        $page =
            Page::query()
                ->where(
                    'slug',
                    'contacto'
                )
                ->first();

        if (
            !$page
        ) {
            $this->error(
                'No existe la página contacto.'
            );

            return self::FAILURE;
        }

        $this->preparePage(
            $page
        );

        $this->prepareSettings();

        $this->newLine();

        $this->info(
            'Contacto preparado correctamente.'
        );

        $this->line(
            'No se sobreescribió contenido institucional existente.'
        );

        return self::SUCCESS;
    }

    private function preparePage(
        Page $page
    ): void {
        $updates = [];

        if (
            !$page->eyebrow
        ) {
            $updates[
                'eyebrow'
            ] =
                'Hablemos';
        }

        if (
            !$page->title
        ) {
            $updates[
                'title'
            ] =
                'Estamos listos para escuchar tu idea.';
        }

        if (
            !$page->summary
        ) {
            $updates[
                'summary'
            ] =
                'Escríbenos, llámanos o contáctanos por WhatsApp. Cuéntanos qué necesitas y te orientaremos para encontrar la mejor solución.';
        }

        if (
            !$page->meta_title
        ) {
            $updates[
                'meta_title'
            ] =
                'Contacto | ADN Publicidad';
        }

        if (
            !$page->meta_description
        ) {
            $updates[
                'meta_description'
            ] =
                'Contacta con ADN Publicidad para consultas, proyectos, cotizaciones y soluciones de diseño, impresión, publicidad y tecnología.';
        }

        if (
            count(
                $updates
            ) >
            0
        ) {
            $page->update(
                $updates
            );
        }

        $sections = [
            [
                'section_key' =>
                    'hero',

                'section_type' =>
                    'hero',

                'subtitle' =>
                    'Contacto ADN',

                'title' =>
                    'Conversemos sobre lo que necesitas.',

                'body' =>
                    'Una buena solución comienza entendiendo bien tu idea. Escríbenos y cuéntanos en qué podemos ayudarte.',

                'content' =>
                    null,

                'settings' =>
                    null,

                'sort_order' =>
                    10,
            ],

            [
                'section_key' =>
                    'contact',

                'section_type' =>
                    'contact_info',

                'subtitle' =>
                    'Estamos cerca',

                'title' =>
                    'Elige la forma más cómoda de comunicarte.',

                'body' =>
                    'Puedes escribirnos por WhatsApp, llamarnos, enviarnos un correo o utilizar el formulario.',

                'content' =>
                    null,

                'settings' =>
                    null,

                'sort_order' =>
                    20,
            ],

            [
                'section_key' =>
                    'form',

                'section_type' =>
                    'contact_form',

                'subtitle' =>
                    'Cuéntanos tu idea',

                'title' =>
                    'Envíanos un mensaje.',

                'body' =>
                    'Déjanos tus datos y una breve descripción de lo que necesitas. Revisaremos tu mensaje para continuar contigo.',

                'content' =>
                    null,

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

                'subtitle' =>
                    '¿Ya sabes qué necesitas?',

                'title' =>
                    'También puedes explorar nuestro catálogo.',

                'body' =>
                    'Consulta nuestros productos y envía una solicitud de cotización directamente desde el sitio.',

                'content' =>
                    null,

                'settings' => [
                    'cta_label' =>
                        'Explorar catálogo',

                    'cta_url' =>
                        '/catalogo',
                ],

                'sort_order' =>
                    40,
            ],
        ];

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

        $this->line(
            'Página Contacto: lista.'
        );
    }

    private function prepareSettings(): void
    {
        $settings = [
            [
                'group' =>
                    'contact',

                'key' =>
                    'phone',

                'value' =>
                    null,

                'type' =>
                    'text',

                'is_public' =>
                    true,

                'sort_order' =>
                    10,
            ],

            [
                'group' =>
                    'contact',

                'key' =>
                    'whatsapp',

                'value' =>
                    null,

                'type' =>
                    'text',

                'is_public' =>
                    true,

                'sort_order' =>
                    20,
            ],

            [
                'group' =>
                    'contact',

                'key' =>
                    'email',

                'value' =>
                    null,

                'type' =>
                    'email',

                'is_public' =>
                    true,

                'sort_order' =>
                    30,
            ],

            [
                'group' =>
                    'contact',

                'key' =>
                    'address',

                'value' =>
                    null,

                'type' =>
                    'textarea',

                'is_public' =>
                    true,

                'sort_order' =>
                    40,
            ],

            [
                'group' =>
                    'contact',

                'key' =>
                    'business_hours',

                'value' =>
                    null,

                'type' =>
                    'textarea',

                'is_public' =>
                    true,

                'sort_order' =>
                    50,
            ],

            [
                'group' =>
                    'contact',

                'key' =>
                    'map_url',

                'value' =>
                    null,

                'type' =>
                    'url',

                'is_public' =>
                    true,

                'sort_order' =>
                    60,
            ],

            [
                'group' =>
                    'social',

                'key' =>
                    'facebook',

                'value' =>
                    null,

                'type' =>
                    'url',

                'is_public' =>
                    true,

                'sort_order' =>
                    10,
            ],

            [
                'group' =>
                    'social',

                'key' =>
                    'instagram',

                'value' =>
                    null,

                'type' =>
                    'url',

                'is_public' =>
                    true,

                'sort_order' =>
                    20,
            ],

            [
                'group' =>
                    'social',

                'key' =>
                    'tiktok',

                'value' =>
                    null,

                'type' =>
                    'url',

                'is_public' =>
                    true,

                'sort_order' =>
                    30,
            ],
        ];

        foreach (
            $settings
            as
            $setting
        ) {
            $exists =
                DB::table(
                    'settings'
                )
                    ->where(
                        'group',
                        $setting[
                            'group'
                        ]
                    )
                    ->where(
                        'key',
                        $setting[
                            'key'
                        ]
                    )
                    ->exists();

            if (
                $exists
            ) {
                continue;
            }

            $payload =
                $setting;

            if (
                Schema::hasColumn(
                    'settings',
                    'created_at'
                )
            ) {
                $payload[
                    'created_at'
                ] =
                    now();
            }

            if (
                Schema::hasColumn(
                    'settings',
                    'updated_at'
                )
            ) {
                $payload[
                    'updated_at'
                ] =
                    now();
            }

            DB::table(
                'settings'
            )->insert(
                $payload
            );
        }

        $this->line(
            'Configuraciones públicas: listas.'
        );
    }
}
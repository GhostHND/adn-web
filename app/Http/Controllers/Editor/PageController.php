<?php

namespace App\Http\Controllers\Editor;

use App\Http\Controllers\Controller;
use App\Http\Requests\Editor\UpdatePageRequest;
use App\Models\Media;
use App\Models\Page;
use App\Models\PageSection;
use App\Services\MediaService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Inertia\Response;

class PageController extends Controller
{
    public function __construct(
        private readonly MediaService $mediaService
    ) {
    }

    public function edit(
        Page $page
    ): Response {
        abort_unless(
            $this->isEditablePage(
                $page
            ),
            404
        );

        $page->load([
            'sections' =>
                fn ($query) =>
                    $query
                        ->orderBy(
                            'sort_order'
                        )
                        ->orderBy(
                            'id'
                        ),

            'sections.media',

            'sections.supportMedia',
        ]);

        return Inertia::render(
            'Editor/Pages/Edit',
            [
                'page' =>
                    $this->serializePage(
                        $page
                    ),

                'mediaLibrary' =>
                    $this->mediaLibrary(),
            ]
        );
    }

    public function update(
        UpdatePageRequest $request,
        Page $page
    ): RedirectResponse {
        abort_unless(
            $this->isEditablePage(
                $page
            ),
            404
        );

        $validated =
            $request->validated();

        DB::transaction(
            function () use (
                $page,
                $validated
            ): void {
                /*
                |--------------------------------------------------------------------------
                | PÁGINA
                |--------------------------------------------------------------------------
                */

                $page->update([
                    'title' =>
                        $validated[
                            'title'
                        ],

                    'eyebrow' =>
                        $validated[
                            'eyebrow'
                        ]
                        ?? null,

                    'summary' =>
                        $validated[
                            'summary'
                        ]
                        ?? null,

                    'status' =>
                        $validated[
                            'status'
                        ],

                    'meta_title' =>
                        $validated[
                            'meta_title'
                        ]
                        ?? null,

                    'meta_description' =>
                        $validated[
                            'meta_description'
                        ]
                        ?? null,

                    'canonical_url' =>
                        $validated[
                            'canonical_url'
                        ]
                        ?? null,

                    'published_at' =>
                        $validated[
                            'status'
                        ] ===
                        'published'
                            ? (
                                $page
                                    ->published_at
                                ??
                                now()
                            )
                            : null,
                ]);

                /*
                |--------------------------------------------------------------------------
                | SECCIONES
                |--------------------------------------------------------------------------
                */

                foreach (
                    $validated[
                        'sections'
                    ]
                    as
                    $sectionData
                ) {
                    $section =
                        $page
                            ->sections()
                            ->whereKey(
                                $sectionData[
                                    'id'
                                ]
                            )
                            ->firstOrFail();

                    $section->update([
                        'title' =>
                            $sectionData[
                                'title'
                            ]
                            ?? null,

                        'subtitle' =>
                            $sectionData[
                                'subtitle'
                            ]
                            ?? null,

                        'body' =>
                            $sectionData[
                                'body'
                            ]
                            ?? null,

                        'media_id' =>
                            $sectionData[
                                'media_id'
                            ]
                            ?? null,

                        'content' =>
                            $this
                                ->normalizeContent(
                                    $sectionData[
                                        'content'
                                    ]
                                    ?? null
                                ),

                        'settings' =>
                            $this
                                ->normalizeSettings(
                                    $sectionData[
                                        'settings'
                                    ]
                                    ?? null
                                ),

                        'sort_order' =>
                            $sectionData[
                                'sort_order'
                            ],

                        'active' =>
                            (bool)
                            $sectionData[
                                'active'
                            ],
                    ]);

                    /*
                    |--------------------------------------------------------------------------
                    | IMÁGENES DE APOYO
                    |--------------------------------------------------------------------------
                    |
                    | Persistimos explícitamente la tabla pivote.
                    |
                    | Esto evita cualquier comportamiento inesperado de sync()
                    | y garantiza que el orden del Editor quede reflejado exactamente
                    | en la base de datos.
                    |
                    */

                    $this->persistSupportMedia(
                        $section,
                        $sectionData[
                            'support_media_ids'
                        ]
                    );
                }
            }
        );

        return back()
            ->with(
                'success',
                sprintf(
                    '%s actualizado correctamente.',
                    $page->name
                )
            );
    }

    private function persistSupportMedia(
        PageSection $section,
        array $mediaIds
    ): void {
        /*
        |--------------------------------------------------------------------------
        | NORMALIZAR
        |--------------------------------------------------------------------------
        */

        $normalizedIds =
            collect(
                $mediaIds
            )
                ->map(
                    fn (
                        mixed $mediaId
                    ): int =>
                        (int)
                        $mediaId
                )
                ->filter(
                    fn (
                        int $mediaId
                    ): bool =>
                        $mediaId >
                        0
                )
                ->unique()
                ->take(
                    6
                )
                ->values();

        /*
        |--------------------------------------------------------------------------
        | LIMPIAR ASIGNACIONES ANTERIORES
        |--------------------------------------------------------------------------
        */

        DB::table(
            'page_section_media'
        )
            ->where(
                'page_section_id',
                $section->id
            )
            ->delete();

        /*
        |--------------------------------------------------------------------------
        | SIN IMÁGENES
        |--------------------------------------------------------------------------
        */

        if (
            $normalizedIds
                ->isEmpty()
        ) {
            return;
        }

        /*
        |--------------------------------------------------------------------------
        | INSERTAR NUEVAS ASIGNACIONES
        |--------------------------------------------------------------------------
        */

        $timestamp =
            now();

        $rows =
            $normalizedIds
                ->map(
                    fn (
                        int $mediaId,
                        int $index
                    ): array => [
                        'page_section_id' =>
                            $section->id,

                        'media_id' =>
                            $mediaId,

                        'sort_order' =>
                            (
                                $index
                                + 1
                            )
                            *
                            10,

                        'created_at' =>
                            $timestamp,

                        'updated_at' =>
                            $timestamp,
                    ]
                )
                ->all();

        DB::table(
            'page_section_media'
        )->insert(
            $rows
        );
    }

    private function isEditablePage(
        Page $page
    ): bool {
        return in_array(
            $page->slug,
            [
                'inicio',
                'sobre-nosotros',
                'servicios',
                'portafolio',
                'contacto',
            ],
            true
        );
    }

    private function mediaLibrary(): array
    {
        return Media::query()
            ->where(
                'is_public',
                true
            )
            ->orderByDesc(
                'id'
            )
            ->limit(
                200
            )
            ->get()
            ->map(
                fn (
                    Media $media
                ): array =>
                    $this
                        ->serializeMedia(
                            $media
                        )
            )
            ->values()
            ->all();
    }

    private function serializePage(
        Page $page
    ): array {
        return [
            'id' =>
                $page->id,

            'slug' =>
                $page->slug,

            'name' =>
                $page->name,

            'title' =>
                $page->title,

            'eyebrow' =>
                $page->eyebrow,

            'summary' =>
                $page->summary,

            'status' =>
                $page->status,

            'meta_title' =>
                $page->meta_title,

            'meta_description' =>
                $page->meta_description,

            'canonical_url' =>
                $page->canonical_url,

            'published_at' =>
                $page
                    ->published_at
                    ?->toIso8601String(),

            'sections' =>
                $page
                    ->sections
                    ->map(
                        fn (
                            PageSection $section
                        ): array => [
                            'id' =>
                                $section->id,

                            'section_key' =>
                                $section
                                    ->section_key,

                            'section_type' =>
                                $section
                                    ->section_type,

                            'title' =>
                                $section->title,

                            'subtitle' =>
                                $section
                                    ->subtitle,

                            'body' =>
                                $section->body,

                            'media_id' =>
                                $section
                                    ->media_id,

                            'media' =>
                                $section->media
                                    ? $this
                                        ->serializeMedia(
                                            $section
                                                ->media
                                        )
                                    : null,

                            'support_media_ids' =>
                                $section
                                    ->supportMedia
                                    ->pluck(
                                        'id'
                                    )
                                    ->map(
                                        fn (
                                            mixed $id
                                        ): int =>
                                            (int)
                                            $id
                                    )
                                    ->values()
                                    ->all(),

                            'support_media' =>
                                $section
                                    ->supportMedia
                                    ->map(
                                        fn (
                                            Media $media
                                        ): array =>
                                            $this
                                                ->serializeMedia(
                                                    $media
                                                )
                                    )
                                    ->values()
                                    ->all(),

                            'content' =>
                                $section
                                    ->content
                                ?? [
                                    'items' =>
                                        [],
                                ],

                            'settings' =>
                                $section
                                    ->settings
                                ?? [],

                            'sort_order' =>
                                $section
                                    ->sort_order,

                            'active' =>
                                (bool)
                                $section
                                    ->active,
                        ]
                    )
                    ->values()
                    ->all(),
        ];
    }

    private function serializeMedia(
        Media $media
    ): array {
        return [
            'id' =>
                $media->id,

            'title' =>
                $media->title,

            'alt_text' =>
                $media->alt_text,

            'width' =>
                $media->width,

            'height' =>
                $media->height,

            'url' =>
                $this
                    ->mediaService
                    ->url(
                        $media
                    ),
        ];
    }

    private function normalizeContent(
        ?array $content
    ): ?array {
        if (
            !$content
        ) {
            return null;
        }

        $items =
            collect(
                $content[
                    'items'
                ]
                ?? []
            )
                ->map(
                    fn (
                        array $item
                    ): array => [
                        'title' =>
                            trim(
                                (string)
                                (
                                    $item[
                                        'title'
                                    ]
                                    ?? ''
                                )
                            ),

                        'label' =>
                            trim(
                                (string)
                                (
                                    $item[
                                        'label'
                                    ]
                                    ?? ''
                                )
                            ),

                        'text' =>
                            trim(
                                (string)
                                (
                                    $item[
                                        'text'
                                    ]
                                    ?? ''
                                )
                            ),

                        'icon' =>
                            trim(
                                (string)
                                (
                                    $item[
                                        'icon'
                                    ]
                                    ?? ''
                                )
                            ),
                    ]
                )
                ->filter(
                    fn (
                        array $item
                    ): bool =>
                        $item[
                            'title'
                        ] !==
                        ''
                        ||
                        $item[
                            'label'
                        ] !==
                        ''
                        ||
                        $item[
                            'text'
                        ] !==
                        ''
                )
                ->values()
                ->all();

        return count(
            $items
        ) > 0
            ? [
                'items' =>
                    $items,
            ]
            : null;
    }

    private function normalizeSettings(
        ?array $settings
    ): ?array {
        if (
            !$settings
        ) {
            return null;
        }

        $normalized = [
            'cta_label' =>
                trim(
                    (string)
                    (
                        $settings[
                            'cta_label'
                        ]
                        ?? ''
                    )
                ),

            'cta_url' =>
                trim(
                    (string)
                    (
                        $settings[
                            'cta_url'
                        ]
                        ?? ''
                    )
                ),
        ];

        if (
            $normalized[
                'cta_label'
            ] ===
            ''
            &&
            $normalized[
                'cta_url'
            ] ===
            ''
        ) {
            return null;
        }

        return $normalized;
    }
}
<?php

namespace App\Http\Controllers\PublicSite;

use App\Http\Controllers\Controller;
use App\Models\Media;
use App\Models\Page;
use App\Models\PageSection;
use App\Models\PortfolioCategory;
use App\Models\PortfolioProject;
use App\Services\MediaService;
use App\Services\WebsiteAdService;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class PortfolioController extends Controller
{
    public function __construct(
        private readonly MediaService $mediaService,
        private readonly WebsiteAdService $websiteAdService
    ) {
    }

    public function index(
        Request $request
    ): Response {
        $categorySlug =
            trim(
                (string)
                $request->query(
                    'categoria',
                    ''
                )
            );

        $page =
            $this->portfolioPage();

        $categories =
            PortfolioCategory::query()
                ->active()
                ->whereHas(
                    'projects',
                    fn (
                        Builder $query
                    ) =>
                        $query->published()
                )
                ->withCount([
                    'projects as projects_count' =>
                        fn (
                            Builder $query
                        ) =>
                            $query->published(),
                ])
                ->orderBy(
                    'sort_order'
                )
                ->orderBy(
                    'name'
                )
                ->get()
                ->map(
                    fn (
                        PortfolioCategory $category
                    ): array => [
                        'id' =>
                            $category->id,

                        'slug' =>
                            $category->slug,

                        'name' =>
                            $category->name,

                        'description' =>
                            $category->description,

                        'projects_count' =>
                            (int)
                            $category
                                ->projects_count,
                    ]
                )
                ->values()
                ->all();

        $validCategorySlugs =
            collect(
                $categories
            )
                ->pluck(
                    'slug'
                )
                ->all();

        if (
            $categorySlug !==
            ''
            &&
            !in_array(
                $categorySlug,
                $validCategorySlugs,
                true
            )
        ) {
            $categorySlug =
                '';
        }

        $projects =
            PortfolioProject::query()
                ->published()
                ->whereHas(
                    'category',
                    fn (
                        Builder $query
                    ) =>
                        $query->active()
                )
                ->whereHas(
                    'mainMedia',
                    fn (
                        Builder $query
                    ) =>
                        $query->where(
                            'is_public',
                            true
                        )
                )
                ->when(
                    $categorySlug !==
                    '',
                    fn (
                        Builder $query
                    ) =>
                        $query->whereHas(
                            'category',
                            fn (
                                Builder $categoryQuery
                            ) =>
                                $categoryQuery->where(
                                    'slug',
                                    $categorySlug
                                )
                        )
                )
                ->with([
                    'category',
                    'mainMedia',
                ])
                ->orderByDesc(
                    'featured'
                )
                ->orderBy(
                    'sort_order'
                )
                ->orderByDesc(
                    'project_date'
                )
                ->orderByDesc(
                    'id'
                )
                ->limit(
                    60
                )
                ->get()
                ->map(
                    fn (
                        PortfolioProject $project
                    ): array =>
                        $this
                            ->serializeProjectCard(
                                $project
                            )
                )
                ->values()
                ->all();

        return Inertia::render(
            'Public/Portfolio/Index',
            [
                'page' =>
                    $this
                        ->serializePage(
                            $page
                        ),

                'categories' =>
                    $categories,

                'projects' =>
                    $projects,

                'filters' => [
                    'category' =>
                        $categorySlug !==
                        ''
                            ? $categorySlug
                            : null,
                ],

                'promotions' =>
                    $this
                        ->websiteAdService
                        ->placement(
                            'site_wide',
                            6
                        ),
            ]
        );
    }

    public function show(
        PortfolioProject $project
    ): Response {
        abort_unless(
            $project->status ===
            PortfolioProject::STATUS_PUBLISHED,
            404
        );

        $project->load([
            'category',
            'mainMedia',
            'images.media',
        ]);

        abort_unless(
            $project->category
            &&
            $project
                ->category
                ->active,
            404
        );

        abort_unless(
            $project->mainMedia
            &&
            $project
                ->mainMedia
                ->is_public,
            404
        );

        $related =
            PortfolioProject::query()
                ->published()
                ->where(
                    'id',
                    '!=',
                    $project->id
                )
                ->where(
                    'category_id',
                    $project->category_id
                )
                ->whereHas(
                    'category',
                    fn (
                        Builder $query
                    ) =>
                        $query->active()
                )
                ->whereHas(
                    'mainMedia',
                    fn (
                        Builder $query
                    ) =>
                        $query->where(
                            'is_public',
                            true
                        )
                )
                ->with([
                    'category',
                    'mainMedia',
                ])
                ->orderByDesc(
                    'featured'
                )
                ->orderBy(
                    'sort_order'
                )
                ->orderByDesc(
                    'project_date'
                )
                ->limit(
                    3
                )
                ->get()
                ->map(
                    fn (
                        PortfolioProject $relatedProject
                    ): array =>
                        $this
                            ->serializeProjectCard(
                                $relatedProject
                            )
                )
                ->values()
                ->all();

        return Inertia::render(
            'Public/Portfolio/Show',
            [
                'project' =>
                    $this
                        ->serializeProjectDetail(
                            $project
                        ),

                'relatedProjects' =>
                    $related,

                'promotions' =>
                    $this
                        ->websiteAdService
                        ->placement(
                            'site_wide',
                            6
                        ),
            ]
        );
    }

    private function portfolioPage(): ?Page
    {
        return Page::query()
            ->where(
                'slug',
                'portafolio'
            )
            ->where(
                'status',
                'published'
            )
            ->with([
                'sections' =>
                    fn (
                        $query
                    ) =>
                        $query
                            ->where(
                                'active',
                                true
                            )
                            ->orderBy(
                                'sort_order'
                            )
                            ->orderBy(
                                'id'
                            ),

                'sections.media',

                'sections.supportMedia',
            ])
            ->first();
    }

    private function serializePage(
        ?Page $page
    ): array {
        if (
            !$page
        ) {
            return [
                'title' =>
                    'Portafolio',

                'eyebrow' =>
                    'ADN Publicidad',

                'summary' =>
                    null,

                'meta_title' =>
                    'Portafolio | ADN Publicidad',

                'meta_description' =>
                    'Conoce algunos de los proyectos realizados por ADN Publicidad.',

                'sections' =>
                    [],
            ];
        }

        return [
            'title' =>
                $page->title,

            'eyebrow' =>
                $page->eyebrow,

            'summary' =>
                $page->summary,

            'meta_title' =>
                $page->meta_title,

            'meta_description' =>
                $page->meta_description,

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

                            'settings' =>
                                $section
                                    ->settings
                                ?? [],

                            'media' =>
                                $section->media
                                &&
                                $section
                                    ->media
                                    ->is_public
                                    ? $this
                                        ->serializeMedia(
                                            $section
                                                ->media
                                        )
                                    : null,

                            'support_media' =>
                                $section
                                    ->supportMedia
                                    ->filter(
                                        fn (
                                            Media $media
                                        ): bool =>
                                            (bool)
                                            $media
                                                ->is_public
                                    )
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
                        ]
                    )
                    ->values()
                    ->all(),
        ];
    }

    private function serializeProjectCard(
        PortfolioProject $project
    ): array {
        return [
            'id' =>
                $project->id,

            'slug' =>
                $project->slug,

            'title' =>
                $project->title,

            'client_name' =>
                $project->client_name,

            'excerpt' =>
                $project->excerpt,

            'project_date' =>
                $project
                    ->project_date
                    ?->format(
                        'Y-m-d'
                    ),

            'featured' =>
                (bool)
                $project->featured,

            'category' =>
                $project->category
                    ? [
                        'id' =>
                            $project
                                ->category
                                ->id,

                        'name' =>
                            $project
                                ->category
                                ->name,

                        'slug' =>
                            $project
                                ->category
                                ->slug,
                    ]
                    : null,

            'main_media' =>
                $project->mainMedia
                    ? $this
                        ->serializeMedia(
                            $project
                                ->mainMedia
                        )
                    : null,
        ];
    }

    private function serializeProjectDetail(
        PortfolioProject $project
    ): array {
        /*
        |--------------------------------------------------------------------------
        | GALERÍA
        |--------------------------------------------------------------------------
        |
        | Enviamos los datos multimedia de dos maneras:
        |
        | 1. A nivel superior:
        |    gallery[n].url
        |    gallery[n].width
        |    gallery[n].height
        |
        | 2. Dentro de media:
        |    gallery[n].media.url
        |
        | Esto mantiene compatibilidad con cualquier componente existente
        | que consuma cualquiera de las dos estructuras.
        |
        */

        $gallery =
            $project
                ->images
                ->filter(
                    fn (
                        $image
                    ): bool =>
                        $image->media
                        &&
                        $image
                            ->media
                            ->is_public
                )
                ->map(
                    function (
                        $image
                    ) use (
                        $project
                    ): array {
                        $media =
                            $this
                                ->serializeMedia(
                                    $image
                                        ->media
                                );

                        $altText =
                            $image
                                ->alt_text
                            ?:
                            $media[
                                'alt_text'
                            ]
                            ?:
                            $project
                                ->title;

                        return [
                            'id' =>
                                $image->id,

                            'media_id' =>
                                $image
                                    ->media_id,

                            'title' =>
                                $media[
                                    'title'
                                ],

                            'alt_text' =>
                                $altText,

                            'width' =>
                                $media[
                                    'width'
                                ],

                            'height' =>
                                $media[
                                    'height'
                                ],

                            'url' =>
                                $media[
                                    'url'
                                ],

                            'media' =>
                                [
                                    ...$media,

                                    'alt_text' =>
                                        $altText,
                                ],
                        ];
                    }
                )
                ->values()
                ->all();

        return [
            'id' =>
                $project->id,

            'code' =>
                $project->code,

            'slug' =>
                $project->slug,

            'title' =>
                $project->title,

            'client_name' =>
                $project->client_name,

            'excerpt' =>
                $project->excerpt,

            'description' =>
                $project->description,

            'project_date' =>
                $project
                    ->project_date
                    ?->format(
                        'Y-m-d'
                    ),

            'featured' =>
                (bool)
                $project->featured,

            'meta_title' =>
                $project->meta_title,

            'meta_description' =>
                $project
                    ->meta_description,

            'category' => [
                'id' =>
                    $project
                        ->category
                        ->id,

                'name' =>
                    $project
                        ->category
                        ->name,

                'slug' =>
                    $project
                        ->category
                        ->slug,
            ],

            'main_media' =>
                $this
                    ->serializeMedia(
                        $project
                            ->mainMedia
                    ),

            'gallery' =>
                $gallery,
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
}
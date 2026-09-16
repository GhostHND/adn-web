<?php

namespace App\Http\Controllers\Editor;

use App\Http\Controllers\Controller;
use App\Http\Requests\Editor\StorePortfolioProjectRequest;
use App\Http\Requests\Editor\UpdatePortfolioProjectRequest;
use App\Models\Media;
use App\Models\PortfolioCategory;
use App\Models\PortfolioImage;
use App\Models\PortfolioProject;
use App\Services\MediaService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Inertia\Response;

class PortfolioProjectController extends Controller
{
    public function __construct(
        private readonly MediaService $mediaService
    ) {
    }

    public function index(): Response
    {
        $projects =
            PortfolioProject::query()
                ->with([
                    'category',
                    'mainMedia',
                ])
                ->withCount(
                    'images'
                )
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
                ->get()
                ->map(
                    fn (
                        PortfolioProject $project
                    ): array =>
                        $this
                            ->serializeProject(
                                $project
                            )
                )
                ->values()
                ->all();

        return Inertia::render(
            'Editor/Portfolio/Projects/Index',
            [
                'projects' =>
                    $projects,
            ]
        );
    }

    public function create(): Response
    {
        return Inertia::render(
            'Editor/Portfolio/Projects/Form',
            [
                'project' =>
                    null,

                'categories' =>
                    $this
                        ->categories(),

                'mediaLibrary' =>
                    $this
                        ->mediaLibrary(),
            ]
        );
    }

    public function store(
        StorePortfolioProjectRequest $request
    ): RedirectResponse {
        $validated =
            $request->validated();

        $project =
            DB::transaction(
                function () use (
                    $validated
                ): PortfolioProject {
                    $project =
                        PortfolioProject::create([
                            'code' =>
                                $this
                                    ->generateCode(),

                            'slug' =>
                                $this
                                    ->uniqueSlug(
                                        $validated[
                                            'slug'
                                        ]
                                        ?: Str::slug(
                                            $validated[
                                                'title'
                                            ]
                                        )
                                    ),

                            'title' =>
                                $validated[
                                    'title'
                                ],

                            'client_name' =>
                                $validated[
                                    'client_name'
                                ]
                                ?? null,

                            'excerpt' =>
                                $validated[
                                    'excerpt'
                                ]
                                ?? null,

                            'description' =>
                                $validated[
                                    'description'
                                ]
                                ?? null,

                            'category_id' =>
                                $validated[
                                    'category_id'
                                ],

                            'main_media_id' =>
                                $validated[
                                    'main_media_id'
                                ],

                            'project_date' =>
                                $validated[
                                    'project_date'
                                ]
                                ?? null,

                            'status' =>
                                $validated[
                                    'status'
                                ],

                            'featured' =>
                                (bool)
                                $validated[
                                    'featured'
                                ],

                            'sort_order' =>
                                $validated[
                                    'sort_order'
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
                        ]);

                    $this
                        ->replaceGallery(
                            $project,
                            $validated[
                                'gallery_media_ids'
                            ]
                        );

                    return $project;
                }
            );

        return redirect()
            ->route(
                'editor.portfolio.projects.edit',
                $project
            )
            ->with(
                'success',
                'Proyecto creado correctamente.'
            );
    }

    public function edit(
        PortfolioProject $project
    ): Response {
        $project->load([
            'category',
            'mainMedia',
            'images.media',
        ]);

        return Inertia::render(
            'Editor/Portfolio/Projects/Form',
            [
                'project' =>
                    $this
                        ->serializeProjectForForm(
                            $project
                        ),

                'categories' =>
                    $this
                        ->categories(),

                'mediaLibrary' =>
                    $this
                        ->mediaLibrary(),
            ]
        );
    }

    public function update(
        UpdatePortfolioProjectRequest $request,
        PortfolioProject $project
    ): RedirectResponse {
        $validated =
            $request->validated();

        DB::transaction(
            function () use (
                $project,
                $validated
            ): void {
                $project->update([
                    'slug' =>
                        $this
                            ->uniqueSlug(
                                $validated[
                                    'slug'
                                ]
                                ?: Str::slug(
                                    $validated[
                                        'title'
                                    ]
                                ),
                                $project->id
                            ),

                    'title' =>
                        $validated[
                            'title'
                        ],

                    'client_name' =>
                        $validated[
                            'client_name'
                        ]
                        ?? null,

                    'excerpt' =>
                        $validated[
                            'excerpt'
                        ]
                        ?? null,

                    'description' =>
                        $validated[
                            'description'
                        ]
                        ?? null,

                    'category_id' =>
                        $validated[
                            'category_id'
                        ],

                    'main_media_id' =>
                        $validated[
                            'main_media_id'
                        ],

                    'project_date' =>
                        $validated[
                            'project_date'
                        ]
                        ?? null,

                    'status' =>
                        $validated[
                            'status'
                        ],

                    'featured' =>
                        (bool)
                        $validated[
                            'featured'
                        ],

                    'sort_order' =>
                        $validated[
                            'sort_order'
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
                ]);

                $this
                    ->replaceGallery(
                        $project,
                        $validated[
                            'gallery_media_ids'
                        ]
                    );
            }
        );

        return back()
            ->with(
                'success',
                'Proyecto actualizado correctamente.'
            );
    }

    public function destroy(
        PortfolioProject $project
    ): RedirectResponse {
        $project->delete();

        return redirect()
            ->route(
                'editor.portfolio.projects.index'
            )
            ->with(
                'success',
                'Proyecto archivado correctamente.'
            );
    }

    private function replaceGallery(
        PortfolioProject $project,
        array $mediaIds
    ): void {
        $mediaIds =
            collect(
                $mediaIds
            )
                ->map(
                    fn (
                        mixed $id
                    ): int =>
                        (int)
                        $id
                )
                ->filter()
                ->unique()
                ->take(
                    20
                )
                ->values();

        PortfolioImage::query()
            ->where(
                'portfolio_project_id',
                $project->id
            )
            ->delete();

        foreach (
            $mediaIds
            as
            $index =>
                $mediaId
        ) {
            $media =
                Media::query()
                    ->findOrFail(
                        $mediaId
                    );

            PortfolioImage::create([
                'portfolio_project_id' =>
                    $project->id,

                'media_id' =>
                    $media->id,

                'alt_text' =>
                    $media->alt_text
                    ?: $project->title,

                'sort_order' =>
                    (
                        $index
                        + 1
                    )
                    *
                    10,
            ]);
        }
    }

    private function categories(): array
    {
        return PortfolioCategory::query()
            ->active()
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

                    'name' =>
                        $category->name,

                    'slug' =>
                        $category->slug,
                ]
            )
            ->values()
            ->all();
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
                300
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

    private function serializeProject(
        PortfolioProject $project
    ): array {
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

            'status' =>
                $project->status,

            'featured' =>
                (bool)
                $project->featured,

            'sort_order' =>
                $project->sort_order,

            'project_date' =>
                $project
                    ->project_date
                    ?->format(
                        'Y-m-d'
                    ),

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

            'images_count' =>
                (int)
                (
                    $project
                        ->images_count
                    ?? 0
                ),
        ];
    }

    private function serializeProjectForForm(
        PortfolioProject $project
    ): array {
        return [
            ...$this
                ->serializeProject(
                    $project
                ),

            'description' =>
                $project->description,

            'category_id' =>
                $project->category_id,

            'main_media_id' =>
                $project->main_media_id,

            'gallery_media_ids' =>
                $project
                    ->images
                    ->pluck(
                        'media_id'
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

            'meta_title' =>
                $project->meta_title,

            'meta_description' =>
                $project
                    ->meta_description,
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

    private function generateCode(): string
    {
        return sprintf(
            'PORT-%s-%s',
            now()->format(
                'ymd'
            ),
            Str::upper(
                Str::random(
                    5
                )
            )
        );
    }

    private function uniqueSlug(
        string $baseSlug,
        ?int $ignoreId = null
    ): string {
        $baseSlug =
            $baseSlug !==
            ''
                ? $baseSlug
                : 'proyecto';

        $slug =
            $baseSlug;

        $counter =
            2;

        while (
            PortfolioProject::query()
                ->when(
                    $ignoreId,
                    fn (
                        $query
                    ) =>
                        $query->where(
                            'id',
                            '!=',
                            $ignoreId
                        )
                )
                ->where(
                    'slug',
                    $slug
                )
                ->exists()
        ) {
            $slug =
                sprintf(
                    '%s-%d',
                    $baseSlug,
                    $counter
                );

            $counter++;
        }

        return $slug;
    }
}
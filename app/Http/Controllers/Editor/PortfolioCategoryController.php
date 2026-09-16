<?php

namespace App\Http\Controllers\Editor;

use App\Http\Controllers\Controller;
use App\Http\Requests\Editor\StorePortfolioCategoryRequest;
use App\Http\Requests\Editor\UpdatePortfolioCategoryRequest;
use App\Models\PortfolioCategory;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Inertia\Response;

class PortfolioCategoryController extends Controller
{
    public function index(): Response
    {
        $categories =
            PortfolioCategory::query()
                ->withCount(
                    'projects'
                )
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

                        'sort_order' =>
                            $category->sort_order,

                        'active' =>
                            (bool)
                            $category->active,

                        'projects_count' =>
                            (int)
                            $category
                                ->projects_count,
                    ]
                )
                ->values()
                ->all();

        return Inertia::render(
            'Editor/Portfolio/Categories/Index',
            [
                'categories' =>
                    $categories,
            ]
        );
    }

    public function create(): Response
    {
        return Inertia::render(
            'Editor/Portfolio/Categories/Form',
            [
                'category' =>
                    null,
            ]
        );
    }

    public function store(
        StorePortfolioCategoryRequest $request
    ): RedirectResponse {
        $validated =
            $request->validated();

        $slug =
            $validated[
                'slug'
            ]
            ?: Str::slug(
                $validated[
                    'name'
                ]
            );

        PortfolioCategory::create([
            'name' =>
                $validated[
                    'name'
                ],

            'slug' =>
                $this
                    ->uniqueSlug(
                        $slug
                    ),

            'description' =>
                $validated[
                    'description'
                ]
                ?? null,

            'sort_order' =>
                $validated[
                    'sort_order'
                ],

            'active' =>
                (bool)
                $validated[
                    'active'
                ],
        ]);

        return redirect()
            ->route(
                'editor.portfolio.categories.index'
            )
            ->with(
                'success',
                'Categoría creada correctamente.'
            );
    }

    public function edit(
        PortfolioCategory $category
    ): Response {
        return Inertia::render(
            'Editor/Portfolio/Categories/Form',
            [
                'category' => [
                    'id' =>
                        $category->id,

                    'slug' =>
                        $category->slug,

                    'name' =>
                        $category->name,

                    'description' =>
                        $category->description,

                    'sort_order' =>
                        $category->sort_order,

                    'active' =>
                        (bool)
                        $category->active,
                ],
            ]
        );
    }

    public function update(
        UpdatePortfolioCategoryRequest $request,
        PortfolioCategory $category
    ): RedirectResponse {
        $validated =
            $request->validated();

        $slug =
            $validated[
                'slug'
            ]
            ?: Str::slug(
                $validated[
                    'name'
                ]
            );

        $category->update([
            'name' =>
                $validated[
                    'name'
                ],

            'slug' =>
                $this
                    ->uniqueSlug(
                        $slug,
                        $category->id
                    ),

            'description' =>
                $validated[
                    'description'
                ]
                ?? null,

            'sort_order' =>
                $validated[
                    'sort_order'
                ],

            'active' =>
                (bool)
                $validated[
                    'active'
                ],
        ]);

        return redirect()
            ->route(
                'editor.portfolio.categories.index'
            )
            ->with(
                'success',
                'Categoría actualizada correctamente.'
            );
    }

    public function destroy(
        PortfolioCategory $category
    ): RedirectResponse {
        if (
            $category
                ->projects()
                ->exists()
        ) {
            return back()
                ->with(
                    'error',
                    'No puedes archivar una categoría que todavía tiene proyectos asignados.'
                );
        }

        $category->delete();

        return back()
            ->with(
                'success',
                'Categoría archivada correctamente.'
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
                : 'categoria';

        $slug =
            $baseSlug;

        $counter =
            2;

        while (
            PortfolioCategory::query()
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
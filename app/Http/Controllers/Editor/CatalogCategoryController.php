<?php

namespace App\Http\Controllers\Editor;

use App\Http\Controllers\Controller;
use App\Http\Requests\Editor\StoreCatalogCategoryRequest;
use App\Http\Requests\Editor\UpdateCatalogCategoryRequest;
use App\Models\CatalogCategory;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Inertia\Response;

class CatalogCategoryController extends Controller
{
    public function index(
        Request $request
    ): Response {
        $search =
            trim(
                (string) $request->input(
                    'search',
                    ''
                )
            );

        $status =
            trim(
                (string) $request->input(
                    'status',
                    ''
                )
            );

        $categories =
            CatalogCategory::query()
                ->with([
                    'parent:id,name',
                ])
                ->withCount([
                    'products',
                    'children',
                ])
                ->when(
                    $search !== '',
                    function ($query) use ($search): void {
                        $query->where(
                            function ($inner) use ($search): void {
                                $inner
                                    ->where(
                                        'name',
                                        'like',
                                        "%{$search}%"
                                    )
                                    ->orWhere(
                                        'code',
                                        'like',
                                        "%{$search}%"
                                    )
                                    ->orWhere(
                                        'slug',
                                        'like',
                                        "%{$search}%"
                                    );
                            }
                        );
                    }
                )
                ->when(
                    in_array(
                        $status,
                        [
                            'draft',
                            'published',
                            'hidden',
                        ],
                        true
                    ),
                    fn ($query) =>
                        $query->where(
                            'status',
                            $status
                        )
                )
                ->orderBy('sort_order')
                ->orderBy('name')
                ->paginate(15)
                ->withQueryString();

        $summary = [
            'total' =>
                CatalogCategory::count(),

            'published' =>
                CatalogCategory::query()
                    ->where(
                        'status',
                        'published'
                    )
                    ->count(),

            'draft' =>
                CatalogCategory::query()
                    ->where(
                        'status',
                        'draft'
                    )
                    ->count(),

            'hidden' =>
                CatalogCategory::query()
                    ->where(
                        'status',
                        'hidden'
                    )
                    ->count(),
        ];

        return Inertia::render(
            'Editor/Catalog/Categories/Index',
            [
                'categories' =>
                    $categories,

                'summary' =>
                    $summary,

                'filters' => [
                    'search' =>
                        $search,

                    'status' =>
                        $status,
                ],
            ]
        );
    }

    public function create(): Response
    {
        return Inertia::render(
            'Editor/Catalog/Categories/Form',
            [
                'mode' =>
                    'create',

                'category' =>
                    null,

                'parentCategories' =>
                    $this->parentCategories(),
            ]
        );
    }

    public function store(
        StoreCatalogCategoryRequest $request
    ): RedirectResponse {
        $validated =
            $request->validated();

        $category =
            CatalogCategory::create([
                ...$validated,

                'code' =>
                    $this->generateCode(),

                'slug' =>
                    $this->generateUniqueSlug(
                        $validated['name']
                    ),
            ]);

        return redirect()
            ->route(
                'editor.catalog.categories.edit',
                $category
            )
            ->with(
                'success',
                'Categoría creada correctamente.'
            );
    }

    public function edit(
        CatalogCategory $category
    ): Response {
        $category->loadCount([
            'products',
            'children',
        ]);

        return Inertia::render(
            'Editor/Catalog/Categories/Form',
            [
                'mode' =>
                    'edit',

                'category' =>
                    $category,

                'parentCategories' =>
                    $this->parentCategories(
                        $category->id
                    ),
            ]
        );
    }

    public function update(
        UpdateCatalogCategoryRequest $request,
        CatalogCategory $category
    ): RedirectResponse {
        $validated =
            $request->validated();

        $parentId =
            isset(
                $validated['parent_id']
            )
                ? (
                    $validated['parent_id']
                        !== null
                            ? (int) $validated['parent_id']
                            : null
                )
                : null;

        if (
            $parentId !== null
            &&
            $this->wouldCreateCycle(
                $category,
                $parentId
            )
        ) {
            return back()
                ->withErrors([
                    'parent_id' =>
                        'No puedes seleccionar una subcategoría de esta categoría como categoría superior.',
                ])
                ->withInput();
        }

        $category->update(
            $validated
        );

        return back()
            ->with(
                'success',
                'Categoría actualizada correctamente.'
            );
    }

    public function destroy(
        CatalogCategory $category
    ): RedirectResponse {
        $productsCount =
            $category
                ->products()
                ->count();

        $childrenCount =
            $category
                ->children()
                ->count();

        if (
            $productsCount > 0
            ||
            $childrenCount > 0
        ) {
            return back()
                ->withErrors([
                    'delete' =>
                        'No puedes eliminar esta categoría mientras tenga productos o subcategorías asociadas.',
                ]);
        }

        $category->delete();

        return redirect()
            ->route(
                'editor.catalog.categories.index'
            )
            ->with(
                'success',
                'Categoría eliminada correctamente.'
            );
    }

    private function parentCategories(
        ?int $excludeId = null
    ): array {
        return CatalogCategory::query()
            ->when(
                $excludeId !== null,
                fn ($query) =>
                    $query->where(
                        'id',
                        '!=',
                        $excludeId
                    )
            )
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get([
                'id',
                'name',
                'code',
                'parent_id',
            ])
            ->toArray();
    }

    private function generateCode(): string
    {
        $nextId =
            (
                (int) CatalogCategory::withTrashed()
                    ->max('id')
            ) + 1;

        return sprintf(
            'CAT-WEB-%03d',
            $nextId
        );
    }

    private function generateUniqueSlug(
        string $name
    ): string {
        $base =
            Str::slug(
                $name
            );

        if ($base === '') {
            $base =
                'categoria';
        }

        $slug =
            $base;

        $counter =
            2;

        while (
            CatalogCategory::withTrashed()
                ->where(
                    'slug',
                    $slug
                )
                ->exists()
        ) {
            $slug =
                $base .
                '-' .
                $counter;

            $counter++;
        }

        return $slug;
    }

    private function wouldCreateCycle(
        CatalogCategory $category,
        int $parentId
    ): bool {
        $currentId =
            $parentId;

        $visited =
            [];

        while ($currentId !== null) {
            if (
                $currentId ===
                $category->id
            ) {
                return true;
            }

            if (
                in_array(
                    $currentId,
                    $visited,
                    true
                )
            ) {
                return true;
            }

            $visited[] =
                $currentId;

            $currentId =
                CatalogCategory::query()
                    ->whereKey(
                        $currentId
                    )
                    ->value(
                        'parent_id'
                    );
        }

        return false;
    }
}
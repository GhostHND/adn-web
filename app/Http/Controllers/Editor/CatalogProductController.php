<?php

namespace App\Http\Controllers\Editor;

use App\Http\Controllers\Controller;
use App\Http\Requests\Editor\UpdateCatalogProductRequest;
use App\Models\AppCatalogProduct;
use App\Models\CatalogCategory;
use App\Models\CatalogProduct;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Inertia\Response;

class CatalogProductController extends Controller
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

        $categoryId =
            $request->integer(
                'category'
            );

        $products =
            AppCatalogProduct::query()
                ->with([
                    'webProduct' =>
                        function (
                            $query
                        ): void {
                            $query
                                ->with([
                                    'category:id,name',
                                    'mainMedia:id,path,disk,alt_text',
                                ])
                                ->withCount([
                                    'fields',
                                    'images',
                                ]);
                        },
                ])
                ->when(
                    $search !== '',
                    function (
                        $query
                    ) use (
                        $search
                    ): void {
                        $query->where(
                            function (
                                $inner
                            ) use (
                                $search
                            ): void {
                                $inner
                                    ->where(
                                        'name',
                                        'like',
                                        "%{$search}%"
                                    )
                                    ->orWhere(
                                        'item_code',
                                        'like',
                                        "%{$search}%"
                                    )
                                    ->orWhere(
                                        'category',
                                        'like',
                                        "%{$search}%"
                                    )
                                    ->orWhere(
                                        'item_type',
                                        'like',
                                        "%{$search}%"
                                    )
                                    ->orWhereHas(
                                        'webProduct',
                                        function (
                                            $web
                                        ) use (
                                            $search
                                        ): void {
                                            $web
                                                ->where(
                                                    'code',
                                                    'like',
                                                    "%{$search}%"
                                                )
                                                ->orWhere(
                                                    'slug',
                                                    'like',
                                                    "%{$search}%"
                                                )
                                                ->orWhere(
                                                    'short_description',
                                                    'like',
                                                    "%{$search}%"
                                                );
                                        }
                                    );
                            }
                        );
                    }
                )
                ->when(
                    $status ===
                        'pending',
                    fn (
                        $query
                    ) =>
                        $query
                            ->where(
                                'active',
                                true
                            )
                            ->whereDoesntHave(
                                'webProduct'
                            )
                )
                ->when(
                    $status ===
                        'prepared',
                    fn (
                        $query
                    ) =>
                        $query->whereHas(
                            'webProduct'
                        )
                )
                ->when(
                    in_array(
                        $status,
                        [
                            'published',
                            'draft',
                            'hidden',
                        ],
                        true
                    ),
                    fn (
                        $query
                    ) =>
                        $query->whereHas(
                            'webProduct',
                            fn (
                                $web
                            ) =>
                                $web->where(
                                    'status',
                                    $status
                                )
                        )
                )
                ->when(
                    $status ===
                        'inactive',
                    fn (
                        $query
                    ) =>
                        $query->where(
                            'active',
                            false
                        )
                )
                ->when(
                    $categoryId >
                        0,
                    fn (
                        $query
                    ) =>
                        $query->whereHas(
                            'webProduct',
                            fn (
                                $web
                            ) =>
                                $web->where(
                                    'category_id',
                                    $categoryId
                                )
                        )
                )
                ->orderByDesc(
                    'active'
                )
                ->orderBy(
                    'name'
                )
                ->paginate(
                    20
                )
                ->withQueryString();

        $products->through(
            fn (
                AppCatalogProduct $product
            ): array =>
                $this
                    ->appProductForIndex(
                        $product
                    )
        );

        $summary = [
            'total' =>
                AppCatalogProduct::query()
                    ->count(),

            'prepared' =>
                AppCatalogProduct::query()
                    ->whereHas(
                        'webProduct'
                    )
                    ->count(),

            'pending' =>
                AppCatalogProduct::query()
                    ->where(
                        'active',
                        true
                    )
                    ->whereDoesntHave(
                        'webProduct'
                    )
                    ->count(),

            'published' =>
                AppCatalogProduct::query()
                    ->where(
                        'active',
                        true
                    )
                    ->whereHas(
                        'webProduct',
                        fn (
                            $query
                        ) =>
                            $query->where(
                                'status',
                                'published'
                            )
                    )
                    ->count(),

            /*
            |--------------------------------------------------------------------------
            | LEGADO ACTIVO
            |--------------------------------------------------------------------------
            |
            | Los productos archivados mediante SoftDeletes no aparecen aquí.
            |
            */

            'legacy' =>
                CatalogProduct::query()
                    ->whereNull(
                        'app_catalog_product_id'
                    )
                    ->count(),
        ];

        /*
        |--------------------------------------------------------------------------
        | PRODUCTOS WEB HEREDADOS ACTIVOS
        |--------------------------------------------------------------------------
        |
        | No usamos withTrashed(). Un producto archivado queda definitivamente
        | fuera de las coincidencias y del Editor normal.
        |
        */

        $legacyProducts =
            CatalogProduct::query()
                ->whereNull(
                    'app_catalog_product_id'
                )
                ->orderBy(
                    'name'
                )
                ->get([
                    'id',
                    'code',
                    'name',
                    'app_reference_code',
                    'status',
                ])
                ->map(
                    fn (
                        CatalogProduct $product
                    ): array => [
                        'id' =>
                            $product->id,

                        'code' =>
                            $product->code,

                        'name' =>
                            $product->name,

                        'app_reference_code' =>
                            $product
                                ->app_reference_code,

                        'status' =>
                            $product->status,
                    ]
                )
                ->values()
                ->all();

        return Inertia::render(
            'Editor/Catalog/Products/Index',
            [
                'products' =>
                    $products,

                'summary' =>
                    $summary,

                'legacyProducts' =>
                    $legacyProducts,

                'categories' =>
                    $this->categories(),

                'filters' => [
                    'search' =>
                        $search,

                    'status' =>
                        $status,

                    'category' =>
                        $categoryId >
                            0
                            ? $categoryId
                            : null,
                ],
            ]
        );
    }

    public function prepare(
        AppCatalogProduct $appProduct
    ): Response | RedirectResponse {
        abort_unless(
            $appProduct->active
            &&
            !$appProduct
                ->source_deleted_at,
            422,
            'El producto está desactivado en ADN APP.'
        );

        /*
        |--------------------------------------------------------------------------
        | YA ESTÁ VINCULADO
        |--------------------------------------------------------------------------
        |
        | Aquí sí usamos withTrashed(), porque un producto que ya estaba
        | formalmente vinculado a la APP puede recuperarse si fue archivado.
        |
        */

        $existing =
            CatalogProduct::withTrashed()
                ->where(
                    'app_catalog_product_id',
                    $appProduct->id
                )
                ->first();

        if (
            $existing
        ) {
            $this
                ->linkMasterProduct(
                    $existing,
                    $appProduct
                );

            return redirect()
                ->route(
                    'editor.catalog.products.edit',
                    $existing
                );
        }

        /*
        |--------------------------------------------------------------------------
        | PRODUCTO WEB HEREDADO ACTIVO
        |--------------------------------------------------------------------------
        |
        | Solo se consideran productos NO archivados.
        |
        */

        $legacy =
            $this
                ->findLegacyProduct(
                    $appProduct
                );

        if (
            $legacy
        ) {
            $this
                ->linkMasterProduct(
                    $legacy,
                    $appProduct
                );

            return redirect()
                ->route(
                    'editor.catalog.products.edit',
                    $legacy
                )
                ->with(
                    'success',
                    'El producto fue vinculado con su contenido web existente. No se creó ningún duplicado.'
                );
        }

        return Inertia::render(
            'Editor/Catalog/Products/Form',
            [
                'mode' =>
                    'prepare',

                'product' =>
                    null,

                'appProduct' =>
                    $this
                        ->appProductForForm(
                            $appProduct
                        ),

                'categories' =>
                    $this->categories(),
            ]
        );
    }

    public function storeFromApp(
        UpdateCatalogProductRequest $request,
        AppCatalogProduct $appProduct
    ): RedirectResponse {
        abort_unless(
            $appProduct->active
            &&
            !$appProduct
                ->source_deleted_at,
            422,
            'El producto está desactivado en ADN APP.'
        );

        $validated =
            $request->validated();

        $product =
            DB::transaction(
                function () use (
                    $appProduct,
                    $validated
                ): CatalogProduct {
                    $existing =
                        CatalogProduct::withTrashed()
                            ->where(
                                'app_catalog_product_id',
                                $appProduct->id
                            )
                            ->first();

                    if (
                        !$existing
                    ) {
                        $existing =
                            $this
                                ->findLegacyProduct(
                                    $appProduct
                                );
                    }

                    if (
                        $existing
                    ) {
                        $this
                            ->linkMasterProduct(
                                $existing,
                                $appProduct
                            );

                        $existing->update(
                            $validated
                        );

                        return $existing;
                    }

                    return CatalogProduct::create([
                        'app_catalog_product_id' =>
                            $appProduct->id,

                        'code' =>
                            $this
                                ->generateCode(),

                        'app_reference_code' =>
                            $appProduct
                                ->item_code,

                        'category_id' =>
                            $validated[
                                'category_id'
                            ],

                        'slug' =>
                            $this
                                ->generateUniqueSlug(
                                    $appProduct
                                        ->name
                                ),

                        'name' =>
                            $appProduct
                                ->name,

                        'short_description' =>
                            $validated[
                                'short_description'
                            ]
                            ?? null,

                        'description' =>
                            $validated[
                                'description'
                            ]
                            ?? null,

                        'features' =>
                            $validated[
                                'features'
                            ]
                            ?? [],

                        'price_visible' =>
                            $validated[
                                'price_visible'
                            ],

                        'price_from' =>
                            $validated[
                                'price_from'
                            ],

                        'reference_price' =>
                            $validated[
                                'reference_price'
                            ]
                            ?? null,

                        'price_note' =>
                            $validated[
                                'price_note'
                            ]
                            ?? null,

                        'quote_mode' =>
                            $validated[
                                'quote_mode'
                            ],

                        'measurement_unit' =>
                            $appProduct
                                ->measurement_unit,

                        'status' =>
                            $validated[
                                'status'
                            ],

                        'featured' =>
                            $validated[
                                'featured'
                            ],

                        'show_on_home' =>
                            $validated[
                                'show_on_home'
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
                }
            );

        return redirect()
            ->route(
                'editor.catalog.products.edit',
                $product
            )
            ->with(
                'success',
                'Producto preparado correctamente para el sitio web.'
            );
    }

    public function edit(
        CatalogProduct $product
    ): Response {
        $product->load([
            'appProduct',
            'category:id,name',
            'fields.options',
        ]);

        abort_unless(
            $product
                ->appProduct,
            404
        );

        $product->loadCount([
            'images',
            'fields',
            'quoteRequestItems',
        ]);

        return Inertia::render(
            'Editor/Catalog/Products/Form',
            [
                'mode' =>
                    'edit',

                'product' =>
                    $this
                        ->productForForm(
                            $product
                        ),

                'appProduct' =>
                    $this
                        ->appProductForForm(
                            $product
                                ->appProduct
                        ),

                'categories' =>
                    $this->categories(),
            ]
        );
    }

    public function update(
        UpdateCatalogProductRequest $request,
        CatalogProduct $product
    ): RedirectResponse {
        $product->load(
            'appProduct'
        );

        abort_unless(
            $product
                ->appProduct,
            404
        );

        $validated =
            $request->validated();

        if (
            !$product
                ->appProduct
                ->active
            ||
            $product
                ->appProduct
                ->source_deleted_at
        ) {
            $validated[
                'status'
            ] =
                'draft';

            $validated[
                'featured'
            ] =
                false;

            $validated[
                'show_on_home'
            ] =
                false;
        }

        $product->update(
            $validated
        );

        $this
            ->linkMasterProduct(
                $product,
                $product
                    ->appProduct
            );

        return back()
            ->with(
                'success',
                'Contenido web actualizado correctamente.'
            );
    }

    private function appProductForIndex(
        AppCatalogProduct $appProduct
    ): array {
        $web =
            $appProduct
                ->webProduct;

        return [
            'id' =>
                $appProduct->id,

            'app_catalog_item_id' =>
                $appProduct
                    ->app_catalog_item_id,

            'item_code' =>
                $appProduct
                    ->item_code,

            'name' =>
                $appProduct->name,

            'description' =>
                $appProduct
                    ->description,

            'item_type' =>
                $appProduct
                    ->item_type,

            'category' =>
                $appProduct
                    ->category,

            'pricing_method' =>
                $appProduct
                    ->pricing_method,

            'measurement_unit' =>
                $appProduct
                    ->measurement_unit,

            'active' =>
                $appProduct
                    ->active,

            'source_updated_at' =>
                $appProduct
                    ->source_updated_at
                    ?->toIso8601String(),

            'web_product' =>
                $web
                    ? [
                        'id' =>
                            $web->id,

                        'code' =>
                            $web->code,

                        'slug' =>
                            $web->slug,

                        'short_description' =>
                            $web
                                ->short_description,

                        'status' =>
                            $web->status,

                        'featured' =>
                            $web->featured,

                        'show_on_home' =>
                            $web
                                ->show_on_home,

                        'price_visible' =>
                            $web
                                ->price_visible,

                        'reference_price' =>
                            $web
                                ->reference_price,

                        'category' =>
                            $web
                                ->category
                                ? [
                                    'id' =>
                                        $web
                                            ->category
                                            ->id,

                                    'name' =>
                                        $web
                                            ->category
                                            ->name,
                                ]
                                : null,

                        'images_count' =>
                            $web
                                ->images_count,

                        'fields_count' =>
                            $web
                                ->fields_count,
                    ]
                    : null,
        ];
    }

    private function appProductForForm(
        AppCatalogProduct $appProduct
    ): array {
        return [
            'id' =>
                $appProduct->id,

            'app_catalog_item_id' =>
                $appProduct
                    ->app_catalog_item_id,

            'item_code' =>
                $appProduct
                    ->item_code,

            'name' =>
                $appProduct->name,

            'description' =>
                $appProduct
                    ->description,

            'item_type' =>
                $appProduct
                    ->item_type,

            'category' =>
                $appProduct
                    ->category,

            'pricing_method' =>
                $appProduct
                    ->pricing_method,

            'measurement_unit' =>
                $appProduct
                    ->measurement_unit,

            'active' =>
                $appProduct
                    ->active,

            'source_updated_at' =>
                $appProduct
                    ->source_updated_at
                    ?->toIso8601String(),

            'suggested_quote_mode' =>
                $this
                    ->webQuoteMode(
                        $appProduct
                            ->pricing_method
                    ),
        ];
    }

    private function productForForm(
        CatalogProduct $product
    ): array {
        return [
            'id' =>
                $product->id,

            'code' =>
                $product->code,

            'slug' =>
                $product->slug,

            'category_id' =>
                $product
                    ->category_id,

            'short_description' =>
                $product
                    ->short_description,

            'description' =>
                $product
                    ->description,

            'features' =>
                $product
                    ->features,

            'price_visible' =>
                $product
                    ->price_visible,

            'price_from' =>
                $product
                    ->price_from,

            'reference_price' =>
                $product
                    ->reference_price,

            'price_note' =>
                $product
                    ->price_note,

            'quote_mode' =>
                $product
                    ->quote_mode,

            'status' =>
                $product->status,

            'featured' =>
                $product
                    ->featured,

            'show_on_home' =>
                $product
                    ->show_on_home,

            'sort_order' =>
                $product
                    ->sort_order,

            'meta_title' =>
                $product
                    ->meta_title,

            'meta_description' =>
                $product
                    ->meta_description,

            'images_count' =>
                $product
                    ->images_count,

            'fields_count' =>
                $product
                    ->fields_count,

            'quote_request_items_count' =>
                $product
                    ->quote_request_items_count,
        ];
    }

    private function findLegacyProduct(
        AppCatalogProduct $appProduct
    ): ?CatalogProduct {
        $masterReference =
            $this
                ->normalizeReference(
                    $appProduct
                        ->item_code
                );

        /*
        |--------------------------------------------------------------------------
        | IMPORTANTE
        |--------------------------------------------------------------------------
        |
        | NO usamos withTrashed().
        |
        | Un producto web archivado se considera cerrado y no puede volver
        | a vincularse automáticamente a otro producto maestro.
        |
        */

        if (
            $masterReference !==
            ''
        ) {
            $referenceMatches =
                CatalogProduct::query()
                    ->whereNull(
                        'app_catalog_product_id'
                    )
                    ->whereNotNull(
                        'app_reference_code'
                    )
                    ->get()
                    ->filter(
                        fn (
                            CatalogProduct $product
                        ): bool =>
                            $this
                                ->normalizeReference(
                                    $product
                                        ->app_reference_code
                                )
                            ===
                            $masterReference
                    )
                    ->values();

            if (
                $referenceMatches
                    ->count()
                ===
                1
            ) {
                return $referenceMatches
                    ->first();
            }
        }

        $nameMatches =
            CatalogProduct::query()
                ->whereNull(
                    'app_catalog_product_id'
                )
                ->where(
                    'name',
                    $appProduct
                        ->name
                )
                ->get();

        if (
            $nameMatches
                ->count()
            ===
            1
        ) {
            return $nameMatches
                ->first();
        }

        return null;
    }

    private function linkMasterProduct(
        CatalogProduct $product,
        AppCatalogProduct $appProduct
    ): void {
        if (
            $product->trashed()
        ) {
            $product->restore();
        }

        $product->forceFill([
            'app_catalog_product_id' =>
                $appProduct->id,

            'app_reference_code' =>
                $appProduct
                    ->item_code,

            'name' =>
                $appProduct->name,

            'measurement_unit' =>
                $appProduct
                    ->measurement_unit,
        ]);

        if (
            !$appProduct
                ->active
            ||
            $appProduct
                ->source_deleted_at
        ) {
            $product->status =
                'draft';

            $product->featured =
                false;

            $product->show_on_home =
                false;
        }

        $product->save();
    }

    private function normalizeReference(
        ?string $reference
    ): string {
        if (
            !$reference
        ) {
            return '';
        }

        return preg_replace(
            '/[^A-Z0-9]+/',
            '',
            mb_strtoupper(
                trim(
                    $reference
                )
            )
        )
        ?? '';
    }

    private function categories(): array
    {
        return CatalogCategory::query()
            ->where(
                'status',
                '!=',
                'hidden'
            )
            ->orderBy(
                'sort_order'
            )
            ->orderBy(
                'name'
            )
            ->get([
                'id',
                'name',
                'code',
                'parent_id',
                'status',
            ])
            ->toArray();
    }

    private function generateCode(): string
    {
        $nextId =
            (
                (int)
                CatalogProduct::withTrashed()
                    ->max(
                        'id'
                    )
            )
            +
            1;

        return sprintf(
            'WEB-%04d',
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

        if (
            $base ===
            ''
        ) {
            $base =
                'producto';
        }

        $slug =
            $base;

        $counter =
            2;

        while (
            CatalogProduct::withTrashed()
                ->where(
                    'slug',
                    $slug
                )
                ->exists()
        ) {
            $slug =
                $base
                . '-'
                . $counter;

            $counter++;
        }

        return $slug;
    }

    private function webQuoteMode(
        string $pricingMethod
    ): string {
        return match (
            $pricingMethod
        ) {
            'AREA' =>
                'AREA',

            'LINEAR' =>
                'LINEAR',

            'UNIT',
            'FIXED',
            'RESALE' =>
                'UNIT',

            default =>
                'CUSTOM',
        };
    }
}
<?php

namespace App\Http\Controllers\PublicSite;

use App\Http\Controllers\Controller;
use App\Models\CatalogCategory;
use App\Models\CatalogProduct;
use App\Models\CatalogProductField;
use App\Models\CatalogProductImage;
use App\Models\CatalogProductOption;
use App\Models\Media;
use App\Models\WebsiteAd;
use App\Services\MediaService;
use App\Services\WebsiteAdService;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class CatalogController extends Controller
{
    public function __construct(
        private readonly MediaService $mediaService,
        private readonly WebsiteAdService $websiteAdService
    ) {
    }

    public function index(
        Request $request
    ): Response {
        $search =
            trim(
                (string) $request->query(
                    'buscar',
                    ''
                )
            );

        $categorySlug =
            trim(
                (string) $request->query(
                    'categoria',
                    ''
                )
            );

        $categories =
            CatalogCategory::query()
                ->where(
                    'status',
                    'published'
                )
                ->withCount([
                    'products' =>
                        fn (
                            Builder $query
                        ) =>
                            $this
                                ->applyPublicProductScope(
                                    $query
                                ),
                ])
                ->orderBy(
                    'sort_order'
                )
                ->orderBy(
                    'name'
                )
                ->get();

        $selectedCategory =
            $categorySlug !== ''
                ? CatalogCategory::query()
                    ->where(
                        'status',
                        'published'
                    )
                    ->where(
                        'slug',
                        $categorySlug
                    )
                    ->first()
                : null;

        $categoryIds =
            [];

        if (
            $selectedCategory
        ) {
            $categoryIds = [
                (int)
                $selectedCategory->id,
            ];

            $childIds =
                CatalogCategory::query()
                    ->where(
                        'parent_id',
                        $selectedCategory->id
                    )
                    ->where(
                        'status',
                        'published'
                    )
                    ->pluck(
                        'id'
                    )
                    ->map(
                        fn (
                            $id
                        ): int =>
                            (int)
                            $id
                    )
                    ->all();

            $categoryIds =
                array_values(
                    array_unique([
                        ...$categoryIds,
                        ...$childIds,
                    ])
                );
        }

        $products =
            CatalogProduct::query();

        $this
            ->applyPublicProductScope(
                $products
            );

        $products =
            $products
                ->with([
                    'category:id,name,slug',
                    'mainMedia',
                ])
                ->when(
                    $search !== '',
                    function (
                        Builder $query
                    ) use (
                        $search
                    ): void {
                        $query->where(
                            function (
                                Builder $inner
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
                                        'short_description',
                                        'like',
                                        "%{$search}%"
                                    )
                                    ->orWhere(
                                        'description',
                                        'like',
                                        "%{$search}%"
                                    );
                            }
                        );
                    }
                )
                ->when(
                    count(
                        $categoryIds
                    ) > 0,
                    fn (
                        Builder $query
                    ) =>
                        $query->whereIn(
                            'category_id',
                            $categoryIds
                        )
                )
                ->orderByDesc(
                    'featured'
                )
                ->orderBy(
                    'sort_order'
                )
                ->orderBy(
                    'name'
                )
                ->paginate(
                    12
                )
                ->withQueryString();

        $products->through(
            fn (
                CatalogProduct $product
            ): array =>
                $this
                    ->serializeProductCard(
                        $product
                    )
        );

        return Inertia::render(
            'Public/Catalog/Index',
            [
                'products' =>
                    $products,

                'categories' =>
                    $categories
                        ->map(
                            fn (
                                CatalogCategory $category
                            ): array => [
                                'id' =>
                                    $category->id,

                                'name' =>
                                    $category->name,

                                'slug' =>
                                    $category->slug,

                                'parent_id' =>
                                    $category
                                        ->parent_id,

                                'products_count' =>
                                    $category
                                        ->products_count,
                            ]
                        )
                        ->values(),

                'selectedCategory' =>
                    $selectedCategory
                        ? [
                            'id' =>
                                $selectedCategory->id,

                            'name' =>
                                $selectedCategory->name,

                            'slug' =>
                                $selectedCategory->slug,
                        ]
                        : null,

                'filters' => [
                    'buscar' =>
                        $search,

                    'categoria' =>
                        $categorySlug,
                ],

                'ads' => [
                    'sidebar' =>
                        $this
                            ->websiteAdService
                            ->placement(
                                WebsiteAd::PLACEMENT_CATALOG_SIDEBAR
                            ),

                    'mobile' =>
                        $this
                            ->websiteAdService
                            ->placement(
                                WebsiteAd::PLACEMENT_CATALOG_MOBILE
                            ),

                    'horizontal' =>
                        $this
                            ->websiteAdService
                            ->placement(
                                WebsiteAd::PLACEMENT_CATALOG_HORIZONTAL
                            ),
                ],
            ]
        );
    }

    public function show(
        string $slug
    ): Response {
        $query =
            CatalogProduct::query();

        $this
            ->applyPublicProductScope(
                $query
            );

        $product =
            $query
                ->where(
                    'slug',
                    $slug
                )
                ->with([
                    'category:id,name,slug',

                    'mainMedia',

                    'images' =>
                        fn (
                            $query
                        ) =>
                            $query
                                ->orderBy(
                                    'sort_order'
                                )
                                ->orderBy(
                                    'id'
                                ),

                    'images.media',

                    'fields' =>
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

                    'fields.options' =>
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
                ])
                ->firstOrFail();

        return Inertia::render(
            'Public/Catalog/Show',
            [
                'product' =>
                    $this
                        ->serializeProductDetail(
                            $product
                        ),

                /*
                |--------------------------------------------------------------------------
                | PROMOCIÓN DEL DETALLE
                |--------------------------------------------------------------------------
                |
                | La pieza desaparece completamente cuando no existen artes
                | vigentes para product_rectangle.
                |
                */

                'ads' => [
                    'rectangle' =>
                        $this
                            ->websiteAdService
                            ->placement(
                                WebsiteAd::PLACEMENT_PRODUCT_RECTANGLE
                            ),
                ],
            ]
        );
    }

    private function applyPublicProductScope(
        Builder $query
    ): Builder {
        return $query
            ->where(
                'status',
                'published'
            )
            ->whereNotNull(
                'app_catalog_product_id'
            )
            ->whereHas(
                'appProduct',
                function (
                    Builder $appQuery
                ): void {
                    $appQuery
                        ->where(
                            'active',
                            true
                        )
                        ->whereNull(
                            'source_deleted_at'
                        );
                }
            );
    }

    private function serializeProductCard(
        CatalogProduct $product
    ): array {
        return [
            'id' =>
                $product->id,

            'code' =>
                $product->code,

            'slug' =>
                $product->slug,

            'name' =>
                $product->name,

            'short_description' =>
                $product
                    ->short_description,

            'featured' =>
                (bool)
                $product->featured,

            'price_visible' =>
                (bool)
                $product->price_visible,

            'price_from' =>
                (bool)
                $product->price_from,

            'reference_price' =>
                $product
                    ->reference_price,

            'price_note' =>
                $product
                    ->price_note,

            'category' =>
                $product->category
                    ? [
                        'id' =>
                            $product
                                ->category
                                ->id,

                        'name' =>
                            $product
                                ->category
                                ->name,

                        'slug' =>
                            $product
                                ->category
                                ->slug,
                    ]
                    : null,

            'image' =>
                $product->mainMedia
                    ? $this
                        ->serializeMedia(
                            $product
                                ->mainMedia
                        )
                    : null,
        ];
    }

    private function serializeProductDetail(
        CatalogProduct $product
    ): array {
        return [
            'id' =>
                $product->id,

            'code' =>
                $product->code,

            'slug' =>
                $product->slug,

            'name' =>
                $product->name,

            'short_description' =>
                $product
                    ->short_description,

            'description' =>
                $product
                    ->description,

            'features' =>
                $product->features
                ?? [],

            'featured' =>
                (bool)
                $product->featured,

            'price_visible' =>
                (bool)
                $product->price_visible,

            'price_from' =>
                (bool)
                $product->price_from,

            'reference_price' =>
                $product
                    ->reference_price,

            'price_note' =>
                $product
                    ->price_note,

            'quote_mode' =>
                $product
                    ->quote_mode,

            'measurement_unit' =>
                $product
                    ->measurement_unit,

            'meta_title' =>
                $product
                    ->meta_title,

            'meta_description' =>
                $product
                    ->meta_description,

            'category' =>
                $product->category
                    ? [
                        'id' =>
                            $product
                                ->category
                                ->id,

                        'name' =>
                            $product
                                ->category
                                ->name,

                        'slug' =>
                            $product
                                ->category
                                ->slug,
                    ]
                    : null,

            'main_media' =>
                $product->mainMedia
                    ? $this
                        ->serializeMedia(
                            $product
                                ->mainMedia
                        )
                    : null,

            'images' =>
                $product
                    ->images
                    ->map(
                        fn (
                            CatalogProductImage $image
                        ): array => [
                            'id' =>
                                $image->id,

                            'media_id' =>
                                $image
                                    ->media_id,

                            'alt_text' =>
                                $image
                                    ->alt_text,

                            'media' =>
                                $this
                                    ->serializeMedia(
                                        $image
                                            ->media
                                    ),
                        ]
                    )
                    ->values(),

            'fields' =>
                $product
                    ->fields
                    ->map(
                        fn (
                            CatalogProductField $field
                        ): array =>
                            $this
                                ->serializeField(
                                    $field
                                )
                    )
                    ->values(),
        ];
    }

    private function serializeField(
        CatalogProductField $field
    ): array {
        return [
            'id' =>
                $field->id,

            'field_key' =>
                $field
                    ->field_key,

            'label' =>
                $field->label,

            'field_type' =>
                $field
                    ->field_type,

            'placeholder' =>
                $field
                    ->placeholder,

            'help_text' =>
                $field
                    ->help_text,

            'unit' =>
                $field->unit,

            'required' =>
                (bool)
                $field->required,

            'min_value' =>
                $field
                    ->min_value,

            'max_value' =>
                $field
                    ->max_value,

            'step' =>
                $field->step,

            'default_value' =>
                $field
                    ->default_value,

            'options' =>
                $field
                    ->options
                    ->map(
                        fn (
                            CatalogProductOption $option
                        ): array => [
                            'id' =>
                                $option->id,

                            'label' =>
                                $option
                                    ->label,

                            'value' =>
                                $option
                                    ->value,

                            'extra_price' =>
                                $option
                                    ->extra_price,
                        ]
                    )
                    ->values(),
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
                $media
                    ->alt_text,

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
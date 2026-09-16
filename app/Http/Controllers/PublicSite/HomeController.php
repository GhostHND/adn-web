<?php

namespace App\Http\Controllers\PublicSite;

use App\Http\Controllers\Controller;
use App\Models\CatalogProduct;
use App\Models\Media;
use App\Models\Page;
use App\Models\PageSection;
use App\Models\WebsiteAd;
use App\Services\MediaService;
use App\Services\WebsiteAdService;
use Illuminate\Database\Eloquent\Builder;
use Inertia\Inertia;
use Inertia\Response;

class HomeController extends Controller
{
    public function __construct(
        private readonly MediaService $mediaService,
        private readonly WebsiteAdService $websiteAdService
    ) {
    }

    public function index(): Response
    {
        $page =
            Page::query()
                ->where(
                    'slug',
                    'inicio'
                )
                ->where(
                    'status',
                    'published'
                )
                ->where(
                    function (
                        Builder $query
                    ): void {
                        $query
                            ->whereNull(
                                'published_at'
                            )
                            ->orWhere(
                                'published_at',
                                '<=',
                                now()
                            );
                    }
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
                ->firstOrFail();

        $products =
            CatalogProduct::query();

        $this->applyPublicProductScope(
            $products
        );

        $products =
            $products
                ->with([
                    'category:id,name,slug',
                    'mainMedia',
                ])
                ->orderByDesc(
                    'featured'
                )
                ->orderBy(
                    'sort_order'
                )
                ->orderBy(
                    'name'
                )
                ->limit(
                    6
                )
                ->get()
                ->map(
                    fn (
                        CatalogProduct $product
                    ): array =>
                        $this
                            ->serializeProduct(
                                $product
                            )
                )
                ->values()
                ->all();

        return Inertia::render(
            'Public/Home',
            [
                'page' =>
                    $this
                        ->serializePage(
                            $page
                        ),

                'products' =>
                    $products,

                'promotions' => [
                    'siteWide' =>
                        $this
                            ->websiteAdService
                            ->placement(
                                WebsiteAd::PLACEMENT_SITE_WIDE
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

    private function serializePage(
        Page $page
    ): array {
        return [
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

            'meta_title' =>
                $page->meta_title,

            'meta_description' =>
                $page->meta_description,

            'canonical_url' =>
                $page->canonical_url,

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

                            'content' =>
                                $section
                                    ->content
                                ?? [],

                            'settings' =>
                                $section
                                    ->settings
                                ?? [],

                            'media' =>
                                $section->media
                                    ? $this
                                        ->serializeMedia(
                                            $section
                                                ->media
                                        )
                                    : null,

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
                        ]
                    )
                    ->values()
                    ->all(),
        ];
    }

    private function serializeProduct(
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
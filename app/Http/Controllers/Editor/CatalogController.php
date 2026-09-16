<?php

namespace App\Http\Controllers\Editor;

use App\Http\Controllers\Controller;
use App\Models\CatalogCategory;
use App\Models\CatalogProduct;
use Inertia\Inertia;
use Inertia\Response;

class CatalogController extends Controller
{
    public function index(): Response
    {
        $stats = [
            'categories' =>
                CatalogCategory::count(),

            'published_categories' =>
                CatalogCategory::query()
                    ->where('status', 'published')
                    ->count(),

            'products' =>
                CatalogProduct::count(),

            'published_products' =>
                CatalogProduct::query()
                    ->where('status', 'published')
                    ->count(),

            'featured_products' =>
                CatalogProduct::query()
                    ->where('featured', true)
                    ->count(),
        ];

        $categories =
            CatalogCategory::query()
                ->withCount('products')
                ->orderBy('sort_order')
                ->orderBy('name')
                ->limit(8)
                ->get([
                    'id',
                    'code',
                    'name',
                    'slug',
                    'status',
                ]);

        $products =
            CatalogProduct::query()
                ->with([
                    'category:id,name',
                ])
                ->latest()
                ->limit(6)
                ->get([
                    'id',
                    'code',
                    'name',
                    'category_id',
                    'status',
                    'featured',
                    'reference_price',
                    'price_visible',
                    'created_at',
                ]);

        return Inertia::render(
            'Editor/Catalog/Index',
            [
                'stats' =>
                    $stats,

                'categories' =>
                    $categories,

                'products' =>
                    $products,
            ]
        );
    }
}
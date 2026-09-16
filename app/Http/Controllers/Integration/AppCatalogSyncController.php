<?php

namespace App\Http\Controllers\Integration;

use App\Http\Controllers\Controller;
use App\Http\Requests\Integration\SyncAppCatalogProductRequest;
use App\Models\AppCatalogProduct;
use App\Models\CatalogProduct;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class AppCatalogSyncController extends Controller
{
    public function store(
        SyncAppCatalogProductRequest $request
    ): JsonResponse {
        $data =
            $request->validated();

        $result =
            DB::transaction(
                function () use (
                    $data
                ): array {
                    $mirror =
                        AppCatalogProduct::query()
                            ->updateOrCreate(
                                [
                                    'app_catalog_item_id' =>
                                        $data[
                                            'app_catalog_item_id'
                                        ],
                                ],
                                [
                                    'item_code' =>
                                        $data[
                                            'item_code'
                                        ],

                                    'name' =>
                                        $data[
                                            'name'
                                        ],

                                    'description' =>
                                        $data[
                                            'description'
                                        ]
                                        ?? null,

                                    'item_type' =>
                                        $data[
                                            'item_type'
                                        ],

                                    'category' =>
                                        $data[
                                            'category'
                                        ]
                                        ?? null,

                                    'pricing_method' =>
                                        $data[
                                            'pricing_method'
                                        ],

                                    'measurement_unit' =>
                                        $data[
                                            'measurement_unit'
                                        ]
                                        ?? null,

                                    'active' =>
                                        (bool)
                                        $data[
                                            'active'
                                        ],

                                    'source_created_at' =>
                                        $data[
                                            'source_created_at'
                                        ]
                                        ?? null,

                                    'source_updated_at' =>
                                        $data[
                                            'source_updated_at'
                                        ]
                                        ?? null,

                                    'source_deleted_at' =>
                                        $data[
                                            'source_deleted_at'
                                        ]
                                        ?? null,

                                    'synced_at' =>
                                        now(),
                                ]
                            );

                    /*
                    |--------------------------------------------------------------------------
                    | PRODUCTO WEB YA VINCULADO
                    |--------------------------------------------------------------------------
                    |
                    | Un producto YA vinculado puede estar archivado.
                    | La relación sigue siendo válida y conservamos el historial.
                    |
                    */

                    $webProduct =
                        CatalogProduct::withTrashed()
                            ->where(
                                'app_catalog_product_id',
                                $mirror->id
                            )
                            ->first();

                    /*
                    |--------------------------------------------------------------------------
                    | LEGADO ACTIVO POR REFERENCIA
                    |--------------------------------------------------------------------------
                    |
                    | Los productos archivados NO participan en coincidencias
                    | automáticas.
                    |
                    */

                    if (
                        !$webProduct
                    ) {
                        $webProduct =
                            CatalogProduct::query()
                                ->whereNull(
                                    'app_catalog_product_id'
                                )
                                ->where(
                                    'app_reference_code',
                                    $mirror
                                        ->item_code
                                )
                                ->first();
                    }

                    /*
                    |--------------------------------------------------------------------------
                    | LEGADO ACTIVO POR NOMBRE
                    |--------------------------------------------------------------------------
                    |
                    | Solo vinculamos cuando existe una única coincidencia
                    | exacta y activa.
                    |
                    */

                    if (
                        !$webProduct
                    ) {
                        $nameMatches =
                            CatalogProduct::query()
                                ->whereNull(
                                    'app_catalog_product_id'
                                )
                                ->where(
                                    'name',
                                    $mirror->name
                                )
                                ->get();

                        if (
                            $nameMatches->count()
                            ===
                            1
                        ) {
                            $webProduct =
                                $nameMatches
                                    ->first();
                        }
                    }

                    /*
                    |--------------------------------------------------------------------------
                    | ACTUALIZAR DATOS MAESTROS
                    |--------------------------------------------------------------------------
                    |
                    | Nunca modificamos:
                    |
                    | - descripción web
                    | - categoría web
                    | - imágenes
                    | - campos
                    | - características
                    | - SEO
                    | - precios públicos configurados en Editor
                    |
                    */

                    if (
                        $webProduct
                    ) {
                        $updates = [
                            'app_catalog_product_id' =>
                                $mirror->id,

                            'app_reference_code' =>
                                $mirror
                                    ->item_code,

                            'name' =>
                                $mirror
                                    ->name,

                            'measurement_unit' =>
                                $mirror
                                    ->measurement_unit,
                        ];

                        /*
                        |--------------------------------------------------------------------------
                        | DESACTIVADO EN ADN APP
                        |--------------------------------------------------------------------------
                        */

                        if (
                            !$mirror
                                ->active
                            ||
                            $mirror
                                ->source_deleted_at
                        ) {
                            $updates[
                                'status'
                            ] =
                                'draft';

                            $updates[
                                'featured'
                            ] =
                                false;

                            $updates[
                                'show_on_home'
                            ] =
                                false;
                        }

                        $webProduct
                            ->forceFill(
                                $updates
                            )
                            ->save();
                    }

                    return [
                        'mirror' =>
                            $mirror,

                        'web_product' =>
                            $webProduct,
                    ];
                }
            );

        /** @var AppCatalogProduct $mirror */
        $mirror =
            $result[
                'mirror'
            ];

        /** @var CatalogProduct|null $webProduct */
        $webProduct =
            $result[
                'web_product'
            ];

        return response()->json([
            'ok' =>
                true,

            'product' => [
                'id' =>
                    $mirror->id,

                'app_catalog_item_id' =>
                    $mirror
                        ->app_catalog_item_id,

                'item_code' =>
                    $mirror
                        ->item_code,

                'name' =>
                    $mirror
                        ->name,

                'active' =>
                    $mirror
                        ->active,

                'web_product_id' =>
                    $webProduct
                        ?->id,

                'web_product_status' =>
                    $webProduct
                        ?->status,
            ],
        ]);
    }
}
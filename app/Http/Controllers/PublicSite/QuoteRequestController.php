<?php

namespace App\Http\Controllers\PublicSite;

use App\Http\Controllers\Controller;
use App\Http\Requests\PublicSite\StoreQuoteRequest;
use App\Models\CatalogProduct;
use App\Services\AppLeadIntegrationService;
use App\Services\QuoteRequestService;
use Illuminate\Http\RedirectResponse;
use Throwable;

class QuoteRequestController extends Controller
{
    public function __construct(
        private readonly QuoteRequestService $quoteRequestService,
        private readonly AppLeadIntegrationService $appLeadIntegrationService
    ) {
    }

    public function store(
        StoreQuoteRequest $request,
        CatalogProduct $product
    ): RedirectResponse {
        /*
        |--------------------------------------------------------------------------
        | SEGURIDAD DE COTIZACIÓN
        |--------------------------------------------------------------------------
        |
        | No basta con que el producto tenga status "published".
        |
        | También debe:
        |
        | - estar vinculado con ADN APP;
        | - existir su producto maestro;
        | - continuar activo;
        | - no haber sido eliminado en la APP.
        |
        | Así evitamos que alguien intente cotizar directamente contra el POST
        | utilizando el slug de un producto retirado.
        |
        */

        $product->loadMissing(
            'appProduct'
        );

        abort_unless(
            $product->status ===
                'published'
            &&
            $product
                ->app_catalog_product_id
                !==
                null
            &&
            $product
                ->appProduct
                !==
                null
            &&
            $product
                ->appProduct
                ->active
            &&
            $product
                ->appProduct
                ->source_deleted_at
                ===
                null,
            404
        );

        $validated =
            $request->validated();

        $quote =
            $this
                ->quoteRequestService
                ->create(
                    $product,
                    $validated,
                    $request->input(
                        'values',
                        []
                    ),
                    $request->file(
                        'files',
                        []
                    ),
                    $request->ip(),
                    $request->userAgent()
                );

        /*
        |--------------------------------------------------------------------------
        | SINCRONIZACIÓN CON ADN APP
        |--------------------------------------------------------------------------
        |
        | La solicitud ya está almacenada en ADN Web antes de enviar la
        | integración. Si ADN APP no responde, el cliente no pierde su
        | solicitud.
        |
        */

        try {
            $this
                ->appLeadIntegrationService
                ->sync(
                    $quote
                );
        } catch (
            Throwable $exception
        ) {
            report(
                $exception
            );
        }

        return redirect()
            ->route(
                'public.catalog.show',
                [
                    'slug' =>
                        $product->slug,
                ]
            )
            ->with(
                'success',
                sprintf(
                    'Solicitud %s enviada correctamente. Nos pondremos en contacto contigo para continuar con la cotización.',
                    $quote
                        ->request_number
                )
            );
    }
}
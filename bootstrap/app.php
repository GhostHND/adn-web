<?php

use App\Http\Middleware\HandleInertiaRequests;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;

return Application::configure(
    basePath: dirname(__DIR__)
)
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(
        function (
            Middleware $middleware
        ): void {
            /*
            |--------------------------------------------------------------------------
            | Integraciones privadas ADN APP -> ADN WEB
            |--------------------------------------------------------------------------
            |
            | Estas rutas no son formularios enviados desde un navegador.
            | Se autentican mediante la firma HMAC privada entre ambos sistemas,
            | por lo que no deben requerir token CSRF.
            |
            | El resto del sitio web y del Editor conserva CSRF normalmente.
            |
            */

            $middleware
                ->validateCsrfTokens(
                    except: [
                        'integrations/adn-app/*',
                    ],
                );

            $middleware
                ->web(
                    append: [
                        HandleInertiaRequests::class,
                    ],
                );
        }
    )
    ->withExceptions(
        function (
            Exceptions $exceptions
        ): void {
            /*
            |--------------------------------------------------------------------------
            | Producción
            |--------------------------------------------------------------------------
            |
            | Nunca expondremos páginas con identidad del framework.
            |
            */

            $exceptions
                ->respond(
                    function (
                        $response,
                        Throwable $exception,
                        Request $request
                    ) {
                        return $response;
                    }
                );
        }
    )
    ->create();
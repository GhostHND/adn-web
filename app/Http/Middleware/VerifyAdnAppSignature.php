<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class VerifyAdnAppSignature
{
    public function handle(
        Request $request,
        Closure $next
    ): Response {
        $secret =
            trim(
                (string) config(
                    'adn_integration.secret',
                    ''
                )
            );

        if (
            $secret ===
            ''
        ) {
            return response()->json(
                [
                    'ok' =>
                        false,

                    'message' =>
                        'Integración no configurada.',
                ],
                503
            );
        }

        $timestampHeader =
            trim(
                (string) $request->header(
                    'X-ADN-Timestamp',
                    ''
                )
            );

        $requestKey =
            trim(
                (string) $request->header(
                    'X-ADN-Idempotency-Key',
                    ''
                )
            );

        $signature =
            strtolower(
                trim(
                    (string) $request->header(
                        'X-ADN-Signature',
                        ''
                    )
                )
            );

        if (
            !preg_match(
                '/^\d{10}$/',
                $timestampHeader
            )
            ||
            $requestKey ===
                ''
            ||
            mb_strlen(
                $requestKey
            ) >
                190
            ||
            !preg_match(
                '/^[a-f0-9]{64}$/',
                $signature
            )
        ) {
            return response()->json(
                [
                    'ok' =>
                        false,

                    'message' =>
                        'Firma de integración inválida.',
                ],
                401
            );
        }

        $timestamp =
            (int) $timestampHeader;

        $maxClockSkew =
            max(
                30,
                (int) config(
                    'adn_integration.max_clock_skew',
                    300
                )
            );

        if (
            abs(
                now()->timestamp
                -
                $timestamp
            ) >
            $maxClockSkew
        ) {
            return response()->json(
                [
                    'ok' =>
                        false,

                    'message' =>
                        'La solicitud de integración expiró.',
                ],
                401
            );
        }

        /*
        |--------------------------------------------------------------------------
        | CLAVE DERIVADA
        |--------------------------------------------------------------------------
        |
        | Aunque utilizamos el mismo secreto compartido entre ambos sistemas,
        | esta dirección utiliza una clave derivada distinta.
        |
        */

        $accessKey =
            hash_hmac(
                'sha256',
                'app-to-web',
                $secret,
                true
            );

        /*
        |--------------------------------------------------------------------------
        | FIRMA
        |--------------------------------------------------------------------------
        |
        | Incluimos método y ruta. De esa forma no se puede modificar
        | SOL-WEB-XXXXXX ni el ID de un archivo sin invalidar la firma.
        |
        */

        $payload =
            $timestampHeader
            . "\n"
            . $requestKey
            . "\n"
            . strtoupper(
                $request->getMethod()
            )
            . "\n"
            . $request->getPathInfo()
            . "\n"
            . $request->getContent();

        $expectedSignature =
            hash_hmac(
                'sha256',
                $payload,
                $accessKey
            );

        if (
            !hash_equals(
                $expectedSignature,
                $signature
            )
        ) {
            return response()->json(
                [
                    'ok' =>
                        false,

                    'message' =>
                        'Firma de integración inválida.',
                ],
                401
            );
        }

        return $next(
            $request
        );
    }
}
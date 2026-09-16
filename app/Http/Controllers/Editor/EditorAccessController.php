<?php

namespace App\Http\Controllers\Editor;

use App\Http\Controllers\Controller;
use App\Models\QuoteRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class EditorAccessController extends Controller
{
    public function consume(
        Request $request
    ): RedirectResponse {
        $requestNumber =
            trim(
                (string) $request->query(
                    'request_number',
                    ''
                )
            );

        $expiresRaw =
            trim(
                (string) $request->query(
                    'expires',
                    ''
                )
            );

        $nonce =
            trim(
                (string) $request->query(
                    'nonce',
                    ''
                )
            );

        $signature =
            strtolower(
                trim(
                    (string) $request->query(
                        'signature',
                        ''
                    )
                )
            );

        abort_unless(
            preg_match(
                '/^SOL-WEB-\d{6,}$/',
                $requestNumber
            ) ===
                1,
            401
        );

        abort_unless(
            preg_match(
                '/^\d{10}$/',
                $expiresRaw
            ) ===
                1,
            401
        );

        abort_unless(
            preg_match(
                '/^[a-f0-9]{64}$/',
                $nonce
            ) ===
                1,
            401
        );

        abort_unless(
            preg_match(
                '/^[a-f0-9]{64}$/',
                $signature
            ) ===
                1,
            401
        );

        $expires =
            (int) $expiresRaw;

        $now =
            now()->timestamp;

        abort_if(
            $expires <
                $now,
            410
        );

        abort_if(
            $expires >
                (
                    $now
                    +
                    300
                ),
            401
        );

        $secret =
            trim(
                (string) config(
                    'adn_integration.secret',
                    ''
                )
            );

        abort_if(
            $secret ===
                '',
            503
        );

        $accessKey =
            hash_hmac(
                'sha256',
                'editor-access',
                $secret,
                true
            );

        $payload =
            $requestNumber
            . "\n"
            . $expires
            . "\n"
            . $nonce;

        $expectedSignature =
            hash_hmac(
                'sha256',
                $payload,
                $accessKey
            );

        abort_unless(
            hash_equals(
                $expectedSignature,
                $signature
            ),
            401
        );

        $cacheKey =
            'adn-editor-access:'
            . hash(
                'sha256',
                $nonce
            );

        $remainingSeconds =
            max(
                1,
                $expires
                -
                $now
            );

        $accepted =
            Cache::add(
                $cacheKey,
                true,
                $remainingSeconds
            );

        abort_unless(
            $accepted,
            410
        );

        $quote =
            QuoteRequest::query()
                ->where(
                    'request_number',
                    $requestNumber
                )
                ->firstOrFail();

        $request
            ->session()
            ->regenerate();

        $request
            ->session()
            ->put(
                'adn_editor_access',
                [
                    'source' =>
                        'adn-app',

                    'request_number' =>
                        $requestNumber,

                    'issued_at' =>
                        $now,

                    'expires_at' =>
                        now()
                            ->addHours(
                                8
                            )
                            ->timestamp,
                ]
            );

        $destination =
            app()->environment(
                'local'
            )
                ? '/editor-preview/cotizaciones/'
                    . $quote->id
                : '/cotizaciones/'
                    . $quote->id;

        return redirect(
            $destination
        );
    }
}
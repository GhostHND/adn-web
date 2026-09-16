<?php

namespace App\Http\Middleware;

use App\Models\Setting;
use Illuminate\Http\Request;
use Inertia\Middleware;
use Throwable;

class HandleInertiaRequests extends Middleware
{
    protected $rootView = 'app';

    public function version(
        Request $request
    ): ?string {
        return parent::version(
            $request
        );
    }

    public function share(
        Request $request
    ): array {
        return [
            ...parent::share(
                $request
            ),

            'editorAuthenticated' =>
                (bool) $request
                    ->session()
                    ->get(
                        'editor_authenticated',
                        false
                    ),

            /*
            |--------------------------------------------------------------------------
            | CONTACTO PÚBLICO
            |--------------------------------------------------------------------------
            |
            | Se comparte globalmente para que el layout público pueda construir
            | el botón directo hacia WhatsApp sin hardcodear el número.
            |
            */

            'publicContact' =>
                fn (): array =>
                    $this
                        ->publicContact(),

            'flash' => [
                'success' =>
                    fn () =>
                        $request
                            ->session()
                            ->get(
                                'success'
                            ),

                'error' =>
                    fn () =>
                        $request
                            ->session()
                            ->get(
                                'error'
                            ),
            ],
        ];
    }

    private function publicContact(): array
    {
        try {
            $whatsapp =
                trim(
                    (string) (
                        Setting::query()
                            ->where(
                                'group',
                                'contact'
                            )
                            ->where(
                                'key',
                                'whatsapp'
                            )
                            ->where(
                                'is_public',
                                true
                            )
                            ->value(
                                'value'
                            )
                        ??
                        ''
                    )
                );
        } catch (
            Throwable
        ) {
            $whatsapp =
                '';
        }

        if (
            $whatsapp ===
            ''
        ) {
            return [
                'whatsapp' =>
                    null,

                'whatsapp_url' =>
                    null,
            ];
        }

        /*
        |--------------------------------------------------------------------------
        | NORMALIZAR NÚMERO
        |--------------------------------------------------------------------------
        |
        | wa.me utiliza únicamente dígitos.
        |
        | Ejemplo:
        | +504 9999-9999
        | se convierte en:
        | 50499999999
        |
        | Si se guarda solamente un número hondureño de 8 dígitos,
        | agregamos automáticamente el código de país 504.
        |
        */

        $digits =
            preg_replace(
                '/\D+/',
                '',
                $whatsapp
            )
            ??
            '';

        if (
            strlen(
                $digits
            ) ===
            8
        ) {
            $digits =
                '504'
                .
                $digits;
        }

        if (
            $digits ===
            ''
        ) {
            return [
                'whatsapp' =>
                    $whatsapp,

                'whatsapp_url' =>
                    null,
            ];
        }

        $message =
            'Hola, vengo desde el sitio web de ADN Publicidad y me gustaría recibir información.';

        return [
            'whatsapp' =>
                $whatsapp,

            'whatsapp_url' =>
                'https://wa.me/'
                .
                $digits
                .
                '?text='
                .
                rawurlencode(
                    $message
                ),
        ];
    }
}
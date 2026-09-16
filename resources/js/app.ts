import '../css/adn-brand.css';

import {
    createInertiaApp,
} from '@inertiajs/vue3';

import {
    createApp,
    h,
} from 'vue';

import type {
    DefineComponent,
} from 'vue';

/*
|--------------------------------------------------------------------------
| IDENTIDAD ADN PUBLICIDAD
|--------------------------------------------------------------------------
*/

const appName =
    'ADN Publicidad';

/*
|--------------------------------------------------------------------------
| CARGA AUTOMÁTICA DE PÁGINAS
|--------------------------------------------------------------------------
*/

const pageModules =
    import.meta.glob(
        './pages/**/*.vue',
        {
            eager: true,
            import: 'default',
        },
    ) as Record<
        string,
        DefineComponent
    >;

/*
|--------------------------------------------------------------------------
| ÍNDICE DE PÁGINAS
|--------------------------------------------------------------------------
*/

const pages =
    new Map<
        string,
        DefineComponent
    >();

for (
    const [
        path,
        component,
    ]
    of Object.entries(
        pageModules,
    )
) {
    const normalizedPath =
        path.replace(
            /\\/g,
            '/',
        );

    const marker =
        '/pages/';

    const markerPosition =
        normalizedPath.lastIndexOf(
            marker,
        );

    let pageName =
        markerPosition >= 0
            ? normalizedPath.slice(
                markerPosition
                    +
                    marker.length,
            )
            : normalizedPath;

    pageName =
        pageName
            .replace(
                /^\.\/pages\//,
                '',
            )
            .replace(
                /^\/resources\/js\/pages\//,
                '',
            )
            .replace(
                /\.vue$/,
                '',
            );

    pages.set(
        pageName,
        component,
    );

    pages.set(
        pageName.toLowerCase(),
        component,
    );
}

/*
|--------------------------------------------------------------------------
| DIAGNÓSTICO LOCAL
|--------------------------------------------------------------------------
*/

if (
    import.meta.env.DEV
) {
    console.debug(
        'ADN Web - páginas detectadas:',
        Array.from(
            pages.keys(),
        ),
    );
}

/*
|--------------------------------------------------------------------------
| INERTIA
|--------------------------------------------------------------------------
*/

createInertiaApp({
    title:
        (
            title:
                string,
        ) => {
            return title
                ? `${title} | ${appName}`
                : appName;
        },

    resolve:
        async (
            name:
                string,
        ): Promise<DefineComponent> => {
            const normalizedName =
                name
                    .replace(
                        /\\/g,
                        '/',
                    )
                    .replace(
                        /^\/+/,
                        '',
                    )
                    .replace(
                        /\.vue$/,
                        '',
                    );

            const page =
                pages.get(
                    normalizedName,
                )
                ??
                pages.get(
                    normalizedName
                        .toLowerCase(),
                );

            if (
                !page
            ) {
                if (
                    import.meta.env.DEV
                ) {
                    console.error(
                        'ADN Web - página solicitada:',
                        normalizedName,
                    );

                    console.error(
                        'ADN Web - páginas disponibles:',
                        Array.from(
                            pages.keys(),
                        ),
                    );
                }

                throw new Error(
                    `ADN Web no pudo cargar la página: ${normalizedName}`,
                );
            }

            return page;
        },

    setup({
        el,
        App,
        props,
        plugin,
    }) {
        const application =
            createApp({
                render:
                    () =>
                        h(
                            App,
                            props,
                        ),
            });

        application.use(
            plugin,
        );

        application.mount(
            el,
        );
    },

    progress: {
        color:
            '#0FA7B4',
    },
});
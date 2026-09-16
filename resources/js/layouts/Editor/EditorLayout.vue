<script setup lang="ts">
import {
    Link,
    usePage,
} from '@inertiajs/vue3';

import {
    ChevronLeft,
    ChevronRight,
    CircleHelp,
    ExternalLink,
    FileText,
    Menu,
    MonitorUp,
    X,
} from '@lucide/vue';

import {
    computed,
    onMounted,
    ref,
    watch,
} from 'vue';

import {
    editorNavigation,
} from '@/config/editorNavigation';

interface Props {
    title?: string;
    eyebrow?: string;
}

interface NavigationActivityItem {
    href?: string | null;
    match?: string | null;
}

withDefaults(
    defineProps<Props>(),
    {
        title:
            'Editor ADN Publicidad',

        eyebrow:
            'Administración del sitio',
    },
);

const adnLogoUrl =
    '/brand/adn-logo.svg';

const page =
    usePage();

const sidebarCollapsed =
    ref(false);

const mobileMenuOpen =
    ref(false);

const newQuotesCount =
    ref(0);

const quotesNavigationItem = {
    label:
        'Cotizaciones',

    href:
        '/editor-preview/cotizaciones',

    icon:
        FileText,

    enabled:
        true,

    match:
        'prefix',

    indent:
        false,
};

const readNumericProperty =
    (
        source:
            unknown,
        key:
            string,
    ): number | null => {
        if (
            !source
            ||
            typeof source !==
                'object'
        ) {
            return null;
        }

        const value =
            Reflect.get(
                source,
                key,
            );

        if (
            typeof value ===
                'number'
            &&
            Number.isFinite(
                value,
            )
        ) {
            return Math.max(
                0,
                Math.floor(
                    value,
                ),
            );
        }

        if (
            typeof value ===
                'string'
            &&
            /^\d+$/.test(
                value,
            )
        ) {
            return Math.max(
                0,
                Number.parseInt(
                    value,
                    10,
                ),
            );
        }

        return null;
    };

const readProperty =
    (
        source:
            unknown,
        key:
            string,
    ): unknown => {
        if (
            !source
            ||
            typeof source !==
                'object'
        ) {
            return null;
        }

        return Reflect.get(
            source,
            key,
        );
    };

const authoritativeNewQuotesCount =
    computed<number | null>(
        () => {
            const props =
                page.props;

            /*
            |--------------------------------------------------------------------------
            | Dashboard
            |--------------------------------------------------------------------------
            |
            | DashboardController ya entrega:
            | stats.new_requests
            |
            */

            const stats =
                readProperty(
                    props,
                    'stats',
                );

            const dashboardCount =
                readNumericProperty(
                    stats,
                    'new_requests',
                );

            if (
                dashboardCount !==
                null
            ) {
                return dashboardCount;
            }

            /*
            |--------------------------------------------------------------------------
            | Listado de cotizaciones
            |--------------------------------------------------------------------------
            |
            | QuoteRequestController entrega:
            | summary.new
            |
            */

            const summary =
                readProperty(
                    props,
                    'summary',
                );

            const quotesCount =
                readNumericProperty(
                    summary,
                    'new',
                );

            if (
                quotesCount !==
                null
            ) {
                return quotesCount;
            }

            return null;
        },
    );

const newQuotesBadge =
    computed(
        () => {
            if (
                newQuotesCount.value >
                99
            ) {
                return '99+';
            }

            return String(
                newQuotesCount.value,
            );
        },
    );

const saveNewQuotesCount =
    (
        count:
            number,
    ) => {
        const normalized =
            Math.max(
                0,
                Math.floor(
                    count,
                ),
            );

        newQuotesCount.value =
            normalized;

        if (
            typeof window !==
            'undefined'
        ) {
            localStorage.setItem(
                'adn-editor-new-quotes-count',
                String(
                    normalized,
                ),
            );
        }
    };

watch(
    authoritativeNewQuotesCount,
    (
        count,
    ) => {
        if (
            count ===
            null
        ) {
            return;
        }

        saveNewQuotesCount(
            count,
        );
    },
    {
        immediate:
            true,
    },
);

onMounted(
    () => {
        const storedSidebar =
            localStorage.getItem(
                'adn-editor-sidebar-collapsed',
            );

        sidebarCollapsed.value =
            storedSidebar ===
            'true';

        /*
        |--------------------------------------------------------------------------
        | Recuperar último conteo conocido
        |--------------------------------------------------------------------------
        |
        | Si la página actual no tiene el resumen de cotizaciones,
        | conservamos el último valor conocido mientras navegamos.
        |
        */

        if (
            authoritativeNewQuotesCount.value ===
            null
        ) {
            const storedQuotes =
                localStorage.getItem(
                    'adn-editor-new-quotes-count',
                );

            if (
                storedQuotes
                &&
                /^\d+$/.test(
                    storedQuotes,
                )
            ) {
                newQuotesCount.value =
                    Number.parseInt(
                        storedQuotes,
                        10,
                    );
            }
        }
    },
);

const setSidebarCollapsed =
    (
        value:
            boolean,
    ) => {
        sidebarCollapsed.value =
            value;

        localStorage.setItem(
            'adn-editor-sidebar-collapsed',
            String(
                value,
            ),
        );
    };

const toggleSidebar =
    () => {
        setSidebarCollapsed(
            !sidebarCollapsed.value,
        );
    };

const currentUrl =
    computed(
        () =>
            page.url
                .split(
                    '?',
                )[0]
                .replace(
                    /\/+$/,
                    '',
                )
                || '/',
    );

const normalizeHref =
    (
        href:
            string,
    ) => {
        return href
            .replace(
                /\/+$/,
                '',
            )
            || '/';
    };

const isActive =
    (
        item:
            NavigationActivityItem,
    ) => {
        if (
            !item.href
        ) {
            return false;
        }

        const href =
            normalizeHref(
                item.href,
            );

        if (
            item.match ===
            'prefix'
        ) {
            return (
                currentUrl.value ===
                    href
                ||
                currentUrl.value.startsWith(
                    `${href}/`,
                )
            );
        }

        return currentUrl.value ===
            href;
    };

const closeMobileMenu =
    () => {
        mobileMenuOpen.value =
            false;
    };
</script>

<template>
    <div
        class="adn-brand-background min-h-screen text-white"
    >
        <!-- Firma cromática ADN -->

        <div
            class="adn-brand-stripe fixed inset-x-0 top-0 z-[70] h-[3px]"
        />

        <!-- Overlay móvil -->

        <Transition
            enter-active-class="transition duration-200"
            enter-from-class="opacity-0"
            enter-to-class="opacity-100"
            leave-active-class="transition duration-200"
            leave-from-class="opacity-100"
            leave-to-class="opacity-0"
        >
            <button
                v-if="mobileMenuOpen"
                type="button"
                aria-label="Cerrar menú"
                class="fixed inset-0 z-40 bg-black/75 backdrop-blur-sm lg:hidden"
                @click="closeMobileMenu"
            />
        </Transition>

        <!-- Sidebar -->

        <aside
            class="fixed inset-y-0 left-0 z-50 flex flex-col border-r border-[var(--adn-border)] bg-[rgba(12,17,18,.96)] shadow-2xl shadow-black/25 backdrop-blur-2xl transition-[width,transform] duration-300"
            :class="[
                sidebarCollapsed
                    ? 'w-[88px]'
                    : 'w-[282px]',

                mobileMenuOpen
                    ? 'translate-x-0'
                    : '-translate-x-full lg:translate-x-0',
            ]"
        >
            <!-- Marca -->

            <div
                class="flex min-h-24 shrink-0 items-center border-b border-[var(--adn-border)] px-4"
                :class="
                    sidebarCollapsed
                        ? 'justify-center'
                        : 'justify-between'
                "
            >
                <Link
                    href="/editor-preview"
                    class="min-w-0"
                    @click="closeMobileMenu"
                >
                    <div
                        class="adn-logo-plate flex items-center justify-center rounded-2xl transition duration-300"
                        :class="
                            sidebarCollapsed
                                ? 'h-[52px] w-[58px] p-1.5'
                                : 'h-[62px] w-[190px] px-4 py-2'
                        "
                    >
                        <img
                            :src="adnLogoUrl"
                            alt="ADN Publicidad"
                            class="max-h-full max-w-full object-contain"
                        >
                    </div>
                </Link>

                <button
                    v-if="!sidebarCollapsed"
                    type="button"
                    class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl text-white/30 transition hover:bg-white/[0.05] hover:text-white lg:hidden"
                    @click="closeMobileMenu"
                >
                    <X
                        class="h-5 w-5"
                    />
                </button>
            </div>

            <!-- Navegación -->

            <div
                class="adn-editor-scroll flex-1 overflow-y-auto px-3 py-5"
            >
                <!--
                |--------------------------------------------------------------------------
                | SOLICITUDES
                |--------------------------------------------------------------------------
                -->

                <div
                    class="mb-6"
                >
                    <p
                        v-if="!sidebarCollapsed"
                        class="mb-2 px-3 text-[9px] font-black uppercase tracking-[0.19em] text-white/20"
                    >
                        Solicitudes
                    </p>

                    <div
                        class="space-y-1"
                    >
                        <Link
                            :href="quotesNavigationItem.href"
                            :title="
                                sidebarCollapsed
                                    ? quotesNavigationItem.label
                                    : undefined
                            "
                            class="group relative flex h-11 items-center rounded-xl text-sm font-bold transition duration-200"
                            :class="[
                                sidebarCollapsed
                                    ? 'justify-center px-0'
                                    : 'gap-3 px-3',

                                isActive(
                                    quotesNavigationItem,
                                )
                                    ? 'bg-[var(--adn-turquoise-soft)] text-[var(--adn-turquoise)]'
                                    : 'text-white/43 hover:bg-white/[0.04] hover:text-white/75',
                            ]"
                            @click="closeMobileMenu"
                        >
                            <!-- Línea activa -->

                            <span
                                v-if="
                                    isActive(
                                        quotesNavigationItem,
                                    )
                                "
                                class="absolute left-0 h-5 w-[3px] rounded-r-full bg-[var(--adn-turquoise)]"
                            />

                            <!-- Icono -->

                            <span
                                class="relative flex shrink-0 items-center justify-center"
                            >
                                <FileText
                                    class="h-[18px] w-[18px]"
                                />

                                <!-- Badge colapsado -->

                                <span
                                    v-if="
                                        sidebarCollapsed
                                        &&
                                        newQuotesCount >
                                            0
                                    "
                                    class="absolute -right-3 -top-3 flex h-[18px] min-w-[18px] items-center justify-center rounded-full border-2 border-[#0c1112] bg-[var(--adn-coral)] px-1 text-[7px] font-black leading-none text-white shadow-[0_0_14px_rgba(232,70,87,.42)]"
                                >
                                    {{ newQuotesBadge }}
                                </span>
                            </span>

                            <template
                                v-if="!sidebarCollapsed"
                            >
                                <span
                                    class="truncate"
                                >
                                    Cotizaciones
                                </span>

                                <!-- Badge normal -->

                                <Transition
                                    enter-active-class="transition duration-300"
                                    enter-from-class="scale-75 opacity-0"
                                    enter-to-class="scale-100 opacity-100"
                                    leave-active-class="transition duration-200"
                                    leave-from-class="scale-100 opacity-100"
                                    leave-to-class="scale-75 opacity-0"
                                >
                                    <span
                                        v-if="
                                            newQuotesCount >
                                            0
                                        "
                                        class="ml-auto flex h-6 min-w-6 items-center justify-center rounded-lg border border-[var(--adn-coral-border)] bg-[var(--adn-coral-soft)] px-2 text-[8px] font-black text-[var(--adn-coral)] shadow-[0_0_16px_rgba(232,70,87,.12)]"
                                    >
                                        {{ newQuotesBadge }}
                                    </span>
                                </Transition>
                            </template>
                        </Link>
                    </div>
                </div>

                <!--
                |--------------------------------------------------------------------------
                | NAVEGACIÓN CONFIGURADA
                |--------------------------------------------------------------------------
                -->

                <div
                    v-for="section in editorNavigation"
                    :key="section.label"
                    class="mb-6"
                >
                    <p
                        v-if="!sidebarCollapsed"
                        class="mb-2 px-3 text-[9px] font-black uppercase tracking-[0.19em] text-white/20"
                    >
                        {{ section.label }}
                    </p>

                    <div
                        class="space-y-1"
                    >
                        <template
                            v-for="item in section.items"
                            :key="item.label"
                        >
                            <Link
                                v-if="
                                    item.enabled
                                    &&
                                    item.href
                                "
                                :href="item.href"
                                :title="
                                    sidebarCollapsed
                                        ? item.label
                                        : undefined
                                "
                                class="group relative flex h-11 items-center rounded-xl text-sm font-bold transition duration-200"
                                :class="[
                                    sidebarCollapsed
                                        ? 'justify-center px-0'
                                        : item.indent
                                            ? 'gap-3 pl-8 pr-3'
                                            : 'gap-3 px-3',

                                    isActive(item)
                                        ? 'bg-[var(--adn-turquoise-soft)] text-[var(--adn-turquoise)]'
                                        : item.indent
                                            ? 'text-white/36 hover:bg-white/[0.04] hover:text-white/72'
                                            : 'text-white/43 hover:bg-white/[0.04] hover:text-white/75',
                                ]"
                                @click="closeMobileMenu"
                            >
                                <span
                                    v-if="isActive(item)"
                                    class="absolute left-0 h-5 w-[3px] rounded-r-full bg-[var(--adn-turquoise)]"
                                />

                                <span
                                    v-if="
                                        item.indent
                                        &&
                                        !sidebarCollapsed
                                    "
                                    class="absolute left-[18px] h-1 w-1 rounded-full bg-white/15"
                                />

                                <component
                                    :is="item.icon"
                                    class="h-[18px] w-[18px] shrink-0"
                                    :class="
                                        item.indent
                                            ? 'h-4 w-4'
                                            : ''
                                    "
                                />

                                <span
                                    v-if="!sidebarCollapsed"
                                    class="truncate"
                                >
                                    {{ item.label }}
                                </span>
                            </Link>

                            <button
                                v-else
                                type="button"
                                disabled
                                :title="
                                    sidebarCollapsed
                                        ? `${item.label} · Próximamente`
                                        : undefined
                                "
                                class="relative flex h-11 w-full cursor-default items-center rounded-xl text-sm font-bold text-white/24"
                                :class="
                                    sidebarCollapsed
                                        ? 'justify-center px-0'
                                        : item.indent
                                            ? 'gap-3 pl-8 pr-3'
                                            : 'gap-3 px-3'
                                "
                            >
                                <component
                                    :is="item.icon"
                                    class="h-[18px] w-[18px] shrink-0"
                                />

                                <template
                                    v-if="!sidebarCollapsed"
                                >
                                    <span
                                        class="truncate"
                                    >
                                        {{ item.label }}
                                    </span>

                                    <span
                                        class="ml-auto rounded-md border border-white/[0.05] px-1.5 py-0.5 text-[7px] font-black uppercase tracking-[0.1em] text-white/18"
                                    >
                                        Próx.
                                    </span>
                                </template>
                            </button>
                        </template>
                    </div>
                </div>
            </div>

            <!-- Pie sidebar -->

            <div
                class="border-t border-[var(--adn-border)] p-3"
            >
                <a
                    href="/"
                    target="_blank"
                    rel="noopener noreferrer"
                    class="flex h-11 items-center rounded-xl text-sm font-bold text-white/42 transition hover:bg-white/[0.04] hover:text-white/75"
                    :class="
                        sidebarCollapsed
                            ? 'justify-center'
                            : 'gap-3 px-3'
                    "
                    :title="
                        sidebarCollapsed
                            ? 'Ver sitio web'
                            : undefined
                    "
                >
                    <MonitorUp
                        class="h-[18px] w-[18px]"
                    />

                    <template
                        v-if="!sidebarCollapsed"
                    >
                        <span>
                            Ver sitio web
                        </span>

                        <ExternalLink
                            class="ml-auto h-3.5 w-3.5 text-white/20"
                        />
                    </template>
                </a>

                <button
                    type="button"
                    class="mt-1 hidden h-10 w-full items-center rounded-xl text-xs font-bold text-white/30 transition hover:bg-white/[0.04] hover:text-white/60 lg:flex"
                    :class="
                        sidebarCollapsed
                            ? 'justify-center'
                            : 'gap-3 px-3'
                    "
                    @click="toggleSidebar"
                >
                    <ChevronRight
                        v-if="sidebarCollapsed"
                        class="h-4 w-4"
                    />

                    <ChevronLeft
                        v-else
                        class="h-4 w-4"
                    />

                    <span
                        v-if="!sidebarCollapsed"
                    >
                        Contraer menú
                    </span>
                </button>
            </div>
        </aside>

        <!-- Área principal -->

        <div
            class="relative min-h-screen transition-[padding] duration-300"
            :class="
                sidebarCollapsed
                    ? 'lg:pl-[88px]'
                    : 'lg:pl-[282px]'
            "
        >
            <!-- Barra superior -->

            <header
                class="sticky top-0 z-30 border-b border-[var(--adn-border)] bg-[rgba(9,13,14,.86)] backdrop-blur-2xl"
            >
                <div
                    class="flex min-h-20 items-center justify-between gap-4 px-4 sm:px-6 xl:px-8"
                >
                    <div
                        class="flex min-w-0 items-center gap-3"
                    >
                        <button
                            type="button"
                            class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl border border-[var(--adn-border)] bg-white/[0.025] text-white/50 transition hover:bg-white/[0.05] hover:text-white lg:hidden"
                            @click="
                                mobileMenuOpen =
                                    true
                            "
                        >
                            <Menu
                                class="h-5 w-5"
                            />
                        </button>

                        <div
                            class="min-w-0"
                        >
                            <p
                                class="truncate text-[9px] font-black uppercase tracking-[0.18em] text-[var(--adn-turquoise)]"
                            >
                                {{ eyebrow }}
                            </p>

                            <p
                                class="mt-0.5 truncate text-sm font-black text-white/88 sm:text-base"
                            >
                                {{ title }}
                            </p>
                        </div>
                    </div>

                    <div
                        class="flex shrink-0 items-center gap-2"
                    >
                        <div
                            class="hidden items-center gap-2 rounded-xl border border-[var(--adn-turquoise-border)] bg-[var(--adn-turquoise-soft)] px-3 py-2 sm:flex"
                        >
                            <span
                                class="h-1.5 w-1.5 rounded-full bg-[var(--adn-turquoise)] shadow-[0_0_10px_rgba(15,167,180,.45)]"
                            />

                            <span
                                class="text-[9px] font-black uppercase tracking-[0.13em] text-[var(--adn-turquoise)]"
                            >
                                ADN Web
                            </span>
                        </div>

                        <button
                            type="button"
                            title="Ayuda"
                            class="flex h-10 w-10 items-center justify-center rounded-xl border border-[var(--adn-border)] bg-white/[0.025] text-white/35 transition hover:bg-white/[0.05] hover:text-white"
                        >
                            <CircleHelp
                                class="h-[18px] w-[18px]"
                            />
                        </button>

                        <div
                            class="hidden h-10 items-center overflow-hidden rounded-xl border border-white/[0.08] bg-white px-2 sm:flex"
                        >
                            <img
                                :src="adnLogoUrl"
                                alt="ADN Publicidad"
                                class="h-7 w-auto object-contain"
                            >
                        </div>
                    </div>
                </div>
            </header>

            <!-- Contenido -->

            <main
                class="px-4 py-6 sm:px-6 sm:py-8 xl:px-8"
            >
                <div
                    class="mx-auto w-full max-w-[1500px]"
                >
                    <slot />
                </div>
            </main>
        </div>
    </div>
</template>

<style scoped>
.adn-editor-scroll {
    scrollbar-width:
        thin;

    scrollbar-color:
        rgba(15, 167, 180, 0.22)
        transparent;
}

.adn-editor-scroll::-webkit-scrollbar {
    width:
        5px;
}

.adn-editor-scroll::-webkit-scrollbar-track {
    background:
        transparent;
}

.adn-editor-scroll::-webkit-scrollbar-thumb {
    border-radius:
        999px;

    background:
        rgba(15, 167, 180, 0.22);
}
</style>
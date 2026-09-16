<script setup lang="ts">
import {
    Link,
    usePage,
} from '@inertiajs/vue3';

import {
    Menu,
    X,
} from '@lucide/vue';

import {
    computed,
    ref,
} from 'vue';

interface PublicContact {
    whatsapp: string | null;
    whatsapp_url: string | null;
}

const logoUrl =
    '/brand/adn-logo.svg';

const mobileOpen =
    ref(
        false,
    );

const page =
    usePage();

const publicContact =
    computed(
        (): PublicContact => {
            return (
                page.props.publicContact
                ?? {
                    whatsapp:
                        null,

                    whatsapp_url:
                        null,
                }
            ) as PublicContact;
        },
    );

const whatsappUrl =
    computed(
        (): string | null =>
            publicContact
                .value
                .whatsapp_url
            ?? null,
    );

const currentYear =
    new Date()
        .getFullYear();

const navigation = [
    {
        label:
            'Inicio',

        href:
            '/',
    },

    {
        label:
            'Sobre nosotros',

        href:
            '/sobre-nosotros',
    },

    {
        label:
            'Servicios',

        href:
            '/servicios',
    },

    {
        label:
            'Catálogo',

        href:
            '/catalogo',
    },

    {
        label:
            'Portafolio',

        href:
            '/portafolio',
    },
];
</script>

<template>
    <div
        class="min-h-screen bg-[#f7f8f8] text-[#1D1D1B]"
    >
        <div
            class="adn-brand-stripe fixed inset-x-0 top-0 z-[80] h-[3px]"
        />

        <header
            class="sticky top-0 z-50 border-b border-black/[0.06] bg-white/95 backdrop-blur-xl"
        >
            <div
                class="mx-auto flex h-[78px] max-w-[1500px] items-center justify-between gap-6 px-5 sm:px-7 xl:px-10"
            >
                <Link
                    href="/"
                    class="flex shrink-0 items-center"
                >
                    <img
                        :src="
                            logoUrl
                        "
                        alt="ADN Publicidad"
                        class="h-12 w-auto object-contain"
                    >
                </Link>

                <nav
                    class="hidden items-center gap-1 lg:flex"
                >
                    <Link
                        v-for="
                            item in navigation
                        "
                        :key="
                            item.href
                        "
                        :href="
                            item.href
                        "
                        class="rounded-xl px-4 py-2.5 text-sm font-black text-black/55 transition hover:bg-black/[0.035] hover:text-[#0FA7B4]"
                    >
                        {{
                            item.label
                        }}
                    </Link>

                    <a
                        v-if="
                            whatsappUrl
                        "
                        :href="
                            whatsappUrl
                        "
                        target="_blank"
                        rel="noopener noreferrer"
                        aria-label="Contactar a ADN Publicidad por WhatsApp"
                        title="Contactar por WhatsApp"
                        class="ml-2 inline-flex h-11 items-center justify-center gap-2.5 rounded-xl bg-[#25D366] px-4 text-sm font-black text-white shadow-[0_10px_26px_rgba(37,211,102,.22)] transition duration-300 hover:-translate-y-0.5 hover:bg-[#20bd5a] hover:shadow-[0_14px_34px_rgba(37,211,102,.30)]"
                    >
                        <svg
                            viewBox="0 0 24 24"
                            fill="none"
                            xmlns="http://www.w3.org/2000/svg"
                            class="h-[19px] w-[19px] shrink-0"
                            aria-hidden="true"
                        >
                            <path
                                d="M12 3.25a8.5 8.5 0 0 0-7.35 12.78L3.5 20.5l4.58-1.08A8.5 8.5 0 1 0 12 3.25Z"
                                fill="currentColor"
                                fill-opacity="0.16"
                            />

                            <path
                                d="M12 3.25a8.5 8.5 0 0 0-7.35 12.78L3.5 20.5l4.58-1.08A8.5 8.5 0 1 0 12 3.25Z"
                                stroke="currentColor"
                                stroke-width="1.8"
                                stroke-linecap="round"
                                stroke-linejoin="round"
                            />

                            <path
                                d="M8.1 7.65c.25-.29.55-.25.78-.24.19.01.4.01.58.02.18.01.43-.07.67.5.25.59.84 2.05.91 2.2.08.15.12.32.02.51-.1.19-.15.3-.3.46-.15.17-.32.37-.46.5-.15.15-.3.31-.13.61.17.3.75 1.23 1.61 1.99 1.11.99 2.04 1.3 2.34 1.45.3.15.47.13.65-.08.18-.21.75-.87.95-1.17.2-.3.4-.25.67-.15.28.1 1.74.82 2.04.97.3.15.5.23.58.35.07.12.07.69-.16 1.35-.22.66-1.31 1.27-1.8 1.35-.46.08-1.04.12-1.68-.08-.39-.12-.9-.29-1.54-.57a13.05 13.05 0 0 1-4.75-4.2c-.35-.46-1.43-1.9-1.43-3.63 0-1.73.9-2.58 1.24-2.93Z"
                                fill="currentColor"
                            />
                        </svg>

                        Contacto
                    </a>

                    <span
                        v-else
                        class="ml-2 inline-flex h-11 cursor-not-allowed items-center justify-center gap-2.5 rounded-xl bg-[#25D366]/40 px-4 text-sm font-black text-white/70"
                        title="WhatsApp pendiente de configuración"
                    >
                        Contacto
                    </span>
                </nav>

                <Link
                    href="/catalogo"
                    class="hidden h-11 items-center justify-center rounded-xl bg-[#0FA7B4] px-5 text-xs font-black text-white shadow-[0_12px_30px_rgba(15,167,180,.18)] transition hover:-translate-y-0.5 lg:inline-flex"
                >
                    Cotizar
                </Link>

                <button
                    type="button"
                    class="flex h-11 w-11 items-center justify-center rounded-xl border border-black/[0.08] lg:hidden"
                    aria-label="Abrir menú"
                    @click="
                        mobileOpen =
                            !mobileOpen
                    "
                >
                    <X
                        v-if="
                            mobileOpen
                        "
                        class="h-5 w-5"
                    />

                    <Menu
                        v-else
                        class="h-5 w-5"
                    />
                </button>
            </div>

            <div
                v-if="
                    mobileOpen
                "
                class="border-t border-black/[0.06] bg-white px-5 py-4 lg:hidden"
            >
                <nav
                    class="grid gap-1"
                >
                    <Link
                        v-for="
                            item in navigation
                        "
                        :key="
                            item.href
                        "
                        :href="
                            item.href
                        "
                        class="rounded-xl px-4 py-3 text-sm font-black text-black/55 transition hover:bg-black/[0.035]"
                        @click="
                            mobileOpen =
                                false
                        "
                    >
                        {{
                            item.label
                        }}
                    </Link>

                    <a
                        v-if="
                            whatsappUrl
                        "
                        :href="
                            whatsappUrl
                        "
                        target="_blank"
                        rel="noopener noreferrer"
                        aria-label="Contactar a ADN Publicidad por WhatsApp"
                        class="mt-2 flex min-h-12 items-center justify-center gap-2.5 rounded-xl bg-[#25D366] px-4 text-sm font-black text-white shadow-[0_10px_25px_rgba(37,211,102,.20)] transition active:scale-[0.98]"
                        @click="
                            mobileOpen =
                                false
                        "
                    >
                        <svg
                            viewBox="0 0 24 24"
                            fill="none"
                            xmlns="http://www.w3.org/2000/svg"
                            class="h-5 w-5 shrink-0"
                            aria-hidden="true"
                        >
                            <path
                                d="M12 3.25a8.5 8.5 0 0 0-7.35 12.78L3.5 20.5l4.58-1.08A8.5 8.5 0 1 0 12 3.25Z"
                                fill="currentColor"
                                fill-opacity="0.16"
                            />

                            <path
                                d="M12 3.25a8.5 8.5 0 0 0-7.35 12.78L3.5 20.5l4.58-1.08A8.5 8.5 0 1 0 12 3.25Z"
                                stroke="currentColor"
                                stroke-width="1.8"
                                stroke-linecap="round"
                                stroke-linejoin="round"
                            />

                            <path
                                d="M8.1 7.65c.25-.29.55-.25.78-.24.19.01.4.01.58.02.18.01.43-.07.67.5.25.59.84 2.05.91 2.2.08.15.12.32.02.51-.1.19-.15.3-.3.46-.15.17-.32.37-.46.5-.15.15-.3.31-.13.61.17.3.75 1.23 1.61 1.99 1.11.99 2.04 1.3 2.34 1.45.3.15.47.13.65-.08.18-.21.75-.87.95-1.17.2-.3.4-.25.67-.15.28.1 1.74.82 2.04.97.3.15.5.23.58.35.07.12.07.69-.16 1.35-.22.66-1.31 1.27-1.8 1.35-.46.08-1.04.12-1.68-.08-.39-.12-.9-.29-1.54-.57a13.05 13.05 0 0 1-4.75-4.2c-.35-.46-1.43-1.9-1.43-3.63 0-1.73.9-2.58 1.24-2.93Z"
                                fill="currentColor"
                            />
                        </svg>

                        Contacto por WhatsApp
                    </a>

                    <span
                        v-else
                        class="mt-2 flex min-h-12 cursor-not-allowed items-center justify-center gap-2.5 rounded-xl bg-[#25D366]/40 px-4 text-sm font-black text-white/70"
                    >
                        Contacto por WhatsApp
                    </span>
                </nav>
            </div>
        </header>

        <main>
            <slot />
        </main>

        <footer
            class="mt-20 bg-[#101516] text-white"
        >
            <div
                class="mx-auto grid max-w-[1500px] gap-10 px-5 py-12 sm:px-7 lg:grid-cols-[1.4fr_1fr_1fr] xl:px-10"
            >
                <div>
                    <div
                        class="inline-flex rounded-2xl bg-white px-5 py-3"
                    >
                        <img
                            :src="
                                logoUrl
                            "
                            alt="ADN Publicidad"
                            class="h-10 w-auto object-contain"
                        >
                    </div>

                    <p
                        class="mt-5 max-w-md text-sm leading-6 text-white/45"
                    >
                        Soluciones publicitarias, impresión y producción
                        visual para marcas, empresas y emprendimientos.
                    </p>
                </div>

                <div>
                    <p
                        class="text-xs font-black uppercase tracking-[0.14em] text-[#0FA7B4]"
                    >
                        Navegación
                    </p>

                    <div
                        class="mt-4 grid gap-2"
                    >
                        <Link
                            href="/catalogo"
                            class="text-sm font-bold text-white/55 hover:text-white"
                        >
                            Catálogo
                        </Link>

                        <Link
                            href="/servicios"
                            class="text-sm font-bold text-white/55 hover:text-white"
                        >
                            Servicios
                        </Link>

                        <Link
                            href="/portafolio"
                            class="text-sm font-bold text-white/55 hover:text-white"
                        >
                            Portafolio
                        </Link>

                        <a
                            v-if="
                                whatsappUrl
                            "
                            :href="
                                whatsappUrl
                            "
                            target="_blank"
                            rel="noopener noreferrer"
                            class="inline-flex items-center gap-2 text-sm font-bold text-[#25D366] transition hover:text-[#48df7c]"
                        >
                            Contacto por WhatsApp
                        </a>
                    </div>
                </div>

                <div>
                    <p
                        class="text-xs font-black uppercase tracking-[0.14em] text-[#E84657]"
                    >
                        ADN Publicidad
                    </p>

                    <p
                        class="mt-4 text-sm leading-6 text-white/45"
                    >
                        Diseño, publicidad, impresión y soluciones
                        visuales para hacer crecer tu marca.
                    </p>
                </div>
            </div>

            <!-- COPYRIGHT -->

            <div
                class="border-t border-white/[0.07]"
            >
                <div
                    class="mx-auto flex max-w-[1500px] flex-col items-center justify-between gap-2 px-5 py-5 text-center sm:flex-row sm:px-7 sm:text-left xl:px-10"
                >
                    <p
                        class="text-[11px] font-medium text-white/30"
                    >
                        © {{ currentYear }} ADN Publicidad. Todos los derechos reservados.
                    </p>

                    <p
                        class="text-[10px] font-bold uppercase tracking-[0.12em] text-white/20"
                    >
                        Diseño · Publicidad · Tecnología
                    </p>
                </div>
            </div>
        </footer>
    </div>
</template>
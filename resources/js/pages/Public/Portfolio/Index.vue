<script setup lang="ts">
import {
    Head,
    Link,
} from '@inertiajs/vue3';

import {
    ArrowRight,
    BriefcaseBusiness,
    CalendarDays,
    Images,
    Layers3,
    Sparkles,
} from '@lucide/vue';

import {
    computed,
} from 'vue';

import PromoSlot
    from '@/components/Public/Promotions/PromoSlot.vue';

import PublicLayout
    from '@/layouts/Public/PublicLayout.vue';

interface Media {
    id: number;
    title: string | null;
    alt_text: string | null;
    width: number | null;
    height: number | null;
    url: string;
}

interface Section {
    id: number;
    section_key: string;
    section_type: string;
    title: string | null;
    subtitle: string | null;
    body: string | null;
    settings: Record<string, unknown>;
    media: Media | null;
    support_media: Media[];
}

interface PageData {
    title: string;
    eyebrow: string | null;
    summary: string | null;
    meta_title: string | null;
    meta_description: string | null;
    sections: Section[];
}

interface Category {
    id: number;
    slug: string;
    name: string;
    description: string | null;
    projects_count: number;
}

interface Project {
    id: number;
    slug: string;
    title: string;
    client_name: string | null;
    excerpt: string | null;
    project_date: string | null;
    featured: boolean;

    category: {
        id: number;
        name: string;
        slug: string;
    } | null;

    main_media: Media | null;
}

interface Promotion {
    id: number;
    [key: string]: unknown;
}

const props =
    defineProps<{
        page: PageData;
        categories: Category[];
        projects: Project[];

        filters: {
            category: string | null;
        };

        promotions: Promotion[];
    }>();

const hero =
    computed(
        (): Section | null =>
            props.page.sections.find(
                (
                    section,
                ) =>
                    section.section_key ===
                    'hero',
            )
            ?? null,
    );

const cta =
    computed(
        (): Section | null =>
            props.page.sections.find(
                (
                    section,
                ) =>
                    section.section_key ===
                    'cta',
            )
            ?? null,
    );

const categoryHref =
    (
        slug:
            string | null,
    ): string => {
        if (
            slug ===
            null
        ) {
            return '/portafolio';
        }

        return `/portafolio?categoria=${encodeURIComponent(slug)}`;
    };

const formatDate =
    (
        value:
            string | null,
    ): string | null => {
        if (
            !value
        ) {
            return null;
        }

        const date =
            new Date(
                `${value}T12:00:00`,
            );

        return new Intl.DateTimeFormat(
            'es-HN',
            {
                month:
                    'long',

                year:
                    'numeric',
            },
        ).format(
            date,
        );
    };
</script>

<template>
    <Head
        :title="
            page.meta_title
            ?? 'Portafolio | ADN Publicidad'
        "
    >
        <meta
            v-if="page.meta_description"
            name="description"
            :content="page.meta_description"
        >
    </Head>

    <PublicLayout>
        <main class="overflow-hidden bg-[#f5f6f7] text-[#1D1D1B]">
            <!-- HERO -->

            <section
                class="relative overflow-hidden bg-[#111718]"
            >
                <div
                    class="pointer-events-none absolute -right-36 -top-40 h-[420px] w-[420px] rounded-full bg-[#0FA7B4]/15 blur-3xl"
                />

                <div
                    class="pointer-events-none absolute -bottom-48 left-1/4 h-[360px] w-[360px] rounded-full bg-[#E84657]/10 blur-3xl"
                />

                <div
                    class="relative mx-auto grid min-h-[500px] max-w-7xl items-center gap-10 px-5 py-16 sm:px-8 lg:grid-cols-[1.05fr_.95fr] lg:px-10 lg:py-20"
                >
                    <div class="max-w-3xl">
                        <div
                            class="inline-flex items-center gap-2 rounded-full border border-white/10 bg-white/[0.04] px-4 py-2 text-[10px] font-black uppercase tracking-[0.16em] text-[#70D7DF]"
                        >
                            <Sparkles class="h-3.5 w-3.5" />

                            {{
                                hero?.subtitle
                                ?? page.eyebrow
                                ?? 'Nuestro trabajo'
                            }}
                        </div>

                        <h1
                            class="mt-6 max-w-4xl text-4xl font-black leading-[0.98] tracking-[-0.055em] text-white sm:text-5xl lg:text-7xl"
                        >
                            {{
                                hero?.title
                                ?? 'Ideas que se convierten en resultados visibles.'
                            }}
                        </h1>

                        <p
                            class="mt-6 max-w-2xl text-sm leading-7 text-white/50 sm:text-base"
                        >
                            {{
                                hero?.body
                                ?? 'Una selección de proyectos donde diseño, producción y tecnología toman forma.'
                            }}
                        </p>

                        <div
                            class="mt-8 flex flex-wrap items-center gap-3"
                        >
                            <a
                                href="#proyectos"
                                class="inline-flex h-12 items-center gap-2 rounded-xl bg-[#0FA7B4] px-5 text-sm font-black text-white shadow-[0_15px_40px_rgba(15,167,180,.18)] transition hover:-translate-y-0.5"
                            >
                                Explorar proyectos

                                <ArrowRight class="h-4 w-4" />
                            </a>

                            <Link
                                href="/catalogo"
                                class="inline-flex h-12 items-center rounded-xl border border-white/10 px-5 text-sm font-black text-white/70 transition hover:bg-white/[0.05] hover:text-white"
                            >
                                Ver catálogo
                            </Link>
                        </div>
                    </div>

                    <div
                        class="relative hidden min-h-[360px] lg:block"
                    >
                        <div
                            v-if="hero?.media"
                            class="absolute inset-0 overflow-hidden rounded-[34px] border border-white/10 bg-white/[0.03] shadow-2xl"
                        >
                            <img
                                :src="hero.media.url"
                                :alt="
                                    hero.media.alt_text
                                    ?? hero.title
                                    ?? 'ADN Publicidad'
                                "
                                class="h-full w-full object-cover"
                            >

                            <div
                                class="absolute inset-0 bg-gradient-to-t from-black/60 via-black/5 to-transparent"
                            />
                        </div>

                        <div
                            v-else
                            class="absolute inset-0 overflow-hidden rounded-[34px] border border-white/10 bg-gradient-to-br from-[#0FA7B4]/25 via-white/[0.03] to-[#ED7E24]/15"
                        >
                            <div
                                class="absolute left-8 top-8 flex h-16 w-16 items-center justify-center rounded-2xl bg-white/10 text-white"
                            >
                                <BriefcaseBusiness class="h-8 w-8" />
                            </div>

                            <div
                                class="absolute bottom-8 left-8 right-8"
                            >
                                <p
                                    class="text-xs font-black uppercase tracking-[0.15em] text-[#70D7DF]"
                                >
                                    ADN Publicidad
                                </p>

                                <p
                                    class="mt-3 max-w-sm text-2xl font-black tracking-[-0.04em] text-white"
                                >
                                    Creamos para que las marcas se hagan notar.
                                </p>
                            </div>
                        </div>

                        <div
                            v-if="hero?.support_media?.[0]"
                            class="absolute -bottom-7 -left-7 h-36 w-44 overflow-hidden rounded-[24px] border-4 border-[#111718] bg-white shadow-xl"
                        >
                            <img
                                :src="hero.support_media[0].url"
                                :alt="
                                    hero.support_media[0].alt_text
                                    ?? ''
                                "
                                class="h-full w-full object-cover"
                            >
                        </div>

                        <div
                            class="absolute -right-5 -top-5 flex h-28 w-28 items-center justify-center rounded-[28px] bg-[#F5C000] shadow-xl"
                        >
                            <Images class="h-10 w-10 text-[#1D1D1B]" />
                        </div>
                    </div>
                </div>
            </section>

            <!-- INTRO / FILTROS -->

            <section
                id="proyectos"
                class="mx-auto max-w-7xl px-5 py-16 sm:px-8 lg:px-10 lg:py-20"
            >
                <div
                    class="flex flex-col justify-between gap-7 lg:flex-row lg:items-end"
                >
                    <div class="max-w-2xl">
                        <p
                            class="text-[10px] font-black uppercase tracking-[0.16em] text-[#E84657]"
                        >
                            Portafolio
                        </p>

                        <h2
                            class="mt-3 text-3xl font-black tracking-[-0.045em] sm:text-4xl"
                        >
                            Trabajo que se puede ver.
                        </h2>

                        <p
                            class="mt-4 text-sm leading-7 text-black/50"
                        >
                            Navega por categorías y conoce proyectos realizados por ADN Publicidad.
                        </p>
                    </div>

                    <div
                        class="inline-flex items-center gap-2 rounded-2xl border border-black/[0.06] bg-white px-4 py-3 shadow-sm"
                    >
                        <Layers3 class="h-4 w-4 text-[#0FA7B4]" />

                        <span
                            class="text-xs font-black text-black/50"
                        >
                            {{ projects.length }}
                            proyectos visibles
                        </span>
                    </div>
                </div>

                <div
                    class="mt-8 flex gap-2 overflow-x-auto pb-2"
                >
                    <Link
                        :href="categoryHref(null)"
                        preserve-scroll
                        class="shrink-0 rounded-full border px-4 py-2.5 text-[11px] font-black transition"
                        :class="
                            filters.category === null
                                ? 'border-[#0FA7B4] bg-[#0FA7B4] text-white'
                                : 'border-black/[0.07] bg-white text-black/45 hover:border-[#0FA7B4]/30 hover:text-[#0FA7B4]'
                        "
                    >
                        Todos
                    </Link>

                    <Link
                        v-for="category in categories"
                        :key="category.id"
                        :href="categoryHref(category.slug)"
                        preserve-scroll
                        class="shrink-0 rounded-full border px-4 py-2.5 text-[11px] font-black transition"
                        :class="
                            filters.category === category.slug
                                ? 'border-[#0FA7B4] bg-[#0FA7B4] text-white'
                                : 'border-black/[0.07] bg-white text-black/45 hover:border-[#0FA7B4]/30 hover:text-[#0FA7B4]'
                        "
                    >
                        {{ category.name }}

                        <span
                            class="ml-1 opacity-50"
                        >
                            {{ category.projects_count }}
                        </span>
                    </Link>
                </div>

                <!-- PROYECTOS -->

                <div
                    v-if="projects.length"
                    class="mt-10 grid gap-5 md:grid-cols-2 xl:grid-cols-3"
                >
                    <Link
                        v-for="(project, index) in projects"
                        :key="project.id"
                        :href="`/portafolio/${project.slug}`"
                        class="group relative overflow-hidden rounded-[26px] border border-black/[0.06] bg-white shadow-[0_12px_35px_rgba(0,0,0,.04)] transition duration-300 hover:-translate-y-1 hover:shadow-[0_20px_50px_rgba(0,0,0,.08)]"
                        :class="
                            project.featured
                            && index === 0
                                ? 'md:col-span-2 xl:col-span-2'
                                : ''
                        "
                    >
                        <div
                            class="relative overflow-hidden bg-[#e9edef]"
                            :class="
                                project.featured
                                && index === 0
                                    ? 'aspect-[16/8]'
                                    : 'aspect-[4/3]'
                            "
                        >
                            <img
                                v-if="project.main_media"
                                :src="project.main_media.url"
                                :alt="
                                    project.main_media.alt_text
                                    ?? project.title
                                "
                                class="h-full w-full object-cover transition duration-700 group-hover:scale-[1.035]"
                            >

                            <div
                                class="absolute inset-0 bg-gradient-to-t from-black/65 via-black/5 to-transparent"
                            />

                            <span
                                v-if="project.featured"
                                class="absolute left-4 top-4 inline-flex items-center gap-1.5 rounded-full bg-[#F5C000] px-3 py-1.5 text-[9px] font-black uppercase tracking-[0.08em] text-[#1D1D1B]"
                            >
                                <Sparkles class="h-3 w-3" />
                                Destacado
                            </span>

                            <div
                                class="absolute bottom-0 left-0 right-0 p-5 sm:p-6"
                            >
                                <p
                                    class="text-[9px] font-black uppercase tracking-[0.15em] text-[#78D9DF]"
                                >
                                    {{
                                        project.category?.name
                                        ?? 'Proyecto'
                                    }}
                                </p>

                                <h3
                                    class="mt-2 max-w-2xl text-xl font-black tracking-[-0.035em] text-white sm:text-2xl"
                                    :class="
                                        project.featured
                                        && index === 0
                                            ? 'lg:text-4xl'
                                            : ''
                                    "
                                >
                                    {{ project.title }}
                                </h3>
                            </div>
                        </div>

                        <div class="p-5 sm:p-6">
                            <div
                                class="flex min-h-14 items-start justify-between gap-5"
                            >
                                <div>
                                    <p
                                        v-if="project.excerpt"
                                        class="line-clamp-2 text-xs leading-6 text-black/45"
                                    >
                                        {{ project.excerpt }}
                                    </p>

                                    <p
                                        v-else
                                        class="text-xs leading-6 text-black/35"
                                    >
                                        Conoce más sobre este proyecto.
                                    </p>
                                </div>

                                <span
                                    class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-[#0FA7B4]/10 text-[#0FA7B4] transition group-hover:bg-[#0FA7B4] group-hover:text-white"
                                >
                                    <ArrowRight class="h-4 w-4" />
                                </span>
                            </div>

                            <div
                                class="mt-5 flex flex-wrap gap-x-5 gap-y-2 border-t border-black/[0.06] pt-4 text-[10px] font-bold text-black/30"
                            >
                                <span
                                    v-if="formatDate(project.project_date)"
                                    class="inline-flex items-center gap-1.5"
                                >
                                    <CalendarDays class="h-3.5 w-3.5" />

                                    {{
                                        formatDate(
                                            project.project_date,
                                        )
                                    }}
                                </span>

                                <span
                                    v-if="project.client_name"
                                >
                                    {{ project.client_name }}
                                </span>
                            </div>
                        </div>
                    </Link>
                </div>

                <div
                    v-else
                    class="mt-10 flex min-h-80 flex-col items-center justify-center rounded-[28px] border border-dashed border-black/10 bg-white px-6 text-center"
                >
                    <BriefcaseBusiness class="h-10 w-10 text-black/15" />

                    <h3
                        class="mt-5 text-xl font-black tracking-[-0.03em]"
                    >
                        Todavía no hay proyectos aquí.
                    </h3>

                    <p
                        class="mt-2 max-w-md text-sm leading-7 text-black/40"
                    >
                        Esta categoría todavía no tiene proyectos publicados.
                    </p>

                    <Link
                        v-if="filters.category"
                        href="/portafolio"
                        class="mt-5 text-sm font-black text-[#0FA7B4]"
                    >
                        Ver todos los proyectos
                    </Link>
                </div>
            </section>

            <!-- PUBLICIDAD -->

            <section
                v-if="promotions.length"
                class="mx-auto max-w-7xl px-5 pb-16 sm:px-8 lg:px-10 lg:pb-20"
            >
                <PromoSlot
                    :ads="promotions"
                    aspect-class="aspect-[16/5]"
                    :interval-ms="8000"
                />
            </section>

            <!-- CTA -->

            <section
                class="px-5 pb-16 sm:px-8 lg:px-10 lg:pb-24"
            >
                <div
                    class="relative mx-auto max-w-7xl overflow-hidden rounded-[32px] bg-[#111718] px-6 py-12 sm:px-10 lg:px-14 lg:py-16"
                >
                    <div
                        class="absolute -right-20 -top-28 h-72 w-72 rounded-full bg-[#0FA7B4]/20 blur-3xl"
                    />

                    <div
                        class="relative flex flex-col justify-between gap-8 lg:flex-row lg:items-end"
                    >
                        <div class="max-w-3xl">
                            <p
                                class="text-[10px] font-black uppercase tracking-[0.16em] text-[#ED7E24]"
                            >
                                {{
                                    cta?.subtitle
                                    ?? 'Tu proyecto puede ser el siguiente'
                                }}
                            </p>

                            <h2
                                class="mt-3 text-3xl font-black tracking-[-0.045em] text-white sm:text-4xl"
                            >
                                {{
                                    cta?.title
                                    ?? '¿Tienes una idea que quieres hacer realidad?'
                                }}
                            </h2>

                            <p
                                class="mt-4 max-w-2xl text-sm leading-7 text-white/45"
                            >
                                {{
                                    cta?.body
                                    ?? 'Explora nuestras soluciones y prepara tu próxima cotización.'
                                }}
                            </p>
                        </div>

                        <Link
                            :href="
                                typeof cta?.settings?.cta_url === 'string'
                                    ? cta.settings.cta_url
                                    : '/catalogo'
                            "
                            class="inline-flex h-12 shrink-0 items-center justify-center gap-2 rounded-xl bg-[#0FA7B4] px-5 text-sm font-black text-white"
                        >
                            {{
                                typeof cta?.settings?.cta_label === 'string'
                                    ? cta.settings.cta_label
                                    : 'Explorar catálogo'
                            }}

                            <ArrowRight class="h-4 w-4" />
                        </Link>
                    </div>
                </div>
            </section>
        </main>
    </PublicLayout>
</template>
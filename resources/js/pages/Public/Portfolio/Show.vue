<script setup lang="ts">
import {
    Head,
    Link,
} from '@inertiajs/vue3';

import {
    ArrowLeft,
    ArrowRight,
    BriefcaseBusiness,
    CalendarDays,
    CheckCircle2,
    ImageIcon,
    Images,
    Layers3,
    Sparkles,
    UserRound,
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

interface ProjectGalleryImage {
    id: number;
    alt_text: string | null;
    sort_order: number;
    media: Media | null;
}

type GalleryItem =
    Media
    |
    ProjectGalleryImage;

interface Category {
    id: number;
    name: string;
    slug: string;
}

interface Project {
    id: number;
    slug: string;
    title: string;
    client_name: string | null;
    excerpt: string | null;
    description: string | null;
    project_date: string | null;
    featured: boolean;

    meta_title?: string | null;
    meta_description?: string | null;

    category:
        Category
        | null;

    main_media:
        Media
        | null;

    images?:
        GalleryItem[];

    gallery?:
        Media[];
}

interface Promotion {
    id: number;
    [key: string]: unknown;
}

const props =
    withDefaults(
        defineProps<{
            project?: Project | null;

            relatedProjects?: Project[];
            related_projects?: Project[];

            promotions?: Promotion[];
        }>(),
        {
            project:
                null,

            relatedProjects:
                () =>
                    [],

            related_projects:
                () =>
                    [],

            promotions:
                () =>
                    [],
        },
    );

const isMedia =
    (
        item:
            GalleryItem,
    ): item is Media => {
        return (
            'url' in
            item
        );
    };

const resolveGalleryMedia =
    (
        item:
            GalleryItem,
    ): Media | null => {
        if (
            isMedia(
                item,
            )
        ) {
            return item;
        }

        return item.media
            ?? null;
    };

const galleryMedia =
    computed(
        (): Media[] => {
            const media:
                Media[] =
                [];

            if (
                props.project
                    ?.gallery
            ) {
                media.push(
                    ...props
                        .project
                        .gallery,
                );
            }

            if (
                props.project
                    ?.images
            ) {
                for (
                    const item
                    of
                    props.project
                        .images
                ) {
                    const resolved =
                        resolveGalleryMedia(
                            item,
                        );

                    if (
                        resolved
                    ) {
                        media.push(
                            resolved,
                        );
                    }
                }
            }

            const unique =
                new Map<
                    number,
                    Media
                >();

            for (
                const item
                of
                media
            ) {
                if (
                    props.project
                        ?.main_media
                        ?.id ===
                    item.id
                ) {
                    continue;
                }

                unique.set(
                    item.id,
                    item,
                );
            }

            return Array.from(
                unique.values(),
            );
        },
    );

const related =
    computed(
        (): Project[] => {
            if (
                props
                    .relatedProjects
                    .length >
                0
            ) {
                return props
                    .relatedProjects;
            }

            return props
                .related_projects;
        },
    );

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

        if (
            Number.isNaN(
                date.getTime(),
            )
        ) {
            return value;
        }

        return new Intl.DateTimeFormat(
            'es-HN',
            {
                day:
                    '2-digit',

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
            project?.meta_title
            ?? (
                project
                    ? `${project.title} | ADN Publicidad`
                    : 'Proyecto | ADN Publicidad'
            )
        "
    >
        <meta
            v-if="
                project?.meta_description
                ?? project?.excerpt
            "
            name="description"
            :content="
                project?.meta_description
                ?? project?.excerpt
                ?? ''
            "
        >
    </Head>

    <PublicLayout>
        <main
            v-if="project"
            class="overflow-hidden bg-[#f5f6f7] text-[#1D1D1B]"
        >
            <!-- HERO DEL PROYECTO -->

            <section
                class="relative overflow-hidden bg-[#101617]"
            >
                <div
                    class="pointer-events-none absolute -right-44 -top-52 h-[520px] w-[520px] rounded-full bg-[#0FA7B4]/15 blur-3xl"
                />

                <div
                    class="pointer-events-none absolute -bottom-52 left-[20%] h-[420px] w-[420px] rounded-full bg-[#E84657]/10 blur-3xl"
                />

                <div
                    class="relative mx-auto max-w-7xl px-5 py-12 sm:px-8 lg:px-10 lg:py-16"
                >
                    <Link
                        href="/portafolio"
                        class="inline-flex items-center gap-2 text-[10px] font-black uppercase tracking-[0.14em] text-white/40 transition hover:text-[#70D7DF]"
                    >
                        <ArrowLeft
                            class="h-4 w-4"
                        />

                        Volver al portafolio
                    </Link>

                    <div
                        class="mt-8 grid items-center gap-10 lg:grid-cols-[.8fr_1.2fr]"
                    >
                        <div>
                            <div
                                class="flex flex-wrap items-center gap-2"
                            >
                                <span
                                    v-if="
                                        project.category
                                    "
                                    class="inline-flex items-center gap-2 rounded-full border border-[#0FA7B4]/25 bg-[#0FA7B4]/10 px-4 py-2 text-[9px] font-black uppercase tracking-[0.14em] text-[#70D7DF]"
                                >
                                    <Layers3
                                        class="h-3.5 w-3.5"
                                    />

                                    {{
                                        project
                                            .category
                                            .name
                                    }}
                                </span>

                                <span
                                    v-if="
                                        project.featured
                                    "
                                    class="inline-flex items-center gap-2 rounded-full border border-[#F5C000]/20 bg-[#F5C000]/10 px-4 py-2 text-[9px] font-black uppercase tracking-[0.14em] text-[#F5C000]"
                                >
                                    <Sparkles
                                        class="h-3.5 w-3.5"
                                    />

                                    Proyecto destacado
                                </span>
                            </div>

                            <h1
                                class="mt-6 text-4xl font-black leading-[.98] tracking-[-0.055em] text-white sm:text-5xl lg:text-6xl"
                            >
                                {{
                                    project.title
                                }}
                            </h1>

                            <p
                                v-if="
                                    project.excerpt
                                "
                                class="mt-6 max-w-xl text-sm leading-7 text-white/50 sm:text-base"
                            >
                                {{
                                    project.excerpt
                                }}
                            </p>

                            <div
                                class="mt-8 flex flex-wrap gap-3"
                            >
                                <div
                                    v-if="
                                        project.project_date
                                    "
                                    class="flex items-center gap-2 rounded-xl border border-white/[0.08] bg-white/[0.035] px-4 py-3 text-xs font-bold text-white/55"
                                >
                                    <CalendarDays
                                        class="h-4 w-4 text-[#0FA7B4]"
                                    />

                                    {{
                                        formatDate(
                                            project.project_date,
                                        )
                                    }}
                                </div>

                                <div
                                    v-if="
                                        project.client_name
                                    "
                                    class="flex items-center gap-2 rounded-xl border border-white/[0.08] bg-white/[0.035] px-4 py-3 text-xs font-bold text-white/55"
                                >
                                    <UserRound
                                        class="h-4 w-4 text-[#ED7E24]"
                                    />

                                    {{
                                        project.client_name
                                    }}
                                </div>
                            </div>
                        </div>

                        <div
                            class="relative"
                        >
                            <div
                                class="pointer-events-none absolute -inset-5 rounded-[34px] bg-gradient-to-br from-[#0FA7B4]/15 via-transparent to-[#ED7E24]/10 blur-2xl"
                            />

                            <div
                                class="relative overflow-hidden rounded-[28px] border border-white/10 bg-white/[0.035] shadow-2xl"
                            >
                                <img
                                    v-if="
                                        project.main_media
                                    "
                                    :src="
                                        project
                                            .main_media
                                            .url
                                    "
                                    :alt="
                                        project
                                            .main_media
                                            .alt_text
                                        ?? project.title
                                    "
                                    class="aspect-[16/10] w-full object-cover"
                                >

                                <div
                                    v-else
                                    class="flex aspect-[16/10] items-center justify-center"
                                >
                                    <ImageIcon
                                        class="h-14 w-14 text-white/10"
                                    />
                                </div>

                                <div
                                    class="absolute inset-x-0 bottom-0 h-36 bg-gradient-to-t from-black/70 to-transparent"
                                />

                                <div
                                    class="absolute bottom-5 left-5 right-5 flex items-end justify-between gap-4"
                                >
                                    <div>
                                        <p
                                            class="text-[9px] font-black uppercase tracking-[0.14em] text-[#70D7DF]"
                                        >
                                            ADN Publicidad
                                        </p>

                                        <p
                                            class="mt-1 text-sm font-black text-white sm:text-base"
                                        >
                                            Trabajo realizado
                                        </p>
                                    </div>

                                    <BriefcaseBusiness
                                        class="h-5 w-5 text-white/60"
                                    />
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <!-- INFORMACIÓN DEL PROYECTO -->

            <section
                class="mx-auto max-w-7xl px-5 py-14 sm:px-8 lg:px-10 lg:py-20"
            >
                <div
                    class="grid gap-10 lg:grid-cols-[.68fr_1.32fr]"
                >
                    <aside>
                        <div
                            class="sticky top-28 rounded-[24px] border border-black/[0.07] bg-white p-6 shadow-[0_16px_45px_rgba(22,34,35,.06)]"
                        >
                            <p
                                class="text-[9px] font-black uppercase tracking-[0.15em] text-[#0FA7B4]"
                            >
                                El proyecto
                            </p>

                            <h2
                                class="mt-3 text-2xl font-black tracking-[-0.04em] text-[#1D1D1B]"
                            >
                                Detalles del trabajo
                            </h2>

                            <div
                                class="mt-6 space-y-4"
                            >
                                <div
                                    v-if="
                                        project.category
                                    "
                                    class="flex items-start gap-3 border-b border-black/[0.06] pb-4"
                                >
                                    <span
                                        class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl bg-[#0FA7B4]/10 text-[#0FA7B4]"
                                    >
                                        <Layers3
                                            class="h-4 w-4"
                                        />
                                    </span>

                                    <div>
                                        <p
                                            class="text-[9px] font-black uppercase tracking-[0.1em] text-black/30"
                                        >
                                            Categoría
                                        </p>

                                        <p
                                            class="mt-1 text-sm font-black"
                                        >
                                            {{
                                                project
                                                    .category
                                                    .name
                                            }}
                                        </p>
                                    </div>
                                </div>

                                <div
                                    v-if="
                                        project.client_name
                                    "
                                    class="flex items-start gap-3 border-b border-black/[0.06] pb-4"
                                >
                                    <span
                                        class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl bg-[#ED7E24]/10 text-[#ED7E24]"
                                    >
                                        <UserRound
                                            class="h-4 w-4"
                                        />
                                    </span>

                                    <div>
                                        <p
                                            class="text-[9px] font-black uppercase tracking-[0.1em] text-black/30"
                                        >
                                            Cliente
                                        </p>

                                        <p
                                            class="mt-1 text-sm font-black"
                                        >
                                            {{
                                                project.client_name
                                            }}
                                        </p>
                                    </div>
                                </div>

                                <div
                                    v-if="
                                        project.project_date
                                    "
                                    class="flex items-start gap-3"
                                >
                                    <span
                                        class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl bg-[#E84657]/10 text-[#E84657]"
                                    >
                                        <CalendarDays
                                            class="h-4 w-4"
                                        />
                                    </span>

                                    <div>
                                        <p
                                            class="text-[9px] font-black uppercase tracking-[0.1em] text-black/30"
                                        >
                                            Fecha
                                        </p>

                                        <p
                                            class="mt-1 text-sm font-black"
                                        >
                                            {{
                                                formatDate(
                                                    project.project_date,
                                                )
                                            }}
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </aside>

                    <div>
                        <p
                            class="text-[9px] font-black uppercase tracking-[0.15em] text-[#ED7E24]"
                        >
                            Trabajo realizado
                        </p>

                        <h2
                            class="mt-3 max-w-3xl text-3xl font-black tracking-[-0.045em] sm:text-4xl"
                        >
                            Una solución construida para destacar.
                        </h2>

                        <div
                            v-if="
                                project.description
                            "
                            class="mt-7 whitespace-pre-line text-sm leading-8 text-black/55 sm:text-base"
                        >
                            {{
                                project.description
                            }}
                        </div>

                        <p
                            v-else
                            class="mt-7 text-sm leading-8 text-black/45"
                        >
                            Este proyecto forma parte de una selección de trabajos realizados por ADN Publicidad.
                        </p>

                        <div
                            class="mt-8 grid gap-3 sm:grid-cols-2"
                        >
                            <div
                                class="flex items-center gap-3 rounded-2xl border border-black/[0.06] bg-white p-4"
                            >
                                <CheckCircle2
                                    class="h-5 w-5 shrink-0 text-[#0FA7B4]"
                                />

                                <span
                                    class="text-xs font-bold text-black/60"
                                >
                                    Trabajo desarrollado por ADN Publicidad
                                </span>
                            </div>

                            <div
                                class="flex items-center gap-3 rounded-2xl border border-black/[0.06] bg-white p-4"
                            >
                                <Sparkles
                                    class="h-5 w-5 shrink-0 text-[#ED7E24]"
                                />

                                <span
                                    class="text-xs font-bold text-black/60"
                                >
                                    Solución adaptada al proyecto
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <!-- GALERÍA -->

            <section
                v-if="
                    galleryMedia.length
                "
                class="border-y border-black/[0.06] bg-white"
            >
                <div
                    class="mx-auto max-w-7xl px-5 py-14 sm:px-8 lg:px-10 lg:py-20"
                >
                    <div
                        class="flex flex-col justify-between gap-5 sm:flex-row sm:items-end"
                    >
                        <div>
                            <div
                                class="flex items-center gap-2 text-[9px] font-black uppercase tracking-[0.15em] text-[#0FA7B4]"
                            >
                                <Images
                                    class="h-4 w-4"
                                />

                                Galería
                            </div>

                            <h2
                                class="mt-3 text-3xl font-black tracking-[-0.045em] sm:text-4xl"
                            >
                                Más de cerca
                            </h2>

                            <p
                                class="mt-3 max-w-xl text-sm leading-7 text-black/45"
                            >
                                Diferentes vistas y detalles del trabajo realizado.
                            </p>
                        </div>

                        <span
                            class="text-[10px] font-black uppercase tracking-[0.12em] text-black/25"
                        >
                            {{
                                galleryMedia.length
                            }}
                            imágenes
                        </span>
                    </div>

                    <div
                        class="mt-8 grid gap-4 sm:grid-cols-2 lg:grid-cols-3"
                    >
                        <figure
                            v-for="
                                (
                                    media,
                                    index
                                ) in galleryMedia
                            "
                            :key="
                                media.id
                            "
                            class="group overflow-hidden rounded-[22px] border border-black/[0.06] bg-[#eef1f1]"
                            :class="
                                index === 0
                                    ? 'sm:col-span-2 lg:col-span-2'
                                    : ''
                            "
                        >
                            <img
                                :src="
                                    media.url
                                "
                                :alt="
                                    media.alt_text
                                    ?? project.title
                                "
                                class="w-full object-cover transition duration-700 group-hover:scale-[1.025]"
                                :class="
                                    index === 0
                                        ? 'aspect-[16/9]'
                                        : 'aspect-[4/3]'
                                "
                                loading="lazy"
                            >
                        </figure>
                    </div>
                </div>
            </section>

            <!-- PUBLICIDAD -->

            <section
                v-if="
                    promotions.length
                "
                class="mx-auto max-w-5xl px-5 py-12 sm:px-8 lg:px-10"
            >
                <PromoSlot
                    :ads="
                        promotions
                    "
                    aspect-class="aspect-[970/250]"
                />
            </section>

            <!-- RELACIONADOS -->

            <section
                v-if="
                    related.length
                "
                class="mx-auto max-w-7xl px-5 py-14 sm:px-8 lg:px-10 lg:py-20"
            >
                <div
                    class="flex flex-col justify-between gap-5 sm:flex-row sm:items-end"
                >
                    <div>
                        <p
                            class="text-[9px] font-black uppercase tracking-[0.15em] text-[#E84657]"
                        >
                            Sigue explorando
                        </p>

                        <h2
                            class="mt-3 text-3xl font-black tracking-[-0.045em] sm:text-4xl"
                        >
                            Otros proyectos
                        </h2>
                    </div>

                    <Link
                        href="/portafolio"
                        class="inline-flex items-center gap-2 text-xs font-black text-[#0FA7B4]"
                    >
                        Ver todo

                        <ArrowRight
                            class="h-4 w-4"
                        />
                    </Link>
                </div>

                <div
                    class="mt-8 grid gap-5 sm:grid-cols-2 lg:grid-cols-3"
                >
                    <Link
                        v-for="
                            item in related
                        "
                        :key="
                            item.id
                        "
                        :href="
                            `/portafolio/${item.slug}`
                        "
                        class="group overflow-hidden rounded-[22px] border border-black/[0.07] bg-white shadow-[0_12px_35px_rgba(22,34,35,.045)] transition duration-300 hover:-translate-y-1 hover:shadow-[0_20px_45px_rgba(22,34,35,.09)]"
                    >
                        <div
                            class="overflow-hidden bg-[#eef1f1]"
                        >
                            <img
                                v-if="
                                    item.main_media
                                "
                                :src="
                                    item.main_media.url
                                "
                                :alt="
                                    item.main_media.alt_text
                                    ?? item.title
                                "
                                class="aspect-[16/10] w-full object-cover transition duration-700 group-hover:scale-[1.035]"
                                loading="lazy"
                            >

                            <div
                                v-else
                                class="flex aspect-[16/10] items-center justify-center"
                            >
                                <ImageIcon
                                    class="h-9 w-9 text-black/10"
                                />
                            </div>
                        </div>

                        <div
                            class="p-5"
                        >
                            <p
                                v-if="
                                    item.category
                                "
                                class="text-[9px] font-black uppercase tracking-[0.12em] text-[#0FA7B4]"
                            >
                                {{
                                    item
                                        .category
                                        .name
                                }}
                            </p>

                            <h3
                                class="mt-2 text-lg font-black tracking-[-0.03em]"
                            >
                                {{
                                    item.title
                                }}
                            </h3>

                            <div
                                class="mt-4 flex items-center gap-2 text-[10px] font-black text-black/30 transition group-hover:text-[#0FA7B4]"
                            >
                                Ver proyecto

                                <ArrowRight
                                    class="h-3.5 w-3.5"
                                />
                            </div>
                        </div>
                    </Link>
                </div>
            </section>

            <!-- CTA FINAL -->

            <section
                class="mx-auto max-w-7xl px-5 pb-16 sm:px-8 lg:px-10 lg:pb-24"
            >
                <div
                    class="relative overflow-hidden rounded-[28px] bg-[#101617] px-6 py-10 text-white sm:px-10 lg:flex lg:items-center lg:justify-between lg:gap-10 lg:px-12 lg:py-12"
                >
                    <div
                        class="pointer-events-none absolute -right-24 -top-32 h-80 w-80 rounded-full bg-[#0FA7B4]/20 blur-3xl"
                    />

                    <div
                        class="relative max-w-2xl"
                    >
                        <p
                            class="text-[9px] font-black uppercase tracking-[0.16em] text-[#70D7DF]"
                        >
                            Tu proyecto puede ser el siguiente
                        </p>

                        <h2
                            class="mt-3 text-3xl font-black tracking-[-0.045em] sm:text-4xl"
                        >
                            ¿Tienes una idea en mente?
                        </h2>

                        <p
                            class="mt-4 text-sm leading-7 text-white/45"
                        >
                            Explora nuestras soluciones y prepara una solicitud de cotización directamente desde el catálogo.
                        </p>
                    </div>

                    <div
                        class="relative mt-7 flex flex-wrap gap-3 lg:mt-0"
                    >
                        <Link
                            href="/catalogo"
                            class="inline-flex h-12 items-center gap-2 rounded-xl bg-[#0FA7B4] px-5 text-sm font-black text-white"
                        >
                            Explorar catálogo

                            <ArrowRight
                                class="h-4 w-4"
                            />
                        </Link>

                        <Link
                            href="/portafolio"
                            class="inline-flex h-12 items-center gap-2 rounded-xl border border-white/10 px-5 text-sm font-black text-white/70"
                        >
                            Más proyectos
                        </Link>
                    </div>
                </div>
            </section>
        </main>

        <!-- PROTECCIÓN CONTRA PROPS INCOMPLETAS -->

        <main
            v-else
            class="flex min-h-[70vh] items-center justify-center bg-[#f5f6f7] px-5"
        >
            <div
                class="max-w-lg rounded-[28px] border border-black/[0.07] bg-white p-8 text-center shadow-xl"
            >
                <BriefcaseBusiness
                    class="mx-auto h-10 w-10 text-[#0FA7B4]"
                />

                <h1
                    class="mt-5 text-2xl font-black tracking-[-0.04em]"
                >
                    Proyecto no disponible
                </h1>

                <p
                    class="mt-3 text-sm leading-7 text-black/45"
                >
                    No fue posible cargar la información de este proyecto.
                </p>

                <Link
                    href="/portafolio"
                    class="mt-6 inline-flex h-11 items-center gap-2 rounded-xl bg-[#0FA7B4] px-5 text-sm font-black text-white"
                >
                    <ArrowLeft
                        class="h-4 w-4"
                    />

                    Volver al portafolio
                </Link>
            </div>
        </main>
    </PublicLayout>
</template>
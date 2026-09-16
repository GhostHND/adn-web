<script setup lang="ts">
import {
    Head,
    Link,
    useForm,
    usePage,
} from '@inertiajs/vue3';

import {
    ArrowLeft,
    CheckCircle2,
    ChevronDown,
    ChevronUp,
    Eye,
    FileText,
    Globe2,
    ImageIcon,
    Images,
    Plus,
    Save,
    Search,
    Trash2,
    X,
} from '@lucide/vue';

import {
    computed,
    ref,
} from 'vue';

import EditorLayout
    from '@/layouts/Editor/EditorLayout.vue';

interface Media {
    id: number;
    title: string | null;
    alt_text: string | null;
    width: number | null;
    height: number | null;
    url: string;
}

interface ContentItem {
    title: string;
    label: string;
    text: string;
    icon: string;
}

interface SectionSettings {
    cta_label?: string;
    cta_url?: string;
}

interface PageSection {
    id: number;
    section_key: string;
    section_type: string;
    title: string | null;
    subtitle: string | null;
    body: string | null;
    media_id: number | null;
    media: Media | null;
    support_media_ids: number[];
    support_media: Media[];

    content: {
        items?: ContentItem[];
    } | null;

    settings: SectionSettings | null;
    sort_order: number;
    active: boolean;
}

interface EditablePage {
    id: number;
    slug: string;
    name: string;
    title: string;
    eyebrow: string | null;
    summary: string | null;
    status: string;
    meta_title: string | null;
    meta_description: string | null;
    canonical_url: string | null;
    published_at: string | null;
    sections: PageSection[];
}

interface SharedProps {
    flash?: {
        success?: string | null;
    };

    [key: string]: unknown;
}

const props =
    defineProps<{
        page: EditablePage;
        mediaLibrary: Media[];
    }>();

const inertiaPage =
    usePage<SharedProps>();

const expandedSections =
    ref<number[]>(
        props.page.sections.map(
            (
                section,
            ) =>
                section.id,
        ),
    );

const mediaSearch =
    ref(
        '',
    );

const normalizeItems =
    (
        section:
            PageSection,
    ): ContentItem[] => {
        return (
            section.content?.items
            ?? []
        ).map(
            (
                item,
            ) => ({
                title:
                    item.title
                    ?? '',

                label:
                    item.label
                    ?? '',

                text:
                    item.text
                    ?? '',

                icon:
                    item.icon
                    ?? '',
            }),
        );
    };

const form =
    useForm({
        title:
            props.page.title,

        eyebrow:
            props.page.eyebrow
            ?? '',

        summary:
            props.page.summary
            ?? '',

        status:
            props.page.status,

        meta_title:
            props.page.meta_title
            ?? '',

        meta_description:
            props.page.meta_description
            ?? '',

        canonical_url:
            props.page.canonical_url
            ?? '',

        sections:
            props.page.sections.map(
                (
                    section,
                ) => ({
                    id:
                        section.id,

                    section_key:
                        section.section_key,

                    section_type:
                        section.section_type,

                    title:
                        section.title
                        ?? '',

                    subtitle:
                        section.subtitle
                        ?? '',

                    body:
                        section.body
                        ?? '',

                    media_id:
                        section.media_id,

                    support_media_ids:
                        [
                            ...section
                                .support_media_ids,
                        ],

                    sort_order:
                        section.sort_order,

                    active:
                        section.active,

                    content: {
                        items:
                            normalizeItems(
                                section,
                            ),
                    },

                    settings: {
                        cta_label:
                            section
                                .settings
                                ?.cta_label
                            ?? '',

                        cta_url:
                            section
                                .settings
                                ?.cta_url
                            ?? '',
                    },
                }),
            ),
    });

const publicUrl =
    computed(
        (): string =>
            props.page.slug ===
            'inicio'
                ? '/'
                : `/${props.page.slug}`,
    );

const filteredMedia =
    computed(
        (): Media[] => {
            const term =
                mediaSearch.value
                    .trim()
                    .toLowerCase();

            if (
                !term
            ) {
                return props
                    .mediaLibrary;
            }

            return props
                .mediaLibrary
                .filter(
                    (
                        media,
                    ) =>
                        (
                            media.title
                            ?? ''
                        )
                            .toLowerCase()
                            .includes(
                                term,
                            )
                        ||
                        (
                            media.alt_text
                            ?? ''
                        )
                            .toLowerCase()
                            .includes(
                                term,
                            ),
                );
        },
    );

const mediaById =
    (
        id:
            number | null,
    ): Media | null => {
        if (
            id ===
            null
        ) {
            return null;
        }

        return props
            .mediaLibrary
            .find(
                (
                    media,
                ) =>
                    media.id ===
                    id,
            )
        ?? null;
    };

const supportMediaFor =
    (
        ids:
            number[],
    ): Media[] => {
        return ids
            .map(
                (
                    id,
                ) =>
                    mediaById(
                        id,
                    ),
            )
            .filter(
                (
                    media,
                ): media is Media =>
                    media !==
                    null,
            );
    };

const toggleSection =
    (
        id:
            number,
    ): void => {
        const index =
            expandedSections.value
                .indexOf(
                    id,
                );

        if (
            index >=
            0
        ) {
            expandedSections.value.splice(
                index,
                1,
            );

            return;
        }

        expandedSections.value.push(
            id,
        );
    };

const isExpanded =
    (
        id:
            number,
    ): boolean =>
        expandedSections.value.includes(
            id,
        );

const addItem =
    (
        sectionIndex:
            number,
    ): void => {
        form.sections[
            sectionIndex
        ].content.items.push({
            title:
                '',

            label:
                '',

            text:
                '',

            icon:
                '',
        });
    };

const removeItem =
    (
        sectionIndex:
            number,

        itemIndex:
            number,
    ): void => {
        form.sections[
            sectionIndex
        ].content.items.splice(
            itemIndex,
            1,
        );
    };

const setMainMedia =
    (
        sectionIndex:
            number,

        mediaId:
            number,
    ): void => {
        form.sections[
            sectionIndex
        ].media_id =
            mediaId;
    };

const clearMainMedia =
    (
        sectionIndex:
            number,
    ): void => {
        form.sections[
            sectionIndex
        ].media_id =
            null;
    };

const toggleSupportMedia =
    (
        sectionIndex:
            number,

        mediaId:
            number,
    ): void => {
        const ids =
            form.sections[
                sectionIndex
            ].support_media_ids;

        const currentIndex =
            ids.indexOf(
                mediaId,
            );

        if (
            currentIndex >=
            0
        ) {
            ids.splice(
                currentIndex,
                1,
            );

            return;
        }

        if (
            ids.length >=
            6
        ) {
            return;
        }

        ids.push(
            mediaId,
        );
    };

const removeSupportMedia =
    (
        sectionIndex:
            number,

        mediaId:
            number,
    ): void => {
        const ids =
            form.sections[
                sectionIndex
            ].support_media_ids;

        const index =
            ids.indexOf(
                mediaId,
            );

        if (
            index >=
            0
        ) {
            ids.splice(
                index,
                1,
            );
        }
    };

const sectionTypeLabel =
    (
        type:
            string,
    ): string => {
        const labels:
            Record<
                string,
                string
            > = {
            hero:
                'Encabezado principal',

            story:
                'Historia / contenido',

            values:
                'Valores',

            process:
                'Proceso',

            services_grid:
                'Servicios',

            cta:
                'Llamado a la acción',
        };

        return labels[
            type
        ]
        ?? type;
    };

const submit =
    (): void => {
        form.transform(
            (
                data,
            ) => ({
                ...data,

                eyebrow:
                    data.eyebrow
                    || null,

                summary:
                    data.summary
                    || null,

                meta_title:
                    data.meta_title
                    || null,

                meta_description:
                    data.meta_description
                    || null,

                canonical_url:
                    data.canonical_url
                    || null,

                sections:
                    data.sections.map(
                        (
                            section,
                        ) => ({
                            id:
                                section.id,

                            title:
                                section.title
                                || null,

                            subtitle:
                                section.subtitle
                                || null,

                            body:
                                section.body
                                || null,

                            media_id:
                                section.media_id,

                            support_media_ids:
                                section
                                    .support_media_ids,

                            sort_order:
                                section.sort_order,

                            active:
                                section.active,

                            content: {
                                items:
                                    section
                                        .content
                                        .items,
                            },

                            settings: {
                                cta_label:
                                    section
                                        .settings
                                        .cta_label
                                    || null,

                                cta_url:
                                    section
                                        .settings
                                        .cta_url
                                    || null,
                            },
                        }),
                    ),
            }),
        ).put(
            `/editor-preview/paginas/${props.page.slug}`,
            {
                preserveScroll:
                    true,
            },
        );
    };
</script>

<template>
    <Head
        :title="
            `Editar ${page.name}`
        "
    />

    <EditorLayout
        :title="
            page.name
        "
        eyebrow="Contenido del sitio"
    >
        <div
            v-if="
                inertiaPage.props.flash?.success
            "
            class="mb-5 flex items-center gap-3 rounded-2xl border border-[var(--adn-turquoise-border)] bg-[var(--adn-turquoise-soft)] px-5 py-4 text-sm font-bold text-[var(--adn-turquoise)]"
        >
            <CheckCircle2
                class="h-4 w-4"
            />

            {{
                inertiaPage.props.flash.success
            }}
        </div>

        <div
            class="mb-6 flex flex-col justify-between gap-4 lg:flex-row lg:items-end"
        >
            <div>
                <Link
                    href="/editor-preview"
                    class="inline-flex items-center gap-2 text-[10px] font-black uppercase tracking-[0.12em] text-white/25 transition hover:text-[var(--adn-turquoise)]"
                >
                    <ArrowLeft
                        class="h-3.5 w-3.5"
                    />

                    Editor
                </Link>

                <h1
                    class="mt-3 text-3xl font-black tracking-[-0.04em] text-white"
                >
                    {{
                        page.name
                    }}
                </h1>

                <p
                    class="mt-2 max-w-2xl text-xs leading-6 text-white/30"
                >
                    Textos, composición y recursos visuales de esta página.
                </p>
            </div>

            <a
                :href="
                    publicUrl
                "
                target="_blank"
                rel="noopener noreferrer"
                class="inline-flex h-11 items-center justify-center gap-2 rounded-xl border border-white/[0.07] bg-white/[0.025] px-4 text-xs font-black text-white/45 transition hover:border-[var(--adn-turquoise-border)] hover:text-[var(--adn-turquoise)]"
            >
                <Eye
                    class="h-4 w-4"
                />

                Ver página
            </a>
        </div>

        <form
            class="space-y-6"
            @submit.prevent="
                submit
            "
        >
            <section
                class="adn-panel rounded-[22px] p-5 sm:p-6"
            >
                <div
                    class="flex items-center gap-3"
                >
                    <span
                        class="flex h-10 w-10 items-center justify-center rounded-xl bg-[var(--adn-turquoise-soft)] text-[var(--adn-turquoise)]"
                    >
                        <FileText
                            class="h-5 w-5"
                        />
                    </span>

                    <div>
                        <h2
                            class="text-sm font-black text-white/85"
                        >
                            Información general
                        </h2>

                        <p
                            class="mt-1 text-[10px] text-white/25"
                        >
                            Identidad, publicación y SEO.
                        </p>
                    </div>
                </div>

                <div
                    class="mt-6 grid gap-5 lg:grid-cols-2"
                >
                    <label>
                        <span
                            class="mb-2 block text-xs font-black text-white/55"
                        >
                            Título
                        </span>

                        <input
                            v-model="
                                form.title
                            "
                            type="text"
                            class="h-11 w-full rounded-xl border border-white/[0.07] bg-black/15 px-4 text-sm text-white outline-none"
                        >
                    </label>

                    <label>
                        <span
                            class="mb-2 block text-xs font-black text-white/55"
                        >
                            Eyebrow
                        </span>

                        <input
                            v-model="
                                form.eyebrow
                            "
                            type="text"
                            class="h-11 w-full rounded-xl border border-white/[0.07] bg-black/15 px-4 text-sm text-white outline-none"
                        >
                    </label>

                    <label
                        class="lg:col-span-2"
                    >
                        <span
                            class="mb-2 block text-xs font-black text-white/55"
                        >
                            Resumen
                        </span>

                        <textarea
                            v-model="
                                form.summary
                            "
                            rows="3"
                            class="w-full rounded-xl border border-white/[0.07] bg-black/15 px-4 py-3 text-sm leading-6 text-white outline-none"
                        />
                    </label>

                    <label>
                        <span
                            class="mb-2 block text-xs font-black text-white/55"
                        >
                            Estado
                        </span>

                        <select
                            v-model="
                                form.status
                            "
                            class="h-11 w-full rounded-xl border border-white/[0.07] bg-[#101719] px-4 text-sm text-white/70"
                        >
                            <option value="published">
                                Publicada
                            </option>

                            <option value="draft">
                                Borrador
                            </option>

                            <option value="hidden">
                                Oculta
                            </option>
                        </select>
                    </label>
                </div>
            </section>

            <section>
                <div
                    class="mb-4"
                >
                    <p
                        class="text-[9px] font-black uppercase tracking-[0.16em] text-[var(--adn-orange)]"
                    >
                        Composición
                    </p>

                    <h2
                        class="mt-1 text-xl font-black text-white"
                    >
                        Secciones y recursos visuales
                    </h2>
                </div>

                <div
                    class="space-y-4"
                >
                    <article
                        v-for="
                            (
                                section,
                                sectionIndex
                            ) in form.sections
                        "
                        :key="
                            section.id
                        "
                        class="adn-panel overflow-hidden rounded-[20px]"
                    >
                        <button
                            type="button"
                            class="flex w-full items-center justify-between gap-4 px-5 py-4 text-left"
                            @click="
                                toggleSection(
                                    section.id,
                                )
                            "
                        >
                            <div
                                class="min-w-0"
                            >
                                <span
                                    class="rounded-lg border border-[var(--adn-turquoise-border)] bg-[var(--adn-turquoise-soft)] px-2 py-1 text-[8px] font-black uppercase tracking-[0.08em] text-[var(--adn-turquoise)]"
                                >
                                    {{
                                        sectionTypeLabel(
                                            section.section_type,
                                        )
                                    }}
                                </span>

                                <p
                                    class="mt-3 truncate text-sm font-black text-white/75"
                                >
                                    {{
                                        section.title
                                        || 'Sin título'
                                    }}
                                </p>
                            </div>

                            <component
                                :is="
                                    isExpanded(
                                        section.id,
                                    )
                                        ? ChevronUp
                                        : ChevronDown
                                "
                                class="h-4 w-4 text-white/30"
                            />
                        </button>

                        <div
                            v-if="
                                isExpanded(
                                    section.id,
                                )
                            "
                            class="border-t border-white/[0.06] p-5"
                        >
                            <div
                                class="flex flex-wrap items-center justify-between gap-4"
                            >
                                <label
                                    class="flex items-center gap-3"
                                >
                                    <input
                                        v-model="
                                            section.active
                                        "
                                        type="checkbox"
                                        class="h-4 w-4 accent-[#0FA7B4]"
                                    >

                                    <span
                                        class="text-xs font-black text-white/50"
                                    >
                                        Sección activa
                                    </span>
                                </label>

                                <label
                                    class="flex items-center gap-2"
                                >
                                    <span
                                        class="text-[10px] font-black text-white/25"
                                    >
                                        Orden
                                    </span>

                                    <input
                                        v-model.number="
                                            section.sort_order
                                        "
                                        type="number"
                                        min="0"
                                        class="h-9 w-24 rounded-xl border border-white/[0.07] bg-black/15 px-3 text-xs text-white"
                                    >
                                </label>
                            </div>

                            <div
                                class="mt-5 grid gap-4"
                            >
                                <input
                                    v-model="
                                        section.subtitle
                                    "
                                    type="text"
                                    placeholder="Subtítulo"
                                    class="h-11 rounded-xl border border-white/[0.07] bg-black/15 px-4 text-sm text-white outline-none"
                                >

                                <input
                                    v-model="
                                        section.title
                                    "
                                    type="text"
                                    placeholder="Título"
                                    class="h-11 rounded-xl border border-white/[0.07] bg-black/15 px-4 text-sm text-white outline-none"
                                >

                                <textarea
                                    v-model="
                                        section.body
                                    "
                                    rows="4"
                                    placeholder="Texto"
                                    class="rounded-xl border border-white/[0.07] bg-black/15 px-4 py-3 text-sm leading-6 text-white outline-none"
                                />
                            </div>

                            <!-- Imagen principal -->

                            <div
                                class="mt-6 border-t border-white/[0.06] pt-5"
                            >
                                <div
                                    class="flex items-center justify-between gap-4"
                                >
                                    <div>
                                        <p
                                            class="text-xs font-black text-white/60"
                                        >
                                            Imagen principal
                                        </p>

                                        <p
                                            class="mt-1 text-[10px] text-white/22"
                                        >
                                            Imagen editorial dominante de esta sección.
                                        </p>
                                    </div>

                                    <button
                                        v-if="
                                            section.media_id
                                        "
                                        type="button"
                                        class="inline-flex items-center gap-2 text-[10px] font-black text-[var(--adn-coral)]"
                                        @click="
                                            clearMainMedia(
                                                sectionIndex,
                                            )
                                        "
                                    >
                                        <X
                                            class="h-3.5 w-3.5"
                                        />

                                        Quitar
                                    </button>
                                </div>

                                <div
                                    v-if="
                                        mediaById(
                                            section.media_id,
                                        )
                                    "
                                    class="mt-4 max-w-md overflow-hidden rounded-2xl border border-white/[0.07] bg-black/15 p-2"
                                >
                                    <img
                                        :src="
                                            mediaById(
                                                section.media_id,
                                            )?.url
                                        "
                                        :alt="
                                            mediaById(
                                                section.media_id,
                                            )?.alt_text
                                            ?? ''
                                        "
                                        class="aspect-[16/9] w-full rounded-xl object-cover"
                                    >
                                </div>

                                <div
                                    v-else
                                    class="mt-4 flex h-32 max-w-md items-center justify-center rounded-2xl border border-dashed border-white/[0.08] bg-black/10"
                                >
                                    <ImageIcon
                                        class="h-8 w-8 text-white/10"
                                    />
                                </div>
                            </div>

                            <!-- Imágenes apoyo -->

                            <div
                                class="mt-6 border-t border-white/[0.06] pt-5"
                            >
                                <div
                                    class="flex items-center justify-between gap-4"
                                >
                                    <div>
                                        <p
                                            class="text-xs font-black text-white/60"
                                        >
                                            Ejemplos visuales
                                        </p>

                                        <p
                                            class="mt-1 text-[10px] text-white/22"
                                        >
                                            Hasta 6 imágenes de apoyo. No funcionan como portafolio.
                                        </p>
                                    </div>

                                    <span
                                        class="text-[10px] font-black text-white/25"
                                    >
                                        {{
                                            section.support_media_ids.length
                                        }}
                                        / 6
                                    </span>
                                </div>

                                <div
                                    v-if="
                                        section.support_media_ids.length
                                    "
                                    class="mt-4 grid grid-cols-2 gap-3 sm:grid-cols-3 lg:grid-cols-6"
                                >
                                    <div
                                        v-for="
                                            media in supportMediaFor(
                                                section.support_media_ids,
                                            )
                                        "
                                        :key="
                                            media.id
                                        "
                                        class="group relative overflow-hidden rounded-xl border border-white/[0.07]"
                                    >
                                        <img
                                            :src="
                                                media.url
                                            "
                                            :alt="
                                                media.alt_text
                                                ?? ''
                                            "
                                            class="aspect-square w-full object-cover"
                                        >

                                        <button
                                            type="button"
                                            class="absolute right-1.5 top-1.5 flex h-7 w-7 items-center justify-center rounded-lg bg-black/70 text-white"
                                            @click="
                                                removeSupportMedia(
                                                    sectionIndex,
                                                    media.id,
                                                )
                                            "
                                        >
                                            <X
                                                class="h-3.5 w-3.5"
                                            />
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <!-- Selector multimedia -->

                            <div
                                class="mt-6 rounded-2xl border border-white/[0.06] bg-black/10 p-4"
                            >
                                <div
                                    class="flex flex-col justify-between gap-3 sm:flex-row sm:items-center"
                                >
                                    <div
                                        class="flex items-center gap-2"
                                    >
                                        <Images
                                            class="h-4 w-4 text-[var(--adn-turquoise)]"
                                        />

                                        <p
                                            class="text-xs font-black text-white/60"
                                        >
                                            Multimedia
                                        </p>
                                    </div>

                                    <div
                                        class="relative sm:w-64"
                                    >
                                        <Search
                                            class="absolute left-3 top-1/2 h-3.5 w-3.5 -translate-y-1/2 text-white/20"
                                        />

                                        <input
                                            v-model="
                                                mediaSearch
                                            "
                                            type="search"
                                            placeholder="Buscar imagen..."
                                            class="h-9 w-full rounded-xl border border-white/[0.06] bg-black/20 pl-9 pr-3 text-xs text-white outline-none"
                                        >
                                    </div>
                                </div>

                                <div
                                    v-if="
                                        filteredMedia.length
                                    "
                                    class="mt-4 grid max-h-[380px] grid-cols-2 gap-3 overflow-y-auto pr-1 sm:grid-cols-3 md:grid-cols-4 xl:grid-cols-6"
                                >
                                    <article
                                        v-for="
                                            media in filteredMedia
                                        "
                                        :key="
                                            media.id
                                        "
                                        class="overflow-hidden rounded-xl border border-white/[0.06] bg-black/15"
                                    >
                                        <img
                                            :src="
                                                media.url
                                            "
                                            :alt="
                                                media.alt_text
                                                ?? ''
                                            "
                                            class="aspect-square w-full object-cover"
                                        >

                                        <div
                                            class="space-y-2 p-2"
                                        >
                                            <button
                                                type="button"
                                                class="w-full rounded-lg bg-[var(--adn-turquoise-soft)] px-2 py-2 text-[8px] font-black uppercase text-[var(--adn-turquoise)]"
                                                @click="
                                                    setMainMedia(
                                                        sectionIndex,
                                                        media.id,
                                                    )
                                                "
                                            >
                                                Principal
                                            </button>

                                            <button
                                                type="button"
                                                class="w-full rounded-lg border border-white/[0.06] px-2 py-2 text-[8px] font-black uppercase text-white/40"
                                                :class="
                                                    section.support_media_ids.includes(
                                                        media.id,
                                                    )
                                                        ? 'border-[var(--adn-orange-border)] bg-[var(--adn-orange-soft)] text-[var(--adn-orange)]'
                                                        : ''
                                                "
                                                @click="
                                                    toggleSupportMedia(
                                                        sectionIndex,
                                                        media.id,
                                                    )
                                                "
                                            >
                                                {{
                                                    section.support_media_ids.includes(
                                                        media.id,
                                                    )
                                                        ? 'Seleccionada'
                                                        : 'Apoyo'
                                                }}
                                            </button>
                                        </div>
                                    </article>
                                </div>

                                <div
                                    v-else
                                    class="mt-4 rounded-xl border border-dashed border-white/[0.07] py-8 text-center text-xs text-white/20"
                                >
                                    No hay imágenes disponibles.
                                    Súbelas primero desde Multimedia.
                                </div>
                            </div>

                            <!-- Elementos -->

                            <div
                                v-if="
                                    section.content.items.length
                                    ||
                                    [
                                        'story',
                                        'values',
                                        'process',
                                        'services_grid',
                                    ].includes(
                                        section.section_type,
                                    )
                                "
                                class="mt-6 border-t border-white/[0.06] pt-5"
                            >
                                <div
                                    class="flex items-center justify-between gap-3"
                                >
                                    <p
                                        class="text-xs font-black text-white/60"
                                    >
                                        Elementos de contenido
                                    </p>

                                    <button
                                        type="button"
                                        class="inline-flex items-center gap-2 rounded-xl border border-[var(--adn-turquoise-border)] bg-[var(--adn-turquoise-soft)] px-3 py-2 text-[10px] font-black text-[var(--adn-turquoise)]"
                                        @click="
                                            addItem(
                                                sectionIndex,
                                            )
                                        "
                                    >
                                        <Plus
                                            class="h-3.5 w-3.5"
                                        />

                                        Agregar
                                    </button>
                                </div>

                                <div
                                    class="mt-4 space-y-3"
                                >
                                    <div
                                        v-for="
                                            (
                                                item,
                                                itemIndex
                                            ) in section.content.items
                                        "
                                        :key="
                                            itemIndex
                                        "
                                        class="rounded-2xl border border-white/[0.055] bg-black/10 p-4"
                                    >
                                        <div
                                            class="grid gap-3 md:grid-cols-[110px_minmax(0,1fr)_110px_auto]"
                                        >
                                            <input
                                                v-model="
                                                    item.label
                                                "
                                                type="text"
                                                placeholder="Etiqueta"
                                                class="h-10 rounded-xl border border-white/[0.06] bg-black/15 px-3 text-xs text-white"
                                            >

                                            <input
                                                v-model="
                                                    item.title
                                                "
                                                type="text"
                                                placeholder="Título"
                                                class="h-10 min-w-0 rounded-xl border border-white/[0.06] bg-black/15 px-3 text-xs text-white"
                                            >

                                            <input
                                                v-model="
                                                    item.icon
                                                "
                                                type="text"
                                                placeholder="Icono"
                                                class="h-10 rounded-xl border border-white/[0.06] bg-black/15 px-3 text-xs text-white"
                                            >

                                            <button
                                                type="button"
                                                class="flex h-10 w-10 items-center justify-center rounded-xl border border-[var(--adn-coral-border)] bg-[var(--adn-coral-soft)] text-[var(--adn-coral)]"
                                                @click="
                                                    removeItem(
                                                        sectionIndex,
                                                        itemIndex,
                                                    )
                                                "
                                            >
                                                <Trash2
                                                    class="h-4 w-4"
                                                />
                                            </button>
                                        </div>

                                        <textarea
                                            v-model="
                                                item.text
                                            "
                                            rows="3"
                                            placeholder="Descripción"
                                            class="mt-3 w-full rounded-xl border border-white/[0.06] bg-black/15 px-3 py-3 text-xs leading-5 text-white"
                                        />
                                    </div>
                                </div>
                            </div>

                            <!-- CTA -->

                            <div
                                v-if="
                                    [
                                        'hero',
                                        'cta',
                                    ].includes(
                                        section.section_type,
                                    )
                                "
                                class="mt-6 grid gap-3 border-t border-white/[0.06] pt-5 sm:grid-cols-2"
                            >
                                <input
                                    v-model="
                                        section.settings.cta_label
                                    "
                                    type="text"
                                    placeholder="Texto del botón"
                                    class="h-11 rounded-xl border border-white/[0.07] bg-black/15 px-4 text-sm text-white"
                                >

                                <input
                                    v-model="
                                        section.settings.cta_url
                                    "
                                    type="text"
                                    placeholder="/catalogo"
                                    class="h-11 rounded-xl border border-white/[0.07] bg-black/15 px-4 text-sm text-white"
                                >
                            </div>
                        </div>
                    </article>
                </div>
            </section>

            <section
                class="adn-panel rounded-[22px] p-5 sm:p-6"
            >
                <div
                    class="flex items-center gap-3"
                >
                    <Globe2
                        class="h-5 w-5 text-[var(--adn-orange)]"
                    />

                    <h2
                        class="text-sm font-black text-white/85"
                    >
                        SEO
                    </h2>
                </div>

                <div
                    class="mt-5 grid gap-4"
                >
                    <input
                        v-model="
                            form.meta_title
                        "
                        type="text"
                        placeholder="Título SEO"
                        class="h-11 rounded-xl border border-white/[0.07] bg-black/15 px-4 text-sm text-white"
                    >

                    <textarea
                        v-model="
                            form.meta_description
                        "
                        rows="3"
                        placeholder="Descripción SEO"
                        class="rounded-xl border border-white/[0.07] bg-black/15 px-4 py-3 text-sm text-white"
                    />

                    <input
                        v-model="
                            form.canonical_url
                        "
                        type="url"
                        inputmode="url"
                        placeholder="URL canónica"
                        class="h-11 rounded-xl border border-white/[0.07] bg-black/15 px-4 text-sm text-white"
                    >
                </div>
            </section>

            <div
                class="sticky bottom-4 z-20 flex justify-end"
            >
                <button
                    type="submit"
                    :disabled="
                        form.processing
                    "
                    class="adn-primary-button flex h-12 min-w-44 items-center justify-center gap-2 rounded-xl px-5 text-sm font-black shadow-xl disabled:opacity-50"
                >
                    <Save
                        class="h-4 w-4"
                    />

                    {{
                        form.processing
                            ? 'Guardando...'
                            : 'Guardar cambios'
                    }}
                </button>
            </div>
        </form>
    </EditorLayout>
</template>
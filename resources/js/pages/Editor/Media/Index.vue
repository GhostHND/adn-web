<script setup lang="ts">
import {
    Head,
    Link,
    router,
    useForm,
    usePage,
} from '@inertiajs/vue3';

import {
    FileText,
    Image,
    Search,
    Trash2,
    Upload,
} from '@lucide/vue';

import {
    reactive,
} from 'vue';

import EditorLayout
    from '@/layouts/Editor/EditorLayout.vue';

interface MediaItem {
    id: number;
    uuid: string;
    title: string | null;
    original_name: string | null;
    mime_type: string | null;
    extension: string | null;
    alt_text: string | null;
    caption: string | null;
    width: number | null;
    height: number | null;
    size: number | null;
    url: string;
    created_at: string;
}

interface PaginationLink {
    url: string | null;
    label: string;
    active: boolean;
}

interface MediaPaginator {
    data: MediaItem[];
    current_page: number;
    last_page: number;
    total: number;
    links: PaginationLink[];
}

interface Summary {
    total: number;
    images: number;
    documents: number;
}

interface Filters {
    search: string;
    type: string;
}

interface SharedProps {
    flash?: {
        success?: string | null;
    };

    errors?: {
        delete?: string;
    };

    [key: string]: unknown;
}

const props =
    defineProps<{
        media: MediaPaginator;
        summary: Summary;
        filters: Filters;
    }>();

const page =
    usePage<SharedProps>();

const filters =
    reactive({
        search:
            props.filters.search
            ?? '',

        type:
            props.filters.type
            ?? '',
    });

const uploadForm =
    useForm<{
        file: File | null;
        title: string;
        alt_text: string;
        caption: string;
    }>({
        file:
            null,

        title:
            '',

        alt_text:
            '',

        caption:
            '',
    });

const selectFile =
    (
        event:
            Event,
    ) => {
        const input =
            event.target;

        if (
            !(
                input
                instanceof
                HTMLInputElement
            )
        ) {
            return;
        }

        uploadForm.file =
            input.files?.[0]
            ?? null;
    };

const upload =
    () => {
        uploadForm.post(
            '/editor-preview/multimedia',
            {
                forceFormData:
                    true,

                preserveScroll:
                    true,

                onSuccess:
                    () => {
                        uploadForm.reset();
                    },
            },
        );
    };

const applyFilters =
    () => {
        router.get(
            '/editor-preview/multimedia',
            {
                search:
                    filters.search
                    || undefined,

                type:
                    filters.type
                    || undefined,
            },
            {
                preserveState:
                    true,

                replace:
                    true,
            },
        );
    };

const clearFilters =
    () => {
        filters.search =
            '';

        filters.type =
            '';

        router.get(
            '/editor-preview/multimedia',
        );
    };

const removeMedia =
    (
        media:
            MediaItem,
    ) => {
        if (
            !window.confirm(
                `¿Eliminar "${media.title ?? media.original_name}" de Multimedia?`,
            )
        ) {
            return;
        }

        router.delete(
            `/editor-preview/multimedia/${media.id}`,
            {
                preserveScroll:
                    true,
            },
        );
    };

const isImage =
    (
        media:
            MediaItem,
    ) =>
        media.mime_type
            ?.startsWith(
                'image/',
            )
        ?? false;

const formatBytes =
    (
        bytes:
            number | null,
    ) => {
        if (
            !bytes
        ) {
            return '—';
        }

        const units =
            [
                'B',
                'KB',
                'MB',
                'GB',
            ];

        let value =
            bytes;

        let unitIndex =
            0;

        while (
            value >= 1024
            &&
            unitIndex <
                units.length - 1
        ) {
            value /=
                1024;

            unitIndex++;
        }

        return `${value.toFixed(
            unitIndex === 0
                ? 0
                : 1,
        )} ${units[unitIndex]}`;
    };
</script>

<template>
    <Head
        title="Multimedia"
    />

    <EditorLayout
        title="Multimedia"
        eyebrow="Contenido ADN"
    >
        <div
            v-if="page.props.flash?.success"
            class="mb-5 rounded-2xl border border-[var(--adn-turquoise-border)] bg-[var(--adn-turquoise-soft)] px-5 py-4 text-sm font-bold text-[var(--adn-turquoise)]"
        >
            {{ page.props.flash.success }}
        </div>

        <div
            v-if="$page.props.errors?.delete"
            class="mb-5 rounded-2xl border border-[var(--adn-coral-border)] bg-[var(--adn-coral-soft)] px-5 py-4 text-sm font-bold text-[var(--adn-coral)]"
        >
            {{ $page.props.errors.delete }}
        </div>

        <section
            class="flex flex-col justify-between gap-5 lg:flex-row lg:items-center"
        >
            <div>
                <p
                    class="text-[9px] font-black uppercase tracking-[0.17em] text-[var(--adn-orange)]"
                >
                    Biblioteca de archivos
                </p>

                <h1
                    class="mt-2 text-3xl font-black tracking-[-0.04em]"
                >
                    Multimedia ADN
                </h1>

                <p
                    class="mt-2 max-w-xl text-sm text-white/35"
                >
                    Imágenes y documentos reutilizables en el
                    sitio web, catálogo, servicios y portafolio.
                </p>
            </div>
        </section>

        <section
            class="mt-6 grid gap-3 sm:grid-cols-3"
        >
            <article
                class="adn-panel rounded-[18px] p-4"
            >
                <p
                    class="text-[9px] font-black uppercase tracking-[0.12em] text-white/25"
                >
                    Archivos
                </p>

                <p
                    class="mt-2 text-2xl font-black text-[var(--adn-turquoise)]"
                >
                    {{ summary.total }}
                </p>
            </article>

            <article
                class="adn-panel rounded-[18px] p-4"
            >
                <p
                    class="text-[9px] font-black uppercase tracking-[0.12em] text-white/25"
                >
                    Imágenes
                </p>

                <p
                    class="mt-2 text-2xl font-black text-[var(--adn-orange)]"
                >
                    {{ summary.images }}
                </p>
            </article>

            <article
                class="adn-panel rounded-[18px] p-4"
            >
                <p
                    class="text-[9px] font-black uppercase tracking-[0.12em] text-white/25"
                >
                    Documentos
                </p>

                <p
                    class="mt-2 text-2xl font-black text-[var(--adn-coral)]"
                >
                    {{ summary.documents }}
                </p>
            </article>
        </section>

        <section
            class="adn-panel mt-5 rounded-[22px] p-5 sm:p-6"
        >
            <div
                class="flex items-center gap-3"
            >
                <div
                    class="flex h-10 w-10 items-center justify-center rounded-xl bg-[var(--adn-turquoise-soft)] text-[var(--adn-turquoise)]"
                >
                    <Upload
                        class="h-5 w-5"
                    />
                </div>

                <div>
                    <h2
                        class="text-sm font-black"
                    >
                        Agregar archivo
                    </h2>

                    <p
                        class="mt-0.5 text-[10px] text-white/28"
                    >
                        JPG, PNG, WEBP o PDF · máximo 20 MB
                    </p>
                </div>
            </div>

            <form
                class="mt-5 grid gap-4 xl:grid-cols-[1.25fr_1fr_1fr_auto]"
                @submit.prevent="upload"
            >
                <label
                    class="flex min-h-12 cursor-pointer items-center rounded-xl border border-dashed border-[var(--adn-turquoise-border)] bg-[var(--adn-turquoise-soft)] px-4"
                >
                    <Upload
                        class="mr-3 h-4 w-4 shrink-0 text-[var(--adn-turquoise)]"
                    />

                    <span
                        class="truncate text-xs font-bold text-white/48"
                    >
                        {{
                            uploadForm.file?.name
                            ?? 'Seleccionar archivo'
                        }}
                    </span>

                    <input
                        type="file"
                        accept=".jpg,.jpeg,.png,.webp,.pdf"
                        class="sr-only"
                        @change="selectFile"
                    >
                </label>

                <input
                    v-model="uploadForm.title"
                    type="text"
                    maxlength="255"
                    placeholder="Título opcional"
                    class="h-12 rounded-xl border border-white/[0.07] bg-black/15 px-4 text-sm text-white outline-none placeholder:text-white/18"
                >

                <input
                    v-model="uploadForm.alt_text"
                    type="text"
                    maxlength="255"
                    placeholder="Texto alternativo"
                    class="h-12 rounded-xl border border-white/[0.07] bg-black/15 px-4 text-sm text-white outline-none placeholder:text-white/18"
                >

                <button
                    type="submit"
                    :disabled="
                        uploadForm.processing
                        ||
                        !uploadForm.file
                    "
                    class="adn-primary-button h-12 rounded-xl px-6 text-xs font-black disabled:cursor-not-allowed disabled:opacity-35"
                >
                    {{
                        uploadForm.processing
                            ? 'Subiendo...'
                            : 'Subir'
                    }}
                </button>
            </form>

            <p
                v-if="uploadForm.errors.file"
                class="mt-3 text-xs font-bold text-[var(--adn-coral)]"
            >
                {{ uploadForm.errors.file }}
            </p>
        </section>

        <section
            class="adn-panel mt-5 rounded-[22px] p-4"
        >
            <form
                class="grid gap-3 lg:grid-cols-[1fr_220px_auto]"
                @submit.prevent="applyFilters"
            >
                <label
                    class="relative"
                >
                    <Search
                        class="absolute left-4 top-1/2 h-4 w-4 -translate-y-1/2 text-white/25"
                    />

                    <input
                        v-model="filters.search"
                        type="search"
                        placeholder="Buscar archivo..."
                        class="h-11 w-full rounded-xl border border-white/[0.07] bg-black/15 pl-11 pr-4 text-sm text-white outline-none placeholder:text-white/18"
                    >
                </label>

                <select
                    v-model="filters.type"
                    class="h-11 rounded-xl border border-white/[0.07] bg-[#101719] px-4 text-sm text-white/70"
                >
                    <option value="">
                        Todos
                    </option>

                    <option value="image">
                        Imágenes
                    </option>

                    <option value="document">
                        Documentos
                    </option>
                </select>

                <div
                    class="flex gap-2"
                >
                    <button
                        type="submit"
                        class="adn-primary-button h-11 rounded-xl px-5 text-xs font-black"
                    >
                        Filtrar
                    </button>

                    <button
                        type="button"
                        class="h-11 rounded-xl border border-white/[0.07] px-4 text-xs font-black text-white/35"
                        @click="clearFilters"
                    >
                        Limpiar
                    </button>
                </div>
            </form>
        </section>

        <section
            v-if="media.data.length"
            class="mt-5 grid gap-4 sm:grid-cols-2 lg:grid-cols-3 2xl:grid-cols-4"
        >
            <article
                v-for="item in media.data"
                :key="item.id"
                class="adn-panel group overflow-hidden rounded-[20px]"
            >
                <a
                    :href="item.url"
                    target="_blank"
                    rel="noopener noreferrer"
                    class="relative flex aspect-[4/3] items-center justify-center overflow-hidden bg-black/20"
                >
                    <img
                        v-if="isImage(item)"
                        :src="item.url"
                        :alt="
                            item.alt_text
                            ?? item.title
                            ?? ''
                        "
                        class="h-full w-full object-cover transition duration-500 group-hover:scale-[1.03]"
                    >

                    <FileText
                        v-else
                        class="h-12 w-12 text-[var(--adn-coral)]"
                    />
                </a>

                <div
                    class="p-4"
                >
                    <div
                        class="flex items-start justify-between gap-3"
                    >
                        <div
                            class="min-w-0"
                        >
                            <p
                                class="truncate text-sm font-black text-white/75"
                            >
                                {{
                                    item.title
                                    ?? item.original_name
                                }}
                            </p>

                            <p
                                class="mt-1 text-[10px] uppercase text-white/25"
                            >
                                {{
                                    item.extension
                                    ?? 'archivo'
                                }}
                                ·
                                {{
                                    formatBytes(
                                        item.size,
                                    )
                                }}
                            </p>
                        </div>

                        <button
                            type="button"
                            title="Eliminar archivo"
                            class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl border border-white/[0.06] text-white/25 transition hover:border-[var(--adn-coral-border)] hover:bg-[var(--adn-coral-soft)] hover:text-[var(--adn-coral)]"
                            @click="
                                removeMedia(
                                    item,
                                )
                            "
                        >
                            <Trash2
                                class="h-4 w-4"
                            />
                        </button>
                    </div>

                    <p
                        v-if="
                            item.width
                            &&
                            item.height
                        "
                        class="mt-3 text-[10px] text-white/25"
                    >
                        {{ item.width }}
                        ×
                        {{ item.height }}
                        px
                    </p>
                </div>
            </article>
        </section>

        <section
            v-else
            class="adn-panel mt-5 flex min-h-72 flex-col items-center justify-center rounded-[22px] px-6 text-center"
        >
            <Image
                class="h-10 w-10 text-white/12"
            />

            <p
                class="mt-4 text-sm font-black text-white/55"
            >
                Todavía no hay archivos
            </p>

            <p
                class="mt-1 text-xs text-white/25"
            >
                Sube la primera imagen de ADN Web.
            </p>
        </section>

        <div
            v-if="media.last_page > 1"
            class="mt-5 flex flex-wrap justify-center gap-2"
        >
            <Link
                v-for="link in media.links"
                :key="link.label"
                :href="link.url ?? ''"
                preserve-scroll
                class="flex min-h-9 min-w-9 items-center justify-center rounded-lg border px-3 text-xs font-black"
                :class="
                    link.active
                        ? 'border-[var(--adn-turquoise-border)] bg-[var(--adn-turquoise-soft)] text-[var(--adn-turquoise)]'
                        : link.url
                            ? 'border-white/[0.06] text-white/35'
                            : 'pointer-events-none border-white/[0.03] text-white/10'
                "
                v-html="link.label"
            />
        </div>
    </EditorLayout>
</template>
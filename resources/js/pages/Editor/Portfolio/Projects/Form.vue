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
    ImageIcon,
    Images,
    Save,
    Search,
    Star,
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

interface Category {
    id: number;
    name: string;
    slug: string;
}

interface Project {
    id: number;
    code: string;
    slug: string;
    title: string;
    client_name: string | null;
    excerpt: string | null;
    description: string | null;
    category_id: number;
    main_media_id: number;
    gallery_media_ids: number[];
    project_date: string | null;
    status: string;
    featured: boolean;
    sort_order: number;
    meta_title: string | null;
    meta_description: string | null;
}

interface SharedProps {
    flash?: {
        success?: string;
    };

    [key: string]: unknown;
}

const props =
    defineProps<{
        project: Project | null;
        categories: Category[];
        mediaLibrary: Media[];
    }>();

const page =
    usePage<SharedProps>();

const search =
    ref('');

const editing =
    computed(
        () =>
            props.project !==
            null,
    );

const form =
    useForm({
        title:
            props.project?.title
            ?? '',

        slug:
            props.project?.slug
            ?? '',

        client_name:
            props.project?.client_name
            ?? '',

        excerpt:
            props.project?.excerpt
            ?? '',

        description:
            props.project?.description
            ?? '',

        category_id:
            props.project?.category_id
            ?? (
                props.categories[0]?.id
                ?? null
            ),

        main_media_id:
            props.project?.main_media_id
            ?? null,

        gallery_media_ids:
            [
                ...(
                    props.project
                        ?.gallery_media_ids
                    ?? []
                ),
            ],

        project_date:
            props.project?.project_date
            ?? '',

        status:
            props.project?.status
            ?? 'draft',

        featured:
            props.project?.featured
            ?? false,

        sort_order:
            props.project?.sort_order
            ?? 10,

        meta_title:
            props.project?.meta_title
            ?? '',

        meta_description:
            props.project?.meta_description
            ?? '',
    });

const filteredMedia =
    computed(
        (): Media[] => {
            const term =
                search.value
                    .trim()
                    .toLowerCase();

            if (
                !term
            ) {
                return props.mediaLibrary;
            }

            return props.mediaLibrary.filter(
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

        return props.mediaLibrary.find(
            (
                media,
            ) =>
                media.id ===
                id,
        )
        ?? null;
    };

const galleryMedia =
    computed(
        (): Media[] =>
            form.gallery_media_ids
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
                ),
    );

const toggleGallery =
    (
        mediaId:
            number,
    ): void => {
        const index =
            form.gallery_media_ids.indexOf(
                mediaId,
            );

        if (
            index >=
            0
        ) {
            form.gallery_media_ids.splice(
                index,
                1,
            );

            return;
        }

        if (
            form.gallery_media_ids.length >=
            20
        ) {
            return;
        }

        form.gallery_media_ids.push(
            mediaId,
        );
    };

const removeGallery =
    (
        mediaId:
            number,
    ): void => {
        const index =
            form.gallery_media_ids.indexOf(
                mediaId,
            );

        if (
            index >=
            0
        ) {
            form.gallery_media_ids.splice(
                index,
                1,
            );
        }
    };

const submit =
    (): void => {
        form.transform(
            (
                data,
            ) => ({
                ...data,

                slug:
                    data.slug
                    || null,

                client_name:
                    data.client_name
                    || null,

                excerpt:
                    data.excerpt
                    || null,

                description:
                    data.description
                    || null,

                project_date:
                    data.project_date
                    || null,

                meta_title:
                    data.meta_title
                    || null,

                meta_description:
                    data.meta_description
                    || null,
            }),
        );

        if (
            props.project
        ) {
            form.put(
                `/editor-preview/portafolio/proyectos/${props.project.slug}`,
                {
                    preserveScroll:
                        true,
                },
            );

            return;
        }

        form.post(
            '/editor-preview/portafolio/proyectos',
        );
    };
</script>

<template>
    <Head
        :title="
            editing
                ? 'Editar proyecto'
                : 'Nuevo proyecto'
        "
    />

    <EditorLayout
        :title="
            editing
                ? project?.title ?? 'Editar proyecto'
                : 'Nuevo proyecto'
        "
        eyebrow="Portafolio"
    >
        <div
            v-if="page.props.flash?.success"
            class="mb-5 flex items-center gap-3 rounded-2xl border border-[var(--adn-turquoise-border)] bg-[var(--adn-turquoise-soft)] px-5 py-4 text-sm font-bold text-[var(--adn-turquoise)]"
        >
            <CheckCircle2 class="h-4 w-4" />
            {{ page.props.flash.success }}
        </div>

        <Link
            href="/editor-preview/portafolio/proyectos"
            class="inline-flex items-center gap-2 text-[10px] font-black uppercase tracking-[0.12em] text-white/25 hover:text-[var(--adn-turquoise)]"
        >
            <ArrowLeft class="h-3.5 w-3.5" />
            Proyectos
        </Link>

        <form
            class="mt-6 space-y-6"
            @submit.prevent="submit"
        >
            <section class="adn-panel rounded-[22px] p-5 sm:p-6">
                <div class="grid gap-5 lg:grid-cols-2">
                    <label>
                        <span class="mb-2 block text-xs font-black text-white/55">
                            Título del proyecto
                        </span>

                        <input
                            v-model="form.title"
                            type="text"
                            class="h-11 w-full rounded-xl border border-white/[0.07] bg-black/15 px-4 text-sm text-white outline-none"
                        >

                        <p
                            v-if="form.errors.title"
                            class="mt-2 text-xs text-[var(--adn-coral)]"
                        >
                            {{ form.errors.title }}
                        </p>
                    </label>

                    <label>
                        <span class="mb-2 block text-xs font-black text-white/55">
                            Categoría
                        </span>

                        <select
                            v-model="form.category_id"
                            class="h-11 w-full rounded-xl border border-white/[0.07] bg-[#101719] px-4 text-sm text-white"
                        >
                            <option
                                v-for="category in categories"
                                :key="category.id"
                                :value="category.id"
                            >
                                {{ category.name }}
                            </option>
                        </select>
                    </label>

                    <label>
                        <span class="mb-2 block text-xs font-black text-white/55">
                            Cliente
                        </span>

                        <input
                            v-model="form.client_name"
                            type="text"
                            placeholder="Opcional"
                            class="h-11 w-full rounded-xl border border-white/[0.07] bg-black/15 px-4 text-sm text-white outline-none placeholder:text-white/15"
                        >
                    </label>

                    <label>
                        <span class="mb-2 block text-xs font-black text-white/55">
                            Fecha del proyecto
                        </span>

                        <input
                            v-model="form.project_date"
                            type="date"
                            class="h-11 w-full rounded-xl border border-white/[0.07] bg-[#101719] px-4 text-sm text-white"
                        >
                    </label>

                    <label class="lg:col-span-2">
                        <span class="mb-2 block text-xs font-black text-white/55">
                            Resumen
                        </span>

                        <textarea
                            v-model="form.excerpt"
                            rows="3"
                            placeholder="Una explicación corta que aparecerá en la tarjeta del proyecto."
                            class="w-full rounded-xl border border-white/[0.07] bg-black/15 px-4 py-3 text-sm leading-6 text-white outline-none placeholder:text-white/15"
                        />
                    </label>

                    <label class="lg:col-span-2">
                        <span class="mb-2 block text-xs font-black text-white/55">
                            Descripción completa
                        </span>

                        <textarea
                            v-model="form.description"
                            rows="7"
                            placeholder="Explica el trabajo, necesidad, solución o proceso que quieras compartir con el cliente."
                            class="w-full rounded-xl border border-white/[0.07] bg-black/15 px-4 py-3 text-sm leading-6 text-white outline-none placeholder:text-white/15"
                        />
                    </label>

                    <label>
                        <span class="mb-2 block text-xs font-black text-white/55">
                            Slug
                        </span>

                        <input
                            v-model="form.slug"
                            type="text"
                            placeholder="Automático si queda vacío"
                            class="h-11 w-full rounded-xl border border-white/[0.07] bg-black/15 px-4 text-sm text-white outline-none placeholder:text-white/15"
                        >
                    </label>

                    <label>
                        <span class="mb-2 block text-xs font-black text-white/55">
                            Estado
                        </span>

                        <select
                            v-model="form.status"
                            class="h-11 w-full rounded-xl border border-white/[0.07] bg-[#101719] px-4 text-sm text-white"
                        >
                            <option value="draft">
                                Borrador
                            </option>

                            <option value="published">
                                Publicado
                            </option>
                        </select>
                    </label>

                    <label>
                        <span class="mb-2 block text-xs font-black text-white/55">
                            Orden
                        </span>

                        <input
                            v-model.number="form.sort_order"
                            type="number"
                            min="0"
                            class="h-11 w-full rounded-xl border border-white/[0.07] bg-black/15 px-4 text-sm text-white"
                        >
                    </label>

                    <label
                        class="flex items-center gap-3 self-end rounded-xl border border-[#F5C000]/15 bg-[#F5C000]/[0.04] px-4 py-3"
                    >
                        <input
                            v-model="form.featured"
                            type="checkbox"
                            class="h-4 w-4 accent-[#F5C000]"
                        >

                        <Star class="h-4 w-4 text-[#F5C000]" />

                        <span class="text-xs font-black text-white/55">
                            Proyecto destacado
                        </span>
                    </label>
                </div>
            </section>

            <!-- Portada -->

            <section class="adn-panel rounded-[22px] p-5 sm:p-6">
                <div>
                    <p class="text-xs font-black text-white/70">
                        Imagen principal
                    </p>

                    <p class="mt-1 text-[10px] text-white/25">
                        Será la portada del proyecto.
                    </p>
                </div>

                <div
                    v-if="mediaById(form.main_media_id)"
                    class="mt-5 max-w-xl overflow-hidden rounded-[22px] border border-white/[0.07] p-2"
                >
                    <img
                        :src="mediaById(form.main_media_id)?.url"
                        :alt="mediaById(form.main_media_id)?.alt_text ?? ''"
                        class="aspect-[16/10] w-full rounded-[16px] object-cover"
                    >
                </div>

                <div
                    v-else
                    class="mt-5 flex aspect-[16/7] max-w-xl items-center justify-center rounded-[22px] border border-dashed border-white/[0.08]"
                >
                    <ImageIcon class="h-9 w-9 text-white/10" />
                </div>
            </section>

            <!-- Galería seleccionada -->

            <section class="adn-panel rounded-[22px] p-5 sm:p-6">
                <div class="flex items-center justify-between gap-4">
                    <div>
                        <p class="text-xs font-black text-white/70">
                            Galería
                        </p>

                        <p class="mt-1 text-[10px] text-white/25">
                            Hasta 20 imágenes.
                        </p>
                    </div>

                    <span class="text-[10px] font-black text-white/25">
                        {{ form.gallery_media_ids.length }} / 20
                    </span>
                </div>

                <div
                    v-if="galleryMedia.length"
                    class="mt-5 grid grid-cols-2 gap-3 sm:grid-cols-3 md:grid-cols-4 xl:grid-cols-6"
                >
                    <div
                        v-for="media in galleryMedia"
                        :key="media.id"
                        class="group relative overflow-hidden rounded-xl border border-white/[0.07]"
                    >
                        <img
                            :src="media.url"
                            :alt="media.alt_text ?? ''"
                            class="aspect-square w-full object-cover"
                        >

                        <button
                            type="button"
                            class="absolute right-1.5 top-1.5 flex h-7 w-7 items-center justify-center rounded-lg bg-black/75 text-white"
                            @click="removeGallery(media.id)"
                        >
                            <X class="h-3.5 w-3.5" />
                        </button>
                    </div>
                </div>
            </section>

            <!-- Multimedia -->

            <section class="adn-panel rounded-[22px] p-5 sm:p-6">
                <div
                    class="flex flex-col justify-between gap-4 sm:flex-row sm:items-center"
                >
                    <div class="flex items-center gap-3">
                        <Images class="h-5 w-5 text-[var(--adn-turquoise)]" />

                        <div>
                            <p class="text-xs font-black text-white/70">
                                Multimedia
                            </p>

                            <p class="mt-1 text-[10px] text-white/25">
                                Selecciona portada y galería desde la biblioteca.
                            </p>
                        </div>
                    </div>

                    <div class="relative sm:w-72">
                        <Search
                            class="absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-white/20"
                        />

                        <input
                            v-model="search"
                            type="search"
                            placeholder="Buscar..."
                            class="h-10 w-full rounded-xl border border-white/[0.06] bg-black/15 pl-10 pr-4 text-xs text-white outline-none"
                        >
                    </div>
                </div>

                <div
                    class="mt-5 grid max-h-[520px] grid-cols-2 gap-3 overflow-y-auto pr-1 sm:grid-cols-3 md:grid-cols-4 xl:grid-cols-6"
                >
                    <article
                        v-for="media in filteredMedia"
                        :key="media.id"
                        class="overflow-hidden rounded-xl border border-white/[0.06] bg-black/10"
                    >
                        <img
                            :src="media.url"
                            :alt="media.alt_text ?? ''"
                            class="aspect-square w-full object-cover"
                        >

                        <div class="space-y-2 p-2">
                            <button
                                type="button"
                                class="w-full rounded-lg px-2 py-2 text-[8px] font-black uppercase"
                                :class="
                                    form.main_media_id === media.id
                                        ? 'bg-[var(--adn-turquoise)] text-white'
                                        : 'bg-[var(--adn-turquoise-soft)] text-[var(--adn-turquoise)]'
                                "
                                @click="form.main_media_id = media.id"
                            >
                                {{
                                    form.main_media_id === media.id
                                        ? 'Portada'
                                        : 'Usar portada'
                                }}
                            </button>

                            <button
                                type="button"
                                class="w-full rounded-lg border px-2 py-2 text-[8px] font-black uppercase"
                                :class="
                                    form.gallery_media_ids.includes(media.id)
                                        ? 'border-[var(--adn-orange-border)] bg-[var(--adn-orange-soft)] text-[var(--adn-orange)]'
                                        : 'border-white/[0.06] text-white/35'
                                "
                                @click="toggleGallery(media.id)"
                            >
                                {{
                                    form.gallery_media_ids.includes(media.id)
                                        ? 'En galería'
                                        : 'Agregar'
                                }}
                            </button>
                        </div>
                    </article>
                </div>
            </section>

            <!-- SEO -->

            <section class="adn-panel rounded-[22px] p-5 sm:p-6">
                <p class="text-xs font-black text-white/70">
                    SEO
                </p>

                <div class="mt-5 grid gap-4">
                    <input
                        v-model="form.meta_title"
                        type="text"
                        placeholder="Título SEO"
                        class="h-11 rounded-xl border border-white/[0.07] bg-black/15 px-4 text-sm text-white"
                    >

                    <textarea
                        v-model="form.meta_description"
                        rows="3"
                        placeholder="Descripción SEO"
                        class="rounded-xl border border-white/[0.07] bg-black/15 px-4 py-3 text-sm text-white"
                    />
                </div>
            </section>

            <div class="sticky bottom-4 z-20 flex justify-end">
                <button
                    type="submit"
                    :disabled="form.processing"
                    class="adn-primary-button inline-flex h-12 min-w-44 items-center justify-center gap-2 rounded-xl px-5 text-sm font-black shadow-xl disabled:opacity-50"
                >
                    <Save class="h-4 w-4" />

                    {{
                        form.processing
                            ? 'Guardando...'
                            : 'Guardar proyecto'
                    }}
                </button>
            </div>
        </form>
    </EditorLayout>
</template>
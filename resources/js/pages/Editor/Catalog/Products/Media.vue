<script setup lang="ts">
import {
    Head,
    Link,
    router,
    useForm,
    usePage,
} from '@inertiajs/vue3';

import {
    ArrowLeft,
    Check,
    ExternalLink,
    ImagePlus,
    Images,
    Library,
    Search,
    Star,
    Trash2,
    Upload,
    X,
} from '@lucide/vue';

import {
    computed,
    ref,
} from 'vue';

import EditorLayout
    from '@/layouts/Editor/EditorLayout.vue';

interface MediaItem {
    id: number;
    title: string | null;
    original_name: string | null;
    mime_type: string | null;
    extension: string | null;
    alt_text: string | null;
    width: number | null;
    height: number | null;
    size: number | null;
    url: string;
}

interface ProductImage {
    id: number;
    media_id: number;
    sort_order: number;
    alt_text: string | null;
    media: MediaItem;
}

interface Product {
    id: number;
    code: string;
    name: string;
    slug: string;

    category: {
        id: number;
        name: string;
    } | null;

    main_media_id: number | null;
    main_media: MediaItem | null;
    images: ProductImage[];
}

interface SharedProps {
    flash?: {
        success?: string | null;
    };

    [key: string]: unknown;
}

const props =
    defineProps<{
        product: Product;
        availableMedia: MediaItem[];
    }>();

const page =
    usePage<SharedProps>();

const previewUrl =
    ref<string | null>(
        null,
    );

const activeMode =
    ref<'upload' | 'library'>(
        'upload',
    );

const mediaSearch =
    ref('');

const form =
    useForm<{
        file: File | null;
        title: string;
        alt_text: string;
    }>({
        file:
            null,

        title:
            props.product.name,

        alt_text:
            props.product.name,
    });

const attachForm =
    useForm<{
        media_id: number | null;
    }>({
        media_id:
            null,
    });

const filteredMedia =
    computed(
        () => {
            const search =
                mediaSearch.value
                    .trim()
                    .toLowerCase();

            if (
                search === ''
            ) {
                return props.availableMedia;
            }

            return props.availableMedia.filter(
                (
                    media,
                ) => {
                    const searchable =
                        [
                            media.title,
                            media.original_name,
                            media.alt_text,
                        ]
                            .filter(
                                Boolean,
                            )
                            .join(
                                ' '
                            )
                            .toLowerCase();

                    return searchable.includes(
                        search,
                    );
                },
            );
        },
    );

const selectImage =
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

        const file =
            input.files?.[0]
            ?? null;

        form.file =
            file;

        if (
            previewUrl.value
        ) {
            URL.revokeObjectURL(
                previewUrl.value,
            );
        }

        previewUrl.value =
            file
                ? URL.createObjectURL(
                    file,
                )
                : null;
    };

const clearSelectedUpload =
    () => {
        form.file =
            null;

        if (
            previewUrl.value
        ) {
            URL.revokeObjectURL(
                previewUrl.value,
            );
        }

        previewUrl.value =
            null;
    };

const uploadImage =
    () => {
        form.post(
            `/editor-preview/catalogo/productos/${props.product.id}/imagenes`,
            {
                forceFormData:
                    true,

                preserveScroll:
                    true,

                onSuccess:
                    () => {
                        clearSelectedUpload();

                        form.title =
                            props.product.name;

                        form.alt_text =
                            props.product.name;
                    },
            },
        );
    };

const attachExisting =
    (
        media:
            MediaItem,
    ) => {
        attachForm.media_id =
            media.id;

        attachForm.post(
            `/editor-preview/catalogo/productos/${props.product.id}/imagenes/existente`,
            {
                preserveScroll:
                    true,

                onSuccess:
                    () => {
                        attachForm.reset();
                    },
            },
        );
    };

const setMain =
    (
        image:
            ProductImage,
    ) => {
        router.put(
            `/editor-preview/catalogo/productos/${props.product.id}/imagenes/principal/${image.media_id}`,
            {},
            {
                preserveScroll:
                    true,
            },
        );
    };

const removeImage =
    (
        image:
            ProductImage,
    ) => {
        if (
            !window.confirm(
                '¿Retirar esta imagen del producto? El archivo seguirá disponible en Multimedia.',
            )
        ) {
            return;
        }

        router.delete(
            `/editor-preview/catalogo/productos/${props.product.id}/imagenes/${image.id}`,
            {
                preserveScroll:
                    true,
            },
        );
    };
</script>

<template>
    <Head
        :title="`Imágenes · ${product.name}`"
    />

    <EditorLayout
        title="Imágenes del producto"
        eyebrow="Catálogo ADN"
    >
        <div
            v-if="page.props.flash?.success"
            class="mb-5 flex items-center gap-3 rounded-2xl border border-[var(--adn-turquoise-border)] bg-[var(--adn-turquoise-soft)] px-5 py-4 text-sm font-bold text-[var(--adn-turquoise)]"
        >
            <Check
                class="h-4 w-4"
            />

            {{ page.props.flash.success }}
        </div>

        <section
            class="flex flex-col justify-between gap-5 lg:flex-row lg:items-center"
        >
            <div
                class="flex min-w-0 items-center gap-4"
            >
                <Link
                    href="/editor-preview/catalogo/productos"
                    class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl border border-white/[0.07] text-white/35 transition hover:bg-white/[0.04] hover:text-white"
                >
                    <ArrowLeft
                        class="h-4 w-4"
                    />
                </Link>

                <div
                    class="min-w-0"
                >
                    <p
                        class="text-[9px] font-black uppercase tracking-[0.16em] text-[var(--adn-orange)]"
                    >
                        {{ product.code }}
                    </p>

                    <h1
                        class="mt-1 truncate text-2xl font-black tracking-[-0.035em] sm:text-3xl"
                    >
                        {{ product.name }}
                    </h1>

                    <p
                        class="mt-1 text-xs text-white/30"
                    >
                        {{
                            product.category?.name
                            ?? 'Sin categoría'
                        }}
                    </p>
                </div>
            </div>

            <Link
                href="/editor-preview/multimedia"
                class="inline-flex h-10 items-center gap-2 rounded-xl border border-white/[0.07] px-4 text-[10px] font-black uppercase tracking-[0.08em] text-white/45 transition hover:bg-white/[0.04]"
            >
                <Images
                    class="h-4 w-4"
                />

                Multimedia

                <ExternalLink
                    class="h-3.5 w-3.5"
                />
            </Link>
        </section>

        <section
            class="mt-6 grid min-w-0 gap-5 xl:grid-cols-[minmax(0,1fr)_430px]"
        >
            <!-- CONTENIDO -->

            <div
                class="min-w-0 space-y-5"
            >
                <!-- Principal -->

                <section
                    class="adn-panel overflow-hidden rounded-[22px]"
                >
                    <div
                        class="border-b border-[var(--adn-border)] px-5 py-5 sm:px-6"
                    >
                        <h2
                            class="text-sm font-black"
                        >
                            Imagen principal
                        </h2>

                        <p
                            class="mt-1 text-[10px] text-white/28"
                        >
                            Es la imagen que representará el producto en el catálogo.
                        </p>
                    </div>

                    <div
                        v-if="product.main_media"
                        class="p-5 sm:p-6"
                    >
                        <div
                            class="relative overflow-hidden rounded-[20px] border border-[var(--adn-turquoise-border)] bg-black/20"
                        >
                            <img
                                :src="product.main_media.url"
                                :alt="
                                    product.main_media.alt_text
                                    ?? product.name
                                "
                                class="aspect-[16/9] w-full object-contain"
                            >

                            <div
                                class="absolute left-4 top-4 flex items-center gap-2 rounded-xl border border-[var(--adn-yellow-border)] bg-black/70 px-3 py-2 backdrop-blur-xl"
                            >
                                <Star
                                    class="h-3.5 w-3.5 fill-[var(--adn-yellow)] text-[var(--adn-yellow)]"
                                />

                                <span
                                    class="text-[8px] font-black uppercase tracking-[0.1em] text-[var(--adn-yellow)]"
                                >
                                    Principal
                                </span>
                            </div>
                        </div>
                    </div>

                    <div
                        v-else
                        class="flex min-h-64 flex-col items-center justify-center px-6 text-center"
                    >
                        <ImagePlus
                            class="h-10 w-10 text-white/12"
                        />

                        <p
                            class="mt-4 text-sm font-black text-white/50"
                        >
                            Sin imagen principal
                        </p>

                        <p
                            class="mt-1 text-xs text-white/25"
                        >
                            La primera imagen asociada se convertirá automáticamente en principal.
                        </p>
                    </div>
                </section>

                <!-- Galería -->

                <section
                    class="adn-panel overflow-hidden rounded-[22px]"
                >
                    <div
                        class="flex items-center justify-between gap-4 border-b border-[var(--adn-border)] px-5 py-5 sm:px-6"
                    >
                        <div>
                            <h2
                                class="text-sm font-black"
                            >
                                Galería
                            </h2>

                            <p
                                class="mt-1 text-[10px] text-white/28"
                            >
                                {{ product.images.length }}
                                imágenes asociadas al producto.
                            </p>
                        </div>

                        <Images
                            class="h-5 w-5 text-[var(--adn-orange)]"
                        />
                    </div>

                    <div
                        v-if="product.images.length"
                        class="grid gap-4 p-5 sm:grid-cols-2 sm:p-6 lg:grid-cols-3"
                    >
                        <article
                            v-for="image in product.images"
                            :key="image.id"
                            class="group overflow-hidden rounded-[18px] border border-white/[0.06] bg-black/15"
                        >
                            <div
                                class="relative aspect-square overflow-hidden bg-black/20"
                            >
                                <img
                                    :src="image.media.url"
                                    :alt="
                                        image.alt_text
                                        ?? product.name
                                    "
                                    class="h-full w-full object-cover transition duration-500 group-hover:scale-[1.03]"
                                >

                                <span
                                    v-if="
                                        product.main_media_id ===
                                        image.media_id
                                    "
                                    class="absolute left-3 top-3 rounded-lg bg-[var(--adn-yellow)] px-2 py-1 text-[7px] font-black uppercase tracking-[0.08em] text-[#1d1d1b]"
                                >
                                    Principal
                                </span>
                            </div>

                            <div
                                class="flex items-center gap-2 p-3"
                            >
                                <button
                                    v-if="
                                        product.main_media_id !==
                                        image.media_id
                                    "
                                    type="button"
                                    class="flex h-9 flex-1 items-center justify-center gap-2 rounded-xl border border-[var(--adn-yellow-border)] bg-[var(--adn-yellow-soft)] text-[9px] font-black uppercase text-[var(--adn-yellow)]"
                                    @click="
                                        setMain(
                                            image,
                                        )
                                    "
                                >
                                    <Star
                                        class="h-3.5 w-3.5"
                                    />

                                    Principal
                                </button>

                                <div
                                    v-else
                                    class="flex h-9 flex-1 items-center justify-center gap-2 rounded-xl border border-[var(--adn-turquoise-border)] bg-[var(--adn-turquoise-soft)] text-[9px] font-black uppercase text-[var(--adn-turquoise)]"
                                >
                                    <Check
                                        class="h-3.5 w-3.5"
                                    />

                                    Seleccionada
                                </div>

                                <button
                                    type="button"
                                    title="Retirar del producto"
                                    class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl border border-white/[0.06] text-white/25 transition hover:border-[var(--adn-coral-border)] hover:bg-[var(--adn-coral-soft)] hover:text-[var(--adn-coral)]"
                                    @click="
                                        removeImage(
                                            image,
                                        )
                                    "
                                >
                                    <Trash2
                                        class="h-4 w-4"
                                    />
                                </button>
                            </div>
                        </article>
                    </div>

                    <div
                        v-else
                        class="flex min-h-64 flex-col items-center justify-center px-6 text-center"
                    >
                        <Images
                            class="h-10 w-10 text-white/12"
                        />

                        <p
                            class="mt-4 text-sm font-black text-white/50"
                        >
                            Galería vacía
                        </p>

                        <p
                            class="mt-1 text-xs text-white/25"
                        >
                            Sube una imagen nueva o elige una existente de Multimedia.
                        </p>
                    </div>
                </section>
            </div>

            <!-- PANEL DERECHO -->

            <aside
                class="min-w-0"
            >
                <section
                    class="adn-panel sticky top-28 min-w-0 overflow-hidden rounded-[22px]"
                >
                    <!-- Tabs -->

                    <div
                        class="grid grid-cols-2 border-b border-[var(--adn-border)]"
                    >
                        <button
                            type="button"
                            class="flex h-14 items-center justify-center gap-2 border-r border-[var(--adn-border)] text-[10px] font-black uppercase tracking-[0.08em] transition"
                            :class="
                                activeMode === 'upload'
                                    ? 'bg-[var(--adn-turquoise-soft)] text-[var(--adn-turquoise)]'
                                    : 'text-white/30 hover:bg-white/[0.025] hover:text-white/60'
                            "
                            @click="
                                activeMode =
                                    'upload'
                            "
                        >
                            <Upload
                                class="h-4 w-4"
                            />

                            Subir nueva
                        </button>

                        <button
                            type="button"
                            class="flex h-14 items-center justify-center gap-2 text-[10px] font-black uppercase tracking-[0.08em] transition"
                            :class="
                                activeMode === 'library'
                                    ? 'bg-[var(--adn-orange-soft)] text-[var(--adn-orange)]'
                                    : 'text-white/30 hover:bg-white/[0.025] hover:text-white/60'
                            "
                            @click="
                                activeMode =
                                    'library'
                            "
                        >
                            <Library
                                class="h-4 w-4"
                            />

                            Multimedia
                        </button>
                    </div>

                    <!-- Subir -->

                    <form
                        v-if="
                            activeMode ===
                            'upload'
                        "
                        class="p-5"
                        @submit.prevent="uploadImage"
                    >
                        <p
                            class="text-[9px] font-black uppercase tracking-[0.16em] text-[var(--adn-turquoise)]"
                        >
                            Nueva imagen
                        </p>

                        <h2
                            class="mt-1 text-lg font-black"
                        >
                            Subir fotografía
                        </h2>

                        <div
                            class="relative mt-5 overflow-hidden rounded-[18px] border border-dashed border-[var(--adn-turquoise-border)] bg-[var(--adn-turquoise-soft)]"
                        >
                            <label
                                class="flex aspect-[4/3] cursor-pointer flex-col items-center justify-center overflow-hidden text-center"
                            >
                                <img
                                    v-if="previewUrl"
                                    :src="previewUrl"
                                    alt="Vista previa"
                                    class="h-full w-full object-contain"
                                >

                                <template
                                    v-else
                                >
                                    <Upload
                                        class="h-8 w-8 text-[var(--adn-turquoise)]"
                                    />

                                    <p
                                        class="mt-3 text-xs font-black text-white/55"
                                    >
                                        Seleccionar imagen
                                    </p>

                                    <p
                                        class="mt-1 text-[9px] text-white/25"
                                    >
                                        JPG, PNG o WEBP
                                    </p>
                                </template>

                                <input
                                    type="file"
                                    accept=".jpg,.jpeg,.png,.webp"
                                    class="sr-only"
                                    @change="selectImage"
                                >
                            </label>

                            <button
                                v-if="form.file"
                                type="button"
                                title="Quitar selección"
                                class="absolute right-3 top-3 flex h-8 w-8 items-center justify-center rounded-lg border border-white/10 bg-black/70 text-white/50 backdrop-blur-xl hover:text-white"
                                @click="
                                    clearSelectedUpload
                                "
                            >
                                <X
                                    class="h-4 w-4"
                                />
                            </button>
                        </div>

                        <p
                            v-if="form.file"
                            class="mt-3 truncate text-[10px] text-white/30"
                        >
                            {{ form.file.name }}
                        </p>

                        <p
                            v-if="form.errors.file"
                            class="mt-2 text-xs font-bold text-[var(--adn-coral)]"
                        >
                            {{ form.errors.file }}
                        </p>

                        <div
                            class="mt-5 space-y-4"
                        >
                            <label
                                class="block"
                            >
                                <span
                                    class="mb-2 block text-xs font-black text-white/50"
                                >
                                    Título
                                </span>

                                <input
                                    v-model="form.title"
                                    type="text"
                                    maxlength="255"
                                    class="h-11 w-full rounded-xl border border-white/[0.07] bg-black/15 px-4 text-sm text-white outline-none"
                                >
                            </label>

                            <label
                                class="block"
                            >
                                <span
                                    class="mb-2 block text-xs font-black text-white/50"
                                >
                                    Texto alternativo
                                </span>

                                <input
                                    v-model="form.alt_text"
                                    type="text"
                                    maxlength="255"
                                    class="h-11 w-full rounded-xl border border-white/[0.07] bg-black/15 px-4 text-sm text-white outline-none"
                                >

                                <p
                                    class="mt-2 text-[9px] leading-4 text-white/22"
                                >
                                    Describe brevemente la imagen para accesibilidad y buscadores.
                                </p>
                            </label>
                        </div>

                        <button
                            type="submit"
                            :disabled="
                                form.processing
                                ||
                                !form.file
                            "
                            class="adn-primary-button mt-5 flex h-12 w-full items-center justify-center gap-2 rounded-xl text-xs font-black disabled:cursor-not-allowed disabled:opacity-35"
                        >
                            <Upload
                                class="h-4 w-4"
                            />

                            {{
                                form.processing
                                    ? 'Subiendo...'
                                    : 'Agregar imagen'
                            }}
                        </button>
                    </form>

                    <!-- Biblioteca -->

                    <div
                        v-else
                        class="p-5"
                    >
                        <p
                            class="text-[9px] font-black uppercase tracking-[0.16em] text-[var(--adn-orange)]"
                        >
                            Biblioteca
                        </p>

                        <h2
                            class="mt-1 text-lg font-black"
                        >
                            Elegir de Multimedia
                        </h2>

                        <p
                            class="mt-1 text-[10px] leading-4 text-white/28"
                        >
                            Reutiliza una imagen ya almacenada sin volver a subir el archivo.
                        </p>

                        <label
                            class="relative mt-5 block"
                        >
                            <Search
                                class="absolute left-3.5 top-1/2 h-4 w-4 -translate-y-1/2 text-white/22"
                            />

                            <input
                                v-model="mediaSearch"
                                type="search"
                                placeholder="Buscar imagen..."
                                class="h-11 w-full rounded-xl border border-white/[0.07] bg-black/15 pl-10 pr-4 text-xs text-white outline-none placeholder:text-white/18"
                            >
                        </label>

                        <div
                            v-if="filteredMedia.length"
                            class="adn-media-picker mt-4 grid max-h-[550px] grid-cols-2 gap-3 overflow-y-auto pr-1"
                        >
                            <article
                                v-for="media in filteredMedia"
                                :key="media.id"
                                class="group overflow-hidden rounded-xl border border-white/[0.06] bg-black/15"
                            >
                                <div
                                    class="aspect-square overflow-hidden bg-black/20"
                                >
                                    <img
                                        :src="media.url"
                                        :alt="
                                            media.alt_text
                                            ?? media.title
                                            ?? ''
                                        "
                                        class="h-full w-full object-cover transition duration-300 group-hover:scale-[1.04]"
                                    >
                                </div>

                                <div
                                    class="p-2.5"
                                >
                                    <p
                                        class="truncate text-[10px] font-black text-white/55"
                                    >
                                        {{
                                            media.title
                                            ?? media.original_name
                                        }}
                                    </p>

                                    <p
                                        v-if="
                                            media.width
                                            &&
                                            media.height
                                        "
                                        class="mt-1 text-[8px] text-white/20"
                                    >
                                        {{ media.width }}
                                        ×
                                        {{ media.height }}
                                        px
                                    </p>

                                    <button
                                        type="button"
                                        :disabled="
                                            attachForm.processing
                                        "
                                        class="mt-2 flex h-8 w-full items-center justify-center gap-1.5 rounded-lg border border-[var(--adn-orange-border)] bg-[var(--adn-orange-soft)] text-[8px] font-black uppercase tracking-[0.07em] text-[var(--adn-orange)] transition hover:-translate-y-px disabled:opacity-40"
                                        @click="
                                            attachExisting(
                                                media,
                                            )
                                        "
                                    >
                                        <ImagePlus
                                            class="h-3.5 w-3.5"
                                        />

                                        Usar imagen
                                    </button>
                                </div>
                            </article>
                        </div>

                        <div
                            v-else
                            class="mt-4 flex min-h-52 flex-col items-center justify-center rounded-xl border border-dashed border-white/[0.06] px-4 text-center"
                        >
                            <Library
                                class="h-8 w-8 text-white/10"
                            />

                            <p
                                class="mt-3 text-xs font-black text-white/40"
                            >
                                {{
                                    mediaSearch
                                        ? 'No encontramos coincidencias'
                                        : 'No hay imágenes disponibles'
                                }}
                            </p>

                            <p
                                class="mt-1 max-w-[240px] text-[9px] leading-4 text-white/20"
                            >
                                Las imágenes que ya están asociadas a este producto no aparecen nuevamente aquí.
                            </p>
                        </div>

                        <Link
                            href="/editor-preview/multimedia"
                            class="mt-4 flex h-10 w-full items-center justify-center gap-2 rounded-xl border border-white/[0.07] text-[9px] font-black uppercase tracking-[0.08em] text-white/35 transition hover:bg-white/[0.035] hover:text-white/60"
                        >
                            <ExternalLink
                                class="h-3.5 w-3.5"
                            />

                            Abrir biblioteca completa
                        </Link>
                    </div>
                </section>
            </aside>
        </section>
    </EditorLayout>
</template>

<style scoped>
.adn-media-picker {
    scrollbar-width:
        thin;

    scrollbar-color:
        rgba(237, 126, 36, 0.25)
        transparent;
}

.adn-media-picker::-webkit-scrollbar {
    width:
        5px;
}

.adn-media-picker::-webkit-scrollbar-track {
    background:
        transparent;
}

.adn-media-picker::-webkit-scrollbar-thumb {
    border-radius:
        999px;

    background:
        rgba(237, 126, 36, 0.25);
}
</style>
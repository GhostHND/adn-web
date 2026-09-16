<script setup lang="ts">
import {
    Head,
    router,
    useForm,
    usePage,
} from '@inertiajs/vue3';

import {
    CalendarClock,
    CheckCircle2,
    ExternalLink,
    ImagePlus,
    Megaphone,
    Pencil,
    Save,
    Trash2,
    X,
} from '@lucide/vue';

import {
    computed,
    ref,
} from 'vue';

import EditorLayout
    from '@/layouts/Editor/EditorLayout.vue';

interface WebsiteAd {
    id: number;
    name: string;
    placement: string;
    platform: string | null;
    image_url: string;
    alt_text: string | null;
    link_url: string | null;
    cta_label: string | null;
    open_in_new_tab: boolean;
    active: boolean;
    sort_order: number;
    starts_at: string | null;
    ends_at: string | null;
    visibility:
        | 'active'
        | 'scheduled'
        | 'expired'
        | 'inactive';
}

interface PlacementOption {
    value: string;
    label: string;
    size: string;
    description: string;
}

interface PlatformOption {
    value: string;
    label: string;
}

interface Summary {
    total: number;
    active: number;
    scheduled: number;
    inactive: number;
}

interface AdForm {
    name: string;
    placement: string;
    platform: string;
    image: File | null;
    alt_text: string;
    link_url: string;
    cta_label: string;
    open_in_new_tab: boolean;
    active: boolean;
    sort_order: number;
    starts_at: string;
    ends_at: string;
}

interface SharedProps {
    flash?: {
        success?: string | null;
    };

    [key: string]: unknown;
}

const props =
    defineProps<{
        ads: WebsiteAd[];
        placements: PlacementOption[];
        platforms: PlatformOption[];
        summary: Summary;
    }>();

const page =
    usePage<SharedProps>();

const firstPlacement =
    props.placements[0]?.value
    ?? 'catalog_sidebar';

const form =
    useForm<AdForm>({
        name:
            '',

        placement:
            firstPlacement,

        platform:
            'general',

        image:
            null,

        alt_text:
            '',

        link_url:
            '',

        cta_label:
            '',

        open_in_new_tab:
            true,

        active:
            true,

        sort_order:
            0,

        starts_at:
            '',

        ends_at:
            '',
    });

const editingId =
    ref<number | null>(
        null,
    );

const currentImageUrl =
    ref<string | null>(
        null,
    );

const localPreviewUrl =
    ref<string | null>(
        null,
    );

const isEditing =
    computed(
        () =>
            editingId.value !==
            null,
    );

const selectedPlacement =
    computed(
        () =>
            props.placements.find(
                (
                    placement,
                ) =>
                    placement.value ===
                    form.placement,
            )
            ?? null,
    );

const previewUrl =
    computed(
        () =>
            localPreviewUrl.value
            ??
            currentImageUrl.value,
    );

const platformLabel =
    (
        value:
            string | null,
    ): string => {
        return props.platforms.find(
            (
                platform,
            ) =>
                platform.value ===
                value,
        )?.label
        ?? 'General';
    };

const placementLabel =
    (
        value:
            string,
    ): string => {
        return props.placements.find(
            (
                placement,
            ) =>
                placement.value ===
                value,
        )?.label
        ?? value;
    };

const visibilityLabel =
    (
        value:
            WebsiteAd[
                'visibility'
            ],
    ): string => {
        const labels:
            Record<
                WebsiteAd[
                    'visibility'
                ],
                string
            > = {
            active:
                'Activo',

            scheduled:
                'Programado',

            expired:
                'Finalizado',

            inactive:
                'Inactivo',
        };

        return labels[
            value
        ];
    };

const visibilityClasses =
    (
        value:
            WebsiteAd[
                'visibility'
            ],
    ): string => {
        if (
            value ===
            'active'
        ) {
            return 'border-emerald-500/20 bg-emerald-500/10 text-emerald-400';
        }

        if (
            value ===
            'scheduled'
        ) {
            return 'border-[var(--adn-yellow-border)] bg-[var(--adn-yellow-soft)] text-[var(--adn-yellow)]';
        }

        if (
            value ===
            'expired'
        ) {
            return 'border-[var(--adn-coral-border)] bg-[var(--adn-coral-soft)] text-[var(--adn-coral)]';
        }

        return 'border-white/[0.07] bg-white/[0.025] text-white/30';
    };

const toInputDate =
    (
        value:
            string | null,
    ): string => {
        if (
            !value
        ) {
            return '';
        }

        return value.slice(
            0,
            16,
        );
    };

const clearPreview =
    (): void => {
        if (
            localPreviewUrl.value
        ) {
            URL.revokeObjectURL(
                localPreviewUrl.value,
            );

            localPreviewUrl.value =
                null;
        }
    };

const handleImage =
    (
        event:
            Event,
    ): void => {
        const input =
            event.currentTarget as HTMLInputElement;

        const file =
            input.files?.item(
                0,
            )
            ?? null;

        clearPreview();

        form.image =
            file;

        if (
            file
        ) {
            localPreviewUrl.value =
                URL.createObjectURL(
                    file,
                );
        }
    };

const resetEditor =
    (): void => {
        clearPreview();

        editingId.value =
            null;

        currentImageUrl.value =
            null;

        form.reset();

        form.clearErrors();

        form.placement =
            firstPlacement;

        form.platform =
            'general';

        form.open_in_new_tab =
            true;

        form.active =
            true;

        form.sort_order =
            0;
    };

const editAd =
    (
        ad:
            WebsiteAd,
    ): void => {
        clearPreview();

        editingId.value =
            ad.id;

        currentImageUrl.value =
            ad.image_url;

        form.clearErrors();

        form.name =
            ad.name;

        form.placement =
            ad.placement;

        form.platform =
            ad.platform
            ?? 'general';

        form.image =
            null;

        form.alt_text =
            ad.alt_text
            ?? '';

        form.link_url =
            ad.link_url
            ?? '';

        form.cta_label =
            ad.cta_label
            ?? '';

        form.open_in_new_tab =
            ad.open_in_new_tab;

        form.active =
            ad.active;

        form.sort_order =
            ad.sort_order;

        form.starts_at =
            toInputDate(
                ad.starts_at,
            );

        form.ends_at =
            toInputDate(
                ad.ends_at,
            );

        window.scrollTo({
            top:
                0,

            behavior:
                'smooth',
        });
    };

const submit =
    (): void => {
        if (
            isEditing.value
        ) {
            form.transform(
                (
                    data,
                ) => ({
                    ...data,

                    _method:
                        'put',

                    platform:
                        data.platform
                        || null,

                    alt_text:
                        data.alt_text
                        || null,

                    link_url:
                        data.link_url
                        || null,

                    cta_label:
                        data.cta_label
                        || null,

                    starts_at:
                        data.starts_at
                        || null,

                    ends_at:
                        data.ends_at
                        || null,
                }),
            ).post(
                `/editor-preview/publicidad/${editingId.value}`,
                {
                    forceFormData:
                        true,

                    preserveScroll:
                        true,

                    onSuccess:
                        () => {
                            resetEditor();
                        },
                },
            );

            return;
        }

        form.transform(
            (
                data,
            ) => ({
                ...data,

                platform:
                    data.platform
                    || null,

                alt_text:
                    data.alt_text
                    || null,

                link_url:
                    data.link_url
                    || null,

                cta_label:
                    data.cta_label
                    || null,

                starts_at:
                    data.starts_at
                    || null,

                ends_at:
                    data.ends_at
                    || null,
            }),
        ).post(
            '/editor-preview/publicidad',
            {
                forceFormData:
                    true,

                preserveScroll:
                    true,

                onSuccess:
                    () => {
                        resetEditor();
                    },
            },
        );
    };

const removeAd =
    (
        ad:
            WebsiteAd,
    ): void => {
        if (
            !window.confirm(
                `¿Archivar la publicidad "${ad.name}"?`,
            )
        ) {
            return;
        }

        router.delete(
            `/editor-preview/publicidad/${ad.id}`,
            {
                preserveScroll:
                    true,
            },
        );
    };
</script>

<template>
    <Head
        title="Publicidad"
    />

    <EditorLayout
        title="Publicidad"
        eyebrow="Contenido del sitio"
    >
        <div
            v-if="
                page.props.flash?.success
            "
            class="mb-5 flex items-center gap-3 rounded-2xl border border-[var(--adn-turquoise-border)] bg-[var(--adn-turquoise-soft)] px-5 py-4 text-sm font-bold text-[var(--adn-turquoise)]"
        >
            <CheckCircle2
                class="h-4 w-4"
            />

            {{
                page.props.flash.success
            }}
        </div>

        <section
            class="flex flex-col justify-between gap-5 xl:flex-row xl:items-end"
        >
            <div>
                <p
                    class="text-[9px] font-black uppercase tracking-[0.16em] text-[var(--adn-orange)]"
                >
                    Comunicación visual
                </p>

                <h1
                    class="mt-2 text-3xl font-black tracking-[-0.04em]"
                >
                    Publicidad del sitio
                </h1>

                <p
                    class="mt-2 max-w-3xl text-xs leading-6 text-white/32"
                >
                    Administra artes para redes sociales, campañas y promociones
                    sin modificar el diseño ni el código del sitio.
                </p>
            </div>

            <div
                class="rounded-2xl border border-[var(--adn-turquoise-border)] bg-[var(--adn-turquoise-soft)] px-5 py-4"
            >
                <p
                    class="text-[9px] font-black uppercase tracking-[0.12em] text-[var(--adn-turquoise)]"
                >
                    Integración sutil
                </p>

                <p
                    class="mt-1 text-[10px] text-white/35"
                >
                    Los espacios desaparecen automáticamente si no hay artes activas.
                </p>
            </div>
        </section>

        <section
            class="mt-6 grid gap-3 sm:grid-cols-2 xl:grid-cols-4"
        >
            <article
                class="adn-panel rounded-[18px] p-5"
            >
                <p
                    class="text-[9px] font-black uppercase tracking-[0.12em] text-white/24"
                >
                    Artes
                </p>

                <p
                    class="mt-2 text-2xl font-black text-white"
                >
                    {{
                        summary.total
                    }}
                </p>
            </article>

            <article
                class="adn-panel rounded-[18px] p-5"
            >
                <p
                    class="text-[9px] font-black uppercase tracking-[0.12em] text-white/24"
                >
                    Activas
                </p>

                <p
                    class="mt-2 text-2xl font-black text-emerald-400"
                >
                    {{
                        summary.active
                    }}
                </p>
            </article>

            <article
                class="adn-panel rounded-[18px] p-5"
            >
                <p
                    class="text-[9px] font-black uppercase tracking-[0.12em] text-white/24"
                >
                    Programadas
                </p>

                <p
                    class="mt-2 text-2xl font-black text-[var(--adn-yellow)]"
                >
                    {{
                        summary.scheduled
                    }}
                </p>
            </article>

            <article
                class="adn-panel rounded-[18px] p-5"
            >
                <p
                    class="text-[9px] font-black uppercase tracking-[0.12em] text-white/24"
                >
                    Inactivas
                </p>

                <p
                    class="mt-2 text-2xl font-black text-white/35"
                >
                    {{
                        summary.inactive
                    }}
                </p>
            </article>
        </section>

        <div
            class="mt-6 grid gap-5 xl:grid-cols-[420px_minmax(0,1fr)]"
        >
            <!-- Editor -->

            <form
                class="adn-panel h-fit rounded-[22px] p-5 xl:sticky xl:top-24"
                @submit.prevent="
                    submit
                "
            >
                <div
                    class="flex items-start justify-between gap-4"
                >
                    <div
                        class="flex items-center gap-3"
                    >
                        <span
                            class="flex h-10 w-10 items-center justify-center rounded-xl bg-[var(--adn-orange-soft)] text-[var(--adn-orange)]"
                        >
                            <Megaphone
                                class="h-5 w-5"
                            />
                        </span>

                        <div>
                            <h2
                                class="text-sm font-black"
                            >
                                {{
                                    isEditing
                                        ? 'Editar arte'
                                        : 'Nueva publicidad'
                                }}
                            </h2>

                            <p
                                class="mt-0.5 text-[10px] text-white/25"
                            >
                                Configuración del espacio publicitario.
                            </p>
                        </div>
                    </div>

                    <button
                        v-if="
                            isEditing
                        "
                        type="button"
                        class="flex h-9 w-9 items-center justify-center rounded-xl border border-white/[0.06] text-white/30 hover:bg-white/[0.04]"
                        @click="
                            resetEditor
                        "
                    >
                        <X
                            class="h-4 w-4"
                        />
                    </button>
                </div>

                <div
                    class="mt-6 space-y-5"
                >
                    <label>
                        <span
                            class="mb-2 block text-xs font-black text-white/55"
                        >
                            Nombre interno
                        </span>

                        <input
                            v-model="
                                form.name
                            "
                            type="text"
                            maxlength="150"
                            placeholder="Ej. Instagram · Catálogo lateral"
                            class="h-11 w-full rounded-xl border border-white/[0.07] bg-black/15 px-4 text-sm text-white outline-none placeholder:text-white/18"
                        >

                        <p
                            v-if="
                                form.errors.name
                            "
                            class="mt-2 text-xs font-bold text-[var(--adn-coral)]"
                        >
                            {{
                                form.errors.name
                            }}
                        </p>
                    </label>

                    <label>
                        <span
                            class="mb-2 block text-xs font-black text-white/55"
                        >
                            Ubicación
                        </span>

                        <select
                            v-model="
                                form.placement
                            "
                            class="h-11 w-full rounded-xl border border-white/[0.07] bg-[#101719] px-4 text-sm text-white/70"
                        >
                            <option
                                v-for="
                                    placement in placements
                                "
                                :key="
                                    placement.value
                                "
                                :value="
                                    placement.value
                                "
                            >
                                {{
                                    placement.label
                                }}
                                ·
                                {{
                                    placement.size
                                }}
                            </option>
                        </select>

                        <div
                            v-if="
                                selectedPlacement
                            "
                            class="mt-2 rounded-xl border border-white/[0.05] bg-white/[0.02] px-3 py-2.5"
                        >
                            <p
                                class="text-[9px] font-black uppercase text-[var(--adn-turquoise)]"
                            >
                                Recomendado:
                                {{
                                    selectedPlacement.size
                                }}
                            </p>

                            <p
                                class="mt-1 text-[10px] leading-5 text-white/25"
                            >
                                {{
                                    selectedPlacement.description
                                }}
                            </p>
                        </div>
                    </label>

                    <label>
                        <span
                            class="mb-2 block text-xs font-black text-white/55"
                        >
                            Red / campaña
                        </span>

                        <select
                            v-model="
                                form.platform
                            "
                            class="h-11 w-full rounded-xl border border-white/[0.07] bg-[#101719] px-4 text-sm text-white/70"
                        >
                            <option
                                v-for="
                                    platform in platforms
                                "
                                :key="
                                    platform.value
                                "
                                :value="
                                    platform.value
                                "
                            >
                                {{
                                    platform.label
                                }}
                            </option>
                        </select>
                    </label>

                    <label>
                        <span
                            class="mb-2 block text-xs font-black text-white/55"
                        >
                            Arte
                        </span>

                        <div
                            class="overflow-hidden rounded-2xl border border-dashed border-white/[0.09] bg-black/15"
                        >
                            <div
                                v-if="
                                    previewUrl
                                "
                                class="border-b border-white/[0.06] p-3"
                            >
                                <img
                                    :src="
                                        previewUrl
                                    "
                                    alt="Vista previa"
                                    class="max-h-64 w-full rounded-xl object-contain"
                                >
                            </div>

                            <label
                                class="flex cursor-pointer items-center justify-center gap-2 px-4 py-4 text-xs font-black text-white/40 transition hover:text-[var(--adn-turquoise)]"
                            >
                                <ImagePlus
                                    class="h-4 w-4"
                                />

                                {{
                                    isEditing
                                        ? 'Cambiar arte'
                                        : 'Seleccionar arte'
                                }}

                                <input
                                    type="file"
                                    accept=".jpg,.jpeg,.png,.webp,.avif"
                                    class="hidden"
                                    @change="
                                        handleImage
                                    "
                                >
                            </label>
                        </div>

                        <p
                            v-if="
                                form.errors.image
                            "
                            class="mt-2 text-xs font-bold text-[var(--adn-coral)]"
                        >
                            {{
                                form.errors.image
                            }}
                        </p>
                    </label>

                    <label>
                        <span
                            class="mb-2 block text-xs font-black text-white/55"
                        >
                            Enlace
                        </span>

                        <input
                            v-model="
                                form.link_url
                            "
                            type="url"
                            inputmode="url"
                            placeholder="https://..."
                            class="h-11 w-full rounded-xl border border-white/[0.07] bg-black/15 px-4 text-sm text-white outline-none placeholder:text-white/18"
                        >
                    </label>

                    <div
                        class="grid gap-4 sm:grid-cols-2 xl:grid-cols-1 2xl:grid-cols-2"
                    >
                        <label>
                            <span
                                class="mb-2 block text-xs font-black text-white/55"
                            >
                                CTA
                            </span>

                            <input
                                v-model="
                                    form.cta_label
                                "
                                type="text"
                                maxlength="80"
                                placeholder="Síguenos"
                                class="h-11 w-full rounded-xl border border-white/[0.07] bg-black/15 px-4 text-sm text-white outline-none placeholder:text-white/18"
                            >
                        </label>

                        <label>
                            <span
                                class="mb-2 block text-xs font-black text-white/55"
                            >
                                Orden
                            </span>

                            <input
                                v-model.number="
                                    form.sort_order
                                "
                                type="number"
                                min="0"
                                max="65535"
                                class="h-11 w-full rounded-xl border border-white/[0.07] bg-black/15 px-4 text-sm text-white outline-none"
                            >
                        </label>
                    </div>

                    <label>
                        <span
                            class="mb-2 block text-xs font-black text-white/55"
                        >
                            Texto alternativo
                        </span>

                        <input
                            v-model="
                                form.alt_text
                            "
                            type="text"
                            maxlength="255"
                            placeholder="Promoción de Instagram de ADN Publicidad"
                            class="h-11 w-full rounded-xl border border-white/[0.07] bg-black/15 px-4 text-sm text-white outline-none placeholder:text-white/18"
                        >
                    </label>

                    <div
                        class="grid gap-4 sm:grid-cols-2 xl:grid-cols-1 2xl:grid-cols-2"
                    >
                        <label>
                            <span
                                class="mb-2 block text-xs font-black text-white/55"
                            >
                                Inicia
                            </span>

                            <input
                                v-model="
                                    form.starts_at
                                "
                                type="datetime-local"
                                class="h-11 w-full rounded-xl border border-white/[0.07] bg-black/15 px-3 text-xs text-white/65 outline-none"
                            >
                        </label>

                        <label>
                            <span
                                class="mb-2 block text-xs font-black text-white/55"
                            >
                                Finaliza
                            </span>

                            <input
                                v-model="
                                    form.ends_at
                                "
                                type="datetime-local"
                                class="h-11 w-full rounded-xl border border-white/[0.07] bg-black/15 px-3 text-xs text-white/65 outline-none"
                            >
                        </label>
                    </div>

                    <div
                        class="space-y-3 rounded-2xl border border-white/[0.06] bg-white/[0.02] p-4"
                    >
                        <label
                            class="flex cursor-pointer items-center justify-between gap-3"
                        >
                            <div>
                                <p
                                    class="text-xs font-black text-white/60"
                                >
                                    Publicidad activa
                                </p>

                                <p
                                    class="mt-1 text-[10px] text-white/24"
                                >
                                    Puede mostrarse en el sitio según su vigencia.
                                </p>
                            </div>

                            <input
                                v-model="
                                    form.active
                                "
                                type="checkbox"
                                class="h-4 w-4 accent-[#0FA7B4]"
                            >
                        </label>

                        <label
                            class="flex cursor-pointer items-center justify-between gap-3"
                        >
                            <div>
                                <p
                                    class="text-xs font-black text-white/60"
                                >
                                    Abrir enlace aparte
                                </p>

                                <p
                                    class="mt-1 text-[10px] text-white/24"
                                >
                                    Recomendado para redes sociales.
                                </p>
                            </div>

                            <input
                                v-model="
                                    form.open_in_new_tab
                                "
                                type="checkbox"
                                class="h-4 w-4 accent-[#ED7E24]"
                            >
                        </label>
                    </div>

                    <button
                        type="submit"
                        :disabled="
                            form.processing
                        "
                        class="adn-primary-button flex h-12 w-full items-center justify-center gap-2 rounded-xl text-sm font-black disabled:opacity-50"
                    >
                        <Save
                            class="h-4 w-4"
                        />

                        {{
                            form.processing
                                ? 'Guardando...'
                                : isEditing
                                    ? 'Guardar cambios'
                                    : 'Crear publicidad'
                        }}
                    </button>
                </div>
            </form>

            <!-- Lista -->

            <section
                class="min-w-0"
            >
                <div
                    v-if="
                        ads.length
                    "
                    class="grid gap-4 lg:grid-cols-2"
                >
                    <article
                        v-for="
                            ad in ads
                        "
                        :key="
                            ad.id
                        "
                        class="adn-panel overflow-hidden rounded-[22px]"
                    >
                        <div
                            class="aspect-[16/7] overflow-hidden border-b border-white/[0.06] bg-black/15"
                        >
                            <img
                                :src="
                                    ad.image_url
                                "
                                :alt="
                                    ad.alt_text
                                    ?? ad.name
                                "
                                class="h-full w-full object-cover"
                            >
                        </div>

                        <div
                            class="p-5"
                        >
                            <div
                                class="flex items-start justify-between gap-4"
                            >
                                <div
                                    class="min-w-0"
                                >
                                    <span
                                        class="inline-flex rounded-lg border px-2 py-1 text-[8px] font-black uppercase tracking-[0.08em]"
                                        :class="
                                            visibilityClasses(
                                                ad.visibility,
                                            )
                                        "
                                    >
                                        {{
                                            visibilityLabel(
                                                ad.visibility,
                                            )
                                        }}
                                    </span>

                                    <h2
                                        class="mt-3 truncate text-base font-black text-white/80"
                                    >
                                        {{
                                            ad.name
                                        }}
                                    </h2>

                                    <p
                                        class="mt-1 text-[10px] text-white/28"
                                    >
                                        {{
                                            placementLabel(
                                                ad.placement,
                                            )
                                        }}
                                        ·
                                        {{
                                            platformLabel(
                                                ad.platform,
                                            )
                                        }}
                                    </p>
                                </div>

                                <div
                                    class="flex shrink-0 gap-2"
                                >
                                    <button
                                        type="button"
                                        title="Editar"
                                        class="flex h-9 w-9 items-center justify-center rounded-xl border border-white/[0.06] text-white/35 transition hover:border-[var(--adn-turquoise-border)] hover:bg-[var(--adn-turquoise-soft)] hover:text-[var(--adn-turquoise)]"
                                        @click="
                                            editAd(
                                                ad,
                                            )
                                        "
                                    >
                                        <Pencil
                                            class="h-4 w-4"
                                        />
                                    </button>

                                    <button
                                        type="button"
                                        title="Archivar"
                                        class="flex h-9 w-9 items-center justify-center rounded-xl border border-white/[0.06] text-white/25 transition hover:border-[var(--adn-coral-border)] hover:bg-[var(--adn-coral-soft)] hover:text-[var(--adn-coral)]"
                                        @click="
                                            removeAd(
                                                ad,
                                            )
                                        "
                                    >
                                        <Trash2
                                            class="h-4 w-4"
                                        />
                                    </button>
                                </div>
                            </div>

                            <div
                                class="mt-4 grid gap-2 sm:grid-cols-2"
                            >
                                <div
                                    class="rounded-xl border border-white/[0.05] bg-black/10 px-3 py-3"
                                >
                                    <p
                                        class="text-[8px] font-black uppercase tracking-[0.09em] text-white/20"
                                    >
                                        Vigencia
                                    </p>

                                    <p
                                        class="mt-1 flex items-center gap-1.5 text-[10px] font-bold text-white/42"
                                    >
                                        <CalendarClock
                                            class="h-3.5 w-3.5"
                                        />

                                        {{
                                            ad.starts_at
                                                ? new Date(
                                                    ad.starts_at,
                                                ).toLocaleDateString(
                                                    'es-HN',
                                                )
                                                : 'Sin fecha inicial'
                                        }}
                                    </p>
                                </div>

                                <div
                                    class="rounded-xl border border-white/[0.05] bg-black/10 px-3 py-3"
                                >
                                    <p
                                        class="text-[8px] font-black uppercase tracking-[0.09em] text-white/20"
                                    >
                                        Prioridad
                                    </p>

                                    <p
                                        class="mt-1 text-[10px] font-bold text-white/42"
                                    >
                                        Orden
                                        {{
                                            ad.sort_order
                                        }}
                                    </p>
                                </div>
                            </div>

                            <a
                                v-if="
                                    ad.link_url
                                "
                                :href="
                                    ad.link_url
                                "
                                target="_blank"
                                rel="noopener noreferrer"
                                class="mt-4 inline-flex items-center gap-2 text-[10px] font-black text-[var(--adn-turquoise)]"
                            >
                                <ExternalLink
                                    class="h-3.5 w-3.5"
                                />

                                Probar enlace
                            </a>
                        </div>
                    </article>
                </div>

                <div
                    v-else
                    class="adn-panel flex min-h-[380px] flex-col items-center justify-center rounded-[22px] px-8 text-center"
                >
                    <Megaphone
                        class="h-11 w-11 text-white/12"
                    />

                    <h2
                        class="mt-4 text-lg font-black text-white/55"
                    >
                        Todavía no hay publicidad
                    </h2>

                    <p
                        class="mt-2 max-w-md text-xs leading-6 text-white/25"
                    >
                        Crea el primer arte. Hasta entonces el sitio mantiene
                        exactamente su estructura actual.
                    </p>
                </div>
            </section>
        </div>
    </EditorLayout>
</template>
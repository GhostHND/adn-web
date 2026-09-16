<script setup lang="ts">
import {
    Head,
    Link,
    router,
    usePage,
} from '@inertiajs/vue3';

import {
    ArrowLeft,
    Building2,
    Check,
    Clock3,
    Download,
    ExternalLink,
    FileImage,
    FileText,
    Mail,
    MessageCircle,
    Package,
    Phone,
    RefreshCw,
    ShieldCheck,
    UserRound,
} from '@lucide/vue';

import {
    ref,
} from 'vue';

import EditorLayout
    from '@/layouts/Editor/EditorLayout.vue';

interface QuoteValueJson {
    field_type?: string;
    field_id_snapshot?: number;
    raw_value?: unknown;
    file_index?: number;
    original_name?: string;
    mime_type?: string | null;
    size?: number;
    path?: string;

    selected_option?: {
        value?: string;
        label?: string;
        extra_price?: string | null;
    };

    [key: string]: unknown;
}

interface QuoteValue {
    id: number;
    field_key: string;
    label: string;
    value_text: string | null;
    value_json: QuoteValueJson | null;
    unit: string | null;
}

interface QuoteFile {
    id: number;
    original_name: string;
    mime_type: string | null;
    size: number;
    preview_url: string;
    download_url: string;
}

interface QuoteItem {
    id: number;
    product_code: string;
    product_name: string;
    quantity: string;
    unit: string | null;
    quote_mode: string;
    reference_price: string | null;
    notes: string | null;
    values: QuoteValue[];
    files: QuoteFile[];
}

interface IntegrationLog {
    id: number;
    direction: string;
    event: string;
    status: string;
    http_status: number | null;
    error_message: string | null;
    attempted_at: string | null;
    completed_at: string | null;
}

interface Quote {
    id: number;
    request_number: string;
    public_token: string;
    status: string;
    client_name: string;
    phone: string;
    whatsapp: string | null;
    email: string | null;
    company: string | null;
    notes: string | null;
    source: string;
    privacy_consent: boolean;
    ip_address: string | null;
    user_agent: string | null;
    app_sync_status: string;
    app_lead_id: number | null;
    app_client_id: number | null;
    sync_attempts: number;
    last_sync_at: string | null;
    sync_error: string | null;
    created_at: string | null;
    updated_at: string | null;
    items: QuoteItem[];
    integration_logs: IntegrationLog[];
}

interface StatusOption {
    value: string;
    label: string;
}

interface SharedProps {
    flash?: {
        success?: string | null;
    };

    [key: string]: unknown;
}

const props =
    defineProps<{
        quote: Quote;
        statusOptions: StatusOption[];
    }>();

const page =
    usePage<SharedProps>();

const selectedStatus =
    ref(
        props.quote.status,
    );

const changingStatus =
    ref(
        false,
    );

const updateStatus =
    () => {
        changingStatus.value =
            true;

        router.patch(
            `/editor-preview/cotizaciones/${props.quote.id}/estado`,
            {
                status:
                    selectedStatus.value,
            },
            {
                preserveScroll:
                    true,

                onFinish:
                    () => {
                        changingStatus.value =
                            false;
                    },
            },
        );
    };

const statusLabel =
    (
        status:
            string,
    ): string => {
        return props.statusOptions.find(
            (
                option,
            ) =>
                option.value ===
                status,
        )?.label
        ?? status;
    };

const statusClass =
    (
        status:
            string,
    ): string => {
        const classes:
            Record<
                string,
                string
            > = {
                new:
                    'border-[var(--adn-coral-border)] bg-[var(--adn-coral-soft)] text-[var(--adn-coral)]',

                reviewing:
                    'border-[var(--adn-orange-border)] bg-[var(--adn-orange-soft)] text-[var(--adn-orange)]',

                contacted:
                    'border-[var(--adn-yellow-border)] bg-[var(--adn-yellow-soft)] text-[var(--adn-yellow)]',

                quoted:
                    'border-[var(--adn-turquoise-border)] bg-[var(--adn-turquoise-soft)] text-[var(--adn-turquoise)]',

                closed:
                    'border-white/[0.08] bg-white/[0.04] text-white/45',

                cancelled:
                    'border-white/[0.05] bg-black/20 text-white/25',
            };

        return classes[
            status
        ]
        ?? classes.closed;
    };

const formatDate =
    (
        value:
            string | null,
    ): string => {
        if (
            !value
        ) {
            return '—';
        }

        return new Intl.DateTimeFormat(
            'es-HN',
            {
                dateStyle:
                    'medium',

                timeStyle:
                    'short',
            },
        ).format(
            new Date(
                value,
            ),
        );
    };

const formatBytes =
    (
        bytes:
            number,
    ): string => {
        if (
            bytes <
            1024
        ) {
            return `${bytes} B`;
        }

        if (
            bytes <
            1024 * 1024
        ) {
            return `${(
                bytes /
                1024
            ).toFixed(
                1,
            )} KB`;
        }

        return `${(
            bytes /
            1024 /
            1024
        ).toFixed(
            1,
        )} MB`;
    };

const isImage =
    (
        file:
            QuoteFile,
    ): boolean => {
        return file.mime_type
            ?.startsWith(
                'image/',
            )
        ?? false;
    };

const whatsappHref =
    (
        number:
            string | null,
    ): string | null => {
        if (
            !number
        ) {
            return null;
        }

        const digits =
            number.replace(
                /\D/g,
                '',
            );

        if (
            !digits
        ) {
            return null;
        }

        const normalized =
            digits.length ===
                8
                ? `504${digits}`
                : digits;

        return `https://wa.me/${normalized}`;
    };

const visibleValues =
    (
        item:
            QuoteItem,
    ): QuoteValue[] => {
        return item.values.filter(
            (
                value,
            ) =>
                value.value_json?.field_type !==
                'file',
        );
    };
</script>

<template>
    <Head
        :title="`${quote.request_number} | Cotizaciones`"
    />

    <EditorLayout
        title="Detalle de cotización"
        eyebrow="Solicitudes ADN"
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

        <!-- ENCABEZADO -->

        <section
            class="flex flex-col justify-between gap-5 lg:flex-row lg:items-center"
        >
            <div
                class="flex min-w-0 items-start gap-4"
            >
                <Link
                    href="/editor-preview/cotizaciones"
                    class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl border border-white/[0.07] text-white/30 transition hover:bg-white/[0.04] hover:text-white"
                >
                    <ArrowLeft
                        class="h-4 w-4"
                    />
                </Link>

                <div
                    class="min-w-0"
                >
                    <p
                        class="text-[9px] font-black uppercase tracking-[0.15em] text-[var(--adn-orange)]"
                    >
                        Solicitud web
                    </p>

                    <h1
                        class="mt-1 text-2xl font-black tracking-[-0.035em] sm:text-3xl"
                    >
                        {{ quote.request_number }}
                    </h1>

                    <div
                        class="mt-3 flex flex-wrap items-center gap-3"
                    >
                        <span
                            class="rounded-lg border px-2.5 py-1 text-[8px] font-black uppercase tracking-[0.08em]"
                            :class="
                                statusClass(
                                    quote.status,
                                )
                            "
                        >
                            {{
                                statusLabel(
                                    quote.status,
                                )
                            }}
                        </span>

                        <span
                            class="flex items-center gap-1.5 text-[10px] text-white/25"
                        >
                            <Clock3
                                class="h-3.5 w-3.5"
                            />

                            {{
                                formatDate(
                                    quote.created_at,
                                )
                            }}
                        </span>
                    </div>
                </div>
            </div>

            <!-- CAMBIO DE ESTADO -->

            <div
                class="adn-panel flex flex-col gap-3 rounded-[18px] p-3 sm:flex-row sm:items-center"
            >
                <select
                    v-model="selectedStatus"
                    class="h-10 min-w-[190px] rounded-xl border border-white/[0.07] bg-[#101719] px-3 text-xs font-bold text-white/65"
                >
                    <option
                        v-for="option in statusOptions"
                        :key="option.value"
                        :value="option.value"
                    >
                        {{ option.label }}
                    </option>
                </select>

                <button
                    type="button"
                    :disabled="
                        changingStatus
                        ||
                        selectedStatus ===
                            quote.status
                    "
                    class="adn-primary-button flex h-10 items-center justify-center gap-2 rounded-xl px-4 text-xs font-black disabled:cursor-not-allowed disabled:opacity-30"
                    @click="updateStatus"
                >
                    <RefreshCw
                        class="h-3.5 w-3.5"
                        :class="
                            changingStatus
                                ? 'animate-spin'
                                : ''
                        "
                    />

                    Actualizar
                </button>
            </div>
        </section>

        <!-- CONTENIDO -->

        <section
            class="mt-6 grid min-w-0 gap-5 xl:grid-cols-[340px_minmax(0,1fr)]"
        >
            <!-- LATERAL -->

            <aside
                class="min-w-0 space-y-5"
            >
                <!-- CLIENTE -->

                <section
                    class="adn-panel rounded-[22px] p-5"
                >
                    <div
                        class="flex items-center gap-3"
                    >
                        <span
                            class="flex h-10 w-10 items-center justify-center rounded-xl bg-[var(--adn-turquoise-soft)] text-[var(--adn-turquoise)]"
                        >
                            <UserRound
                                class="h-4 w-4"
                            />
                        </span>

                        <div>
                            <p
                                class="text-sm font-black"
                            >
                                Cliente
                            </p>

                            <p
                                class="text-[9px] text-white/25"
                            >
                                Información de contacto
                            </p>
                        </div>
                    </div>

                    <div
                        class="mt-5"
                    >
                        <p
                            class="text-lg font-black text-white/75"
                        >
                            {{ quote.client_name }}
                        </p>

                        <p
                            v-if="quote.company"
                            class="mt-1 flex items-center gap-2 text-xs text-white/30"
                        >
                            <Building2
                                class="h-3.5 w-3.5"
                            />

                            {{ quote.company }}
                        </p>
                    </div>

                    <div
                        class="mt-5 grid gap-2"
                    >
                        <a
                            :href="`tel:${quote.phone}`"
                            class="flex min-h-11 items-center gap-3 rounded-xl border border-white/[0.06] bg-black/10 px-3 text-xs font-bold text-white/45 transition hover:border-[var(--adn-turquoise-border)] hover:text-white"
                        >
                            <Phone
                                class="h-4 w-4 text-[var(--adn-turquoise)]"
                            />

                            {{ quote.phone }}
                        </a>

                        <a
                            v-if="
                                whatsappHref(
                                    quote.whatsapp,
                                )
                            "
                            :href="
                                whatsappHref(
                                    quote.whatsapp,
                                )
                                ?? '#'
                            "
                            target="_blank"
                            rel="noopener noreferrer"
                            class="flex min-h-11 items-center gap-3 rounded-xl border border-white/[0.06] bg-black/10 px-3 text-xs font-bold text-white/45 transition hover:border-[var(--adn-turquoise-border)] hover:text-white"
                        >
                            <MessageCircle
                                class="h-4 w-4 text-[var(--adn-turquoise)]"
                            />

                            WhatsApp
                        </a>

                        <a
                            v-if="quote.email"
                            :href="`mailto:${quote.email}`"
                            class="flex min-h-11 items-center gap-3 rounded-xl border border-white/[0.06] bg-black/10 px-3 text-xs font-bold text-white/45 transition hover:border-[var(--adn-turquoise-border)] hover:text-white"
                        >
                            <Mail
                                class="h-4 w-4 text-[var(--adn-orange)]"
                            />

                            <span
                                class="truncate"
                            >
                                {{ quote.email }}
                            </span>
                        </a>
                    </div>
                </section>

                <!-- INTEGRACIÓN -->

                <section
                    class="adn-panel rounded-[22px] p-5"
                >
                    <p
                        class="text-[9px] font-black uppercase tracking-[0.13em] text-[var(--adn-yellow)]"
                    >
                        Integración APP
                    </p>

                    <p
                        class="mt-2 text-sm font-black uppercase text-white/60"
                    >
                        {{ quote.app_sync_status }}
                    </p>

                    <div
                        class="mt-4 grid gap-2 text-[10px] text-white/28"
                    >
                        <div
                            class="flex justify-between gap-4"
                        >
                            <span>
                                Intentos
                            </span>

                            <strong
                                class="text-white/50"
                            >
                                {{ quote.sync_attempts }}
                            </strong>
                        </div>

                        <div
                            class="flex justify-between gap-4"
                        >
                            <span>
                                Lead APP
                            </span>

                            <strong
                                class="text-white/50"
                            >
                                {{
                                    quote.app_lead_id
                                    ?? '—'
                                }}
                            </strong>
                        </div>

                        <div
                            class="flex justify-between gap-4"
                        >
                            <span>
                                Cliente APP
                            </span>

                            <strong
                                class="text-white/50"
                            >
                                {{
                                    quote.app_client_id
                                    ?? '—'
                                }}
                            </strong>
                        </div>

                        <div
                            class="flex justify-between gap-4"
                        >
                            <span>
                                Último intento
                            </span>

                            <strong
                                class="text-right text-white/50"
                            >
                                {{
                                    formatDate(
                                        quote.last_sync_at,
                                    )
                                }}
                            </strong>
                        </div>
                    </div>

                    <p
                        v-if="quote.sync_error"
                        class="mt-4 rounded-xl border border-[var(--adn-coral-border)] bg-[var(--adn-coral-soft)] p-3 text-[10px] leading-5 text-[var(--adn-coral)]"
                    >
                        {{ quote.sync_error }}
                    </p>
                </section>

                <!-- SEGURIDAD -->

                <section
                    class="adn-panel rounded-[22px] p-5"
                >
                    <div
                        class="flex items-start gap-3"
                    >
                        <ShieldCheck
                            class="mt-0.5 h-4 w-4 shrink-0 text-[var(--adn-turquoise)]"
                        />

                        <div>
                            <p
                                class="text-xs font-black text-white/55"
                            >
                                Consentimiento
                            </p>

                            <p
                                class="mt-1 text-[9px] leading-4 text-white/22"
                            >
                                {{
                                    quote.privacy_consent
                                        ? 'Aceptado por el cliente.'
                                        : 'No registrado.'
                                }}
                            </p>
                        </div>
                    </div>

                    <div
                        class="mt-4 border-t border-white/[0.05] pt-4"
                    >
                        <p
                            class="text-[8px] uppercase tracking-[0.1em] text-white/18"
                        >
                            IP
                        </p>

                        <p
                            class="mt-1 break-all text-[10px] text-white/35"
                        >
                            {{
                                quote.ip_address
                                ?? '—'
                            }}
                        </p>
                    </div>

                    <div
                        class="mt-4 border-t border-white/[0.05] pt-4"
                    >
                        <p
                            class="text-[8px] uppercase tracking-[0.1em] text-white/18"
                        >
                            Origen
                        </p>

                        <p
                            class="mt-1 text-[10px] font-black uppercase text-white/35"
                        >
                            {{ quote.source }}
                        </p>
                    </div>
                </section>
            </aside>

            <!-- SOLICITUD -->

            <div
                class="min-w-0 space-y-5"
            >
                <section
                    v-for="item in quote.items"
                    :key="item.id"
                    class="adn-panel overflow-hidden rounded-[22px]"
                >
                    <!-- Producto -->

                    <header
                        class="flex flex-col justify-between gap-4 border-b border-white/[0.06] px-5 py-5 sm:flex-row sm:items-center sm:px-6"
                    >
                        <div
                            class="flex min-w-0 items-center gap-4"
                        >
                            <span
                                class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-[var(--adn-orange-soft)] text-[var(--adn-orange)]"
                            >
                                <Package
                                    class="h-5 w-5"
                                />
                            </span>

                            <div
                                class="min-w-0"
                            >
                                <p
                                    class="text-[8px] font-black uppercase tracking-[0.1em] text-[var(--adn-orange)]"
                                >
                                    {{ item.product_code }}
                                </p>

                                <h2
                                    class="mt-1 truncate text-lg font-black text-white/75"
                                >
                                    {{ item.product_name }}
                                </h2>

                                <p
                                    class="mt-1 text-[9px] uppercase text-white/22"
                                >
                                    {{ item.quote_mode }}

                                    <template
                                        v-if="item.unit"
                                    >
                                        · {{ item.unit }}
                                    </template>
                                </p>
                            </div>
                        </div>

                        <div
                            class="rounded-xl border border-[var(--adn-turquoise-border)] bg-[var(--adn-turquoise-soft)] px-4 py-2"
                        >
                            <p
                                class="text-[8px] font-black uppercase tracking-[0.1em] text-[var(--adn-turquoise)]"
                            >
                                Cantidad
                            </p>

                            <p
                                class="mt-0.5 text-lg font-black text-white/75"
                            >
                                {{ item.quantity }}
                            </p>
                        </div>
                    </header>

                    <!-- Valores -->

                    <div
                        class="p-5 sm:p-6"
                    >
                        <div
                            v-if="
                                visibleValues(
                                    item,
                                ).length
                            "
                            class="grid gap-3 sm:grid-cols-2 xl:grid-cols-3"
                        >
                            <article
                                v-for="value in visibleValues(item)"
                                :key="value.id"
                                class="rounded-[16px] border border-white/[0.06] bg-black/10 p-4"
                            >
                                <p
                                    class="text-[8px] font-black uppercase tracking-[0.09em] text-white/22"
                                >
                                    {{ value.label }}
                                </p>

                                <p
                                    class="mt-2 break-words text-sm font-black text-white/60"
                                >
                                    {{
                                        value.value_text
                                        ?? '—'
                                    }}

                                    <span
                                        v-if="value.unit"
                                        class="ml-1 text-[10px] font-bold text-white/25"
                                    >
                                        {{ value.unit }}
                                    </span>
                                </p>
                            </article>
                        </div>

                        <div
                            v-else
                            class="rounded-[16px] border border-dashed border-white/[0.06] p-5 text-center"
                        >
                            <p
                                class="text-xs text-white/25"
                            >
                                Esta solicitud no tiene valores adicionales.
                            </p>
                        </div>

                        <!-- ARCHIVOS -->

                        <div
                            v-if="item.files.length"
                            class="mt-6 border-t border-white/[0.06] pt-6"
                        >
                            <div
                                class="flex items-center justify-between gap-4"
                            >
                                <div>
                                    <p
                                        class="text-sm font-black"
                                    >
                                        Archivos del cliente
                                    </p>

                                    <p
                                        class="mt-1 text-[9px] text-white/23"
                                    >
                                        {{ item.files.length }}

                                        archivo{{
                                            item.files.length ===
                                            1
                                                ? ''
                                                : 's'
                                        }}

                                        adjunto{{
                                            item.files.length ===
                                            1
                                                ? ''
                                                : 's'
                                        }}.
                                    </p>
                                </div>
                            </div>

                            <div
                                class="mt-4 grid gap-3 sm:grid-cols-2 2xl:grid-cols-3"
                            >
                                <article
                                    v-for="file in item.files"
                                    :key="file.id"
                                    class="overflow-hidden rounded-[18px] border border-white/[0.06] bg-black/10"
                                >
                                    <!-- Imagen -->

                                    <a
                                        v-if="isImage(file)"
                                        :href="file.preview_url"
                                        target="_blank"
                                        rel="noopener noreferrer"
                                        class="block aspect-[16/10] overflow-hidden bg-black/20"
                                    >
                                        <img
                                            :src="file.preview_url"
                                            :alt="file.original_name"
                                            class="h-full w-full object-cover transition duration-500 hover:scale-[1.03]"
                                        >
                                    </a>

                                    <!-- Documento -->

                                    <a
                                        v-else
                                        :href="file.preview_url"
                                        target="_blank"
                                        rel="noopener noreferrer"
                                        class="flex aspect-[16/10] items-center justify-center bg-black/20"
                                    >
                                        <FileText
                                            class="h-10 w-10 text-[var(--adn-coral)]"
                                        />
                                    </a>

                                    <div
                                        class="p-4"
                                    >
                                        <div
                                            class="flex min-w-0 items-start gap-3"
                                        >
                                            <FileImage
                                                v-if="isImage(file)"
                                                class="mt-0.5 h-4 w-4 shrink-0 text-[var(--adn-turquoise)]"
                                            />

                                            <FileText
                                                v-else
                                                class="mt-0.5 h-4 w-4 shrink-0 text-[var(--adn-coral)]"
                                            />

                                            <div
                                                class="min-w-0 flex-1"
                                            >
                                                <p
                                                    class="truncate text-[10px] font-black text-white/55"
                                                    :title="file.original_name"
                                                >
                                                    {{ file.original_name }}
                                                </p>

                                                <p
                                                    class="mt-1 text-[8px] text-white/22"
                                                >
                                                    {{
                                                        formatBytes(
                                                            file.size,
                                                        )
                                                    }}
                                                </p>
                                            </div>
                                        </div>

                                        <div
                                            class="mt-3 grid grid-cols-2 gap-2"
                                        >
                                            <a
                                                :href="file.preview_url"
                                                target="_blank"
                                                rel="noopener noreferrer"
                                                class="flex h-9 items-center justify-center gap-2 rounded-xl border border-white/[0.06] text-[8px] font-black uppercase tracking-[0.06em] text-white/35 transition hover:bg-white/[0.03] hover:text-white"
                                            >
                                                <ExternalLink
                                                    class="h-3.5 w-3.5"
                                                />

                                                Ver
                                            </a>

                                            <a
                                                :href="file.download_url"
                                                class="flex h-9 items-center justify-center gap-2 rounded-xl border border-[var(--adn-turquoise-border)] bg-[var(--adn-turquoise-soft)] text-[8px] font-black uppercase tracking-[0.06em] text-[var(--adn-turquoise)] transition hover:-translate-y-px"
                                            >
                                                <Download
                                                    class="h-3.5 w-3.5"
                                                />

                                                Descargar
                                            </a>
                                        </div>
                                    </div>
                                </article>
                            </div>
                        </div>
                    </div>
                </section>

                <!-- SIN ITEMS -->

                <section
                    v-if="!quote.items.length"
                    class="adn-panel flex min-h-64 flex-col items-center justify-center rounded-[22px] px-6 text-center"
                >
                    <Package
                        class="h-10 w-10 text-white/10"
                    />

                    <p
                        class="mt-4 text-sm font-black text-white/45"
                    >
                        Solicitud sin productos
                    </p>
                </section>
            </div>
        </section>
    </EditorLayout>
</template>
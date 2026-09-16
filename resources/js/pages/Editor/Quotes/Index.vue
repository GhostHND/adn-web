<script setup lang="ts">
import {
    Head,
    Link,
    router,
} from '@inertiajs/vue3';

import {
    ArrowRight,
    Clock3,
    FileText,
    Search,
    Send,
    UserRound,
} from '@lucide/vue';

import {
    reactive,
} from 'vue';

import EditorLayout
    from '@/layouts/Editor/EditorLayout.vue';

interface QuoteListItem {
    id: number;
    request_number: string;
    status: string;
    client_name: string;
    phone: string;
    whatsapp: string | null;
    email: string | null;
    company: string | null;
    source: string;
    app_sync_status: string;
    created_at: string | null;
    items_count: number;

    product: {
        name: string;
        code: string;
        quantity: string;
    } | null;
}

interface PaginationLink {
    url: string | null;
    label: string;
    active: boolean;
}

interface QuotePaginator {
    data: QuoteListItem[];
    total: number;
    current_page: number;
    last_page: number;
    links: PaginationLink[];
}

interface Summary {
    total: number;
    new: number;
    reviewing: number;
    closed: number;
    pending_sync: number;
}

interface Filters {
    search: string;
    status: string;
    sync: string;
}

const props =
    defineProps<{
        requests: QuotePaginator;
        summary: Summary;
        filters: Filters;
    }>();

const filters =
    reactive({
        search:
            props.filters.search
            ?? '',

        status:
            props.filters.status
            ?? '',

        sync:
            props.filters.sync
            ?? '',
    });

const applyFilters =
    () => {
        router.get(
            '/editor-preview/cotizaciones',
            {
                search:
                    filters.search
                    || undefined,

                status:
                    filters.status
                    || undefined,

                sync:
                    filters.sync
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

        filters.status =
            '';

        filters.sync =
            '';

        router.get(
            '/editor-preview/cotizaciones',
        );
    };

const statusLabel =
    (
        status:
            string,
    ): string => {
        const labels:
            Record<string, string> = {
                new:
                    'Nueva',

                reviewing:
                    'En revisión',

                contacted:
                    'Contactado',

                quoted:
                    'Cotizada',

                closed:
                    'Cerrada',

                cancelled:
                    'Cancelada',
            };

        return labels[
            status
        ]
        ?? status;
    };

const statusClass =
    (
        status:
            string,
    ): string => {
        const classes:
            Record<string, string> = {
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

const syncClass =
    (
        status:
            string,
    ): string => {
        if (
            status ===
            'synced'
        ) {
            return 'text-[var(--adn-turquoise)]';
        }

        if (
            status ===
            'failed'
        ) {
            return 'text-[var(--adn-coral)]';
        }

        return 'text-[var(--adn-yellow)]';
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
                day:
                    '2-digit',

                month:
                    'short',

                year:
                    'numeric',

                hour:
                    '2-digit',

                minute:
                    '2-digit',
            },
        ).format(
            new Date(
                value,
            ),
        );
    };
</script>

<template>
    <Head
        title="Cotizaciones"
    />

    <EditorLayout
        title="Cotizaciones"
        eyebrow="Solicitudes ADN"
    >
        <section>
            <p
                class="text-[9px] font-black uppercase tracking-[0.16em] text-[var(--adn-orange)]"
            >
                Solicitudes del sitio web
            </p>

            <h1
                class="mt-2 text-3xl font-black tracking-[-0.04em]"
            >
                Cotizaciones recibidas
            </h1>

            <p
                class="mt-2 max-w-2xl text-sm text-white/32"
            >
                Consulta las solicitudes enviadas por los clientes y revisa
                todos los datos, medidas y archivos asociados.
            </p>
        </section>

        <section
            class="mt-6 grid gap-3 sm:grid-cols-2 xl:grid-cols-5"
        >
            <article
                class="adn-panel rounded-[18px] p-4"
            >
                <p class="text-[8px] font-black uppercase tracking-[0.12em] text-white/22">
                    Total
                </p>

                <p class="mt-2 text-2xl font-black text-white/75">
                    {{ summary.total }}
                </p>
            </article>

            <article
                class="adn-panel rounded-[18px] p-4"
            >
                <p class="text-[8px] font-black uppercase tracking-[0.12em] text-white/22">
                    Nuevas
                </p>

                <p class="mt-2 text-2xl font-black text-[var(--adn-coral)]">
                    {{ summary.new }}
                </p>
            </article>

            <article
                class="adn-panel rounded-[18px] p-4"
            >
                <p class="text-[8px] font-black uppercase tracking-[0.12em] text-white/22">
                    En revisión
                </p>

                <p class="mt-2 text-2xl font-black text-[var(--adn-orange)]">
                    {{ summary.reviewing }}
                </p>
            </article>

            <article
                class="adn-panel rounded-[18px] p-4"
            >
                <p class="text-[8px] font-black uppercase tracking-[0.12em] text-white/22">
                    Cerradas
                </p>

                <p class="mt-2 text-2xl font-black text-white/50">
                    {{ summary.closed }}
                </p>
            </article>

            <article
                class="adn-panel rounded-[18px] p-4"
            >
                <p class="text-[8px] font-black uppercase tracking-[0.12em] text-white/22">
                    Pendientes APP
                </p>

                <p class="mt-2 text-2xl font-black text-[var(--adn-yellow)]">
                    {{ summary.pending_sync }}
                </p>
            </article>
        </section>

        <section
            class="adn-panel mt-5 rounded-[22px] p-4"
        >
            <form
                class="grid gap-3 xl:grid-cols-[1fr_190px_190px_auto]"
                @submit.prevent="applyFilters"
            >
                <label class="relative">
                    <Search
                        class="absolute left-4 top-1/2 h-4 w-4 -translate-y-1/2 text-white/20"
                    />

                    <input
                        v-model="filters.search"
                        type="search"
                        placeholder="Buscar solicitud, cliente, teléfono o producto..."
                        class="h-11 w-full rounded-xl border border-white/[0.07] bg-black/15 pl-11 pr-4 text-sm text-white outline-none placeholder:text-white/16"
                    >
                </label>

                <select
                    v-model="filters.status"
                    class="h-11 rounded-xl border border-white/[0.07] bg-[#101719] px-4 text-sm text-white/65"
                >
                    <option value="">
                        Todos los estados
                    </option>

                    <option value="new">
                        Nuevas
                    </option>

                    <option value="reviewing">
                        En revisión
                    </option>

                    <option value="contacted">
                        Contactado
                    </option>

                    <option value="quoted">
                        Cotizada
                    </option>

                    <option value="closed">
                        Cerrada
                    </option>

                    <option value="cancelled">
                        Cancelada
                    </option>
                </select>

                <select
                    v-model="filters.sync"
                    class="h-11 rounded-xl border border-white/[0.07] bg-[#101719] px-4 text-sm text-white/65"
                >
                    <option value="">
                        Integración APP
                    </option>

                    <option value="pending">
                        Pendiente
                    </option>

                    <option value="synced">
                        Sincronizada
                    </option>

                    <option value="failed">
                        Error
                    </option>
                </select>

                <div class="flex gap-2">
                    <button
                        type="submit"
                        class="adn-primary-button h-11 rounded-xl px-5 text-xs font-black"
                    >
                        Filtrar
                    </button>

                    <button
                        type="button"
                        class="h-11 rounded-xl border border-white/[0.07] px-4 text-xs font-black text-white/30"
                        @click="clearFilters"
                    >
                        Limpiar
                    </button>
                </div>
            </form>
        </section>

        <section
            v-if="requests.data.length"
            class="mt-5 grid gap-3"
        >
            <Link
                v-for="quote in requests.data"
                :key="quote.id"
                :href="`/editor-preview/cotizaciones/${quote.id}`"
                class="adn-panel group grid min-w-0 gap-4 rounded-[20px] p-4 transition hover:-translate-y-0.5 hover:border-[var(--adn-turquoise-border)] sm:p-5 xl:grid-cols-[190px_minmax(190px,1fr)_minmax(220px,1.2fr)_150px_120px_42px] xl:items-center"
            >
                <div>
                    <p
                        class="text-xs font-black text-[var(--adn-turquoise)]"
                    >
                        {{ quote.request_number }}
                    </p>

                    <div
                        class="mt-2 inline-flex rounded-lg border px-2.5 py-1 text-[8px] font-black uppercase tracking-[0.08em]"
                        :class="statusClass(quote.status)"
                    >
                        {{ statusLabel(quote.status) }}
                    </div>
                </div>

                <div class="min-w-0">
                    <div class="flex items-center gap-2">
                        <UserRound
                            class="h-4 w-4 shrink-0 text-white/20"
                        />

                        <p class="truncate text-sm font-black text-white/70">
                            {{ quote.client_name }}
                        </p>
                    </div>

                    <p class="mt-1 truncate text-[10px] text-white/25">
                        {{ quote.phone }}
                    </p>
                </div>

                <div class="min-w-0">
                    <p class="truncate text-sm font-black text-white/60">
                        {{
                            quote.product?.name
                            ?? 'Sin producto'
                        }}
                    </p>

                    <p
                        v-if="quote.product"
                        class="mt-1 text-[10px] text-white/25"
                    >
                        {{ quote.product.code }}
                        ·
                        Cantidad {{ quote.product.quantity }}
                    </p>
                </div>

                <div>
                    <div class="flex items-center gap-2 text-[10px] text-white/30">
                        <Clock3 class="h-3.5 w-3.5" />

                        {{ formatDate(quote.created_at) }}
                    </div>
                </div>

                <div>
                    <p
                        class="text-[8px] font-black uppercase tracking-[0.08em]"
                        :class="syncClass(quote.app_sync_status)"
                    >
                        APP:
                        {{ quote.app_sync_status }}
                    </p>
                </div>

                <span
                    class="flex h-10 w-10 items-center justify-center rounded-xl border border-white/[0.06] text-white/25 transition group-hover:border-[var(--adn-turquoise-border)] group-hover:bg-[var(--adn-turquoise-soft)] group-hover:text-[var(--adn-turquoise)]"
                >
                    <ArrowRight class="h-4 w-4" />
                </span>
            </Link>
        </section>

        <section
            v-else
            class="adn-panel mt-5 flex min-h-72 flex-col items-center justify-center rounded-[22px] px-6 text-center"
        >
            <FileText class="h-10 w-10 text-white/10" />

            <p class="mt-4 text-sm font-black text-white/50">
                No encontramos cotizaciones
            </p>

            <p class="mt-1 text-xs text-white/23">
                Las nuevas solicitudes del sitio aparecerán aquí.
            </p>
        </section>

        <div
            v-if="requests.last_page > 1"
            class="mt-5 flex flex-wrap justify-center gap-2"
        >
            <Link
                v-for="link in requests.links"
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
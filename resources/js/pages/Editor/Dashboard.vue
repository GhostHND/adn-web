<script setup lang="ts">
import {
    Head,
} from '@inertiajs/vue3';

import {
    ArrowUpRight,
    Boxes,
    CircleDot,
    Clock3,
    FileImage,
    FileText,
    FolderKanban,
    Layers3,
    MessageSquareText,
    PackageSearch,
    Plus,
    Sparkles,
} from '@lucide/vue';

import EditorLayout
    from '@/layouts/Editor/EditorLayout.vue';

interface Stats {
    pages: number;
    services: number;
    categories: number;
    products: number;
    portfolio: number;
    media: number;
    requests: number;
    new_requests: number;
    pending_sync: number;
}

interface RecentRequest {
    id: number;
    request_number: string;
    client_name: string;
    company: string | null;
    status: string;
    app_sync_status: string;
    created_at: string;
}

interface Props {
    stats: Stats;
    recentRequests: RecentRequest[];
}

const props =
    defineProps<Props>();

const formatNumber =
    (
        value:
            number,
    ) =>
        new Intl.NumberFormat(
            'es-HN',
        ).format(
            value,
        );

const formatDate =
    (
        value:
            string,
    ) =>
        new Intl.DateTimeFormat(
            'es-HN',
            {
                dateStyle:
                    'medium',

                timeStyle:
                    'short',
            },
        ).format(
            new Date(value),
        );

const statusLabel =
    (
        status:
            string,
    ) => {
        const statuses:
            Record<string, string> =
        {
            new:
                'Nueva',

            pending:
                'Pendiente',

            sent:
                'Enviada',

            synced:
                'Sincronizada',

            failed:
                'Error',

            completed:
                'Completada',
        };

        return statuses[
            status
        ] ?? status;
    };

const statCards = [
    {
        label:
            'Páginas',

        value:
            () =>
                props.stats.pages,

        description:
            'Contenido estructural',

        icon:
            FileText,

        accent:
            '#0FA7B4',

        soft:
            'rgba(15, 167, 180, .10)',
    },

    {
        label:
            'Servicios',

        value:
            () =>
                props.stats.services,

        description:
            'Servicios registrados',

        icon:
            Layers3,

        accent:
            '#E84657',

        soft:
            'rgba(232, 70, 87, .10)',
    },

    {
        label:
            'Productos',

        value:
            () =>
                props.stats.products,

        description:
            `${props.stats.categories} categorías`,

        icon:
            Boxes,

        accent:
            '#ED7E24',

        soft:
            'rgba(237, 126, 36, .10)',
    },

    {
        label:
            'Portafolio',

        value:
            () =>
                props.stats.portfolio,

        description:
            'Trabajos registrados',

        icon:
            FolderKanban,

        accent:
            '#F5C000',

        soft:
            'rgba(245, 192, 0, .10)',
    },

    {
        label:
            'Multimedia',

        value:
            () =>
                props.stats.media,

        description:
            'Archivos disponibles',

        icon:
            FileImage,

        accent:
            '#0FA7B4',

        soft:
            'rgba(15, 167, 180, .10)',
    },

    {
        label:
            'Solicitudes',

        value:
            () =>
                props.stats.requests,

        description:
            `${props.stats.new_requests} nuevas`,

        icon:
            MessageSquareText,

        accent:
            '#E84657',

        soft:
            'rgba(232, 70, 87, .10)',
    },
];
</script>

<template>
    <Head
        title="Editor"
    />

    <EditorLayout
        title="Dashboard"
        eyebrow="Editor ADN Publicidad"
    >
        <!-- Hero -->

        <section
            class="adn-panel relative overflow-hidden rounded-[26px] p-6 sm:p-8"
        >
            <div
                class="absolute inset-x-0 top-0 h-[3px] adn-brand-stripe"
            />

            <div
                class="pointer-events-none absolute -right-20 -top-28 h-72 w-72 rounded-full bg-[rgba(15,167,180,.08)] blur-[100px]"
            />

            <div
                class="pointer-events-none absolute -bottom-28 left-[30%] h-60 w-60 rounded-full bg-[rgba(232,70,87,.04)] blur-[100px]"
            />

            <div
                class="relative flex flex-col justify-between gap-7 xl:flex-row xl:items-center"
            >
                <div>
                    <div
                        class="inline-flex items-center gap-2 rounded-full border border-[var(--adn-turquoise-border)] bg-[var(--adn-turquoise-soft)] px-3 py-1.5"
                    >
                        <Sparkles
                            class="h-3.5 w-3.5 text-[var(--adn-turquoise)]"
                        />

                        <span
                            class="text-[9px] font-black uppercase tracking-[0.16em] text-[var(--adn-turquoise)]"
                        >
                            Centro de contenido
                        </span>
                    </div>

                    <h1
                        class="mt-5 max-w-3xl text-3xl font-black tracking-[-0.035em] text-white sm:text-4xl"
                    >
                        Todo tu sitio web,

                        <span
                            class="text-[var(--adn-turquoise)]"
                        >
                            bajo la identidad ADN.
                        </span>
                    </h1>

                    <p
                        class="mt-3 max-w-2xl text-sm leading-6 text-white/42"
                    >
                        Administra el contenido, catálogo,
                        portafolio y solicitudes de ADN Publicidad
                        desde un entorno creado alrededor de la
                        identidad visual de la empresa.
                    </p>
                </div>

                <div
                    class="flex flex-wrap gap-2.5"
                >
                    <a
                        href="/"
                        target="_blank"
                        rel="noopener noreferrer"
                        class="inline-flex h-11 items-center justify-center gap-2 rounded-xl border border-white/[0.075] bg-white/[0.03] px-4 text-xs font-black text-white/65 transition hover:bg-white/[0.055] hover:text-white"
                    >
                        Ver sitio

                        <ArrowUpRight
                            class="h-4 w-4"
                        />
                    </a>

                    <button
                        type="button"
                        disabled
                        class="adn-primary-button inline-flex h-11 cursor-not-allowed items-center justify-center gap-2 rounded-xl px-4 text-xs font-black opacity-40"
                    >
                        <Plus
                            class="h-4 w-4"
                        />

                        Nuevo producto
                    </button>
                </div>
            </div>
        </section>

        <!-- Métricas -->

        <section
            class="mt-5 grid gap-3 sm:grid-cols-2 xl:grid-cols-3 2xl:grid-cols-6"
        >
            <article
                v-for="card in statCards"
                :key="card.label"
                class="group relative overflow-hidden rounded-[20px] border border-[var(--adn-border)] bg-[rgba(17,23,24,.88)] p-5 transition duration-300 hover:-translate-y-0.5 hover:border-white/[0.12]"
                :style="{
                    '--card-accent':
                        card.accent,

                    '--card-soft':
                        card.soft,
                }"
            >
                <span
                    class="absolute inset-x-0 top-0 h-[2px] bg-[var(--card-accent)] opacity-80"
                />

                <div
                    class="flex items-start justify-between"
                >
                    <div
                        class="flex h-10 w-10 items-center justify-center rounded-xl bg-[var(--card-soft)] text-[var(--card-accent)]"
                    >
                        <component
                            :is="card.icon"
                            class="h-[18px] w-[18px]"
                        />
                    </div>

                    <CircleDot
                        class="h-3.5 w-3.5 text-[var(--card-accent)] opacity-35"
                    />
                </div>

                <p
                    class="mt-5 text-3xl font-black tracking-[-0.04em] text-white"
                >
                    {{
                        formatNumber(
                            card.value(),
                        )
                    }}
                </p>

                <p
                    class="mt-1 text-xs font-bold text-white/60"
                >
                    {{ card.label }}
                </p>

                <p
                    class="mt-1 text-[10px] text-white/28"
                >
                    {{ card.description }}
                </p>
            </article>
        </section>

        <!-- Contenido inferior -->

        <section
            class="mt-5 grid gap-5 xl:grid-cols-[1.55fr_.75fr]"
        >
            <article
                class="adn-panel overflow-hidden rounded-[22px]"
            >
                <div
                    class="flex items-center justify-between border-b border-[var(--adn-border)] px-5 py-4 sm:px-6"
                >
                    <div>
                        <h2
                            class="text-sm font-black text-white/88"
                        >
                            Solicitudes recientes
                        </h2>

                        <p
                            class="mt-1 text-[10px] text-white/28"
                        >
                            Cotizaciones recibidas desde el sitio
                        </p>
                    </div>

                    <div
                        class="flex h-9 w-9 items-center justify-center rounded-xl bg-[var(--adn-coral-soft)] text-[var(--adn-coral)]"
                    >
                        <PackageSearch
                            class="h-[17px] w-[17px]"
                        />
                    </div>
                </div>

                <div
                    v-if="recentRequests.length > 0"
                    class="overflow-x-auto"
                >
                    <table
                        class="w-full min-w-[680px]"
                    >
                        <thead>
                            <tr
                                class="border-b border-white/[0.045]"
                            >
                                <th
                                    class="px-6 py-3 text-left text-[9px] font-black uppercase tracking-[0.13em] text-white/22"
                                >
                                    Solicitud
                                </th>

                                <th
                                    class="px-4 py-3 text-left text-[9px] font-black uppercase tracking-[0.13em] text-white/22"
                                >
                                    Cliente
                                </th>

                                <th
                                    class="px-4 py-3 text-left text-[9px] font-black uppercase tracking-[0.13em] text-white/22"
                                >
                                    Estado
                                </th>

                                <th
                                    class="px-4 py-3 text-left text-[9px] font-black uppercase tracking-[0.13em] text-white/22"
                                >
                                    Integración
                                </th>

                                <th
                                    class="px-6 py-3 text-right text-[9px] font-black uppercase tracking-[0.13em] text-white/22"
                                >
                                    Fecha
                                </th>
                            </tr>
                        </thead>

                        <tbody>
                            <tr
                                v-for="request in recentRequests"
                                :key="request.id"
                                class="border-b border-white/[0.035] last:border-0"
                            >
                                <td
                                    class="px-6 py-4 text-xs font-black text-[var(--adn-turquoise)]"
                                >
                                    {{
                                        request.request_number
                                    }}
                                </td>

                                <td
                                    class="px-4 py-4"
                                >
                                    <p
                                        class="text-xs font-bold text-white/70"
                                    >
                                        {{
                                            request.client_name
                                        }}
                                    </p>

                                    <p
                                        v-if="request.company"
                                        class="mt-0.5 text-[10px] text-white/25"
                                    >
                                        {{
                                            request.company
                                        }}
                                    </p>
                                </td>

                                <td
                                    class="px-4 py-4"
                                >
                                    <span
                                        class="inline-flex rounded-lg border border-[var(--adn-coral-border)] bg-[var(--adn-coral-soft)] px-2 py-1 text-[9px] font-black uppercase tracking-[0.08em] text-[var(--adn-coral)]"
                                    >
                                        {{
                                            statusLabel(
                                                request.status,
                                            )
                                        }}
                                    </span>
                                </td>

                                <td
                                    class="px-4 py-4"
                                >
                                    <span
                                        class="inline-flex rounded-lg border border-[var(--adn-orange-border)] bg-[var(--adn-orange-soft)] px-2 py-1 text-[9px] font-black uppercase tracking-[0.08em] text-[var(--adn-orange)]"
                                    >
                                        {{
                                            statusLabel(
                                                request.app_sync_status,
                                            )
                                        }}
                                    </span>
                                </td>

                                <td
                                    class="px-6 py-4 text-right text-[10px] text-white/27"
                                >
                                    {{
                                        formatDate(
                                            request.created_at,
                                        )
                                    }}
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <div
                    v-else
                    class="flex min-h-64 flex-col items-center justify-center px-6 py-12 text-center"
                >
                    <div
                        class="flex h-14 w-14 items-center justify-center rounded-2xl bg-[var(--adn-coral-soft)] text-[var(--adn-coral)]"
                    >
                        <MessageSquareText
                            class="h-6 w-6"
                        />
                    </div>

                    <p
                        class="mt-4 text-sm font-black text-white/55"
                    >
                        Aún no hay solicitudes
                    </p>

                    <p
                        class="mt-1 max-w-sm text-xs leading-5 text-white/25"
                    >
                        Las solicitudes del catálogo
                        aparecerán aquí.
                    </p>
                </div>
            </article>

            <div
                class="space-y-5"
            >
                <article
                    class="adn-panel rounded-[22px] p-5"
                >
                    <p
                        class="text-[9px] font-black uppercase tracking-[0.15em] text-white/25"
                    >
                        Estado del sistema
                    </p>

                    <h2
                        class="mt-1 text-sm font-black text-white/85"
                    >
                        ADN Web
                    </h2>

                    <div
                        class="mt-5 space-y-3"
                    >
                        <div
                            class="flex items-center justify-between rounded-xl border border-[var(--adn-turquoise-border)] bg-[var(--adn-turquoise-soft)] px-3.5 py-3"
                        >
                            <span
                                class="text-xs font-bold text-white/60"
                            >
                                Base de datos
                            </span>

                            <span
                                class="text-[9px] font-black uppercase text-[var(--adn-turquoise)]"
                            >
                                Operativa
                            </span>
                        </div>

                        <div
                            class="flex items-center justify-between rounded-xl border border-[var(--adn-coral-border)] bg-[var(--adn-coral-soft)] px-3.5 py-3"
                        >
                            <span
                                class="text-xs font-bold text-white/60"
                            >
                                Editor
                            </span>

                            <span
                                class="text-[9px] font-black uppercase text-[var(--adn-coral)]"
                            >
                                Local
                            </span>
                        </div>
                    </div>
                </article>

                <article
                    class="adn-panel relative overflow-hidden rounded-[22px] p-5"
                >
                    <span
                        class="absolute inset-x-0 top-0 h-[2px] bg-[var(--adn-orange)]"
                    />

                    <div
                        class="flex items-center gap-3"
                    >
                        <div
                            class="flex h-9 w-9 items-center justify-center rounded-xl bg-[var(--adn-orange-soft)] text-[var(--adn-orange)]"
                        >
                            <Clock3
                                class="h-4 w-4"
                            />
                        </div>

                        <div>
                            <p
                                class="text-xs font-black text-white/70"
                            >
                                Integración con app
                            </p>

                            <p
                                class="mt-0.5 text-[10px] text-white/27"
                            >
                                Solicitudes pendientes
                            </p>
                        </div>
                    </div>

                    <div
                        class="mt-5 flex items-end justify-between"
                    >
                        <span
                            class="text-4xl font-black tracking-[-0.05em] text-white"
                        >
                            {{
                                formatNumber(
                                    stats.pending_sync,
                                )
                            }}
                        </span>

                        <span
                            class="rounded-lg border border-[var(--adn-yellow-border)] bg-[var(--adn-yellow-soft)] px-2 py-1 text-[8px] font-black uppercase tracking-[0.1em] text-[var(--adn-yellow)]"
                        >
                            Próxima fase
                        </span>
                    </div>
                </article>
            </div>
        </section>
    </EditorLayout>
</template>
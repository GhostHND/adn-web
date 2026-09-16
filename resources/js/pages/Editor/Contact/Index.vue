<script setup lang="ts">
import {
    Head,
    Link,
    useForm,
    usePage,
} from '@inertiajs/vue3';

import {
    ArrowRight,
    CheckCircle2,
    CircleAlert,
    ExternalLink,
    Mail,
    MapPin,
    MessageSquareText,
    Phone,
    Save,
    Settings2,
    Sparkles,
} from '@lucide/vue';

import {
    computed,
} from 'vue';

import EditorLayout
    from '@/layouts/Editor/EditorLayout.vue';

interface ContactSettings {
    phone: string | null;
    whatsapp: string | null;
    email: string | null;
    address: string | null;
    business_hours: string | null;
    map_url: string | null;
}

interface SocialSettings {
    facebook: string | null;
    instagram: string | null;
    tiktok: string | null;
}

interface Stats {
    total: number;
    new: number;
    pending_sync: number;
    failed_sync: number;
    synced: number;
}

interface RecentMessage {
    id: number;
    message_number: string | null;
    name: string;
    subject: string;
    status: string;
    app_sync_status: string;
    created_at: string | null;
}

const props =
    defineProps<{
        settings: {
            contact: ContactSettings;
            social: SocialSettings;
        };

        stats: Stats;

        recentMessages: RecentMessage[];
    }>();

const page =
    usePage();

const flash =
    computed(
        () =>
            (
                page.props.flash
                ?? {}
            ) as {
                success?: string | null;
                error?: string | null;
            },
    );

const form =
    useForm({
        phone:
            props.settings.contact.phone
            ?? '',

        whatsapp:
            props.settings.contact.whatsapp
            ?? '',

        email:
            props.settings.contact.email
            ?? '',

        address:
            props.settings.contact.address
            ?? '',

        business_hours:
            props.settings.contact.business_hours
            ?? '',

        map_url:
            props.settings.contact.map_url
            ?? '',

        facebook:
            props.settings.social.facebook
            ?? '',

        instagram:
            props.settings.social.instagram
            ?? '',

        tiktok:
            props.settings.social.tiktok
            ?? '',
    });

const save =
    (): void => {
        form.put(
            '/editor-preview/contacto/configuracion',
            {
                preserveScroll:
                    true,
            },
        );
    };

const statusLabel =
    (
        status:
            string,
    ): string => {
        const labels:
            Record<string, string> =
        {
            new:
                'Nuevo',

            reviewing:
                'En revisión',

            contacted:
                'Contactado',

            closed:
                'Cerrado',

            spam:
                'Spam',
        };

        return labels[
            status
        ] ?? status;
    };

const syncLabel =
    (
        status:
            string,
    ): string => {
        const labels:
            Record<string, string> =
        {
            pending:
                'Pendiente',

            synced:
                'Sincronizado',

            failed:
                'Error',
        };

        return labels[
            status
        ] ?? status;
    };

const inputClass =
    'h-11 w-full rounded-xl border border-[var(--adn-border)] bg-white/[0.035] px-4 text-sm text-white outline-none transition placeholder:text-white/20 focus:border-[var(--adn-turquoise)]/50';

const textareaClass =
    'min-h-[110px] w-full resize-y rounded-xl border border-[var(--adn-border)] bg-white/[0.035] px-4 py-3 text-sm leading-6 text-white outline-none transition placeholder:text-white/20 focus:border-[var(--adn-turquoise)]/50';
</script>

<template>
    <Head
        title="Contacto"
    />

    <EditorLayout
        title="Contacto"
        eyebrow="Sitio web"
    >
        <section
            class="adn-panel relative overflow-hidden rounded-[26px] p-6 sm:p-8"
        >
            <div
                class="adn-brand-stripe absolute inset-x-0 top-0 h-[3px]"
            />

            <div
                class="relative flex flex-col justify-between gap-6 lg:flex-row lg:items-center"
            >
                <div
                    class="max-w-2xl"
                >
                    <div
                        class="inline-flex items-center gap-2 rounded-full border border-[var(--adn-turquoise-border)] bg-[var(--adn-turquoise-soft)] px-3 py-1.5"
                    >
                        <Sparkles
                            class="h-3.5 w-3.5 text-[var(--adn-turquoise)]"
                        />

                        <span
                            class="text-[9px] font-black uppercase tracking-[0.16em] text-[var(--adn-turquoise)]"
                        >
                            Contacto ADN
                        </span>
                    </div>

                    <h1
                        class="mt-5 text-3xl font-black tracking-[-0.04em] sm:text-4xl"
                    >
                        Conversaciones que pueden convertirse en

                        <span
                            class="text-[var(--adn-turquoise)]"
                        >
                            nuevos proyectos.
                        </span>
                    </h1>

                    <p
                        class="mt-3 max-w-xl text-sm leading-6 text-white/40"
                    >
                        Administra tus datos públicos de contacto,
                        revisa los mensajes recibidos y controla
                        su sincronización con ADN APP.
                    </p>
                </div>

                <div
                    class="flex flex-wrap gap-2"
                >
                    <Link
                        href="/editor-preview/contacto/mensajes"
                        class="adn-primary-button inline-flex h-11 items-center justify-center gap-2 rounded-xl px-5 text-xs font-black"
                    >
                        Ver mensajes

                        <ArrowRight
                            class="h-4 w-4"
                        />
                    </Link>

                    <a
                        href="/contacto"
                        target="_blank"
                        rel="noopener noreferrer"
                        class="inline-flex h-11 items-center justify-center gap-2 rounded-xl border border-white/[0.08] bg-white/[0.04] px-4 text-xs font-black text-white/55 transition hover:bg-white/[0.08] hover:text-white"
                    >
                        Ver página

                        <ExternalLink
                            class="h-4 w-4"
                        />
                    </a>
                </div>
            </div>
        </section>

        <div
            v-if="
                flash.success
                ||
                flash.error
            "
            class="mt-5 rounded-[18px] border p-4"
            :class="
                flash.error
                    ? 'border-[var(--adn-coral-border)] bg-[var(--adn-coral-soft)]'
                    : 'border-[var(--adn-turquoise-border)] bg-[var(--adn-turquoise-soft)]'
            "
        >
            <div
                class="flex items-start gap-3"
            >
                <CircleAlert
                    v-if="
                        flash.error
                    "
                    class="mt-0.5 h-4 w-4 shrink-0 text-[var(--adn-coral)]"
                />

                <CheckCircle2
                    v-else
                    class="mt-0.5 h-4 w-4 shrink-0 text-[var(--adn-turquoise)]"
                />

                <p
                    class="text-xs font-bold text-white/65"
                >
                    {{
                        flash.error
                        ?? flash.success
                    }}
                </p>
            </div>
        </div>

        <section
            class="mt-5 grid gap-4 sm:grid-cols-2 xl:grid-cols-5"
        >
            <article
                class="adn-panel rounded-[20px] p-5"
            >
                <MessageSquareText
                    class="h-5 w-5 text-[var(--adn-turquoise)]"
                />

                <p
                    class="mt-4 text-3xl font-black"
                >
                    {{ stats.total }}
                </p>

                <p
                    class="text-xs text-white/40"
                >
                    Mensajes
                </p>
            </article>

            <article
                class="adn-panel rounded-[20px] p-5"
            >
                <Mail
                    class="h-5 w-5 text-[var(--adn-coral)]"
                />

                <p
                    class="mt-4 text-3xl font-black"
                >
                    {{ stats.new }}
                </p>

                <p
                    class="text-xs text-white/40"
                >
                    Nuevos
                </p>
            </article>

            <article
                class="adn-panel rounded-[20px] p-5"
            >
                <Settings2
                    class="h-5 w-5 text-[var(--adn-orange)]"
                />

                <p
                    class="mt-4 text-3xl font-black"
                >
                    {{ stats.pending_sync }}
                </p>

                <p
                    class="text-xs text-white/40"
                >
                    Pendientes APP
                </p>
            </article>

            <article
                class="adn-panel rounded-[20px] p-5"
            >
                <CheckCircle2
                    class="h-5 w-5 text-[var(--adn-yellow)]"
                />

                <p
                    class="mt-4 text-3xl font-black"
                >
                    {{ stats.synced }}
                </p>

                <p
                    class="text-xs text-white/40"
                >
                    Sincronizados
                </p>
            </article>

            <article
                class="adn-panel rounded-[20px] p-5"
            >
                <CircleAlert
                    class="h-5 w-5 text-[var(--adn-coral)]"
                />

                <p
                    class="mt-4 text-3xl font-black"
                >
                    {{ stats.failed_sync }}
                </p>

                <p
                    class="text-xs text-white/40"
                >
                    Error de sincronización
                </p>
            </article>
        </section>

        <section
            class="mt-5 grid gap-5 xl:grid-cols-[minmax(0,1.15fr)_minmax(320px,.85fr)]"
        >
            <form
                class="adn-panel rounded-[22px] p-6"
                @submit.prevent="
                    save
                "
            >
                <div
                    class="flex items-center justify-between gap-4 border-b border-[var(--adn-border)] pb-5"
                >
                    <div>
                        <h2
                            class="text-sm font-black"
                        >
                            Información pública
                        </h2>

                        <p
                            class="mt-1 text-[10px] text-white/30"
                        >
                            Datos visibles en /contacto
                        </p>
                    </div>

                    <Phone
                        class="h-5 w-5 text-[var(--adn-turquoise)]"
                    />
                </div>

                <div
                    class="mt-6 grid gap-5 sm:grid-cols-2"
                >
                    <div>
                        <label
                            class="mb-2 block text-[9px] font-black uppercase tracking-[0.1em] text-white/35"
                        >
                            Teléfono
                        </label>

                        <input
                            v-model="
                                form.phone
                            "
                            type="text"
                            placeholder="+504 0000-0000"
                            :class="
                                inputClass
                            "
                        />

                        <p
                            v-if="
                                form.errors.phone
                            "
                            class="mt-2 text-[10px] font-bold text-[var(--adn-coral)]"
                        >
                            {{
                                form.errors.phone
                            }}
                        </p>
                    </div>

                    <div>
                        <label
                            class="mb-2 block text-[9px] font-black uppercase tracking-[0.1em] text-white/35"
                        >
                            WhatsApp
                        </label>

                        <input
                            v-model="
                                form.whatsapp
                            "
                            type="text"
                            placeholder="+504 0000-0000"
                            :class="
                                inputClass
                            "
                        />

                        <p
                            v-if="
                                form.errors.whatsapp
                            "
                            class="mt-2 text-[10px] font-bold text-[var(--adn-coral)]"
                        >
                            {{
                                form.errors.whatsapp
                            }}
                        </p>
                    </div>

                    <div
                        class="sm:col-span-2"
                    >
                        <label
                            class="mb-2 block text-[9px] font-black uppercase tracking-[0.1em] text-white/35"
                        >
                            Correo electrónico
                        </label>

                        <input
                            v-model="
                                form.email
                            "
                            type="email"
                            placeholder="contacto@adnpublicidad.site"
                            :class="
                                inputClass
                            "
                        />

                        <p
                            v-if="
                                form.errors.email
                            "
                            class="mt-2 text-[10px] font-bold text-[var(--adn-coral)]"
                        >
                            {{
                                form.errors.email
                            }}
                        </p>
                    </div>

                    <div
                        class="sm:col-span-2"
                    >
                        <label
                            class="mb-2 block text-[9px] font-black uppercase tracking-[0.1em] text-white/35"
                        >
                            Dirección
                        </label>

                        <textarea
                            v-model="
                                form.address
                            "
                            placeholder="Dirección pública de ADN Publicidad"
                            :class="
                                textareaClass
                            "
                        />

                        <p
                            v-if="
                                form.errors.address
                            "
                            class="mt-2 text-[10px] font-bold text-[var(--adn-coral)]"
                        >
                            {{
                                form.errors.address
                            }}
                        </p>
                    </div>

                    <div
                        class="sm:col-span-2"
                    >
                        <label
                            class="mb-2 block text-[9px] font-black uppercase tracking-[0.1em] text-white/35"
                        >
                            Horarios
                        </label>

                        <textarea
                            v-model="
                                form.business_hours
                            "
                            placeholder="Lunes a viernes..."
                            :class="
                                textareaClass
                            "
                        />

                        <p
                            v-if="
                                form.errors.business_hours
                            "
                            class="mt-2 text-[10px] font-bold text-[var(--adn-coral)]"
                        >
                            {{
                                form.errors.business_hours
                            }}
                        </p>
                    </div>

                    <div
                        class="sm:col-span-2"
                    >
                        <label
                            class="mb-2 block text-[9px] font-black uppercase tracking-[0.1em] text-white/35"
                        >
                            Enlace de ubicación
                        </label>

                        <div
                            class="relative"
                        >
                            <MapPin
                                class="absolute left-4 top-1/2 h-4 w-4 -translate-y-1/2 text-white/20"
                            />

                            <input
                                v-model="
                                    form.map_url
                                "
                                type="url"
                                placeholder="https://maps.google.com/..."
                                :class="
                                    `${inputClass} pl-11`
                                "
                            />
                        </div>

                        <p
                            v-if="
                                form.errors.map_url
                            "
                            class="mt-2 text-[10px] font-bold text-[var(--adn-coral)]"
                        >
                            {{
                                form.errors.map_url
                            }}
                        </p>
                    </div>
                </div>

                <div
                    class="mt-8 border-t border-[var(--adn-border)] pt-6"
                >
                    <h3
                        class="text-sm font-black"
                    >
                        Redes sociales
                    </h3>

                    <div
                        class="mt-5 grid gap-4"
                    >
                        <input
                            v-model="
                                form.facebook
                            "
                            type="url"
                            placeholder="https://facebook.com/..."
                            :class="
                                inputClass
                            "
                        />

                        <input
                            v-model="
                                form.instagram
                            "
                            type="url"
                            placeholder="https://instagram.com/..."
                            :class="
                                inputClass
                            "
                        />

                        <input
                            v-model="
                                form.tiktok
                            "
                            type="url"
                            placeholder="https://tiktok.com/@..."
                            :class="
                                inputClass
                            "
                        />
                    </div>
                </div>

                <button
                    type="submit"
                    :disabled="
                        form.processing
                    "
                    class="adn-primary-button mt-6 inline-flex h-11 items-center justify-center gap-2 rounded-xl px-5 text-xs font-black disabled:opacity-50"
                >
                    <Save
                        class="h-4 w-4"
                    />

                    {{
                        form.processing
                            ? 'Guardando...'
                            : 'Guardar información'
                    }}
                </button>
            </form>

            <div
                class="space-y-5"
            >
                <section
                    class="adn-panel rounded-[22px] p-6"
                >
                    <div
                        class="flex items-center justify-between"
                    >
                        <div>
                            <h2
                                class="text-sm font-black"
                            >
                                Contenido de la página
                            </h2>

                            <p
                                class="mt-1 text-[10px] text-white/30"
                            >
                                Hero, textos, CTA e imágenes
                            </p>
                        </div>

                        <Settings2
                            class="h-5 w-5 text-[var(--adn-orange)]"
                        />
                    </div>

                    <Link
                        href="/editor-preview/paginas/contacto/editar"
                        class="mt-5 flex items-center justify-between rounded-xl border border-white/[0.06] bg-white/[0.035] p-4 transition hover:bg-white/[0.06]"
                    >
                        <div>
                            <p
                                class="text-xs font-black text-white/75"
                            >
                                Editar contenido visual
                            </p>

                            <p
                                class="mt-1 text-[10px] text-white/25"
                            >
                                Utiliza el editor CMS existente.
                            </p>
                        </div>

                        <ArrowRight
                            class="h-4 w-4 text-[var(--adn-turquoise)]"
                        />
                    </Link>
                </section>

                <section
                    class="adn-panel overflow-hidden rounded-[22px]"
                >
                    <div
                        class="flex items-center justify-between border-b border-[var(--adn-border)] px-6 py-5"
                    >
                        <div>
                            <h2
                                class="text-sm font-black"
                            >
                                Mensajes recientes
                            </h2>

                            <p
                                class="mt-1 text-[10px] text-white/30"
                            >
                                Últimos contactos recibidos
                            </p>
                        </div>

                        <Link
                            href="/editor-preview/contacto/mensajes"
                            class="text-xs font-black text-[var(--adn-turquoise)]"
                        >
                            Ver todos
                        </Link>
                    </div>

                    <div
                        v-if="
                            recentMessages.length
                        "
                        class="divide-y divide-white/[0.045]"
                    >
                        <Link
                            v-for="
                                message in recentMessages
                            "
                            :key="
                                message.id
                            "
                            :href="
                                `/editor-preview/contacto/mensajes/${message.id}`
                            "
                            class="block px-6 py-4 transition hover:bg-white/[0.025]"
                        >
                            <div
                                class="flex items-start justify-between gap-4"
                            >
                                <div
                                    class="min-w-0"
                                >
                                    <p
                                        class="truncate text-sm font-black text-white/75"
                                    >
                                        {{
                                            message.name
                                        }}
                                    </p>

                                    <p
                                        class="mt-1 truncate text-xs text-white/35"
                                    >
                                        {{
                                            message.subject
                                        }}
                                    </p>

                                    <p
                                        class="mt-2 text-[9px] text-white/20"
                                    >
                                        {{
                                            message.message_number
                                        }}
                                        ·
                                        {{
                                            message.created_at
                                        }}
                                    </p>
                                </div>

                                <div
                                    class="shrink-0 text-right"
                                >
                                    <p
                                        class="text-[8px] font-black uppercase tracking-[0.08em] text-[var(--adn-turquoise)]"
                                    >
                                        {{
                                            statusLabel(
                                                message.status,
                                            )
                                        }}
                                    </p>

                                    <p
                                        class="mt-1 text-[8px] font-bold text-white/25"
                                    >
                                        {{
                                            syncLabel(
                                                message.app_sync_status,
                                            )
                                        }}
                                    </p>
                                </div>
                            </div>
                        </Link>
                    </div>

                    <div
                        v-else
                        class="px-6 py-10 text-center"
                    >
                        <MessageSquareText
                            class="mx-auto h-7 w-7 text-white/15"
                        />

                        <p
                            class="mt-3 text-xs font-black text-white/40"
                        >
                            Todavía no hay mensajes.
                        </p>
                    </div>
                </section>
            </div>
        </section>
    </EditorLayout>
</template>
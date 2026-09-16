<script setup lang="ts">
import {
    Head,
    Link,
    useForm,
    usePage,
} from '@inertiajs/vue3';

import {
    ArrowRight,
    Building2,
    CheckCircle2,
    Clock3,
    ExternalLink,
    Mail,
    MapPin,
    MessageCircle,
    Phone,
    Send,
    Share2,
    ShieldCheck,
    Sparkles,
} from '@lucide/vue';

import {
    computed,
    watch,
} from 'vue';

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

interface PageSection {
    id: number;
    section_key: string;
    section_type: string;
    title: string | null;
    subtitle: string | null;
    body: string | null;

    content: {
        items?: {
            title?: string;
            label?: string;
            text?: string;
            icon?: string;
        }[];
    };

    settings: {
        cta_label?: string;
        cta_url?: string;
    };

    media: Media | null;
    support_media: Media[];
}

interface ContactPage {
    slug: string;
    name: string;
    title: string | null;
    eyebrow: string | null;
    summary: string | null;
    meta_title: string | null;
    meta_description: string | null;
    canonical_url: string | null;
    sections: PageSection[];
}

interface ContactSettings {
    phone?: string | null;
    whatsapp?: string | null;
    email?: string | null;
    address?: string | null;
    business_hours?: string | null;
    map_url?: string | null;
}

interface SocialSettings {
    facebook?: string | null;
    instagram?: string | null;
    tiktok?: string | null;
}

interface SpamProtection {
    startedAt: number;
    token: string;
}

interface SharedFlash {
    success?: string | null;
    error?: string | null;
}

const props =
    defineProps<{
        page: ContactPage;
        contact: ContactSettings;
        social: SocialSettings;
        spamProtection: SpamProtection;
    }>();

const inertiaPage =
    usePage();

const flash =
    computed(
        (): SharedFlash => {
            return (
                inertiaPage.props.flash
                ?? {}
            ) as SharedFlash;
        },
    );

const section =
    (
        key:
            string,
    ): PageSection | null => {
        return props.page.sections.find(
            (
                item,
            ) =>
                item.section_key ===
                key,
        )
        ?? null;
    };

const hero =
    computed(
        () =>
            section(
                'hero',
            ),
    );

const contactSection =
    computed(
        () =>
            section(
                'contact',
            ),
    );

const formSection =
    computed(
        () =>
            section(
                'form',
            ),
    );

const ctaSection =
    computed(
        () =>
            section(
                'cta',
            ),
    );

const heroImage =
    computed(
        (): Media | null => {
            if (
                hero.value?.media
            ) {
                return hero.value.media;
            }

            return (
                hero.value
                    ?.support_media[
                        0
                    ]
                ?? null
            );
        },
    );

const phoneHref =
    computed(
        (): string | null => {
            const value =
                props.contact.phone
                    ?.trim();

            if (
                !value
            ) {
                return null;
            }

            const normalized =
                value.replace(
                    /[^\d+]/g,
                    '',
                );

            return normalized
                ? `tel:${normalized}`
                : null;
        },
    );

const whatsappHref =
    computed(
        (): string | null => {
            const value =
                props.contact.whatsapp
                    ?.trim();

            if (
                !value
            ) {
                return null;
            }

            if (
                /^https?:\/\//i.test(
                    value,
                )
            ) {
                return value;
            }

            let digits =
                value.replace(
                    /\D/g,
                    '',
                );

            if (
                digits.length ===
                8
            ) {
                digits =
                    `504${digits}`;
            }

            return digits
                ? `https://wa.me/${digits}`
                : null;
        },
    );

const emailHref =
    computed(
        (): string | null => {
            const value =
                props.contact.email
                    ?.trim();

            return value
                ? `mailto:${value}`
                : null;
        },
    );

const directContactCount =
    computed(
        (): number => {
            return [
                props.contact.whatsapp,
                props.contact.phone,
                props.contact.email,
                props.contact.address,
                props.contact.business_hours,
            ].filter(
                (
                    value,
                ) =>
                    Boolean(
                        value?.trim(),
                    ),
            ).length;
        },
    );

const socialLinks =
    computed(
        () => {
            const values = [
                {
                    key:
                        'facebook',

                    label:
                        'Facebook',

                    url:
                        props.social.facebook,
                },

                {
                    key:
                        'instagram',

                    label:
                        'Instagram',

                    url:
                        props.social.instagram,
                },

                {
                    key:
                        'tiktok',

                    label:
                        'TikTok',

                    url:
                        props.social.tiktok,
                },
            ];

            return values.filter(
                (
                    item,
                ) =>
                    Boolean(
                        item.url?.trim(),
                    ),
            );
        },
    );

const form =
    useForm({
        name:
            '',

        company:
            '',

        phone:
            '',

        email:
            '',

        preferred_contact:
            'whatsapp',

        subject:
            '',

        message:
            '',

        privacy_consent:
            false,

        website:
            '',

        form_started_at:
            props
                .spamProtection
                .startedAt,

        form_token:
            props
                .spamProtection
                .token,
    });

watch(
    () =>
        props.spamProtection,
    (
        protection,
    ) => {
        form.form_started_at =
            protection.startedAt;

        form.form_token =
            protection.token;
    },
    {
        deep:
            true,
    },
);

const submit =
    (): void => {
        form.post(
            '/contacto',
            {
                preserveScroll:
                    true,

                onSuccess:
                    () => {
                        form.name =
                            '';

                        form.company =
                            '';

                        form.phone =
                            '';

                        form.email =
                            '';

                        form.preferred_contact =
                            'whatsapp';

                        form.subject =
                            '';

                        form.message =
                            '';

                        form.privacy_consent =
                            false;

                        form.website =
                            '';
                    },
            },
        );
    };

const fieldClass =
    'h-12 w-full rounded-xl border border-black/[0.08] bg-white px-4 text-sm font-medium text-[#1D1D1B] outline-none transition placeholder:text-black/25 focus:border-[#0FA7B4]/55 focus:ring-4 focus:ring-[#0FA7B4]/[0.07]';

const textareaClass =
    'min-h-[150px] w-full resize-y rounded-xl border border-black/[0.08] bg-white px-4 py-3 text-sm font-medium leading-6 text-[#1D1D1B] outline-none transition placeholder:text-black/25 focus:border-[#0FA7B4]/55 focus:ring-4 focus:ring-[#0FA7B4]/[0.07]';
</script>

<template>
    <Head
        :title="
            page.meta_title
            ?? 'Contacto | ADN Publicidad'
        "
    >
        <meta
            v-if="
                page.meta_description
            "
            head-key="description"
            name="description"
            :content="
                page.meta_description
            "
        >

        <link
            v-if="
                page.canonical_url
            "
            head-key="canonical"
            rel="canonical"
            :href="
                page.canonical_url
            "
        >
    </Head>

    <PublicLayout>
        <!-- HERO -->

        <section
            class="relative overflow-hidden bg-[#101516] text-white"
        >
            <div
                class="absolute -left-36 -top-36 h-[420px] w-[420px] rounded-full bg-[#0FA7B4]/12 blur-3xl"
            />

            <div
                class="absolute -right-28 bottom-[-160px] h-[420px] w-[420px] rounded-full bg-[#E84657]/10 blur-3xl"
            />

            <div
                class="absolute left-[45%] top-[-220px] h-[380px] w-[380px] rounded-full bg-[#ED7E24]/[0.06] blur-3xl"
            />

            <div
                class="relative mx-auto grid max-w-[1500px] items-center gap-10 px-5 py-16 sm:px-7 sm:py-20 lg:grid-cols-[minmax(0,1fr)_minmax(360px,.72fr)] lg:py-24 xl:px-10"
            >
                <div
                    class="max-w-3xl"
                >
                    <div
                        class="inline-flex items-center gap-2 rounded-full border border-[#0FA7B4]/25 bg-[#0FA7B4]/10 px-3 py-2 text-[10px] font-black uppercase tracking-[0.14em] text-[#35c4ce]"
                    >
                        <Sparkles
                            class="h-3.5 w-3.5"
                        />

                        {{
                            hero?.subtitle
                            ?? page.eyebrow
                            ?? 'Contacto ADN'
                        }}
                    </div>

                    <h1
                        class="mt-6 text-4xl font-black leading-[0.98] tracking-[-0.05em] sm:text-5xl lg:text-6xl"
                    >
                        {{
                            hero?.title
                            ?? page.title
                            ?? 'Conversemos sobre tu proyecto.'
                        }}
                    </h1>

                    <p
                        v-if="
                            hero?.body
                            ?? page.summary
                        "
                        class="mt-5 max-w-2xl text-base leading-7 text-white/50"
                    >
                        {{
                            hero?.body
                            ?? page.summary
                        }}
                    </p>

                    <div
                        class="mt-8 flex flex-wrap gap-3"
                    >
                        <a
                            v-if="
                                whatsappHref
                            "
                            :href="
                                whatsappHref
                            "
                            target="_blank"
                            rel="noopener noreferrer"
                            class="inline-flex min-h-12 items-center justify-center gap-2 rounded-xl bg-[#0FA7B4] px-5 text-xs font-black text-white transition duration-300 hover:-translate-y-0.5 hover:bg-[#0d96a2]"
                        >
                            <MessageCircle
                                class="h-4 w-4"
                            />

                            Escribir por WhatsApp
                        </a>

                        <a
                            v-if="
                                phoneHref
                            "
                            :href="
                                phoneHref
                            "
                            class="inline-flex min-h-12 items-center justify-center gap-2 rounded-xl border border-white/[0.09] bg-white/[0.05] px-5 text-xs font-black text-white transition hover:bg-white/[0.09]"
                        >
                            <Phone
                                class="h-4 w-4"
                            />

                            Llamar
                        </a>

                        <a
                            v-if="
                                !whatsappHref
                                &&
                                !phoneHref
                            "
                            href="#mensaje"
                            class="inline-flex min-h-12 items-center justify-center gap-2 rounded-xl bg-[#0FA7B4] px-5 text-xs font-black text-white transition duration-300 hover:-translate-y-0.5 hover:bg-[#0d96a2]"
                        >
                            <Send
                                class="h-4 w-4"
                            />

                            Enviar un mensaje
                        </a>
                    </div>
                </div>

                <div
                    v-if="
                        heroImage
                    "
                    class="relative hidden lg:block"
                >
                    <div
                        class="absolute -inset-4 rounded-[34px] bg-[linear-gradient(135deg,#0FA7B4,#ED7E24,#E84657)] opacity-15 blur-2xl"
                    />

                    <div
                        class="relative overflow-hidden rounded-[30px] border border-white/[0.08] bg-white/[0.04] p-2 shadow-2xl"
                    >
                        <img
                            :src="
                                heroImage.url
                            "
                            :alt="
                                heroImage.alt_text
                                ?? hero?.title
                                ?? 'ADN Publicidad'
                            "
                            class="aspect-[5/4] w-full rounded-[24px] object-cover"
                        >
                    </div>
                </div>

                <div
                    v-else
                    class="relative hidden lg:block"
                >
                    <div
                        class="overflow-hidden rounded-[30px] border border-white/[0.08] bg-white/[0.035] p-7 shadow-2xl backdrop-blur-xl"
                    >
                        <div
                            class="flex h-14 w-14 items-center justify-center rounded-2xl bg-[#0FA7B4]/12 text-[#35c4ce] ring-1 ring-[#0FA7B4]/20"
                        >
                            <MessageCircle
                                class="h-6 w-6"
                            />
                        </div>

                        <p
                            class="mt-7 text-[10px] font-black uppercase tracking-[0.15em] text-[#35c4ce]"
                        >
                            ADN Publicidad
                        </p>

                        <p
                            class="mt-3 text-2xl font-black leading-tight tracking-[-0.035em]"
                        >
                            Una buena solución comienza con una buena conversación.
                        </p>

                        <div
                            class="mt-7 h-[3px] w-full overflow-hidden rounded-full bg-white/[0.05]"
                        >
                            <div
                                class="h-full w-2/3 rounded-full bg-[linear-gradient(90deg,#0FA7B4,#ED7E24,#E84657)]"
                            />
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- MEDIOS DE CONTACTO -->

        <section
            class="bg-[#f6f8f8]"
        >
            <div
                class="mx-auto max-w-[1500px] px-5 py-12 sm:px-7 sm:py-16 xl:px-10"
            >
                <div
                    class="flex flex-col justify-between gap-5 lg:flex-row lg:items-end"
                >
                    <div
                        class="max-w-2xl"
                    >
                        <p
                            class="text-[10px] font-black uppercase tracking-[0.15em] text-[#ED7E24]"
                        >
                            {{
                                contactSection?.subtitle
                                ?? 'Estamos cerca'
                            }}
                        </p>

                        <h2
                            class="mt-3 text-3xl font-black tracking-[-0.04em] text-[#1D1D1B] sm:text-4xl"
                        >
                            {{
                                contactSection?.title
                                ?? 'Elige cómo quieres comunicarte.'
                            }}
                        </h2>

                        <p
                            v-if="
                                contactSection?.body
                            "
                            class="mt-4 max-w-xl text-sm leading-7 text-black/40"
                        >
                            {{
                                contactSection.body
                            }}
                        </p>
                    </div>

                    <p
                        v-if="
                            directContactCount
                        "
                        class="text-xs font-bold text-black/25"
                    >
                        {{
                            directContactCount
                        }}
                        vías de contacto disponibles
                    </p>
                </div>

                <div
                    v-if="
                        directContactCount
                    "
                    class="mt-8 grid gap-4 sm:grid-cols-2 xl:grid-cols-3"
                >
                    <!-- WhatsApp -->

                    <a
                        v-if="
                            contact.whatsapp
                            &&
                            whatsappHref
                        "
                        :href="
                            whatsappHref
                        "
                        target="_blank"
                        rel="noopener noreferrer"
                        class="group relative overflow-hidden rounded-[24px] border border-black/[0.06] bg-white p-6 shadow-[0_12px_35px_rgba(0,0,0,.035)] transition duration-300 hover:-translate-y-1 hover:shadow-[0_20px_45px_rgba(0,0,0,.07)]"
                    >
                        <div
                            class="absolute inset-x-0 top-0 h-[3px] bg-[#0FA7B4]"
                        />

                        <div
                            class="flex items-start justify-between gap-4"
                        >
                            <span
                                class="flex h-12 w-12 items-center justify-center rounded-2xl bg-[#0FA7B4]/10 text-[#0FA7B4]"
                            >
                                <MessageCircle
                                    class="h-5 w-5"
                                />
                            </span>

                            <ExternalLink
                                class="h-4 w-4 text-black/15 transition group-hover:text-[#0FA7B4]"
                            />
                        </div>

                        <p
                            class="mt-6 text-[9px] font-black uppercase tracking-[0.12em] text-[#0FA7B4]"
                        >
                            WhatsApp
                        </p>

                        <p
                            class="mt-2 text-lg font-black text-[#1D1D1B]"
                        >
                            {{
                                contact.whatsapp
                            }}
                        </p>

                        <p
                            class="mt-2 text-xs leading-5 text-black/35"
                        >
                            Escríbenos directamente para consultas rápidas.
                        </p>
                    </a>

                    <!-- Teléfono -->

                    <a
                        v-if="
                            contact.phone
                            &&
                            phoneHref
                        "
                        :href="
                            phoneHref
                        "
                        class="group relative overflow-hidden rounded-[24px] border border-black/[0.06] bg-white p-6 shadow-[0_12px_35px_rgba(0,0,0,.035)] transition duration-300 hover:-translate-y-1 hover:shadow-[0_20px_45px_rgba(0,0,0,.07)]"
                    >
                        <div
                            class="absolute inset-x-0 top-0 h-[3px] bg-[#E84657]"
                        />

                        <div
                            class="flex items-start justify-between gap-4"
                        >
                            <span
                                class="flex h-12 w-12 items-center justify-center rounded-2xl bg-[#E84657]/10 text-[#E84657]"
                            >
                                <Phone
                                    class="h-5 w-5"
                                />
                            </span>

                            <ArrowRight
                                class="h-4 w-4 text-black/15 transition group-hover:translate-x-1 group-hover:text-[#E84657]"
                            />
                        </div>

                        <p
                            class="mt-6 text-[9px] font-black uppercase tracking-[0.12em] text-[#E84657]"
                        >
                            Teléfono
                        </p>

                        <p
                            class="mt-2 text-lg font-black text-[#1D1D1B]"
                        >
                            {{
                                contact.phone
                            }}
                        </p>

                        <p
                            class="mt-2 text-xs leading-5 text-black/35"
                        >
                            Llámanos para conversar directamente.
                        </p>
                    </a>

                    <!-- Correo -->

                    <a
                        v-if="
                            contact.email
                            &&
                            emailHref
                        "
                        :href="
                            emailHref
                        "
                        class="group relative overflow-hidden rounded-[24px] border border-black/[0.06] bg-white p-6 shadow-[0_12px_35px_rgba(0,0,0,.035)] transition duration-300 hover:-translate-y-1 hover:shadow-[0_20px_45px_rgba(0,0,0,.07)]"
                    >
                        <div
                            class="absolute inset-x-0 top-0 h-[3px] bg-[#ED7E24]"
                        />

                        <div
                            class="flex items-start justify-between gap-4"
                        >
                            <span
                                class="flex h-12 w-12 items-center justify-center rounded-2xl bg-[#ED7E24]/10 text-[#ED7E24]"
                            >
                                <Mail
                                    class="h-5 w-5"
                                />
                            </span>

                            <ArrowRight
                                class="h-4 w-4 text-black/15 transition group-hover:translate-x-1 group-hover:text-[#ED7E24]"
                            />
                        </div>

                        <p
                            class="mt-6 text-[9px] font-black uppercase tracking-[0.12em] text-[#ED7E24]"
                        >
                            Correo
                        </p>

                        <p
                            class="mt-2 break-all text-base font-black text-[#1D1D1B]"
                        >
                            {{
                                contact.email
                            }}
                        </p>

                        <p
                            class="mt-2 text-xs leading-5 text-black/35"
                        >
                            Ideal para información detallada o documentación.
                        </p>
                    </a>

                    <!-- Ubicación -->

                    <component
                        :is="
                            contact.map_url
                                ? 'a'
                                : 'article'
                        "
                        v-if="
                            contact.address
                        "
                        :href="
                            contact.map_url
                            ?? undefined
                        "
                        :target="
                            contact.map_url
                                ? '_blank'
                                : undefined
                        "
                        :rel="
                            contact.map_url
                                ? 'noopener noreferrer'
                                : undefined
                        "
                        class="group relative overflow-hidden rounded-[24px] border border-black/[0.06] bg-white p-6 shadow-[0_12px_35px_rgba(0,0,0,.035)] transition duration-300 hover:-translate-y-1 hover:shadow-[0_20px_45px_rgba(0,0,0,.07)]"
                    >
                        <div
                            class="absolute inset-x-0 top-0 h-[3px] bg-[#F5C000]"
                        />

                        <div
                            class="flex items-start justify-between gap-4"
                        >
                            <span
                                class="flex h-12 w-12 items-center justify-center rounded-2xl bg-[#F5C000]/15 text-[#a88300]"
                            >
                                <MapPin
                                    class="h-5 w-5"
                                />
                            </span>

                            <ExternalLink
                                v-if="
                                    contact.map_url
                                "
                                class="h-4 w-4 text-black/15 transition group-hover:text-[#a88300]"
                            />
                        </div>

                        <p
                            class="mt-6 text-[9px] font-black uppercase tracking-[0.12em] text-[#a88300]"
                        >
                            Ubicación
                        </p>

                        <p
                            class="mt-2 whitespace-pre-line text-base font-black leading-6 text-[#1D1D1B]"
                        >
                            {{
                                contact.address
                            }}
                        </p>

                        <p
                            v-if="
                                contact.map_url
                            "
                            class="mt-2 text-xs leading-5 text-black/35"
                        >
                            Abrir ubicación.
                        </p>
                    </component>

                    <!-- Horarios -->

                    <article
                        v-if="
                            contact.business_hours
                        "
                        class="relative overflow-hidden rounded-[24px] border border-black/[0.06] bg-white p-6 shadow-[0_12px_35px_rgba(0,0,0,.035)]"
                    >
                        <div
                            class="absolute inset-x-0 top-0 h-[3px] bg-[#1D1D1B]"
                        />

                        <span
                            class="flex h-12 w-12 items-center justify-center rounded-2xl bg-black/[0.05] text-black/55"
                        >
                            <Clock3
                                class="h-5 w-5"
                            />
                        </span>

                        <p
                            class="mt-6 text-[9px] font-black uppercase tracking-[0.12em] text-black/40"
                        >
                            Horarios
                        </p>

                        <p
                            class="mt-2 whitespace-pre-line text-sm font-bold leading-6 text-[#1D1D1B]"
                        >
                            {{
                                contact.business_hours
                            }}
                        </p>
                    </article>
                </div>

                <div
                    v-else
                    class="mt-8 rounded-[24px] border border-black/[0.06] bg-white p-6 shadow-[0_12px_35px_rgba(0,0,0,.035)] sm:p-8"
                >
                    <MessageCircle
                        class="h-7 w-7 text-[#0FA7B4]"
                    />

                    <h3
                        class="mt-4 text-xl font-black tracking-[-0.025em]"
                    >
                        Puedes escribirnos desde el formulario.
                    </h3>

                    <p
                        class="mt-2 max-w-lg text-sm leading-6 text-black/40"
                    >
                        Déjanos los detalles de tu consulta y podremos continuar contigo.
                    </p>
                </div>

                <!-- Redes sociales -->

                <div
                    v-if="
                        socialLinks.length
                    "
                    class="mt-6 flex flex-col gap-4 rounded-[22px] border border-black/[0.06] bg-white px-5 py-5 sm:flex-row sm:items-center sm:justify-between sm:px-6"
                >
                    <div
                        class="flex items-center gap-3"
                    >
                        <span
                            class="flex h-10 w-10 items-center justify-center rounded-xl bg-[#101516] text-white"
                        >
                            <Share2
                                class="h-4 w-4"
                            />
                        </span>

                        <div>
                            <p
                                class="text-sm font-black text-[#1D1D1B]"
                            >
                                Síguenos en redes
                            </p>

                            <p
                                class="mt-0.5 text-[10px] text-black/30"
                            >
                                Contenido, proyectos y novedades de ADN.
                            </p>
                        </div>
                    </div>

                    <div
                        class="flex flex-wrap gap-2"
                    >
                        <a
                            v-for="
                                item in socialLinks
                            "
                            :key="
                                item.key
                            "
                            :href="
                                item.url
                                ?? '#'
                            "
                            target="_blank"
                            rel="noopener noreferrer"
                            class="inline-flex h-10 items-center gap-2 rounded-xl border border-black/[0.07] bg-[#f8f9f9] px-4 text-xs font-black text-black/45 transition hover:border-[#0FA7B4]/30 hover:text-[#0FA7B4]"
                        >
                            {{
                                item.label
                            }}

                            <ExternalLink
                                class="h-3.5 w-3.5"
                            />
                        </a>
                    </div>
                </div>
            </div>
        </section>

        <!-- FORMULARIO -->

        <section
            id="mensaje"
            class="scroll-mt-24 bg-white"
        >
            <div
                class="mx-auto grid max-w-[1500px] gap-8 px-5 py-14 sm:px-7 sm:py-20 lg:grid-cols-[minmax(300px,.72fr)_minmax(0,1.28fr)] lg:gap-14 xl:px-10"
            >
                <!-- Información lateral -->

                <div>
                    <p
                        class="text-[10px] font-black uppercase tracking-[0.15em] text-[#E84657]"
                    >
                        {{
                            formSection?.subtitle
                            ?? 'Cuéntanos tu idea'
                        }}
                    </p>

                    <h2
                        class="mt-3 text-3xl font-black tracking-[-0.04em] text-[#1D1D1B] sm:text-4xl"
                    >
                        {{
                            formSection?.title
                            ?? 'Envíanos un mensaje.'
                        }}
                    </h2>

                    <p
                        v-if="
                            formSection?.body
                        "
                        class="mt-4 text-sm leading-7 text-black/40"
                    >
                        {{
                            formSection.body
                        }}
                    </p>

                    <div
                        class="mt-8 space-y-4"
                    >
                        <div
                            class="flex items-start gap-3"
                        >
                            <span
                                class="mt-0.5 flex h-9 w-9 shrink-0 items-center justify-center rounded-xl bg-[#0FA7B4]/10 text-[#0FA7B4]"
                            >
                                <CheckCircle2
                                    class="h-4 w-4"
                                />
                            </span>

                            <div>
                                <p
                                    class="text-sm font-black"
                                >
                                    Información clara
                                </p>

                                <p
                                    class="mt-1 text-xs leading-5 text-black/35"
                                >
                                    Cuéntanos qué necesitas y cualquier detalle importante.
                                </p>
                            </div>
                        </div>

                        <div
                            class="flex items-start gap-3"
                        >
                            <span
                                class="mt-0.5 flex h-9 w-9 shrink-0 items-center justify-center rounded-xl bg-[#ED7E24]/10 text-[#ED7E24]"
                            >
                                <ShieldCheck
                                    class="h-4 w-4"
                                />
                            </span>

                            <div>
                                <p
                                    class="text-sm font-black"
                                >
                                    Datos protegidos
                                </p>

                                <p
                                    class="mt-1 text-xs leading-5 text-black/35"
                                >
                                    Utilizamos tus datos únicamente para gestionar tu consulta.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Formulario -->

                <div
                    class="rounded-[26px] border border-black/[0.07] bg-[#f8f9f9] p-5 shadow-[0_20px_60px_rgba(22,34,35,.05)] sm:p-7 lg:p-8"
                >
                    <div
                        v-if="
                            flash.success
                        "
                        class="mb-6 rounded-2xl border border-[#0FA7B4]/20 bg-[#0FA7B4]/[0.07] p-4"
                    >
                        <div
                            class="flex items-start gap-3"
                        >
                            <span
                                class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl bg-[#0FA7B4] text-white"
                            >
                                <CheckCircle2
                                    class="h-4 w-4"
                                />
                            </span>

                            <div>
                                <p
                                    class="text-sm font-black text-[#1D1D1B]"
                                >
                                    Mensaje recibido
                                </p>

                                <p
                                    class="mt-1 text-xs leading-5 text-black/45"
                                >
                                    {{
                                        flash.success
                                    }}
                                </p>
                            </div>
                        </div>
                    </div>

                    <form
                        class="grid gap-5"
                        @submit.prevent="
                            submit
                        "
                    >
                        <!-- Honeypot -->

                        <div
                            class="pointer-events-none absolute -left-[10000px] top-auto h-px w-px overflow-hidden"
                            aria-hidden="true"
                        >
                            <label
                                for="website"
                            >
                                Sitio web
                            </label>

                            <input
                                id="website"
                                v-model="
                                    form.website
                                "
                                type="text"
                                name="website"
                                tabindex="-1"
                                autocomplete="off"
                            >
                        </div>

                        <div
                            class="grid gap-5 sm:grid-cols-2"
                        >
                            <div>
                                <label
                                    for="contact-name"
                                    class="mb-2 block text-[10px] font-black uppercase tracking-[0.09em] text-black/45"
                                >
                                    Nombre
                                    <span
                                        class="text-[#E84657]"
                                    >
                                        *
                                    </span>
                                </label>

                                <input
                                    id="contact-name"
                                    v-model="
                                        form.name
                                    "
                                    type="text"
                                    autocomplete="name"
                                    maxlength="160"
                                    placeholder="Tu nombre"
                                    :class="
                                        fieldClass
                                    "
                                >

                                <p
                                    v-if="
                                        form.errors.name
                                    "
                                    class="mt-2 text-xs font-bold text-[#E84657]"
                                >
                                    {{
                                        form.errors.name
                                    }}
                                </p>
                            </div>

                            <div>
                                <label
                                    for="contact-company"
                                    class="mb-2 block text-[10px] font-black uppercase tracking-[0.09em] text-black/45"
                                >
                                    Empresa
                                    <span
                                        class="font-bold normal-case tracking-normal text-black/20"
                                    >
                                        opcional
                                    </span>
                                </label>

                                <div
                                    class="relative"
                                >
                                    <Building2
                                        class="absolute left-4 top-1/2 h-4 w-4 -translate-y-1/2 text-black/20"
                                    />

                                    <input
                                        id="contact-company"
                                        v-model="
                                            form.company
                                        "
                                        type="text"
                                        autocomplete="organization"
                                        maxlength="190"
                                        placeholder="Empresa o negocio"
                                        :class="
                                            `${fieldClass} pl-11`
                                        "
                                    >
                                </div>

                                <p
                                    v-if="
                                        form.errors.company
                                    "
                                    class="mt-2 text-xs font-bold text-[#E84657]"
                                >
                                    {{
                                        form.errors.company
                                    }}
                                </p>
                            </div>
                        </div>

                        <div
                            class="grid gap-5 sm:grid-cols-2"
                        >
                            <div>
                                <label
                                    for="contact-phone"
                                    class="mb-2 block text-[10px] font-black uppercase tracking-[0.09em] text-black/45"
                                >
                                    Teléfono / WhatsApp
                                </label>

                                <div
                                    class="relative"
                                >
                                    <Phone
                                        class="absolute left-4 top-1/2 h-4 w-4 -translate-y-1/2 text-black/20"
                                    />

                                    <input
                                        id="contact-phone"
                                        v-model="
                                            form.phone
                                        "
                                        type="tel"
                                        autocomplete="tel"
                                        maxlength="40"
                                        placeholder="Tu número"
                                        :class="
                                            `${fieldClass} pl-11`
                                        "
                                    >
                                </div>

                                <p
                                    v-if="
                                        form.errors.phone
                                    "
                                    class="mt-2 text-xs font-bold text-[#E84657]"
                                >
                                    {{
                                        form.errors.phone
                                    }}
                                </p>
                            </div>

                            <div>
                                <label
                                    for="contact-email"
                                    class="mb-2 block text-[10px] font-black uppercase tracking-[0.09em] text-black/45"
                                >
                                    Correo electrónico
                                </label>

                                <div
                                    class="relative"
                                >
                                    <Mail
                                        class="absolute left-4 top-1/2 h-4 w-4 -translate-y-1/2 text-black/20"
                                    />

                                    <input
                                        id="contact-email"
                                        v-model="
                                            form.email
                                        "
                                        type="email"
                                        autocomplete="email"
                                        maxlength="190"
                                        placeholder="correo@ejemplo.com"
                                        :class="
                                            `${fieldClass} pl-11`
                                        "
                                    >
                                </div>

                                <p
                                    v-if="
                                        form.errors.email
                                    "
                                    class="mt-2 text-xs font-bold text-[#E84657]"
                                >
                                    {{
                                        form.errors.email
                                    }}
                                </p>
                            </div>
                        </div>

                        <p
                            class="-mt-2 text-[10px] leading-5 text-black/30"
                        >
                            Debes indicar al menos un teléfono o un correo electrónico.
                        </p>

                        <div
                            class="grid gap-5 sm:grid-cols-2"
                        >
                            <div>
                                <label
                                    for="contact-preference"
                                    class="mb-2 block text-[10px] font-black uppercase tracking-[0.09em] text-black/45"
                                >
                                    ¿Cómo prefieres que te contactemos?
                                </label>

                                <select
                                    id="contact-preference"
                                    v-model="
                                        form.preferred_contact
                                    "
                                    :class="
                                        fieldClass
                                    "
                                >
                                    <option
                                        value="whatsapp"
                                    >
                                        WhatsApp
                                    </option>

                                    <option
                                        value="phone"
                                    >
                                        Llamada telefónica
                                    </option>

                                    <option
                                        value="email"
                                    >
                                        Correo electrónico
                                    </option>
                                </select>

                                <p
                                    v-if="
                                        form.errors.preferred_contact
                                    "
                                    class="mt-2 text-xs font-bold text-[#E84657]"
                                >
                                    {{
                                        form.errors.preferred_contact
                                    }}
                                </p>
                            </div>

                            <div>
                                <label
                                    for="contact-subject"
                                    class="mb-2 block text-[10px] font-black uppercase tracking-[0.09em] text-black/45"
                                >
                                    Asunto
                                    <span
                                        class="text-[#E84657]"
                                    >
                                        *
                                    </span>
                                </label>

                                <input
                                    id="contact-subject"
                                    v-model="
                                        form.subject
                                    "
                                    type="text"
                                    maxlength="190"
                                    placeholder="¿En qué podemos ayudarte?"
                                    :class="
                                        fieldClass
                                    "
                                >

                                <p
                                    v-if="
                                        form.errors.subject
                                    "
                                    class="mt-2 text-xs font-bold text-[#E84657]"
                                >
                                    {{
                                        form.errors.subject
                                    }}
                                </p>
                            </div>
                        </div>

                        <div>
                            <div
                                class="mb-2 flex items-center justify-between gap-3"
                            >
                                <label
                                    for="contact-message"
                                    class="block text-[10px] font-black uppercase tracking-[0.09em] text-black/45"
                                >
                                    Mensaje
                                    <span
                                        class="text-[#E84657]"
                                    >
                                        *
                                    </span>
                                </label>

                                <span
                                    class="text-[9px] font-bold text-black/20"
                                >
                                    {{
                                        form.message.length
                                    }}
                                    / 5000
                                </span>
                            </div>

                            <textarea
                                id="contact-message"
                                v-model="
                                    form.message
                                "
                                maxlength="5000"
                                placeholder="Cuéntanos qué necesitas, para qué proyecto es o cualquier detalle que debamos conocer."
                                :class="
                                    textareaClass
                                "
                            />

                            <p
                                v-if="
                                    form.errors.message
                                "
                                class="mt-2 text-xs font-bold text-[#E84657]"
                            >
                                {{
                                    form.errors.message
                                }}
                            </p>
                        </div>

                        <label
                            class="flex cursor-pointer items-start gap-3 rounded-xl border border-black/[0.06] bg-white p-4"
                        >
                            <input
                                v-model="
                                    form.privacy_consent
                                "
                                type="checkbox"
                                class="mt-0.5 h-4 w-4 shrink-0 accent-[#0FA7B4]"
                            >

                            <span
                                class="text-xs leading-5 text-black/45"
                            >
                                Autorizo el uso de estos datos exclusivamente para atender y dar seguimiento a mi consulta.
                            </span>
                        </label>

                        <p
                            v-if="
                                form.errors.privacy_consent
                            "
                            class="-mt-3 text-xs font-bold text-[#E84657]"
                        >
                            {{
                                form.errors.privacy_consent
                            }}
                        </p>

                        <div
                            v-if="
                                form.errors.form_token
                                ||
                                form.errors.form_started_at
                                ||
                                form.errors.website
                            "
                            class="rounded-xl border border-[#E84657]/20 bg-[#E84657]/[0.06] px-4 py-3 text-xs font-bold leading-5 text-[#b52f3e]"
                        >
                            {{
                                form.errors.form_token
                                ?? form.errors.form_started_at
                                ?? form.errors.website
                            }}
                        </div>

                        <button
                            type="submit"
                            :disabled="
                                form.processing
                            "
                            class="group flex min-h-13 w-full items-center justify-between gap-4 rounded-[16px] bg-[#101516] px-5 py-4 text-left text-white shadow-[0_14px_35px_rgba(15,21,22,.14)] transition duration-300 hover:-translate-y-0.5 hover:bg-[#0FA7B4] disabled:pointer-events-none disabled:opacity-50"
                        >
                            <span>
                                <span
                                    class="block text-[9px] font-black uppercase tracking-[0.12em] text-[#35c4ce] group-hover:text-white/70"
                                >
                                    {{
                                        form.processing
                                            ? 'Enviando'
                                            : 'Contacto ADN'
                                    }}
                                </span>

                                <span
                                    class="mt-1 block text-sm font-black"
                                >
                                    {{
                                        form.processing
                                            ? 'Procesando mensaje...'
                                            : 'Enviar mensaje'
                                    }}
                                </span>
                            </span>

                            <span
                                class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-white/[0.07] transition group-hover:bg-white/[0.14]"
                            >
                                <Send
                                    class="h-4 w-4"
                                />
                            </span>
                        </button>
                    </form>
                </div>
            </div>
        </section>

        <!-- CTA -->

        <section
            v-if="
                ctaSection
            "
            class="px-5 pb-14 sm:px-7 sm:pb-20 xl:px-10"
        >
            <div
                class="relative mx-auto max-w-[1500px] overflow-hidden rounded-[28px] bg-[#101516] px-6 py-9 text-white shadow-[0_22px_60px_rgba(15,21,22,.12)] sm:px-9 sm:py-11 lg:flex lg:items-center lg:justify-between lg:gap-10"
            >
                <div
                    class="absolute -right-20 -top-24 h-72 w-72 rounded-full bg-[#0FA7B4]/15 blur-3xl"
                />

                <div
                    class="absolute -bottom-32 left-1/3 h-64 w-64 rounded-full bg-[#E84657]/10 blur-3xl"
                />

                <div
                    class="relative max-w-2xl"
                >
                    <p
                        v-if="
                            ctaSection.subtitle
                        "
                        class="text-[9px] font-black uppercase tracking-[0.14em] text-[#35c4ce]"
                    >
                        {{
                            ctaSection.subtitle
                        }}
                    </p>

                    <h2
                        class="mt-3 text-2xl font-black tracking-[-0.035em] sm:text-3xl"
                    >
                        {{
                            ctaSection.title
                        }}
                    </h2>

                    <p
                        v-if="
                            ctaSection.body
                        "
                        class="mt-3 max-w-xl text-sm leading-6 text-white/45"
                    >
                        {{
                            ctaSection.body
                        }}
                    </p>
                </div>

                <Link
                    v-if="
                        ctaSection.settings.cta_url
                    "
                    :href="
                        ctaSection.settings.cta_url
                    "
                    class="relative mt-7 inline-flex min-h-12 shrink-0 items-center justify-center gap-2 rounded-xl bg-[#0FA7B4] px-5 text-xs font-black text-white transition duration-300 hover:-translate-y-0.5 lg:mt-0"
                >
                    {{
                        ctaSection.settings.cta_label
                        ?? 'Explorar catálogo'
                    }}

                    <ArrowRight
                        class="h-4 w-4"
                    />
                </Link>
            </div>
        </section>
    </PublicLayout>
</template>
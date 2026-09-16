<script setup lang="ts">
import {
    computed,
    onBeforeUnmount,
    onMounted,
    ref,
    watch,
} from 'vue';

interface WebsiteAdInput {
    id: number;
}

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
}

const props =
    withDefaults(
        defineProps<{
            ads: WebsiteAdInput[];
            aspectClass?: string;
            intervalMs?: number;
        }>(),
        {
            aspectClass:
                'aspect-[3/2]',

            intervalMs:
                8000,
        },
    );

const currentIndex =
    ref(
        0,
    );

let rotationTimer:
    ReturnType<
        typeof setInterval
    >
    | null =
    null;

/*
|--------------------------------------------------------------------------
| LECTURA SEGURA DE PROPIEDADES
|--------------------------------------------------------------------------
|
| PromoSlot es reutilizado por diferentes páginas públicas.
|
| Algunas páginas tipan sus promociones con una interfaz genérica y otras
| pueden utilizar una interfaz más específica.
|
| El componente solamente exige que cada elemento tenga un ID y normaliza
| internamente los demás valores.
|
*/

const readString =
    (
        ad:
            WebsiteAdInput,

        key:
            string,
    ): string | null => {
        const value =
            Reflect.get(
                ad,
                key,
            );

        if (
            typeof value !==
            'string'
        ) {
            return null;
        }

        const normalized =
            value.trim();

        return normalized !==
            ''
            ? normalized
            : null;
    };

const readBoolean =
    (
        ad:
            WebsiteAdInput,

        key:
            string,
    ): boolean => {
        const value =
            Reflect.get(
                ad,
                key,
            );

        return (
            value ===
            true
            ||
            value ===
            1
            ||
            value ===
            '1'
            ||
            value ===
            'true'
        );
    };

/*
|--------------------------------------------------------------------------
| PROMOCIONES NORMALIZADAS
|--------------------------------------------------------------------------
|
| Todos los consumidores terminan utilizando la misma estructura interna.
|
| Si por algún motivo una promoción no tiene imagen, no se renderiza.
|
*/

const normalizedAds =
    computed(
        (): WebsiteAd[] =>
            props.ads
                .map(
                    (
                        ad,
                    ): WebsiteAd => {
                        return {
                            id:
                                ad.id,

                            name:
                                readString(
                                    ad,
                                    'name',
                                )
                                ??
                                'Publicidad',

                            placement:
                                readString(
                                    ad,
                                    'placement',
                                )
                                ??
                                '',

                            platform:
                                readString(
                                    ad,
                                    'platform',
                                ),

                            image_url:
                                readString(
                                    ad,
                                    'image_url',
                                )
                                ??
                                '',

                            alt_text:
                                readString(
                                    ad,
                                    'alt_text',
                                ),

                            link_url:
                                readString(
                                    ad,
                                    'link_url',
                                ),

                            cta_label:
                                readString(
                                    ad,
                                    'cta_label',
                                ),

                            open_in_new_tab:
                                readBoolean(
                                    ad,
                                    'open_in_new_tab',
                                ),
                        };
                    },
                )
                .filter(
                    (
                        ad,
                    ): boolean =>
                        ad.image_url !==
                        '',
                ),
    );

const activeAd =
    computed(
        (): WebsiteAd | null =>
            normalizedAds
                .value[
                    currentIndex.value
                ]
            ??
            null,
    );

/*
|--------------------------------------------------------------------------
| ROTACIÓN
|--------------------------------------------------------------------------
*/

const clearRotation =
    (): void => {
        if (
            rotationTimer
        ) {
            clearInterval(
                rotationTimer,
            );

            rotationTimer =
                null;
        }
    };

const startRotation =
    (): void => {
        clearRotation();

        if (
            normalizedAds
                .value
                .length <=
            1
        ) {
            return;
        }

        if (
            typeof window !==
            'undefined'
            &&
            window
                .matchMedia(
                    '(prefers-reduced-motion: reduce)',
                )
                .matches
        ) {
            return;
        }

        rotationTimer =
            setInterval(
                () => {
                    const total =
                        normalizedAds
                            .value
                            .length;

                    if (
                        total <=
                        1
                    ) {
                        currentIndex.value =
                            0;

                        clearRotation();

                        return;
                    }

                    currentIndex.value =
                        (
                            currentIndex.value
                            + 1
                        )
                        %
                        total;
                },
                props.intervalMs,
            );
    };

const selectAd =
    (
        index:
            number,
    ): void => {
        if (
            index <
            0
            ||
            index >=
            normalizedAds
                .value
                .length
        ) {
            return;
        }

        currentIndex.value =
            index;

        startRotation();
    };

/*
|--------------------------------------------------------------------------
| CAMBIOS EN LA LISTA
|--------------------------------------------------------------------------
*/

watch(
    () =>
        normalizedAds
            .value
            .map(
                (
                    ad,
                ) =>
                    ad.id,
            )
            .join(
                ',',
            ),

    () => {
        currentIndex.value =
            0;

        startRotation();
    },
);

/*
|--------------------------------------------------------------------------
| CICLO DE VIDA
|--------------------------------------------------------------------------
*/

onMounted(
    () => {
        startRotation();
    },
);

onBeforeUnmount(
    () => {
        clearRotation();
    },
);
</script>

<template>
    <div
        v-if="activeAd"
        class="w-full"
    >
        <div
            class="group relative overflow-hidden rounded-[22px] border border-black/[0.07] bg-[#eef1f1] shadow-[0_12px_35px_rgba(22,34,35,.055)]"
            :class="
                aspectClass
            "
        >
            <!-- PUBLICIDAD CON ENLACE -->

            <a
                v-if="
                    activeAd.link_url
                "
                :href="
                    activeAd.link_url
                "
                :target="
                    activeAd.open_in_new_tab
                        ? '_blank'
                        : undefined
                "
                :rel="
                    activeAd.open_in_new_tab
                        ? 'noopener noreferrer'
                        : undefined
                "
                class="absolute inset-0 block"
            >
                <Transition
                    mode="out-in"
                    enter-active-class="transition duration-500"
                    enter-from-class="opacity-0 scale-[1.01]"
                    enter-to-class="opacity-100 scale-100"
                    leave-active-class="transition duration-300"
                    leave-from-class="opacity-100"
                    leave-to-class="opacity-0"
                >
                    <img
                        :key="
                            activeAd.id
                        "
                        :src="
                            activeAd.image_url
                        "
                        :alt="
                            activeAd.alt_text
                            ?? activeAd.name
                        "
                        class="h-full w-full object-cover"
                    >
                </Transition>

                <span
                    v-if="
                        activeAd.cta_label
                    "
                    class="absolute bottom-3 right-3 rounded-xl border border-white/20 bg-[#101516]/80 px-3 py-2 text-[9px] font-black uppercase tracking-[0.08em] text-white shadow-lg backdrop-blur-md transition duration-300 group-hover:bg-[#0FA7B4]"
                >
                    {{
                        activeAd.cta_label
                    }}
                </span>
            </a>

            <!-- PUBLICIDAD SIN ENLACE -->

            <div
                v-else
                class="absolute inset-0"
            >
                <Transition
                    mode="out-in"
                    enter-active-class="transition duration-500"
                    enter-from-class="opacity-0 scale-[1.01]"
                    enter-to-class="opacity-100 scale-100"
                    leave-active-class="transition duration-300"
                    leave-from-class="opacity-100"
                    leave-to-class="opacity-0"
                >
                    <img
                        :key="
                            activeAd.id
                        "
                        :src="
                            activeAd.image_url
                        "
                        :alt="
                            activeAd.alt_text
                            ?? activeAd.name
                        "
                        class="h-full w-full object-cover"
                    >
                </Transition>
            </div>
        </div>

        <!-- INDICADORES DE ROTACIÓN -->

        <div
            v-if="
                normalizedAds.length >
                1
            "
            class="mt-2 flex items-center justify-center gap-1.5"
        >
            <button
                v-for="
                    (
                        ad,
                        index
                    ) in normalizedAds
                "
                :key="
                    ad.id
                "
                type="button"
                :aria-label="
                    `Mostrar publicidad ${index + 1}`
                "
                class="h-1.5 rounded-full transition-all duration-300"
                :class="
                    index ===
                    currentIndex
                        ? 'w-5 bg-[#0FA7B4]'
                        : 'w-1.5 bg-black/15 hover:bg-black/25'
                "
                @click="
                    selectAd(
                        index,
                    )
                "
            />
        </div>
    </div>
</template>
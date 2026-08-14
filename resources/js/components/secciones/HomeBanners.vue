<script setup lang="ts">
import { onMounted, ref } from 'vue';
import http from '../../api/http';
import type { HomeBanner } from '../../types';

const banners = ref<HomeBanner[]>([]);
const loading = ref(true);
const error = ref<string | null>(null);

async function cargar() {
    loading.value = true;
    error.value = null;

    try {
        const { data } = await http.get<{ data: HomeBanner[] }>('/home-banners');
        banners.value = data.data;
    } catch {
        error.value = 'No se pudieron cargar los banners.';
    } finally {
        loading.value = false;
    }
}

function abrir(banner: HomeBanner) {
    if (banner.link) {
        window.open(banner.link, '_blank', 'noopener');
    }
}

onMounted(cargar);
</script>

<template>
    <section class="mb-6">
        
        <div v-if="loading" class="d-flex align-center justify-center py-16">
            <v-progress-circular indeterminate color="primary" size="48"></v-progress-circular>
        </div>

        <v-alert v-else-if="error" type="error" variant="tonal" density="compact" class="mb-2">
            {{ error }}
        </v-alert>

        <v-carousel
            v-else-if="banners.length > 0"
            cycle
            interval="6000"
            show-arrows="hover"
            height="380"
            class="rounded-xl overflow-hidden"
        >
            <v-carousel-item v-for="banner in banners" :key="banner.id">
                <v-img
                    :src="banner.url"
                    :alt="banner.title ?? 'Banner'"
                    cover
                    height="100%"
                ></v-img>

                <!-- Textos dinámicos sobre el banner (control desde la tabla) -->
                <div
                    class="banner-overlay"
                    :class="{ 'banner-overlay--clickable': banner.link }"
                    @click="abrir(banner)"
                >
                    <h2 v-if="banner.title" class="text-h4 font-weight-bold text-white">
                        {{ banner.title }}
                    </h2>
                    <p v-if="banner.subtitle" class="text-subtitle-1 text-white mt-1">
                        {{ banner.subtitle }}
                    </p>
                    <v-btn
                        v-if="banner.link"
                        color="white"
                        variant="flat"
                        class="mt-3 text-none"
                        text-color="primary"
                    >
                        Ver más
                    </v-btn>
                </div>
            </v-carousel-item>
        </v-carousel>
    </section>
</template>

<style scoped>
.banner-overlay {
    position: absolute;
    inset: 0;
    display: flex;
    flex-direction: column;
    justify-content: center;
    align-items: flex-start;
    padding: 0 48px;
    background: linear-gradient(to right, rgba(0, 0, 0, 0.45), transparent 60%);
}

.banner-overlay--clickable {
    cursor: pointer;
}

@media (max-width: 600px) {
    .banner-overlay {
        padding: 0 20px;
    }
}
</style>

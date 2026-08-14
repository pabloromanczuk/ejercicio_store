<script setup lang="ts">
import HomeBanners from './HomeBanners.vue';
import ProductSlider from './ProductSlider.vue';
import type { Producto } from '../../types';

const emit = defineEmits<{
    'ver-producto': [producto: Producto];
    'ver-catalogo': [];
}>();
</script>

<template>
    <div>

        <HomeBanners />

        <ProductSlider titulo="Productos destacados" @ver-producto="emit('ver-producto', $event)" />

        <div class="text-center mt-4">
            <v-btn
                color="primary"
                variant="flat"
                size="large"
                prepend-icon="mdi-view-grid-outline"
                class="text-none"
                @click="emit('ver-catalogo')"
            >
                Ver todo el catálogo
            </v-btn>
        </div>
    </div>
</template>

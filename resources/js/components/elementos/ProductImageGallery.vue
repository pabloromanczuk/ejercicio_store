<script setup lang="ts">
import { computed, ref } from 'vue';
import { FALLBACK_PRODUCT_IMAGE } from '../../utils/images';
import ProductImage from './ProductImage.vue';

const props = withDefaults(defineProps<{
    images?: string[];
    alt?: string;
}>(), {
    images: () => [],
    alt: '',
});

const index = ref(0);

// Si no hay imágenes, se usa el fallback global.
const urls = computed<string[]>(() => {
    const validas = props.images.filter(Boolean);
    return validas.length > 0 ? validas : [FALLBACK_PRODUCT_IMAGE];
});

function prev() {
    index.value = (index.value - 1 + urls.value.length) % urls.value.length;
}

function next() {
    index.value = (index.value + 1) % urls.value.length;
}
</script>

<template>
    <div class="product-gallery">
        <template v-if="urls.length === 1">
            <ProductImage :src="urls[0]" :alt="alt" />
        </template>

        <template v-else>
            <v-window v-model="index">
                <v-window-item v-for="(src, i) in urls" :key="i">
                    <ProductImage :src="src" :alt="alt" />
                </v-window-item>
            </v-window>

            <v-btn
                class="gallery-btn gallery-btn--prev"
                icon="mdi-chevron-left"
                variant="flat"
                color="surface"
                size="small"
                :aria-label="`Imagen anterior (${index + 1} de ${urls.length})`"
                @click.stop="prev"
            ></v-btn>
            <v-btn
                class="gallery-btn gallery-btn--next"
                icon="mdi-chevron-right"
                variant="flat"
                color="surface"
                size="small"
                :aria-label="`Imagen siguiente (${index + 1} de ${urls.length})`"
                @click.stop="next"
            ></v-btn>

            <div class="gallery-dots">
                <button
                    v-for="(src, i) in urls"
                    :key="i"
                    type="button"
                    class="gallery-dot"
                    :class="{ 'gallery-dot--active': i === index }"
                    :aria-label="`Ir a la imagen ${i + 1}`"
                    @click.stop="index = i"
                ></button>
            </div>
        </template>
    </div>
</template>

<style scoped>
.product-gallery {
    position: relative;
    width: 100%;
}

.gallery-btn {
    position: absolute;
    top: 50%;
    transform: translateY(-50%);
    z-index: 2;
    opacity: 0.9;
}

.gallery-btn--prev {
    left: 6px;
}

.gallery-btn--next {
    right: 6px;
}

.gallery-dots {
    position: absolute;
    bottom: 6px;
    left: 0;
    right: 0;
    display: flex;
    justify-content: center;
    gap: 5px;
    z-index: 2;
}

.gallery-dot {
    width: 8px;
    height: 8px;
    border-radius: 999px;
    border: none;
    padding: 0;
    background: rgba(0, 0, 0, 0.25);
    cursor: pointer;
    transition: background-color 0.2s;
}

.gallery-dot--active {
    background: rgb(var(--v-theme-primary));
}
</style>

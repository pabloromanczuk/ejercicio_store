<script setup lang="ts">
import { computed, onMounted, ref } from 'vue';
import http from '../../api/http';
import ProductImage from '../elementos/ProductImage.vue';
import PriceText from '../elementos/PriceText.vue';
import type { Producto } from '../../types';

const props = withDefaults(defineProps<{
    excluirId?: number;
    titulo?: string;
}>(), {
    excluirId: undefined,
    titulo: 'También te puede interesar',
});

const emit = defineEmits<{
    'ver-producto': [producto: Producto];
}>();

const LIMIT = 8;

const cargados = ref<Producto[]>([]);
const loading = ref(true);
const error = ref<string | null>(null);

// Exhibición , reactivo, excluye productos sin precio o sin stock, y el producto que se está viendo actualmente.
const productos = computed<Producto[]>(() =>
    cargados.value.filter(
        (p) => p.tiene_precio && p.tiene_stock && p.id !== props.excluirId,
    ),
);

async function cargar() {
    loading.value = true;
    error.value = null;

    try {
        const { data } = await http.get<{ data: Producto[] }>('/products/random', {
            params: { limit: LIMIT, excluir: props.excluirId },
        });

        cargados.value = data.data;
    } catch {
        error.value = 'No se pudieron cargar los accesos rápidos.';
    } finally {
        loading.value = false;
    }
}

function mezclar() {
    cargar();
}

onMounted(cargar);
</script>

<template>
    <section class="mt-6">
        <div class="d-flex align-center justify-space-between mb-1">
            <h2 class="text-h6 font-weight-bold">{{ titulo }}</h2>
            <v-btn
                variant="text"
                color="primary"
                prepend-icon="mdi-shuffle-variant"
                class="text-none"
                :loading="loading"
                @click="mezclar"
            >
                Mezclar
            </v-btn>
        </div>
        <p class="text-body-2 text-medium-emphasis mb-3">
            Una selección al azar de productos para empezar a comprar.
        </p>

        <!-- Carga -->
        <div v-if="loading" class="d-flex align-center justify-center py-10">
            <v-progress-circular indeterminate color="primary" size="40"></v-progress-circular>
        </div>

        <!-- Error -->
        <v-alert v-else-if="error" type="error" variant="tonal" density="compact" class="mb-2">
            {{ error }}
        </v-alert>

        <!-- Slide horizontal de productos -->
        <v-slide-group v-else-if="productos.length > 0" show-arrows class="product-slider">
            <v-slide-group-item
                v-for="producto in productos"
                :key="producto.id"
                v-slot="{ toggle }"
            >
                <v-card
                    class="product-slider__item"
                    flat
                    border
                    role="button"
                    tabindex="0"
                    :aria-label="`Abrir ${producto.detalle}`"
                    @click="emit('ver-producto', producto)"
                    @keydown.enter.prevent="emit('ver-producto', producto)"
                >
                    <ProductImage :src="producto.primary_image_url" :alt="producto.detalle" />
                    <div class="pa-2">
                        <div class="text-body-2 font-weight-bold text-truncate">
                            {{ producto.detalle }}
                        </div>
                        <div class="text-caption text-medium-emphasis">
                            <PriceText :value="producto.precio_con_iva" />
                            <span class="ml-1">· IVA {{ producto.iva }}%</span>
                        </div>
                    </div>
                </v-card>
            </v-slide-group-item>
        </v-slide-group>
    </section>
</template>

<style scoped>
.product-slider__item {
    width: 160px;
    margin: 4px;
    cursor: pointer;
    overflow: hidden;
    transition: transform 0.15s ease, box-shadow 0.15s ease;
}

.product-slider__item:hover {
    transform: translateY(-3px);
    box-shadow: 0 8px 20px rgba(0, 0, 0, 0.14);
}
</style>

<script setup lang="ts">
import { computed, ref } from 'vue';
import { useCartStore } from '../../stores/cart';
import { useProductAvailability } from '../../composables/useProductAvailability';
import StockChip from '../elementos/StockChip.vue';
import QuantityStepper from '../elementos/QuantityStepper.vue';
import ProductImageGallery from '../elementos/ProductImageGallery.vue';
import ProductPricing from '../bloques/ProductPricing.vue';
import { showToast } from '../../composables/useToast';
import type { Producto } from '../../types';

const props = defineProps<{
    producto: Producto;
}>();

const cart = useCartStore();
const cantidad = ref(1);
const mensaje = ref<string | null>(null);

const { disponibleRestante, sinStock } = useProductAvailability(() => props.producto);

const imagenes = computed<string[]>(() => {
    const media = props.producto.media ?? [];
    const urls = media
        .filter((m) => m.type === 'image')
        .sort((a, b) => a.sort_order - b.sort_order)
        .map((m) => m.url);

    return urls.length > 0 ? urls : [props.producto.primary_image_url];
});

// control de estados para la card
const sinPrecioConfigurado = computed(() => !props.producto.tiene_precio);
const sinStockConfigurado = computed(() => !props.producto.tiene_stock);
const deshabilitado = computed(() => sinPrecioConfigurado.value || sinStockConfigurado.value);

const cartel = computed<string>(() => {
    if (sinPrecioConfigurado.value) return 'Sin precio';
    if (sinStockConfigurado.value) return 'Sin stock';
    return '';
});

function agregar() {
    mensaje.value = null;
    const cant = Math.max(1, Math.min(Number(cantidad.value) || 1, disponibleRestante.value));

    const resultado = cart.addItem(props.producto, cant);
    if (!resultado.ok) {
        mensaje.value = resultado.message ?? null;
        return;
    }

    showToast(`"${props.producto.detalle}" agregado al carrito (${cant} un.)`);
    cantidad.value = 1;
}
</script>

<template>
    <v-card
        class="product-card d-flex flex-column"
        :class="{ 'product-card--disabled': deshabilitado }"
        height="100%"
        elevation="0"
    >
        <ProductImageGallery :images="imagenes" :alt="producto.detalle" />

        <v-card-text>
            <div class="text-overline text-medium-emphasis">Cód. {{ producto.codigo }}</div>
            <div class="text-h6 font-weight-bold mb-2">{{ producto.detalle }}</div>
            <ProductPricing v-if="!sinPrecioConfigurado" :producto="producto" />
        </v-card-text>

        <v-card-text class="pt-0">
            <v-chip
                v-if="cartel"
                color="error"
                size="small"
                variant="tonal"
                prepend-icon="mdi-alert-circle-outline"
            >
                {{ cartel }}
            </v-chip>
            <StockChip v-else :disponible="disponibleRestante" />
        </v-card-text>

        <v-alert
            v-if="mensaje"
            type="error"
            variant="tonal"
            density="compact"
            class="mx-4 mb-2"
        >
            {{ mensaje }}
        </v-alert>

        <v-spacer></v-spacer>

        <v-card-actions class="d-flex flex-column ga-2 px-4 pb-4">
            <QuantityStepper
                v-model="cantidad"
                :min="1"
                :max="disponibleRestante"
                :disabled="sinStock || deshabilitado"
                class="w-100"
            />
            <v-btn
                color="primary"
                variant="flat"
                size="small"
                block
                prepend-icon="mdi-cart-plus"
                :disabled="sinStock || deshabilitado"
                class="text-none"
                @click="agregar"
            >
                Agregar al carrito
            </v-btn>
        </v-card-actions>
    </v-card>
</template>

<style scoped>
.product-card {
    box-shadow: 0 4px 18px rgba(0, 0, 0, 0.10), 0 1px 4px rgba(0, 0, 0, 0.06);
    transition: box-shadow 0.25s ease;
}

.product-card:hover {
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.16), 0 2px 6px rgba(0, 0, 0, 0.10);
}

.product-card--disabled {
    opacity: 0.6;
    filter: saturate(0.7);
}

.product-card--disabled:hover {
    box-shadow: 0 4px 18px rgba(0, 0, 0, 0.10), 0 1px 4px rgba(0, 0, 0, 0.06);
}
</style>

<script setup lang="ts">
import { computed, ref } from 'vue';
import { useCartStore } from '../../stores/cart';
import { useProductAvailability } from '../../composables/useProductAvailability';
import { showToast } from '../../composables/useToast';
import ProductImageGallery from '../elementos/ProductImageGallery.vue';
import ProductPricing from '../bloques/ProductPricing.vue';
import StockChip from '../elementos/StockChip.vue';
import QuantityStepper from '../elementos/QuantityStepper.vue';
import ProductSlider from './ProductSlider.vue';
import type { Producto } from '../../types';

const props = defineProps<{
    producto: Producto;
}>();

const emit = defineEmits<{
    back: [];
    'ver-producto': [producto: Producto];
}>();

const cart = useCartStore();
const cantidad = ref(1);
const mensaje = ref<string | null>(null);

const { enCarrito, disponibleRestante, sinStock } = useProductAvailability(() => props.producto);

const imagenes = computed<string[]>(() => {
    const media = props.producto.media ?? [];
    const urls = media
        .filter((m) => m.type === 'image')
        .sort((a, b) => a.sort_order - b.sort_order)
        .map((m) => m.url);

    return urls.length > 0 ? urls : [props.producto.primary_image_url];
});

const sinPrecio = computed(() => !props.producto.tiene_precio);
const sinStockConfigurado = computed(() => !props.producto.tiene_stock);

// Deshabilitamos la compra si no hay precio/stock configurado o ya se agotó
const deshabilitado = computed(() => sinPrecio.value || sinStockConfigurado.value || sinStock.value);

const cartel = computed<string>(() => {
    if (sinPrecio.value) return 'Sin precio';
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
    <div>
        <v-card class="mx-auto" max-width="960" flat border>
            <v-card-item>
                <v-btn
                    variant="text"
                    prepend-icon="mdi-arrow-left"
                    class="text-none"
                    @click="emit('back')"
                >
                    Volver al catálogo
                </v-btn>
            </v-card-item>

        <v-divider></v-divider>

        <v-card-text>
            <v-row>
                
                <v-col cols="12" md="6">
                    <ProductImageGallery :images="imagenes" :alt="producto.detalle" />
                </v-col>

                <v-col cols="12" md="6">
                    <div class="text-overline text-medium-emphasis">Cód. {{ producto.codigo }}</div>
                    <h1 class="text-h4 font-weight-bold mb-1">{{ producto.detalle }}</h1>

                    <div class="my-3">
                        <ProductPricing v-if="!sinPrecio" :producto="producto" />
                    </div>

                    <div class="mb-3">
                        <v-chip
                            v-if="cartel"
                            color="error"
                            variant="tonal"
                            prepend-icon="mdi-alert-circle-outline"
                        >
                            {{ cartel }}
                        </v-chip>
                        <StockChip v-else :disponible="disponibleRestante" />
                    </div>

                    <v-divider class="my-4"></v-divider>

                    <dl class="text-body-2 mb-4">
                        <div class="d-flex justify-space-between py-1">
                            <dt class="text-medium-emphasis">Lista de precios</dt>
                            <dd class="font-weight-medium">{{ producto.pricelist ?? '—' }}</dd>
                        </div>
                        <div class="d-flex justify-space-between py-1">
                            <dt class="text-medium-emphasis">Depósito</dt>
                            <dd class="font-weight-medium">{{ producto.warehouse ?? '—' }}</dd>
                        </div>
                        <div class="d-flex justify-space-between py-1">
                            <dt class="text-medium-emphasis">En tu carrito</dt>
                            <dd class="font-weight-medium">{{ enCarrito }} un.</dd>
                        </div>
                    </dl>

                    <v-alert
                        v-if="mensaje"
                        type="error"
                        variant="tonal"
                        density="compact"
                        class="mb-3"
                    >
                        {{ mensaje }}
                    </v-alert>

                    <div class="d-flex align-center ga-3 flex-wrap">
                        <QuantityStepper
                            v-model="cantidad"
                            :min="1"
                            :max="disponibleRestante"
                            :disabled="deshabilitado"
                            style="max-width: 180px;"
                        />
                        <v-btn
                            color="primary"
                            variant="flat"
                            size="large"
                            class="text-none flex-grow-1"
                            prepend-icon="mdi-cart-plus"
                            :disabled="deshabilitado"
                            @click="agregar"
                        >
                            Agregar al carrito
                        </v-btn>
                    </div>
                </v-col>
            </v-row>
        </v-card-text>
    </v-card>

    <ProductSlider :excluir-id="producto.id" @ver-producto="emit('ver-producto', $event)" />

    </div>
</template>

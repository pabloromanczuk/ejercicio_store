<script setup lang="ts">
import type { LineaCarrito } from '../../types';
import PriceText from '../elementos/PriceText.vue';

defineProps<{
    items: LineaCarrito[];
}>();
</script>

<template>
    <div>
        <div
            v-for="item in items"
            :key="item.product_id"
            class="d-flex justify-space-between align-start ga-3 py-3 border-b"
            style="min-width: 0;"
        >
            <div class="flex-grow-1" style="min-width: 0;">
                <div class="text-subtitle-1 font-weight-medium text-truncate">{{ item.detalle }}</div>
                <div class="text-caption text-medium-emphasis">
                    {{ item.cantidad }} × <PriceText :value="item.precio_unitario" /> · IVA {{ item.iva }}%
                </div>
            </div>
            <div class="text-right">
                <div class="font-weight-medium"><PriceText :value="item.total" /></div>
                <div class="text-caption text-medium-emphasis">Subtotal</div>
            </div>
        </div>
    </div>
</template>

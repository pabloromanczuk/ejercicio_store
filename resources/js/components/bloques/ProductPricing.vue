<script setup lang="ts">
import type { Producto } from '../../types';
import PriceText from '../elementos/PriceText.vue';

defineProps<{
    producto: Producto;
}>();
</script>

<template>
    <div>
        <div class="d-flex align-baseline ga-1 flex-wrap">
            <PriceText :value="producto.precio_unitario" size="headline" tone="primary" />
            <span class="text-caption text-medium-emphasis">por unidad · sin IVA</span>
        </div>
        <div class="text-caption text-medium-emphasis">
            + IVA {{ producto.iva }}% · <PriceText :value="producto.precio_con_iva" /> final
        </div>
    </div>
</template>

<script setup lang="ts">
import type { SaleItem } from '../../types';
import PriceText from '../elementos/PriceText.vue';

defineProps<{
    items: SaleItem[];
}>();
</script>

<template>
    <v-list>
        <v-list-item v-for="item in items" :key="item.codigo">
            <template #title>
                <div class="text-subtitle-1">{{ item.detalle }}</div>
            </template>
            <template #subtitle>
                <div class="text-caption">
                    {{ item.cantidad }} × <PriceText :value="item.precio_unitario" />
                </div>
            </template>
            <template #append>
                <span class="font-weight-medium"><PriceText :value="item.total" /></span>
            </template>
        </v-list-item>
    </v-list>
</template>

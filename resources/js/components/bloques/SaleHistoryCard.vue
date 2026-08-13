<script setup lang="ts">
import { formatDate, formatTime } from '../../utils/format';
import type { Venta } from '../../types';
import PriceText from '../elementos/PriceText.vue';
import SaleItemsList from './SaleItemsList.vue';

defineProps<{
    sale: Venta;
}>();
</script>

<template>
    <v-expansion-panel>
        <v-expansion-panel-title>
            <div class="d-flex justify-space-between align-center ga-2 w-100">
                <div class="d-flex align-center ga-2 text-truncate" style="min-width: 0;">
                    <v-icon icon="mdi-receipt-text-outline" color="primary"></v-icon>
                    <span class="font-weight-medium text-truncate">{{ sale.numero }}</span>
                </div>
                <div class="text-right">
                    <div class="font-weight-bold pb-1"><PriceText :value="sale.total" /></div>
                    <div class="text-medium-emphasis" style="font-size: 0.7rem;">{{ formatDate(sale.fecha) }}</div>
                    <div class="text-medium-emphasis" style="font-size: 0.7rem;">{{ formatTime(sale.fecha) }}</div>
                </div>
            </div>
        </v-expansion-panel-title>

        <v-expansion-panel-text>
            <div class="d-flex justify-space-between text-caption text-medium-emphasis mb-1">
                <span>
                    Estado: <span class="text-capitalize">{{ sale.estado }}</span> · {{ sale.cantidad_items }} ítems
                </span>
                <span>{{ sale.moneda }}</span>
            </div>

            <SaleItemsList :items="sale.items" />
        </v-expansion-panel-text>
    </v-expansion-panel>
</template>

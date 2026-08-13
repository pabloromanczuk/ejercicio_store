<script setup lang="ts">
import type { Venta } from '../../types';
import SaleItemsList from '../bloques/SaleItemsList.vue';
import TotalsRow from '../elementos/TotalsRow.vue';

defineProps<{
    sale: Venta;
}>();

const emit = defineEmits<{
    'new-order': [];
}>();
</script>

<template>
    <v-card class="mx-auto" max-width="560">
        <v-card-item class="text-center">
            <v-icon icon="mdi-check-circle" size="56" color="success" class="mb-2"></v-icon>
            <div class="text-h5 font-weight-bold">¡Compra confirmada!</div>
            <div class="text-body-2 text-medium-emphasis mt-1">Se generó el comprobante:</div>
            <v-chip color="primary" variant="tonal" class="mt-2" size="large">
                {{ sale.numero }}
            </v-chip>
        </v-card-item>

        <v-divider></v-divider>

        <SaleItemsList :items="sale.items" />

        <v-divider></v-divider>

        <v-card-text>
            <TotalsRow label="Subtotal" :value="sale.subtotal" />
            <TotalsRow label="IVA" :value="sale.iva_total" />
            <TotalsRow label="Total" :value="sale.total" total />
        </v-card-text>

        <v-card-text>
            <p class="text-caption text-medium-emphasis">
                Podés consultar esta venta vía API en
                <code>GET /api/sales/{{ sale.id }}</code>
            </p>
            <v-btn color="primary" variant="flat" block class="mt-2" @click="emit('new-order')">
                Hacer otra compra
            </v-btn>
        </v-card-text>
    </v-card>
</template>

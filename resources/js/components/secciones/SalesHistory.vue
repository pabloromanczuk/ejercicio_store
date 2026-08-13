<script setup lang="ts">
import { onMounted, ref } from 'vue';
import http from '../../api/http';
import SaleHistoryCard from '../bloques/SaleHistoryCard.vue';
import EmptyState from '../elementos/EmptyState.vue';
import type { Venta } from '../../types';

const emit = defineEmits<{
    back: [];
}>();

const ventas = ref<Venta[]>([]);
const loading = ref(true);
const error = ref<string | null>(null);

async function cargarHistorial() {
    loading.value = true;
    error.value = null;
    try {
        const { data } = await http.get<{ data: Venta[] }>('/sales');
        ventas.value = data.data;
    } catch (e) {
        error.value = 'No se pudo cargar el historial de compras.';
    } finally {
        loading.value = false;
    }
}

onMounted(cargarHistorial);
</script>

<template>
    <div>
        <div class="d-flex align-center justify-space-between mb-4">
            <div>
                <div class="text-h5 font-weight-bold">Historial de compras</div>
                <div class="text-body-2 text-medium-emphasis">Todas las ventas registradas</div>
            </div>
            <v-btn
                icon="mdi-arrow-left"
                variant="text"
                aria-label="Volver al catálogo"
                @click="emit('back')"
            ></v-btn>
        </div>

        <div v-if="loading" class="text-center py-16">
            <v-progress-circular indeterminate color="primary" size="48"></v-progress-circular>
            <p class="text-body-2 text-medium-emphasis mt-4">Cargando historial…</p>
        </div>

        <v-alert v-else-if="error" type="error" variant="tonal" class="mb-4">
            {{ error }}
        </v-alert>

        <v-expansion-panels v-else-if="ventas.length > 0" variant="accordion">
            <SaleHistoryCard v-for="sale in ventas" :key="sale.id" :sale="sale" />
        </v-expansion-panels>

        <EmptyState v-else icon="mdi-history" text="Todavía no hay compras registradas." />
    </div>
</template>

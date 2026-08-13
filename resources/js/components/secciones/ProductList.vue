<script setup lang="ts">
import { onMounted, ref } from 'vue';
import http from '../../api/http';
import ProductCard from './ProductCard.vue';
import type { Producto } from '../../types';

const productos = ref<Producto[]>([]);
const loading = ref(true);
const error = ref<string | null>(null);

async function cargarProductos() {
    loading.value = true;
    error.value = null;
    try {
        const { data } = await http.get<{ data: Producto[] }>('/products');
        productos.value = data.data;
    } catch (e) {
        error.value = 'No se pudieron cargar los productos.';
    } finally {
        loading.value = false;
    }
}

onMounted(cargarProductos);
</script>

<template>
    <div>
        <div v-if="loading" class="text-center py-16">
            <v-progress-circular indeterminate color="primary" size="48"></v-progress-circular>
            <p class="text-body-2 text-medium-emphasis mt-4">Cargando productos…</p>
        </div>

        <v-alert v-else-if="error" type="error" variant="tonal" class="mb-4">
            {{ error }}
        </v-alert>

        <v-row v-else>
            <v-col
                v-for="producto in productos"
                :key="producto.id"
                cols="12"
                sm="6"
                md="4"
                lg="3"
            >
                <ProductCard :producto="producto" />
            </v-col>
        </v-row>
    </div>
</template>

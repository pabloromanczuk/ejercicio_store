<script setup lang="ts">
import { ref } from 'vue';
import AppLayout from './layouts/AppLayout.vue';
import ProductList from './components/secciones/ProductList.vue';
import CheckoutView from './components/secciones/CheckoutView.vue';
import CheckoutSuccess from './components/secciones/CheckoutSuccess.vue';
import SalesHistory from './components/secciones/SalesHistory.vue';
import type { Venta } from './types';

type View = 'catalog' | 'checkout' | 'history';

const completedSale = ref<Venta | null>(null);
const view = ref<View>('catalog');

function handleCheckout() {
    completedSale.value = null;
    view.value = 'checkout';
}

function handleHistory() {
    completedSale.value = null;
    view.value = 'history';
}

function handleCatalog() {
    completedSale.value = null;
    view.value = 'catalog';
}

function handleBackToCatalog() {
    view.value = 'catalog';
}

function handleSaleCompleted(sale: Venta) {
    completedSale.value = sale;
    view.value = 'catalog';
}

function startNewOrder() {
    completedSale.value = null;
    view.value = 'catalog';
}
</script>

<template>
    <AppLayout @checkout="handleCheckout" @history="handleHistory" @catalog="handleCatalog">
        <template #content>
            <CheckoutSuccess v-if="completedSale" :sale="completedSale" @new-order="startNewOrder" />
            <SalesHistory v-else-if="view === 'history'" @back="handleBackToCatalog" />
            <CheckoutView v-else-if="view === 'checkout'" @sale-completed="handleSaleCompleted" @back="handleBackToCatalog" />
            <ProductList v-else />
        </template>
    </AppLayout>
</template>

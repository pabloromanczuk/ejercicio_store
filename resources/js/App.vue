<script setup lang="ts">
import { ref } from 'vue';
import { useCartStore, CARRITO_STORAGE_KEY } from './stores/cart';
import AppLayout from './layouts/AppLayout.vue';
import HomeView from './components/secciones/HomeView.vue';
import ProductList from './components/secciones/ProductList.vue';
import ProductDetail from './components/secciones/ProductDetail.vue';
import CheckoutView from './components/secciones/CheckoutView.vue';
import CheckoutSuccess from './components/secciones/CheckoutSuccess.vue';
import SalesHistory from './components/secciones/SalesHistory.vue';
import type { Producto, Venta } from './types';

type View = 'home' | 'catalog' | 'checkout' | 'history' | 'detail';

const completedSale = ref<Venta | null>(null);
const view = ref<View>('home');
const productoSeleccionado = ref<Producto | null>(null);

// Persistencia del carrito en localStorage
const cart = useCartStore();
cart.$subscribe(() => {
    localStorage.setItem(CARRITO_STORAGE_KEY, JSON.stringify(cart.items));
});

function handleHome() {
    completedSale.value = null;
    productoSeleccionado.value = null;
    view.value = 'home';
}

function handleCheckout() {
    completedSale.value = null;
    productoSeleccionado.value = null;
    view.value = 'checkout';
}

function handleHistory() {
    completedSale.value = null;
    productoSeleccionado.value = null;
    view.value = 'history';
}

function handleCatalog() {
    completedSale.value = null;
    productoSeleccionado.value = null;
    view.value = 'catalog';
}

function handleBackToCatalog() {
    view.value = 'catalog';
}

function handleVerProducto(producto: Producto) {
    completedSale.value = null;
    productoSeleccionado.value = producto;
    view.value = 'detail';
}

function handleSaleCompleted(sale: Venta) {
    completedSale.value = sale;
    productoSeleccionado.value = null;
    view.value = 'catalog';
}

function startNewOrder() {
    completedSale.value = null;
    productoSeleccionado.value = null;
    view.value = 'catalog';
}
</script>

<template>
    <AppLayout @home="handleHome" @checkout="handleCheckout" @history="handleHistory" @catalog="handleCatalog">
        <template #content>
            <HomeView
                v-if="view === 'home'"
                @ver-producto="handleVerProducto"
                @ver-catalogo="handleCatalog"
            />
            <CheckoutSuccess v-else-if="completedSale" :sale="completedSale" @new-order="startNewOrder" />
            <SalesHistory v-else-if="view === 'history'" @back="handleBackToCatalog" />
            <CheckoutView v-else-if="view === 'checkout'" @sale-completed="handleSaleCompleted" @back="handleBackToCatalog" />
            <ProductDetail
                v-else-if="view === 'detail' && productoSeleccionado"
                :producto="productoSeleccionado"
                @back="handleBackToCatalog"
                @ver-producto="handleVerProducto"
            />
            <ProductList v-else @ver-producto="handleVerProducto" />
        </template>
    </AppLayout>
</template>

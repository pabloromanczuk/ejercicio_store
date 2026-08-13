<script setup lang="ts">
import { computed, ref } from 'vue';
import { useDisplay, useTheme } from 'vuetify';
import { useCartStore } from '../stores/cart';
import CartDrawer from '../components/secciones/CartDrawer.vue';
import AppFooter from '../components/secciones/AppFooter.vue';
import ToastHost from '../components/elementos/ToastHost.vue';

const emit = defineEmits<{
    checkout: [];
    history: [];
    catalog: [];
}>();

const theme = useTheme();
const { mobile } = useDisplay();
const cart = useCartStore();
const cartOpen = ref(false);

const isDark = computed<boolean>(() => theme.name.value === 'dark');

function toggleTheme() {
    theme.change(isDark.value ? 'light' : 'dark');
}

function handleCheckout() {
    cartOpen.value = false;
    emit('checkout');
}
</script>

<template>
    <v-app>
        <v-app-bar flat>
            <template #prepend>
                <v-btn
                    icon
                    variant="text"
                    class="ml-2"
                    aria-label="Ir a la tienda"
                    @click="emit('catalog')"
                >
                    <v-icon icon="mdi-cart"></v-icon>
                </v-btn>
            </template>

            <v-app-bar-title>Hace tu compra</v-app-bar-title>

            <template #append>
                <v-btn
                    icon
                    variant="text"
                    aria-label="Ver historial de compras"
                    @click="emit('history')"
                >
                    <v-icon>mdi-history</v-icon>
                </v-btn>

                <v-btn
                    icon
                    variant="text"
                    :aria-label="isDark ? 'Activar tema claro' : 'Activar tema oscuro'"
                    @click="toggleTheme"
                >
                    <v-icon>{{ isDark ? 'mdi-white-balance-sunny' : 'mdi-weather-night' }}</v-icon>
                </v-btn>

                <v-btn
                    color="primary"
                    variant="outlined"
                    :class="mobile ? 'mr-2' : 'mr-3'"
                    :aria-label="`Abrir carrito (${cart.cantidadTotal} artículos)`"
                    @click="cartOpen = true"
                >
                    <v-icon icon="mdi-cart-outline" start></v-icon>
                    <span class="font-weight-medium">{{ cart.cantidadTotal }}</span>
                </v-btn>
            </template>
        </v-app-bar>

        <v-main>
            <v-container class="py-6">
                <slot name="content"></slot>
            </v-container>
        </v-main>

        <AppFooter />

        <CartDrawer v-model="cartOpen" @checkout="handleCheckout" />
        <ToastHost />
    </v-app>
</template>

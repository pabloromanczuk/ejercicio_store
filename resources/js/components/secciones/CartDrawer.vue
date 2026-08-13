<script setup lang="ts">
import { useCartStore } from '../../stores/cart';
import { showToast } from '../../composables/useToast';
import CartLineItem from '../bloques/CartLineItem.vue';
import CartTotals from '../bloques/CartTotals.vue';
import EmptyState from '../elementos/EmptyState.vue';

const model = defineModel<boolean>({ default: false });
const emit = defineEmits<{
    checkout: [];
}>();

const cart = useCartStore();

/**  emitimos hacia checkout al confirmar carrito */
function irAConfirmar() {
    model.value = false;
    emit('checkout');
}

function quitarItem(productId: number) {
    const item = cart.items.find((it) => it.product_id === productId);
    cart.removeItem(productId);
    if (item) {
        showToast(`"${item.detalle}" quitado del carrito`, 'error');
    }
}

function actualizarCantidad(productId: number, cantidad: number) {
    const item = cart.items.find((it) => it.product_id === productId);

    if (cantidad <= 0) {
        cart.updateCantidad(productId, 0);
        if (item) {
            showToast(`"${item.detalle}" quitado del carrito`, 'error');
        }
        return;
    }

    cart.updateCantidad(productId, cantidad);
}
</script>

<template>
    <v-navigation-drawer
        v-model="model"
        location="end"
        temporary
        width="420"
    >
        <div class="d-flex flex-column h-100">
            <div class="d-flex align-center justify-space-between pa-4">
                <div class="text-h6 font-weight-bold">Tu carrito</div>
                <v-btn
                    icon="mdi-close"
                    variant="text"
                    aria-label="Cerrar carrito"
                    @click="model = false"
                ></v-btn>
            </div>
            <v-divider></v-divider>

            <div class="flex-grow-1 overflow-y-auto pa-4">
                <template v-if="cart.lineas.length > 0">
                    <CartLineItem
                        v-for="linea in cart.lineas"
                        :key="linea.product_id"
                        :linea="linea"
                        @update:cantidad="actualizarCantidad"
                        @remove="quitarItem"
                    />
                </template>

                <EmptyState v-else icon="mdi-cart-outline" text="El carrito está vacío." />
            </div>

            <v-divider></v-divider>

            <CartTotals
                :subtotal="cart.subtotal"
                :iva-total="cart.ivaTotal"
                :total="cart.total"
                :error="cart.checkoutError"
                :checking-out="cart.checkingOut"
                :disabled="cart.items.length === 0"
                @confirm="irAConfirmar"
            />
        </div>
    </v-navigation-drawer>
</template>

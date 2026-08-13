<script setup lang="ts">
import { ref } from 'vue';
import { useCartStore } from '../../stores/cart';
import { formatMoney } from '../../utils/format';
import { showToast } from '../../composables/useToast';
import CheckoutItemsList from '../bloques/CheckoutItemsList.vue';
import CartTotals from '../bloques/CartTotals.vue';
import ConfirmDialog from '../elementos/ConfirmDialog.vue';
import type { Venta } from '../../types';

const emit = defineEmits<{
    'sale-completed': [sale: Venta];
    back: [];
}>();

const cart = useCartStore();
const confirmOpen = ref(false);

async function realizarCompra() {
    confirmOpen.value = false;
    const resultado = await cart.checkout();
    if (resultado.ok && resultado.sale) {
        showToast('¡Compra realizada con éxito!');
        emit('sale-completed', resultado.sale);
    }
}
</script>

<template>
    <v-card class="mx-auto" max-width="640">
        <v-card-item>
            <div class="d-flex align-center justify-space-between ga-2">
                <div>
                    <div class="text-h5 font-weight-bold">Confirmar compra</div>
                    <div class="text-body-2 text-medium-emphasis">Revisá el detalle antes de finalizar</div>
                </div>
                <v-btn
                    icon="mdi-arrow-left"
                    variant="text"
                    aria-label="Volver al catálogo"
                    @click="emit('back')"
                ></v-btn>
            </div>
        </v-card-item>
        <v-divider></v-divider>

        <v-card-text>
            <CheckoutItemsList v-if="cart.lineas.length > 0" :items="cart.lineas" />
            <div v-else class="text-center text-medium-emphasis pa-8">
                No hay ítems en el carrito.
            </div>
        </v-card-text>

        <v-divider></v-divider>

        <CartTotals
            :subtotal="cart.subtotal"
            :iva-total="cart.ivaTotal"
            :total="cart.total"
            :error="cart.checkoutError"
            :checking-out="cart.checkingOut"
            :disabled="cart.lineas.length === 0"
            button-label="Realizar compra"
            @confirm="confirmOpen = true"
        />

        <ConfirmDialog
            v-model="confirmOpen"
            title="Confirmar compra"
            :message="`¿Confirmás la compra de ${cart.cantidadTotal} ítems por ${formatMoney(cart.total)}?`"
            confirm-text="Sí, comprar"
            @confirm="realizarCompra"
        />
    </v-card>
</template>

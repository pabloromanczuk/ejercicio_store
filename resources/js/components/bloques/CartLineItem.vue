<script setup lang="ts">
import { computed, ref } from 'vue';
import type { LineaCarrito } from '../../types';
import QuantityStepper from '../elementos/QuantityStepper.vue';
import PriceText from '../elementos/PriceText.vue';
import ConfirmDialog from '../elementos/ConfirmDialog.vue';

const props = defineProps<{
    linea: LineaCarrito;
}>();

const emit = defineEmits<{
    'update:cantidad': [productId: number, cantidad: number];
    remove: [productId: number];
}>();

const confirmOpen = ref(false);
const confirmAction = ref<'remove' | 'to-zero'>('remove');

function pedirRemover() {
    confirmAction.value = 'remove';
    confirmOpen.value = true;
}

function onConfirmar() {
    if (confirmAction.value === 'remove') {
        emit('remove', props.linea.product_id);
    } else {
        emit('update:cantidad', props.linea.product_id, 0);
    }
}

const mensajeConfirmacion = computed<string>(() => {
    if (confirmAction.value === 'remove') {
        return `¿Querés quitar "${props.linea.detalle}" del carrito?`;
    }
    return `Si bajás la cantidad a 0, "${props.linea.detalle}" desaparecerá del carrito. ¿Continuar?`;
});

const cantidad = computed<number>({
    get: () => props.linea.cantidad,
    set: (value: number) => {
        // Si se decrementa a 0, levantamos confirmacion
        if (value <= 0) {
            confirmAction.value = 'to-zero';
            confirmOpen.value = true;
        } else {
            emit('update:cantidad', props.linea.product_id, value);
        }
    },
});
</script>

<template>
    <div class="d-flex justify-space-between align-start ga-3 py-3 border-b" style="min-width: 0;">
        <div class="flex-grow-1" style="min-width: 0;">
            <div class="text-subtitle-1 font-weight-medium text-truncate">{{ linea.detalle }}</div>
            <div class="text-caption text-medium-emphasis">
                <PriceText :value="linea.precio_unitario" /> · IVA {{ linea.iva }}%
            </div>
            <div class="text-caption mt-1">
                Subtotal: <PriceText :value="linea.total" />
            </div>
        </div>

        <div class="d-flex flex-column align-end ga-2">
            <QuantityStepper
                v-model="cantidad"
                :min="0"
                :max="linea.stock"
            />
            <v-btn
                size="small"
                variant="text"
                color="error"
                prepend-icon="mdi-trash-can-outline"
                @click="pedirRemover"
            >
                Quitar
            </v-btn>
        </div>
    </div>

    <ConfirmDialog
        v-model="confirmOpen"
        title="Quitar del carrito"
        :message="mensajeConfirmacion"
        confirm-text="Sí, quitar"
        color="error"
        @confirm="onConfirmar"
    />
</template>

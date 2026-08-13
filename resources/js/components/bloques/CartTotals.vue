<script setup lang="ts">
import TotalsRow from '../elementos/TotalsRow.vue';

withDefaults(defineProps<{
    subtotal: number;
    ivaTotal: number;
    total: number;
    error: string | null;
    checkingOut: boolean;
    disabled: boolean;
    buttonLabel?: string;
}>(), {
    error: null,
    buttonLabel: 'Confirmar compra',
});

const emit = defineEmits<{
    confirm: [];
}>();
</script>

<template>
    <div class="pa-4">
        <v-alert v-if="error" type="error" variant="tonal" class="mb-3">
            {{ error }}
        </v-alert>

        <TotalsRow label="Subtotal" :value="subtotal" />
        <TotalsRow label="IVA" :value="ivaTotal" />
        <TotalsRow label="Total" :value="total" total />

        <v-btn
            color="primary"
            variant="flat"
            block
            class="mt-4"
            :loading="checkingOut"
            :disabled="disabled"
            @click="emit('confirm')"
        >
            {{ buttonLabel }}
        </v-btn>
    </div>
</template>

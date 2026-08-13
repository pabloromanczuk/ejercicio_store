<script setup lang="ts">
import { computed } from 'vue';

const model = defineModel<number>({ default: 1 });

const props = withDefaults(defineProps<{
    min?: number;
    max?: number;
    disabled?: boolean;
}>(), {
    min: 1,
    max: 999,
    disabled: false,
});

const atMin = computed<boolean>(() => (model.value ?? props.min) <= props.min);
const atMax = computed<boolean>(() => (model.value ?? props.min) >= props.max);

function decrement() {
    if (props.disabled) return;
    model.value = Math.max(props.min, (model.value ?? props.min) - 1);
}

function increment() {
    if (props.disabled) return;
    model.value = Math.min(props.max, (model.value ?? props.min) + 1);
}
</script>

<template>
    <div class="d-flex align-center justify-space-between ga-2" :class="{ 'opacity-50': disabled }">
        <v-btn
            icon="mdi-minus"
            variant="outlined"
            size="small"
            :disabled="disabled || atMin"
            aria-label="Disminuir cantidad"
            @click="decrement"
        ></v-btn>

        <v-text-field
            :model-value="model"
            type="number"
            :min="min"
            :max="max"
            :disabled="disabled"
            hide-details
            density="compact"
            variant="outlined"
            class="text-center flex-grow-1"
            style="max-width: 80px;"
            @update:model-value="(v) => (model = Number(v))"
        ></v-text-field>

        <v-btn
            icon="mdi-plus"
            variant="outlined"
            size="small"
            :disabled="disabled || atMax"
            aria-label="Aumentar cantidad"
            @click="increment"
        ></v-btn>
    </div>
</template>

<style scoped>

/* quito flechas de input incremental*/
:deep(input[type='number'])::-webkit-outer-spin-button,
:deep(input[type='number'])::-webkit-inner-spin-button {
    -webkit-appearance: none;
    margin: 0;
}

:deep(input[type='number']) {
    -moz-appearance: textfield;
    appearance: textfield;
    text-align: center;
}
</style>

<script setup lang="ts">
import { computed } from 'vue';

const props = defineProps<{
    disponible: number;
}>();

type StockState = 'out' | 'low' | 'ok';

const state = computed<StockState>(() => {
    if (props.disponible <= 0) return 'out';
    if (props.disponible <= 5) return 'low';
    return 'ok';
});

const color = computed<string>(() => {
    if (state.value === 'out') return 'error';
    if (state.value === 'low') return 'warning';
    return 'success';
});

const text = computed<string>(() => {
    if (state.value === 'out') return 'Sin stock disponible';
    return `${props.disponible} disponibles`;
});
</script>

<template>
    <v-chip :color="color" size="small" variant="tonal">
        {{ text }}
    </v-chip>
</template>

<script setup lang="ts">
import { formatMoney } from '../../utils/format';

withDefaults(defineProps<{
    label: string;
    value: number;
    total?: boolean;
}>(), {
    total: false,
});
</script>

<template>
    <div
        class="d-flex justify-space-between align-baseline"
        :class="total ? 'text-h6 font-weight-bold mt-1' : 'text-body-1'"
    >
        <span>{{ label }}</span>
        <span>{{ formatMoney(value) }}</span>
    </div>
</template>

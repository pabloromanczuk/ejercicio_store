<script setup lang="ts">
import { computed } from 'vue';
import { formatMoney } from '../../utils/format';

const props = withDefaults(defineProps<{
    value: number;
    size?: 'body' | 'title' | 'headline';
    tone?: 'default' | 'primary' | 'muted';
}>(), {
    size: 'body',
    tone: 'default',
});

const cls = computed<string>(() => {
    const sizeClass = {
        body: 'text-body-1',
        title: 'text-h6 font-weight-bold',
        headline: 'text-h5 font-weight-bold',
    }[props.size];
    const toneClass = {
        default: '',
        primary: 'text-primary',
        muted: 'text-medium-emphasis',
    }[props.tone];

    return [sizeClass, toneClass].join(' ');
});
</script>

<template>
    <span :class="cls">{{ formatMoney(value) }}</span>
</template>

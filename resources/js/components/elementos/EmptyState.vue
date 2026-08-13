<script setup lang="ts">
withDefaults(defineProps<{
    icon?: string;
    text: string;
}>(), {
    icon: 'mdi-information-outline',
});
</script>

<template>
    <div class="text-center text-medium-emphasis pa-8">
        <v-icon :icon="icon" size="48" class="mb-2"></v-icon>
        <div>{{ text }}</div>
    </div>
</template>

<script setup lang="ts">
import { useToast } from '../../composables/useToast';

const { toasts, dismissToast } = useToast();
</script>

<template>
    <v-snackbar
        v-for="toast in toasts"
        :key="toast.id"
        :model-value="true"
        :color="toast.color"
        :timeout="toast.timeout"
        location="bottom"
        @update:model-value="(v) => !v && dismissToast(toast.id)"
    >
        {{ toast.message }}
    </v-snackbar>
</template>

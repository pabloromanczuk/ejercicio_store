<script setup lang="ts">
const model = defineModel<boolean>({ default: false });

withDefaults(defineProps<{
    title: string;
    message: string;
    confirmText?: string;
    cancelText?: string;
    color?: string;
}>(), {
    confirmText: 'Confirmar',
    cancelText: 'Cancelar',
    color: 'primary',
});

const emit = defineEmits<{
    confirm: [];
}>();

function confirmar() {
    model.value = false;
    emit('confirm');
}
</script>

<template>
    <v-dialog v-model="model" max-width="420">
        <v-card>
            <v-card-title class="text-h6 font-weight-bold">{{ title }}</v-card-title>
            <v-card-text>{{ message }}</v-card-text>
            <v-card-actions>
                <v-spacer></v-spacer>
                <v-btn variant="text" @click="model = false">{{ cancelText }}</v-btn>
                <v-btn :color="color" variant="flat" @click="confirmar">{{ confirmText }}</v-btn>
            </v-card-actions>
        </v-card>
    </v-dialog>
</template>

<script setup lang="ts">
interface RedSocial {
    label: string;
    icon: string;
    url: string;
}

const redes: RedSocial[] = [
    { label: 'Facebook', icon: 'mdi-facebook', url: 'https://facebook.com' },
    { label: 'Instagram', icon: 'mdi-instagram', url: 'https://instagram.com' },
    { label: 'X (Twitter)', icon: 'mdi-twitter', url: 'https://x.com' },
    { label: 'WhatsApp', icon: 'mdi-whatsapp', url: 'https://wa.me' },
];
</script>

<template>
    <v-footer class="footer pa-6">
        <div
            class="d-flex flex-column flex-sm-row align-center justify-space-between w-100 ga-4"
        >
            <div class="d-flex align-center ga-2">
                <v-icon icon="mdi-cart" color="primary"></v-icon>
                <span class="text-h6 font-weight-bold">Tienda</span>
            </div>

            <div class="d-flex align-center ga-2">
                <v-btn
                    v-for="red in redes"
                    :key="red.label"
                    :icon="red.icon"
                    variant="text"
                    color="primary"
                    :aria-label="red.label"
                    :href="red.url"
                    target="_blank"
                    rel="noopener"
                ></v-btn>
            </div>
        </div>
    </v-footer>
</template>

<style scoped>
.footer {
    border-top: 1px solid rgba(var(--v-border-color), var(--v-border-opacity));
}
</style>

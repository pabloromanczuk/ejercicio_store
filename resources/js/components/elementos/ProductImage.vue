<script setup lang="ts">
import { ref } from 'vue';
import { FALLBACK_PRODUCT_IMAGE } from '../../utils/images';

const props = withDefaults(defineProps<{
    src: string;
    alt?: string;
    fallbackSrc?: string;
}>(), {
    alt: '',
    fallbackSrc: FALLBACK_PRODUCT_IMAGE,
});

const currentSrc = ref(props.src);

function onError() {
    if (currentSrc.value !== props.fallbackSrc) {
        currentSrc.value = props.fallbackSrc;
    }
}
</script>

<template>
    <v-img
        :src="currentSrc"
        :alt="alt"
        cover
        aspect-ratio="1"
        @error="onError"
    ></v-img>
</template>

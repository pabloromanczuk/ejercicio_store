import { computed, toValue, type MaybeRefOrGetter } from 'vue';
import { useCartStore } from '../stores/cart';
import type { Producto } from '../types';

/**
 * Lógica de disponibilidad de un producto teniendo en cuenta lo que ya
 * está en el carrito. Separada de la UI para poder reutilizarla.
 */
export function useProductAvailability(producto: MaybeRefOrGetter<Producto>) {
    const cart = useCartStore();

    const productoRef = computed(() => toValue(producto));

    const enCarrito = computed<number>(() => {
        const item = cart.items.find((it) => it.product_id === productoRef.value.id);
        return item ? item.cantidad : 0;
    });

    const disponibleRestante = computed<number>(() => productoRef.value.stock - enCarrito.value);
    const sinStock = computed<boolean>(() => disponibleRestante.value <= 0);
    const stockBajo = computed<boolean>(() => disponibleRestante.value > 0 && disponibleRestante.value <= 5);

    return {
        enCarrito,
        disponibleRestante,
        sinStock,
        stockBajo,
    };
}

import { defineStore } from 'pinia';
import http from '../api/http';
import type { CarritoItem, LineaCarrito, Producto, Venta } from '../types';

export interface AgregarResultado {
    ok: boolean;
    message?: string;
}

export interface CheckoutResultado {
    ok: boolean;
    sale?: Venta;
    message?: string;
}

export const useCartStore = defineStore('cart', {
    state: (): {
        items: CarritoItem[];
        checkingOut: boolean;
        checkoutError: string | null;
        lastSale: Venta | null;
    } => ({
        // items: [{ product_id, codigo, detalle, precio_unitario, iva, stock, cantidad }]
        items: [],
        checkingOut: false,
        checkoutError: null,
        lastSale: null,
    }),

    getters: {
        cantidadTotal: (state): number => state.items.reduce((acc, it) => acc + it.cantidad, 0),

        // Detalle de cada línea con subtotal / iva / total ya calculados,
        // en espejo de lo que hace el backend para que lo mostrado en el
        // carrito coincida con lo que finalmente se persiste.
        lineas: (state): LineaCarrito[] => state.items.map((it) => {
            const subtotal = round2(it.precio_unitario * it.cantidad);
            const ivaMonto = round2(subtotal * (it.iva / 100));
            const total = round2(subtotal + ivaMonto);
            return { ...it, subtotal, ivaMonto, total };
        }),

        subtotal(): number {
            return round2(this.lineas.reduce((acc, l) => acc + l.subtotal, 0));
        },

        ivaTotal(): number {
            return round2(this.lineas.reduce((acc, l) => acc + l.ivaMonto, 0));
        },

        total(): number {
            return round2(this.lineas.reduce((acc, l) => acc + l.total, 0));
        },
    },

    actions: {
        addItem(product: Producto, cantidad = 1): AgregarResultado {
            const existente = this.items.find((it) => it.product_id === product.id);
            const cantidadPrevia = existente ? existente.cantidad : 0;
            const nuevaCantidad = cantidadPrevia + cantidad;

            if (nuevaCantidad > product.stock) {
                return { ok: false, message: `Solo hay ${product.stock} unidades disponibles de "${product.detalle}".` };
            }

            if (existente) {
                existente.cantidad = nuevaCantidad;
            } else {
                this.items.push({
                    product_id: product.id,
                    codigo: product.codigo,
                    detalle: product.detalle,
                    precio_unitario: product.precio_unitario,
                    iva: product.iva,
                    stock: product.stock,
                    cantidad,
                });
            }

            return { ok: true };
        },

        updateCantidad(productId: number, cantidad: number): void {
            const item = this.items.find((it) => it.product_id === productId);
            if (!item) return;

            if (cantidad <= 0) {
                this.removeItem(productId);
                return;
            }

            item.cantidad = Math.min(cantidad, item.stock);
        },

        removeItem(productId: number): void {
            this.items = this.items.filter((it) => it.product_id !== productId);
        },

        clear(): void {
            this.items = [];
        },

        async checkout(): Promise<CheckoutResultado> {
            this.checkingOut = true;
            this.checkoutError = null;

            try {
                const payload = {
                    items: this.items.map((it) => ({
                        product_id: it.product_id,
                        cantidad: it.cantidad,
                    })),
                };

                const { data } = await http.post<{ data: Venta }>('/sales', payload);
                this.lastSale = data.data;
                this.clear();
                return { ok: true, sale: this.lastSale };
            } catch (error) {
                const message = (error as { response?: { data?: { message?: string } } })?.response?.data?.message
                    || 'No se pudo cerrar la compra. Intentá nuevamente.';
                this.checkoutError = message;
                return { ok: false, message };
            } finally {
                this.checkingOut = false;
            }
        },
    },
});

function round2(value: number): number {
    return Math.round((value + Number.EPSILON) * 100) / 100;
}

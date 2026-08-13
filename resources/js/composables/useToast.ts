import { ref } from 'vue';

export interface ToastItem {
    id: number;
    message: string;
    color: string;
    timeout: number;
}

/**
 * Servicio global de toasts (capa de presentación). Se muestra un único
 * toast a la vez (el nuevo reemplaza al anterior) para evitar superposición.
 */
const toasts = ref<ToastItem[]>([]);
let nextId = 1;

export function showToast(message: string, color = 'success', timeout = 2500): void {
    const id = nextId++;
    toasts.value = [{ id, message, color, timeout }];
}

export function dismissToast(id: number): void {
    toasts.value = toasts.value.filter((t) => t.id !== id);
}

export function useToast() {
    return { toasts, showToast, dismissToast };
}

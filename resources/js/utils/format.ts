/**
 * Utilidades puras de formateo (capa de presentación, sin estado).
 */

/** Formatea un importe como moneda local con 2 decimales. */
export function formatMoney(value: number): string {
    return `$${value.toFixed(2)}`;
}

/** Formatea una fecha ISO como fecha local (dd/mm/aaaa, es-AR). */
export function formatDate(iso: string): string {
    const date = new Date(iso);
    if (Number.isNaN(date.getTime())) return iso;

    return date.toLocaleDateString('es-AR', {
        year: 'numeric',
        month: '2-digit',
        day: '2-digit',
    });
}

/** Formatea una fecha ISO como hora local (hh:mm, es-AR). */
export function formatTime(iso: string): string {
    const date = new Date(iso);
    if (Number.isNaN(date.getTime())) return iso;

    return date.toLocaleTimeString('es-AR', {
        hour: '2-digit',
        minute: '2-digit',
    });
}

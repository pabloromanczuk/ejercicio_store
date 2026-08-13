/**
 * Tipos del frontend, espejo de los Resources de Laravel
 * (ProductResource, SaleResource, SaleItemResource).
 */

/** Producto tal como lo devuelve GET /api/products. */
export interface Producto {
    id: number;
    codigo: number;
    detalle: string;
    /** Código de la lista de precios activa del producto. */
    pricelist: string | null;
    /** Código del depósito donde el producto tiene stock. */
    warehouse: string | null;
    /** Precio unitario resuelto desde la lista vigente (sin IVA). */
    precio_unitario: number;
    /** IVA porcentual resuelto desde la lista vigente. */
    iva: number;
    /** Precio unitario con IVA incluido. */
    precio_con_iva: number;
    stock: number;
    /** Medios asociados al producto (imágenes), si vienen cargados. */
    media?: ProductMedia[];
    /** URL de la imagen principal (o del fallback si el producto no tiene media). */
    primary_image_url: string;
    /** Si el producto tiene precio resuelto (lista válida + relación en el pivote). */
    tiene_precio: boolean;
    /** Si el producto tiene stock resuelto (depósito válido + relación en el pivote). */
    tiene_stock: boolean;
}

/** Medio asociado a un producto, tal como lo devuelve ProductMediaResource. */
export interface ProductMedia {
    id: number;
    type: string;
    /** URL pública generada dinámicamente a partir del path. */
    url: string;
    sort_order: number;
    is_primary: boolean;
}

/** Ítem de venta, tal como lo devuelve SaleItemResource. */
export interface SaleItem {
    product_id: number;
    codigo: number;
    detalle: string;
    cantidad: number;
    precio_unitario: number;
    iva_pct: number;
    subtotal: number;
    iva_monto: number;
    total: number;
}

/** Venta, tal como la devuelve SaleResource. */
export interface Venta {
    id: number;
    numero: string;
    estado: string;
    moneda: string;
    fecha: string;
    subtotal: number;
    iva_total: number;
    total: number;
    cantidad_items: number;
    items: SaleItem[];
    integracion_erp: {
        sincronizado: boolean;
        sincronizado_at: string | null;
    };
}

/** Línea almacenada en el carrito (estado del store). */
export interface CarritoItem {
    product_id: number;
    codigo: number;
    detalle: string;
    precio_unitario: number;
    iva: number;
    stock: number;
    cantidad: number;
}

/** Línea del carrito con subtotal / iva / total ya calculados (getter lineas). */
export interface LineaCarrito extends CarritoItem {
    subtotal: number;
    ivaMonto: number;
    total: number;
}

/**
 * Tipos del frontend, espejo de los Resources de Laravel
 * (ProductResource, SaleResource,etc).
 */

export interface Producto {
    id: number;
    codigo: number;
    detalle: string;
    pricelist: string | null;
    warehouse: string | null;
    precio_unitario: number;
    iva: number;
    precio_con_iva: number;
    stock: number;
    media?: ProductMedia[];
    primary_image_url: string;
    tiene_precio: boolean;
    tiene_stock: boolean;
}

export interface ProductMedia {
    id: number;
    type: string;
    url: string;
    sort_order: number;
    is_primary: boolean;
}

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

export interface HomeBanner {
    id: number;
    image: string;
    title: string | null;
    subtitle: string | null;
    link: string | null;
    url: string;
    sort_order: number;
    is_active: boolean;
}

export interface CarritoItem {
    product_id: number;
    codigo: number;
    detalle: string;
    precio_unitario: number;
    iva: number;
    stock: number;
    cantidad: number;
}

export interface LineaCarrito extends CarritoItem {
    subtotal: number;
    ivaMonto: number;
    total: number;
}

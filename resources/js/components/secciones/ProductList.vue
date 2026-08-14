<script setup lang="ts">
import { computed, onBeforeUnmount, onMounted, ref, watch } from 'vue';
import http from '../../api/http';
import ProductCard from './ProductCard.vue';
import EmptyState from '../elementos/EmptyState.vue';
import CatalogFilterBar from '../bloques/CatalogFilterBar.vue';
import { showToast } from '../../composables/useToast';
import type { Producto } from '../../types';

interface Paginacion {
    data: Producto[];
    meta: {
        current_page: number;
        last_page: number;
        total: number;
        per_page: number;
    };
}

interface Filtros {
    search: string;
    sort: 'detalle' | 'price' | 'stock';
    order: 'asc' | 'desc';
    soloDisponibles: boolean;
}

const emit = defineEmits<{
    'ver-producto': [producto: Producto];
}>();

const LIMIT = 10;

const productos = ref<Producto[]>([]);
const loading = ref(true);      // carga inicial o cambio de filtros (pantalla completa)
const loadingMore = ref(false); // loader de scroll (siguientes páginas)
const error = ref<string | null>(null);
const page = ref(1);
const lastPage = ref(1);

// Criterios de búsqueda/orden; se resuelven 100% en el servidor.
const filtros = ref<Filtros>({
    search: '',
    sort: 'detalle',
    order: 'asc',
    soloDisponibles: false,
});

// Texto inmediato del campo de búsqueda; se aplica a `filtros.search` con debounce.
const busquedaInput = ref('');

const hasMore = computed(() => page.value < lastPage.value);

const hayFiltros = computed(
    () =>
        filtros.value.search !== '' ||
        filtros.value.sort !== 'detalle' ||
        filtros.value.order !== 'asc' ||
        filtros.value.soloDisponibles,
);

const sentinel = ref<HTMLElement | null>(null);
let observer: IntersectionObserver | null = null;

// Contador de generación: descarta respuestas obsoletas si el usuario cambia
// los criterios mientras una petición anterior sigue en vuelo.
let generacion = 0;

async function cargarPagina(pagina: number, append = true): Promise<boolean> {
    const generacionActual = generacion;

    try {
        const { data } = await http.get<Paginacion>('/products', {
            params: {
                page: pagina,
                limit: LIMIT,
                search: filtros.value.search || undefined,
                sort: filtros.value.sort,
                order: filtros.value.order,
                disponible: filtros.value.soloDisponibles ? 1 : undefined,
            },
        });

        if (generacionActual !== generacion) return false; // respuesta obsoleta

        productos.value = append
            ? [...productos.value, ...data.data]
            : data.data;

        page.value = data.meta.current_page;
        lastPage.value = data.meta.last_page;

        return true;
    } catch (e) {
        if (generacionActual !== generacion) return false;

        if (productos.value.length === 0) {
            error.value = 'No se pudieron cargar los productos.';
        } else {
            showToast('No se pudieron cargar más productos.', 'error');
        }

        return false;
    }
}

async function cargarMas() {
    if (loadingMore.value || !hasMore.value) return;

    loadingMore.value = true;
    const ok = await cargarPagina(page.value + 1);
    loadingMore.value = false;

    // Si el sentinel sigue en pantalla (la vista aún no se llenó) y quedan
    // páginas, seguimos cargando hasta llenar la ventana o agotar páginas.
    if (
        ok
        && hasMore.value
        && sentinel.value
        && sentinel.value.getBoundingClientRect().top < window.innerHeight
    ) {
        cargarMas();
    }
}

/** Reinicia el listado desde la página 1 con los criterios actuales. */
async function reiniciar() {
    generacion++;
    page.value = 1;
    lastPage.value = 1;
    productos.value = [];
    error.value = null;
    loading.value = true;

    window.scrollTo({ top: 0 });
    await cargarPagina(1, false);
    loading.value = false;
}

let debounceTimer: number | undefined;

watch(busquedaInput, () => {
    window.clearTimeout(debounceTimer);
    debounceTimer = window.setTimeout(() => {
        filtros.value.search = busquedaInput.value.trim();
    }, 350);
});

// recargar listado al cambiar filtros (sort/order/soloDisponibles/search)
watch(
    () => [filtros.value.search, filtros.value.sort, filtros.value.order, filtros.value.soloDisponibles],
    () => reiniciar(),
);

function limpiarFiltros() {
    busquedaInput.value = '';
    filtros.value.search = '';
    filtros.value.sort = 'detalle';
    filtros.value.order = 'asc';
    filtros.value.soloDisponibles = false;
    // El watch sobre `filtros` dispara el reload con los valores por defecto.
}

onMounted(async () => {
    await reiniciar();
});

// Cada vez que el sentinel se (re)crea (montaje inicial o tras un filtrado),
// lo volvemos a observar para mantener el scroll infinito funcionando.
watch(sentinel, (el) => {
    observer?.disconnect();
    observer = null;
    if (!el) return;

    observer = new IntersectionObserver((entries) => {
        if (entries[0].isIntersecting) {
            cargarMas();
        }
    }, { rootMargin: '200px 0px' });

    observer.observe(el);
});

onBeforeUnmount(() => observer?.disconnect());
</script>

<template>
    <div>
        <CatalogFilterBar
            v-model:search="busquedaInput"
            v-model:sort="filtros.sort"
            v-model:order="filtros.order"
            v-model:soloDisponibles="filtros.soloDisponibles"
            @limpiar="limpiarFiltros"
        />

        <!-- Carga inicial o cambio de filtros -->
        <div v-if="loading" class="text-center py-16">
            <v-progress-circular indeterminate color="primary" size="48"></v-progress-circular>
            <p class="text-body-2 text-medium-emphasis mt-4">Cargando productos…</p>
        </div>

        <!-- Error en la primera página -->
        <v-alert v-else-if="error && productos.length === 0" type="error" variant="tonal" class="mb-4">
            {{ error }}
        </v-alert>

        <!-- Catálogo vacío (sin resultados para los filtros) -->
        <EmptyState
            v-else-if="productos.length === 0"
            icon="mdi-package-variant-closed"
            :text="hayFiltros
                ? 'No se encontraron productos con los filtros seleccionados.'
                : 'No hay productos disponibles.'"
        />

        <!-- Catálogo con productos -->
        <template v-else>
            <v-row>
                <v-col
                    v-for="producto in productos"
                    :key="producto.id"
                    cols="12"
                    sm="6"
                    md="4"
                    lg="3"
                >
                    <ProductCard :producto="producto" @ver-detalle="emit('ver-producto', $event)" />
                </v-col>
            </v-row>

            <!-- Sentinel: al llegar hasta acá se pide la siguiente página. -->
            <div ref="sentinel" class="text-center py-6">
                <v-progress-circular
                    v-if="loadingMore"
                    indeterminate
                    color="primary"
                    size="40"
                ></v-progress-circular>
                <p v-else-if="!hasMore" class="text-body-2 text-medium-emphasis">
                    No hay más productos.
                </p>
            </div>
        </template>
    </div>
</template>

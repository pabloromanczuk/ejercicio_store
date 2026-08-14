<script setup lang="ts">
import { computed } from 'vue';

type OrdenCampo = 'detalle' | 'price' | 'stock';
type OrdenDireccion = 'asc' | 'desc';

const props = defineProps<{
    search: string;
    sort: OrdenCampo;
    order: OrdenDireccion;
    soloDisponibles: boolean;
}>();

const emit = defineEmits<{
    'update:search': [value: string];
    'update:sort': [value: OrdenCampo];
    'update:order': [value: OrdenDireccion];
    'update:soloDisponibles': [value: boolean];
    limpiar: [];
}>();

const opcionesOrden: { title: string; value: `${OrdenCampo}:${OrdenDireccion}` }[] = [
    { title: 'Nombre (A–Z)', value: 'detalle:asc' },
    { title: 'Precio (menor a mayor)', value: 'price:asc' },
    { title: 'Precio (mayor a menor)', value: 'price:desc' },
    { title: 'Stock (menor a mayor)', value: 'stock:asc' },
    { title: 'Stock (mayor a menor)', value: 'stock:desc' },
];

const orden = computed({
    get: () => `${props.sort}:${props.order}`,
    set: (value: string) => {
        const [sort, order] = value.split(':') as [OrdenCampo, OrdenDireccion];
        emit('update:sort', sort);
        emit('update:order', order);
    },
});

const disponibilidad = computed({
    get: () => (props.soloDisponibles ? 'disponible' : 'todos'),
    set: (value: string) => emit('update:soloDisponibles', value === 'disponible'),
});

const hayFiltros = computed(
    () =>
        props.search !== '' ||
        props.sort !== 'detalle' ||
        props.order !== 'asc' ||
        props.soloDisponibles,
);
</script>

<template>
    <v-card class="mb-4" flat border>
        <v-card-text class="pa-3 pa-sm-4">
            <v-row align="center">
                <!-- Búsqueda -->
                <v-col cols="12" md="5">
                    <v-text-field
                        :model-value="search"
                        @update:model-value="emit('update:search', $event ?? '')"
                        label="Buscar producto…"
                        placeholder="Nombre o código"
                        prepend-inner-icon="mdi-magnify"
                        clearable
                        density="comfortable"
                        hide-details
                        single-line
                    ></v-text-field>
                </v-col>

                <!-- Ordenamiento (precio / stock / nombre, asc-desc) -->
                <v-col cols="12" sm="6" md="3">
                    <v-select
                        v-model="orden"
                        :items="opcionesOrden"
                        label="Ordenar por"
                        prepend-inner-icon="mdi-sort"
                        density="comfortable"
                        hide-details
                        single-line
                    ></v-select>
                </v-col>

                <!-- Disponibilidad: todos / solo disponibles -->
                <v-col cols="12" sm="6" md="3">
                    <v-btn-toggle
                        v-model="disponibilidad"
                        mandatory
                        density="comfortable"
                        color="primary"
                        variant="outlined"
                        divided
                    >
                        <v-btn value="todos" size="small">Todos</v-btn>
                        <v-btn value="disponible" size="small">Disponibles</v-btn>
                    </v-btn-toggle>
                </v-col>

                <!-- Limpiar filtros -->
                <v-col cols="12" md="auto" class="text-left text-md-right">
                    <v-btn
                        v-if="hayFiltros"
                        variant="text"
                        color="primary"
                        prepend-icon="mdi-filter-off-outline"
                        @click="emit('limpiar')"
                    >
                        Limpiar
                    </v-btn>
                </v-col>
            </v-row>
        </v-card-text>
    </v-card>
</template>

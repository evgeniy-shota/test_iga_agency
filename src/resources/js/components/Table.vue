<script setup lang="ts">
import { ref } from 'vue';
import { Method } from '@inertiajs/core';

interface Props {
    href?: string;
    tabindex?: number;
    method?: Method;
    as?: string;
    columns?: Array<string>;
    data?: Array<string>;
}

const tempcol: Array<object> = ref([
    { id: 1, name: 'Col1' },
    { id: 2, name: 'Col2' },
    { id: 3, name: 'Col3' },
    { id: 4, name: 'Col4' },
]);

const tempdata: Array<object> = ref([
    { id: 1, status: 'Allowed', someVal: 'val-1', someVal2: 'val2-1' },
    { id: 2, status: 'Allowed', someVal: 'val-2', someVal2: 'val2-2' },
    { id: 3, status: 'Allowed', someVal: 'val-3', someVal2: 'val2-3' },
    { id: 4, status: 'Allowed', someVal: 'val-4', someVal2: 'val2-4' },
]);

defineProps<Props>();

function selectItem(id: string) {
    console.log(id);
}

</script>

<template v-if="props.columns!==undefined">
    <table class="table-auto">
        <thead>
            <tr class="border-b-2">
                <th v-for="col in columns" :key="col" class="border-x-1 p-1">{{ col }}</th>
                <!-- <th class="border-x-1 p-1">Song</th> -->
                <!-- <th class="border-x-1 p-1">Artist</th> -->
                <!-- <th class="border-x-1 p-1">Year</th> -->
            </tr>
        </thead>
        <tbody>
            <tr v-for="row in data" :key="row.id" @click="selectItem(row.id)"
                class="hover:bg-gray-100 border-b-2 cursor-pointer">
                <td v-for="item in row" :key="item + row.id" class="border-x-1 p-1">{{ item }}</td>
            </tr>
        </tbody>
    </table>
</template>

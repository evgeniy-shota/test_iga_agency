<script setup lang="ts">
import { ref } from 'vue';
import { Method } from '@inertiajs/core';
import { router } from '@inertiajs/vue3';

interface Props {
    sheetId: Int16Array;
    columns?: Array<string>;
    data?: Array<string>;
}

const props = defineProps<Props>();

function selectItem(id: string) {
    router.visit(route('rows.show', id));
}

</script>

<template v-if="props.columns!==undefined">
    <div class=" relativew-full h-[75vh] overflow-auto">
        <table class="table-auto">
            <thead>
                <tr class="border-b-2">
                    <th v-for="col in props.columns" :key="col" class="border-x-1 p-1">{{ col }}</th>
                </tr>
            </thead>
            <tbody>
                <tr v-for="row in props.data" :key="row.id" @click="selectItem(row.id)"
                    class="hover:bg-gray-100 border-b-2 cursor-pointer">
                    <td v-for="item in row" :key="item + row.id" class="border-x-1 p-1">{{ item }}</td>
                </tr>
            </tbody>
        </table>
    </div>

</template>

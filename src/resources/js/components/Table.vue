<script setup lang="ts">
import  { Method } from '@inertiajs/core';

interface Props {
    href?: string;
    tabindex?: number;
    method?: Method;
    as?: string;
}

defineProps<Props>();

function selectItem(id: string) {
    console.log(id);
} 

</script>

<template>
    <table class="table-auto">
        <thead>
            <tr class="border-b-2">
                <th class="border-x-1 p-1">Song</th>
                <th class="border-x-1 p-1">Artist</th>
                <th class="border-x-1 p-1">Year</th>
            </tr>
        </thead>
        <tbody>
            <tr @click="selectItem(12)" class="hover:bg-gray-100 border-b-2 cursor-pointer">
                <td class="border-x-1 p-1">The Sliding Mr. Bones (Next Stop, Pottersville)</td>
                <td class="border-x-1 p-1">Malcolm Lockyer</td>
                <td class="border-x-1 p-1">1961</td>
            </tr>
            <tr class="border-b-2 cursor-pointer">
                <td class="border-x-1 p-1">Witchy Woman</td>
                <td class="border-x-1 p-1">The Eagles</td>
                <td class="border-x-1 p-1">1972</td>
            </tr>
            <tr class="border-b-2 cursor-pointer">
                <td class="border-x-1 p-1">Shining Star</td>
                <td class="border-x-1 p-1">Earth, Wind, and Fire</td>
                <td class="border-x-1 p-1">1975</td>
            </tr>
        </tbody>
    </table>
</template>

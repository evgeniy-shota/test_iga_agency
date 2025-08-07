<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, router, useForm, usePoll } from '@inertiajs/vue3';
// import PlaceholderPattern from '../components/PlaceholderPattern.vue';
import Table from '@/components/Table.vue';
import Input from '@/components/ui/input/Input.vue';
import Button from '@/components/ui/button/Button.vue';
import { computed, Ref, ref } from 'vue';

interface Props {
    spreadsheets?: object;
    output?: string;
}

const props = defineProps<Props>();

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Fetch Output',
        href: '/fetch',
    },
];

</script>

<template>

    <Head title="Dashboard" />

    <AppLayout :breadcrumbs="breadcrumbs" :spreadsheets="props.spreadsheets">
        <div v-if="$page.props.flash.message" class="w-auto mx-4 px-2 bg-green-300 text-green-800 rounded">
            {{ $page.props.flash.message }}
        </div>

        <div class="flex h-full flex-1 flex-col gap-4 rounded-xl p-4 overflow-x-auto">
            {{ props.output }}
        </div>
    </AppLayout>
</template>

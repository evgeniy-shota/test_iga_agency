<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, useForm } from '@inertiajs/vue3';
// import PlaceholderPattern from '../components/PlaceholderPattern.vue';
import Table from '@/components/Table.vue';
import Input from '@/components/ui/input/Input.vue';
import Button from '@/components/ui/button/Button.vue';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Dashboard',
        href: '/dashboard',
    },
];

const formUrl = useForm({
    url: '',
});

const formAdd = useForm({
    number: '',
});

const formClear = useForm({
    number: '',
});

const submitUrl = () => {
    formUrl.post(route(''), {
        onFinish: () => formUrl.reset('url'),
    });
};

const submitAddLines = () => {
    formAdd.post(route(''), {
        onFinish: () => formAdd.reset('number'),
    });
};

const submitClear = () => {
    formClear.delete(route(''), {
        onFinish: () => formUrl.reset('url'),
    });
};

</script>

<template>

    <Head title="Dashboard" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="w-full rounded-xl p-4 overflow-x-auto">

            <form @submit.prevent="submitUrl" class="w-2/3 inline-block me-1">
                <div class="relative flex gap-1 rounded-xl md:min-h-min ">
                    <Input name="url" type="url" />
                    <Button>
                        Set spreadsheet
                    </Button>
                </div>
            </form>

            <form @submit.prevent="submitAddLines" class="inline-block me-1">
                <div class="relative w-full flex gap-1">
                    <Input name="linesNumber" type="number" :defaultValue="1000" min="1" max="1000" class="w-[5rem]" />
                    <Button>
                        Add lines
                    </Button>
                </div>
            </form>

            <form @submit.prevent="submitClear" class="inline-block">
                <div class="relative w-full flex gap-1">
                    <Button variant="destructive">
                        Clear
                    </Button>
                </div>
            </form>
        </div>

        <div class="flex h-full flex-1 flex-col gap-4 rounded-xl p-4 overflow-x-auto">
            <div
                class="relative min-h-[100vh] flex-1 rounded-xl border border-sidebar-border/70 md:min-h-min dark:border-sidebar-border">
                <Table />
            </div>
        </div>
    </AppLayout>
</template>

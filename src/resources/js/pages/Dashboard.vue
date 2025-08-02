<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, useForm } from '@inertiajs/vue3';
// import PlaceholderPattern from '../components/PlaceholderPattern.vue';
import Table from '@/components/Table.vue';
import Input from '@/components/ui/input/Input.vue';
import Button from '@/components/ui/button/Button.vue';
import { computed, ref } from 'vue';

interface Props {
    spreadsheet?: object;
    columns?: Array<string>;
    spreadsheetData?: Array<object>;
}

const props = defineProps<Props>();

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Dashboard',
        href: '/dashboard',
    },
];

const currentSheetTitle: string = ref(props.spreadsheet.current_sheet);

const currentSheetTitleComp = computed(() => {
    return currentSheetTitle.value;
})

function setCurrentSheetTitle(title) {

    currentSheetTitle.value = title
    submitUrl();
}

const formUrl = useForm({
    url: props.spreadsheet.url,
    spreadsheet: props.spreadsheet.id ?? null,
    sheet: currentSheetTitleComp ?? '',
});

const formAdd = useForm({
    number: 1000,
});

const formClear = useForm({
    number: '',
});

const submitUrl = () => {
    formUrl.post(route('spreadsheet.create'), {
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
        <div class="w-full rounded-xl p-4 overflow-x-auto flex align-top">

            <!-- URL form -->
            <form @submit.prevent="submitUrl" class="w-2/3 inline-block me-1">
                <div class="relative flex gap-1 rounded-xl md:min-h-min ">
                    <div class="w-full">
                        <Input name="url" v-model="formUrl.url" required :defaultValue="props.spreadsheet.url"
                            type="url" placeholder="Enter url" />
                        <div class="text-rose-700 text-sm">{{ formUrl.errors.url }}</div>
                    </div>

                    <Button type="submit">
                        Confirm url
                    </Button>
                </div>
            </form>

            <!-- Add lines form -->
            <form @submit.prevent="submitAddLines" class="inline-block me-1">
                <div class="relative w-full flex gap-1">
                    <!-- <Input name="linesNumber" type="number" :defaultValue="1000" min="1" max="1000" class="w-[5rem]" /> -->
                    <Button type="submit">
                        Add 1000 lines
                    </Button>
                </div>
            </form>

            <!-- Clear form -->
            <form @submit.prevent="submitClear" class="inline-block">
                <div class="relative w-full flex gap-1">
                    <Button type="submit" variant="destructive">
                        Clear
                    </Button>
                </div>
            </form>
        </div>

        <div class="flex gap-2 px-4 overflow-x-auto overflow-y-hidden">
            <Button v-for="(sheetId, sheetName) in JSON.parse(props.spreadsheet.sheets)" :key="sheetId"
                @click="setCurrentSheetTitle(sheetName)" variant="outline" class="border-1 border-gray-300"
                :class="{ 'text-sky-600': props.spreadsheet.current_sheet == sheetName }">
                {{ sheetName }}
            </Button>
        </div>

        <div class="flex h-full flex-1 flex-col gap-4 rounded-xl p-4 overflow-x-auto">
            <div
                class="relative min-h-[100vh] flex-1 rounded-xl border border-sidebar-border/70 md:min-h-min dark:border-sidebar-border">
                <Table :data="props.spreadsheetData" :columns="props.columns" />
            </div>
        </div>
    </AppLayout>
</template>

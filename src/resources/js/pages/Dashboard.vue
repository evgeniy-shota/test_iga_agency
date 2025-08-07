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
    spreadsheet?: object;
    sheets?: Array<object>;
    columns?: Array<string>;
    rows?: Array<object>;
}

const props = defineProps<Props>();

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Dashboard',
        href: '/dashboard',
    },
];

const currentSheetTitle: Ref<string> = ref(props.spreadsheet?.current_sheet ?? '');
const currentUrl: Ref<string> = ref(props.spreadsheet?.url ?? '');

const currentSheetTitleComp = computed(() => {
    return currentSheetTitle.value ?? '';
})

const currentUrlComp = computed(() => {
    return currentUrl.value ?? '';
})

const isSpreadsheetSet = computed(() => {
    return (props.spreadsheet && props.spreadsheet?.id);
})

usePoll(60000);
// usePoll(10000);

const getCurrentSheet = computed(() => {
    console.log(props.sheets)

    if (props.sheets?.length == 1) {
        return props.sheets[0].id;
    }

    for (let sheet: Object of props.sheets) {
        if (sheet.is_current) {
            return sheet.id
        }
    }

})

function openRowEditor(sheetId: string) {
    router.visit(route('rows.index', { sheetId: sheetId }))
}

function setCurrentSheetTitle(title: string) {
    currentSheetTitle.value = title
    submitUrlForm();
}

const formUrl = useForm({
    url: currentUrlComp,
    sheet: currentSheetTitleComp,
});

const formAdd = useForm({
    number: 1000,
});

const formClear = useForm({
    number: '',
});

function submitUrlForm() {
    if (props.spreadsheet?.id == null
        || props.spreadsheet?.url !== formUrl.url) {
        createSpreadSheet()
    } else {
        updateSpreadSheet(props.spreadsheet.id)
    }
}

function submitClearForm() {
    if (props.spreadsheet?.id !== null) {
        clearSpreadSheet(props.spreadsheet?.id)
    }
}

function submitAddLines(id: string) {
    submitAddLinesToSheet(id)
}

function submitDeleteLines(id: string) {
    submitDeleteAllRows(id)
}

const createSpreadSheet = () => {
    formUrl.post(route('spreadsheet.create'), {
        onFinish: () => formUrl.reset('url'),
    });
};

const updateSpreadSheet = (id: string) => {
    formUrl.put(route('spreadsheet.update', id), {
        onFinish: () => formUrl.reset('url'),
    });
};

const submitAddLinesToSheet = (sheetId: string) => {
    console.log(getCurrentSheet);
    formAdd.post(route('rows.addmultiplerows', sheetId), {
        onFinish: () => formAdd.reset('number'),
    });
};

const submitDeleteAllRows = (sheetId: string) => {
    formClear.delete(route('rows.deleteallrows', sheetId), {
        onFinish: () => formUrl.reset('url'),
    });
};

</script>

<template>

    <Head title="Dashboard" />

    <AppLayout :breadcrumbs="breadcrumbs" :spreadsheets="props.spreadsheets" :title="props.spreadsheet?.title">
        <div v-if="$page.props.flash.message" class="w-auto mx-4 px-2 bg-green-300 text-green-800 rounded">
            {{ $page.props.flash.message }}
        </div>
        <div v-if="$page.props.flash.error" class="w-auto mx-4 px-2 bg-rose-300 text-rose-800 rounded">
            {{ $page.props.flash.error }}
        </div>

        <div class="w-full rounded-xl p-4 overflow-x-auto flex align-top">
            <!-- URL form -->
            <form @submit.prevent="submitUrlForm" class="w-2/3 inline-block me-1">
                <div class="relative flex gap-1 rounded-xl md:min-h-min ">
                    <div class="w-full">
                        <Input name="url" v-model="currentUrl" required type="url" placeholder="Enter url" />
                        <div class="text-rose-700 text-sm">{{ formUrl.errors.url }}</div>
                    </div>

                    <Button type="submit" class="bg-green-600 hover:bg-green-700">
                        Confirm url
                    </Button>
                </div>
            </form>

            <!-- Add lines form -->
            <form @submit.prevent="submitAddLines(getCurrentSheet)" class="inline-block me-1">
                <div class="relative w-full flex gap-1">
                    <!-- <Input name="linesNumber" type="number" :defaultValue="1000" min="1" max="1000" class="w-[5rem]" /> -->
                    <Button :disabled="!isSpreadsheetSet" type="submit">
                        Add 1000 rows
                    </Button>
                </div>
            </form>

            <!-- Clear form -->
            <form @submit.prevent="submitDeleteLines(getCurrentSheet)" class="inline-block me-1">
                <div class="relative w-full flex gap-1">
                    <Button :disabled="!isSpreadsheetSet" type="submit" variant="destructive">
                        Clear
                    </Button>
                </div>
            </form>

            <!-- router.visit(route('rows.index', '')) -->
            <Button @click="openRowEditor(getCurrentSheet)" :disabled="!isSpreadsheetSet">
                Add row
            </Button>
        </div>

        <div v-if="isSpreadsheetSet" class="flex gap-2 px-4 overflow-x-auto overflow-y-hidden">
            <Button v-for="(sheet) in props.sheets" :key="sheet.id" @click="setCurrentSheetTitle(sheet.id)"
                variant="outline" class="border-1 border-gray-300" :class="{ 'text-sky-600': sheet.is_current }">
                {{ sheet.title }}
            </Button>
        </div>

        <div class="flex h-full flex-1 flex-col gap-4 rounded-xl p-4 overflow-x-auto">
            <div
                class="relative min-h-[100vh] flex-1 rounded-xl border border-sidebar-border/70 md:min-h-min dark:border-sidebar-border">
                <Table :data="props.rows" :columns="props.columns" :sheetId="getCurrentSheet" />
            </div>
        </div>
    </AppLayout>
</template>

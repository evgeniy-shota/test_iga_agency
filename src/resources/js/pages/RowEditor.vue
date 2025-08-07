<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, useForm } from '@inertiajs/vue3';
import Input from '@/components/ui/input/Input.vue';
import Button from '@/components/ui/button/Button.vue';
import { computed, Ref, ref } from 'vue';

interface Props {
    spreadsheets?: object;
    row?: object;
    sheetId: number;
    availableRowStatuses: Array<string>;
    message?: string;
}

const props = defineProps<Props>();

const rowStatus: Ref<string> = ref(props?.row?.status ?? '');
const name: Ref<string> = ref(props?.row?.name ?? '');
const reserved_count: Ref<string> = ref(props?.row?.reserved_count ?? '');
const total_count: Ref<string> = ref(props?.row?.total_count ?? '');

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Row editor',
        href: '/rows',
    },
];

const formRow = useForm({
    sheet_id: props.sheetId,
    status: rowStatus.value,
    name: name.value,
    reserved_count: reserved_count.value,
    total_count: total_count.value,
});

function submitForm() {
    if (props.row === null || props.row?.id === null) {
        console.log(formRow);
        createRow()
    } else {
        updateRow(props.row?.id)
    }
}

function submitDeleteRow(id) {
    if (id == null) {
        console.log("can't delete");
        return;
    }

    deleteRow(id)
}

const createRow = () => {
    formRow.post(route('rows.create'), {
        onFinish: () => formRow.reset('url'),
    });
};

const updateRow = (id: string) => {
    formRow.put(route('rows.update', id), {
        onFinish: () => formRow.reset('url'),
    });
};

const deleteRow = (id: string) => {
    formRow.delete(route('rows.delete', id), {
        onFinish: () => formRow.reset('url'),
    });
};

</script>

<template>

    <Head title="Dashboard" />

    <AppLayout :breadcrumbs="breadcrumbs" :spreadsheets="props.spreadsheets">
        <div v-if="props.message" class="p-3 bg-green-300 text-green-700">{{ props.message }}</div>
        <div v-if="$page.props.flash.message" class="p-3 bg-rose-300 text-rose-700">{{ $page.props.flash.message }}
        </div>
        <div class="w-full rounded-xl p-4 overflow-x-auto flex align-top">
            <!-- URL form -->
            <form @submit.prevent="submitForm" class="w-full inline-block me-1">
                <div class="flex gap-2 mb-2">
                    <Button type="submit" class="bg-green-600 hover:bg-green-700">
                        Save
                    </Button>

                    <Button type="reset" class="bg-gray-600 hover:bg-gray-700">
                        Clear
                    </Button>

                    <Button form="" @click="submitDeleteRow(props.row?.id)" class="bg-rose-600 hover:bg-rose-700">
                        Delete
                    </Button>
                </div>

                <div class="bg-gray-100 p-1 mb-1">
                    <label for="rowStatus">Status</label>
                    <select id="rowStatus" v-model="formRow.status" class="w-full border-gray-200 border-2 p-2 rounded">
                        <option disabled value="">Choose any status</option>
                        <option v-for="status in props.availableRowStatuses" :value="status" :key="status">{{
                            status }}
                        </option>
                    </select>
                    <div class="text-rose-700 text-sm">{{ formRow.errors.status }}</div>
                </div>

                <div class="w-full mb-1 p-1 rounded bg-gray-100">
                    <label for="">Name</label>
                    <Input id="name" name="name" v-model="formRow.name" type="text" placeholder="Enter some text"
                        class="" />
                    <div class="text-rose-700 text-sm">{{ formRow.errors.name }}</div>
                </div>

                <div class="w-full mb-1 p-1 rounded bg-gray-100">
                    <label for="">Reserved count</label>
                    <Input id="reserved_count" name="reserved_count" v-model="formRow.reserved_count" type="number"
                        placeholder="Enter some text" class="" />
                    <div class="text-rose-700 text-sm">{{ formRow.errors.reserved_count }}</div>
                </div>

                <div class="w-full mb-1 p-1 rounded bg-gray-100">
                    <label for="">Total count</label>
                    <Input id="total_count" name="total_count" v-model="formRow.total_count" type="number"
                        placeholder="Enter some text" class="" />
                    <div class="text-rose-700 text-sm">{{ formRow.errors.total_count }}</div>
                </div>

            </form>
        </div>

        <!-- <Button @click="console.log(checkProps)"> show</Button> -->

        <!-- <div class="flex h-full flex-1 flex-col gap-4 rounded-xl p-4 overflow-x-auto">
            <div
                class="relative min-h-[100vh] flex-1 rounded-xl border border-sidebar-border/70 md:min-h-min dark:border-sidebar-border">
            </div>
        </div> -->
    </AppLayout>
</template>

<script setup lang="ts">
import { usePage } from '@inertiajs/vue3'
import AppLayout from '@/layouts/app/AppSidebarLayout.vue';
import type { BreadcrumbItem } from '@/types';

const page = usePage()

import { ref, watch } from 'vue'

const toast = ref(null);
const showToast = (message, type = 'success') => {
    toast.value = { message, type }

    setTimeout(() => {
        toast.value = null
    }, 3000)
}


watch(
    () => page.props.flash,
    (flash) => {
        if (flash.success) {
            showToast(flash.success, 'success')
        }

        if (flash.error) {
            showToast(flash.error, 'error')
        }
    },
    { immediate: true }
)


type Props = {
    breadcrumbs?: BreadcrumbItem[];
};

withDefaults(defineProps<Props>(), {
    breadcrumbs: () => [],
});

</script>

<template>
    <AppLayout :breadcrumbs="breadcrumbs">
        <div
            v-if="toast"
            class="fixed top-5 right-5 px-4 py-3 rounded-lg shadow-lg text-white z-50 transition-all duration-300"
            :class="{
                'bg-green-600': toast.type === 'success',
                'bg-red-600': toast.type === 'error'
            }"
        >
            {{ toast.message }}
        </div>
        <slot />
    </AppLayout>
</template>

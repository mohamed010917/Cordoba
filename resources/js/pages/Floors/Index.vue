<script setup lang="ts">
import { computed, h, ref } from 'vue'
import { router, Link, Head } from '@inertiajs/vue3'
import { type ColumnDef } from '@tanstack/vue-table'
import { Button } from '@/components/ui/button'
import DataTable from '@/components/shared/DataTable.vue'

import { route } from 'ziggy-js'
import AppLayout from '@/layouts/AppLayout.vue'
console.log(route().has('manager.floors.create'))

interface Floor {
    id: number
    name: string
    number: number
    manager?: { id: number; name: string }
}

interface PaginatedFloors {
    data: Floor[]
    current_page: number
    last_page: number
    per_page: number
    total: number
    from: number
    to: number
}

const props = defineProps<{
    floors: PaginatedFloors
    isAdmin: boolean
    filters: Record<string, string>
    url: string
}>()

const deleteErrorMessage = ref<string | null>(null)

const columns = computed<ColumnDef<Floor>[]>(() => {
    const cols: ColumnDef<Floor>[] = [
        {
            accessorKey: 'name',
            header: 'Name',
            enableSorting: true,
        },
        {
            accessorKey: 'number',
            header: 'Number',
            enableSorting: true,
        },
    ]

    if (props.isAdmin) {
        cols.push({
            id: 'manager_name',
            header: 'Manager Name',
            enableSorting: false,
            cell: ({ row }) => row.original.manager?.name ?? '—',
        })
    }

    cols.push({
        id: 'actions',
        header: 'Actions',
        enableSorting: false,
        cell: ({ row }) => {
            const floor = row.original

            return h('div', { class: 'flex items-center gap-2' }, [
                h(
                    Link,
                    { href: route('manager.floors.edit', floor.id) },
                    {
                        default: () =>
                            h(
                                Button,
                                { type: 'button', variant: 'outline', size: 'sm' },
                                { default: () => 'Edit' },
                            ),
                    },
                ),
                h(
                    Button,
                    {
                        type: 'button',
                        variant: 'destructive',
                        size: 'sm',
                        onClick: () => deleteFloor(floor),
                    },
                    { default: () => 'Delete' },
                ),
            ])
        },
    })

    return cols
})

function deleteFloor(floor: Floor) {
    if (!confirm(`Delete floor "${floor.name}"? This cannot be undone.`)) {
        return
    }

    router.delete(route('manager.floors.destroy', floor.id), {
        preserveState: true,
        onSuccess: () => {
            deleteErrorMessage.value = null
        },
        onError: (errors) => {
            deleteErrorMessage.value = String(Object.values(errors)[0] ?? 'Failed to delete floor.')
        },
    })
}

const breadcrumbs = [
    { title: 'Floors', href: '/Manger/Floors' },
]
</script>

<template>
    <Head title="Manage Floors" />
    <AppLayout :breadcrumbs="breadcrumbs">

        <div class="p-6 space-y-4">
            <div class="flex items-center justify-between">
                <h1 class="text-2xl font-semibold">Manage Floors</h1>
                <Link :href="route('manager.floors.create')">
                    <Button>Add Floor</Button>
                </Link>
            </div>

            <div
                v-if="deleteErrorMessage"
                class="rounded-md border border-destructive/30 bg-destructive/10 px-3 py-2 text-sm text-destructive"
            >
                {{ deleteErrorMessage }}
            </div>
    
            <DataTable
                :columns="columns"
                :paginatedData="floors"
                routeName="manager.floors.index"
                :filters="filters"
                searchPlaceholder="Search by name or number..."
            />
        </div>
    </AppLayout>
</template>
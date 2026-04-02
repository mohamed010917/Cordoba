<script setup lang="ts">
import { computed, h, ref } from 'vue'
import { router, Link, Head } from '@inertiajs/vue3'
import { type ColumnDef } from '@tanstack/vue-table'
import { Button } from '@/components/ui/button'
import DataTable from '@/components/shared/DataTable.vue'
import { route } from 'ziggy-js'
import AppLayout from '@/layouts/AppLayout.vue'
import Swal from 'sweetalert2'

interface Room {
    id: number
    number: string
    capacity: number
    price_cents: number
    floor?: { id: number; name: string; number: number }
    manager?: { id: number; name: string }
}

interface PaginatedRooms {
    data: Room[]
    current_page: number
    last_page: number
    per_page: number
    total: number
    from: number
    to: number
}

const props = defineProps<{
    rooms: PaginatedRooms
    isAdmin: boolean
    filters: Record<string, string>
}>()

const deleteErrorMessage = ref<string | null>(null)

const formatPrice = (cents: number) =>
    new Intl.NumberFormat('en-US', { style: 'currency', currency: 'USD' }).format(cents / 100)

const columns = computed<ColumnDef<Room>[]>(() => {
    const cols: ColumnDef<Room>[] = [
        { accessorKey: 'number', header: 'Room #', enableSorting: true },
        { accessorKey: 'capacity', header: 'Capacity', enableSorting: true },
        {
            id: 'price',
            header: 'Price',
            enableSorting: true,
            cell: ({ row }) => formatPrice(row.original.price_cents),
        },
        {
            id: 'floor',
            header: 'Floor',
            enableSorting: false,
            cell: ({ row }) => row.original.floor ? `${row.original.floor.name} (#${row.original.floor.number})` : '—',
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
            const room = row.original
            return h('div', { class: 'flex items-center gap-2' }, [
                h(
                    Link,
                    { href: route('manager.rooms.edit', room.id) },
                    { default: () => h(Button, { type: 'button', variant: 'outline', size: 'sm' }, { default: () => 'Edit' }) },
                ),
                h(
                    Button,
                    {
                        type: 'button',
                        variant: 'destructive',
                        size: 'sm',
                        onClick: () => deleteRoom(room),
                    },
                    { default: () => 'Delete' },
                ),
            ])
        },
    })

    return cols
})

async function deleteRoom(room: Room) {
    const result = await Swal.fire({
        title: `Delete room "${room.number}"?`,
        text: 'This cannot be undone.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Yes, delete',
        cancelButtonText: 'Cancel',
    })

    if (!result.isConfirmed) {
        return
    }

    router.delete(route('manager.rooms.destroy', room.id), {
        preserveState: true,
        onSuccess: () => {
            deleteErrorMessage.value = null
        },
        onError: (errors) => {
            deleteErrorMessage.value = String(Object.values(errors)[0] ?? 'Failed to delete room.')
        },
    })
}
const breadcrumbs = [
    { title: 'rooms ', href: '/Manger/rooms' },
]


</script>

<template>
    <Head title=" Floors" />
    <AppLayout :breadcrumbs="breadcrumbs">

        <div class="p-6 space-y-4">
            <div class="flex items-center justify-between">
                <h1 class="text-2xl font-semibold">Manage Rooms</h1>
                <Link :href="route('manager.rooms.create')">
                    <Button>Add Room</Button>
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
                :paginatedData="rooms"
                routeName="manager.rooms.index"
                :filters="filters"
                searchPlaceholder="Search by room number, floor, or capacity..."
            />
        </div>
    </AppLayout>
</template>

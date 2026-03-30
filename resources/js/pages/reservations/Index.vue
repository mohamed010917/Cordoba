<script setup lang="ts">
import { Head, useForm } from '@inertiajs/vue3';
import { ref } from 'vue';
import DeleteConfirmationDialog from '@/components/DeleteConfirmationDialog.vue';
import Heading from '@/components/Heading.vue';
import { Button } from '@/components/ui/button';
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from '@/components/ui/table';
import AppLayout from '@/layouts/AppLayout.vue';

interface Reservation {
    id: number;
    accompany_number: number;
    paid_price_cents: number;
    created_at: string;
    room: {
        number: string;
    };
}

defineProps<{
    reservations: Reservation[];
}>();

const formatPrice = (cents: number) => {
    return new Intl.NumberFormat('en-US', {
        style: 'currency',
        currency: 'USD',
    }).format(cents / 100);
};

const formatDate = (date: string) => {
    return new Date(date).toLocaleDateString();
};

const showDeleteDialog = ref(false);
const reservationToDelete = ref<number | null>(null);
const deleteForm = useForm({});

const confirmDelete = (id: number) => {
    reservationToDelete.value = id;
    showDeleteDialog.value = true;
};

const handleDelete = () => {
    if (reservationToDelete.value) {
        deleteForm.delete(route('reservations.destroy', reservationToDelete.value), {
            onSuccess: () => {
                showDeleteDialog.value = false;
                reservationToDelete.value = null;
            },
        });
    }
};
</script>

<template>
    <Head title="My Reservations" />

    <AppLayout>
        <div class="px-4 py-6">
            <Heading title="My Reservations" description="Manage your current and past room bookings." />

            <div class="mt-8 border rounded-md">
                <Table>
                    <TableHeader>
                        <TableRow>
                            <TableHead>Room #</TableHead>
                            <TableHead>Date</TableHead>
                            <TableHead>Accompanies</TableHead>
                            <TableHead>Price Paid</TableHead>
                            <TableHead class="text-right">Actions</TableHead>
                        </TableRow>
                    </TableHeader>
                    <TableBody>
                        <TableRow v-for="reservation in reservations" :key="reservation.id">
                            <TableCell class="font-medium">#{{ reservation.room.number }}</TableCell>
                            <TableCell>{{ formatDate(reservation.created_at) }}</TableCell>
                            <TableCell>{{ reservation.accompany_number }}</TableCell>
                            <TableCell>{{ formatPrice(reservation.paid_price_cents) }}</TableCell>
                            <TableCell class="text-right">
                                <Button variant="destructive" size="sm" @click="confirmDelete(reservation.id)">
                                    Cancel
                                </Button>
                            </TableCell>
                        </TableRow>
                        <TableRow v-if="reservations.length === 0">
                            <TableCell colspan="5" class="text-center py-10 text-muted-foreground">
                                You have no reservations yet.
                            </TableCell>
                        </TableRow>
                    </TableBody>
                </Table>
            </div>
        </div>

        <DeleteConfirmationDialog
            :open="showDeleteDialog"
            title="Cancel Reservation?"
            description="Are you sure you want to cancel this reservation? This cannot be undone."
            :loading="deleteForm.processing"
            @close="showDeleteDialog = false"
            @confirm="handleDelete"
        />
    </AppLayout>
</template>

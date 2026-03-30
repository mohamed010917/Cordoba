<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3'
import Heading from '@/components/Heading.vue'
import { Button } from '@/components/ui/button'
import {
    Card,
    CardContent,
    CardDescription,
    CardFooter,
    CardHeader,
    CardTitle,
} from '@/components/ui/card'
import AppLayout from '@/layouts/AppLayout.vue'
import { route } from 'ziggy-js'

interface Room {
    id: number
    number: string
    capacity: number
    price_cents: number
    floor?: {
        number: number
    }
}

defineProps<{
    rooms: Room[]
}>()

const formatPrice = (cents: number) => {
    return new Intl.NumberFormat('en-US', {
        style: 'currency',
        currency: 'USD',
    }).format(cents / 100)
}
</script>

<template>
    <Head title="Available Rooms" />

    <AppLayout>
        <div class="px-4 py-6">
            <Heading title="Available Rooms" description="Browse and book our luxury rooms." />

            <div class="mt-8 grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3">
                <Card v-for="room in rooms" :key="room.id" class="flex flex-col">
                    <CardHeader>
                        <CardTitle>Room #{{ room.number }}</CardTitle>
                        <CardDescription v-if="room.floor">Located on Floor {{ room.floor.number }}</CardDescription>
                    </CardHeader>
                    <CardContent class="flex-grow">
                        <div class="space-y-2">
                            <p class="text-sm text-muted-foreground">Capacity: {{ room.capacity }} people</p>
                            <p class="text-2xl font-bold">{{ formatPrice(room.price_cents) }} / night</p>
                        </div>
                    </CardContent>
                    <CardFooter>
                        <Button as-child class="w-full">
                            <Link :href="route('reservations.create', room.id)">Book Now</Link>
                        </Button>
                    </CardFooter>
                </Card>
            </div>

            <div v-if="rooms.length === 0" class="mt-8 text-center">
                <p class="text-muted-foreground">No rooms available at the moment. Please check back later.</p>
            </div>
        </div>
    </AppLayout>
</template>

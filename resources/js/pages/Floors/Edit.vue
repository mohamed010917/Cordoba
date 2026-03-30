<script setup lang="ts">
import { useForm, Link, Head } from '@inertiajs/vue3'
import { Button } from '@/components/ui/button'
import { Input } from '@/components/ui/input'
import { Label } from '@/components/ui/label'
import InputError from '@/components/InputError.vue'

import { route } from 'ziggy-js'
import AppLayout from '@/layouts/AppLayout.vue'

interface Floor {
    id: number
    name: string
    number: number
}

const props = defineProps<{
    floor: Floor
}>()

const form = useForm({
    name: props.floor.name,
})

function submit() {
    form.put(route('manager.floors.update', props.floor.id))
}

const breadcrumbs = [
    { title: 'Floors edit', href: '/Manger/Floors/create' },
]


</script>

<template>
    <Head title="edit Floors" />
    <AppLayout :breadcrumbs="breadcrumbs">

        <div class="p-6 max-w-md space-y-6">
            <div class="flex items-center justify-between">
                <h1 class="text-2xl font-semibold">Edit Floor</h1>
                <Link :href="route('manager.floors.index')">
                    <Button variant="outline">Back</Button>
                </Link>
            </div>
    
            <form @submit.prevent="submit" class="space-y-4">
                <div class="space-y-1">
                    <Label>Floor Number (cannot be changed)</Label>
                    <Input :value="floor.number" disabled />
                </div>
    
                <div class="space-y-1">
                    <Label for="name">Floor Name</Label>
                    <Input
                        id="name"
                        v-model="form.name"
                        placeholder="Enter floor name"
                    />
                    <InputError :message="form.errors.name" />
                </div>
    
                <Button type="submit" :disabled="form.processing">
                    Save Changes
                </Button>
            </form>
        </div>
    </AppLayout>
</template>
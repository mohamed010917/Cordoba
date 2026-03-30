<script setup lang="ts">
import { useForm, Link } from '@inertiajs/vue3'
import { Button } from '@/components/ui/button'
import { Input } from '@/components/ui/input'
import { Label } from '@/components/ui/label'
import InputError from '@/components/InputError.vue'

import { route } from 'ziggy-js'

defineProps<{
    nextNumber: number
}>()

const form = useForm({
    name: '',
})

function submit() {
    form.post(route('manager.floors.store'))
}
</script>

<template>
    <div class="p-6 max-w-md space-y-6">
        <div class="flex items-center justify-between">
            <h1 class="text-2xl font-semibold">Create Floor</h1>
            <Link :href="route('manager.floors.index')">
                <Button variant="outline">Back</Button>
            </Link>
        </div>

        <form @submit.prevent="submit" class="space-y-4">
            <div class="space-y-1">
                <Label>Floor Number (auto-generated)</Label>
                <Input :value="nextNumber" disabled />
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
                Create Floor
            </Button>
        </form>
    </div>
</template>
<script setup lang="ts">
import { useForm, Link } from '@inertiajs/vue3'
import { Button } from '@/components/ui/button'
import { Input } from '@/components/ui/input'
import { Label } from '@/components/ui/label'
import InputError from '@/components/InputError.vue'
import { route } from 'ziggy-js'

interface FloorOption {
	id: number
	name: string
	number: number
}

interface ManagerOption {
	id: number
	name: string
}

interface Room {
	id: number
	number: string
	capacity: number
	price_cents: number
	floor_id: number
	manager_id: number | null
}

const props = defineProps<{
	room: Room
	floors: FloorOption[]
	managers: ManagerOption[]
	isAdmin: boolean
}>()

const form = useForm({
	number: props.room.number,
	capacity: props.room.capacity,
	price_cents: props.room.price_cents,
	floor_id: props.room.floor_id,
	manager_id: props.room.manager_id ?? '',
})

function submit() {
	form.put(route('manager.rooms.update', props.room.id))
}
</script>

<template>
	<div class="p-6 max-w-xl space-y-6">
		<div class="flex items-center justify-between">
			<h1 class="text-2xl font-semibold">Edit Room</h1>
			<Link :href="route('manager.rooms.index')"><Button variant="outline">Back</Button></Link>
		</div>

		<form @submit.prevent="submit" class="space-y-4">
			<div class="space-y-1">
				<Label for="number">Room Number</Label>
				<Input id="number" v-model="form.number" placeholder="101-A" />
				<InputError :message="form.errors.number" />
			</div>

			<div class="grid grid-cols-2 gap-4">
				<div class="space-y-1">
					<Label for="capacity">Capacity</Label>
					<Input id="capacity" type="number" min="1" v-model.number="form.capacity" />
					<InputError :message="form.errors.capacity" />
				</div>

				<div class="space-y-1">
					<Label for="price_cents">Price (cents)</Label>
					<Input id="price_cents" type="number" min="0" v-model.number="form.price_cents" />
					<InputError :message="form.errors.price_cents" />
				</div>
			</div>

			<div class="space-y-1">
				<Label for="floor_id">Floor</Label>
				<select id="floor_id" v-model="form.floor_id" class="w-full rounded-md border p-2">
					<option value="" disabled>Select floor</option>
					<option v-for="f in floors" :key="f.id" :value="f.id">{{ f.name }} (#{{ f.number }})</option>
				</select>
				<InputError :message="form.errors.floor_id" />
			</div>

			<div v-if="isAdmin" class="space-y-1">
				<Label for="manager_id">Manager</Label>
				<select id="manager_id" v-model="form.manager_id" class="w-full rounded-md border p-2">
					<option value="">Select manager</option>
					<option v-for="m in managers" :key="m.id" :value="m.id">{{ m.name }}</option>
				</select>
				<InputError :message="form.errors.manager_id" />
			</div>

			<Button type="submit" :disabled="form.processing">Save Changes</Button>
		</form>
	</div>
</template>

<script setup>
import AppLayout from '@/layouts/AppLayout.vue'
import { Head, Link, useForm } from '@inertiajs/vue3'
import { onMounted, ref, watch } from 'vue'

const props = defineProps({
  client: Object,
  countries: Array,
})

const cities = ref([])

const jsonHeaders = { Accept: 'application/json' }

const form = useForm({
  name: props.client.name || '',
  email: props.client.email || '',
  password: '',
  password_confirmation: '',
  phone: props.client.phone || '',
  national_id: props.client.national_id || '',
  gender: props.client.gender || '',
  country_id: props.client.country_id || '',
  city_id: props.client.city_id || '',
  image: null,
  is_active: props.client.is_active ?? true,
})

async function loadCities() {
  if (!form.country_id) {
    cities.value = []
    return
  }
  try {
    const res = await fetch(`/api/cities/${form.country_id}`, { headers: jsonHeaders })
    if (!res.ok) {
      throw new Error(`HTTP ${res.status}`)
    }
    cities.value = await res.json()
  } catch {
    cities.value = []
  }
}

watch(
  () => form.country_id,
  () => {
    form.city_id = ''
    cities.value = []
    if (form.country_id) {
      loadCities()
    }
  },
)

onMounted(() => {
  if (form.country_id) {
    loadCities().then(() => {
      if (form.city_id && !cities.value.some((c) => String(c.id) === String(form.city_id))) {
        form.city_id = ''
      }
    })
  }
})

function submit() {
  const url = `/manager/clients/${props.client.id}`

  form.transform((data) => {
    data._method = 'put'
    return data
  }).post(url)
}

function handleImageChange(event) {
  form.image = event.target.files[0]
}
</script>

<template>
  <Head title="Edit Client" />

  <AppLayout>
    <div class="min-h-screen bg-gray-50 px-6 py-8 dark:bg-gray-950">
      <!-- Header -->
      <div class="mb-8 flex items-start justify-between">
        <div class="space-y-1">
          <div class="flex items-center gap-2.5">
            <div class="h-7 w-1 rounded-full bg-indigo-500"></div>
            <h1 class="text-2xl font-bold tracking-tight text-gray-900 dark:text-white">
              Edit Client
            </h1>
          </div>
          <p class="pl-3.5 text-sm text-gray-500 dark:text-gray-400">
            Update client information
          </p>
        </div>

        <Link
          href="/manager/clients"
          class="inline-flex items-center gap-2 rounded-xl border border-gray-200 bg-white px-5 py-2.5 text-sm font-semibold text-gray-700 shadow-sm transition-all hover:bg-gray-50 hover:shadow-md active:scale-95 dark:border-gray-800 dark:bg-gray-900 dark:text-gray-200 dark:hover:bg-gray-800"
        >
          <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
          </svg>
          Back
        </Link>
      </div>

      <!-- Form Card -->
      <div class="rounded-2xl border border-gray-200 bg-white p-6 shadow-sm dark:border-gray-800 dark:bg-gray-900">
        <form @submit.prevent="submit" class="grid grid-cols-1 gap-6 md:grid-cols-2">
          <!-- Name -->
          <div>
            <label class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">
              Name
            </label>
            <input
              v-model="form.name"
              type="text"
              class="w-full rounded-xl border border-gray-200 bg-white px-4 py-3 text-sm text-gray-900 outline-none transition-all hover:border-indigo-300 focus:border-indigo-500 focus:ring-4 focus:ring-indigo-100 dark:border-gray-700 dark:bg-gray-950 dark:text-white dark:hover:border-indigo-500/60 dark:focus:border-indigo-500 dark:focus:ring-indigo-500/10"
              placeholder="Enter client name"
            />
            <div v-if="form.errors.name" class="mt-2 text-sm text-red-500">
              {{ form.errors.name }}
            </div>
          </div>

          <!-- Email -->
          <div>
            <label class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">
              Email
            </label>
            <input
              v-model="form.email"
              type="email"
              class="w-full rounded-xl border border-gray-200 bg-white px-4 py-3 text-sm text-gray-900 outline-none transition-all hover:border-indigo-300 focus:border-indigo-500 focus:ring-4 focus:ring-indigo-100 dark:border-gray-700 dark:bg-gray-950 dark:text-white dark:hover:border-indigo-500/60 dark:focus:border-indigo-500 dark:focus:ring-indigo-500/10"
              placeholder="Enter email address"
            />
            <div v-if="form.errors.email" class="mt-2 text-sm text-red-500">
              {{ form.errors.email }}
            </div>
          </div>

          <!-- Password -->
          <div>
            <label class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">
              New Password
            </label>
            <input
              v-model="form.password"
              type="password"
              class="w-full rounded-xl border border-gray-200 bg-white px-4 py-3 text-sm text-gray-900 outline-none transition-all hover:border-indigo-300 focus:border-indigo-500 focus:ring-4 focus:ring-indigo-100 dark:border-gray-700 dark:bg-gray-950 dark:text-white dark:hover:border-indigo-500/60 dark:focus:border-indigo-500 dark:focus:ring-indigo-500/10"
              placeholder="Leave blank if you do not want to change it"
            />
            <div v-if="form.errors.password" class="mt-2 text-sm text-red-500">
              {{ form.errors.password }}
            </div>
          </div>

          <!-- Confirm Password -->
          <div>
            <label class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">
              Confirm New Password
            </label>
            <input
              v-model="form.password_confirmation"
              type="password"
              class="w-full rounded-xl border border-gray-200 bg-white px-4 py-3 text-sm text-gray-900 outline-none transition-all hover:border-indigo-300 focus:border-indigo-500 focus:ring-4 focus:ring-indigo-100 dark:border-gray-700 dark:bg-gray-950 dark:text-white dark:hover:border-indigo-500/60 dark:focus:border-indigo-500 dark:focus:ring-indigo-500/10"
              placeholder="Confirm new password"
            />
          </div>

          <!-- Phone -->
          <div>
            <label class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">
              Phone
            </label>
            <input
              v-model="form.phone"
              type="text"
              class="w-full rounded-xl border border-gray-200 bg-white px-4 py-3 text-sm text-gray-900 outline-none transition-all hover:border-indigo-300 focus:border-indigo-500 focus:ring-4 focus:ring-indigo-100 dark:border-gray-700 dark:bg-gray-950 dark:text-white dark:hover:border-indigo-500/60 dark:focus:border-indigo-500 dark:focus:ring-indigo-500/10"
              placeholder="Enter phone number"
            />
            <div v-if="form.errors.phone" class="mt-2 text-sm text-red-500">
              {{ form.errors.phone }}
            </div>
          </div>

          <!-- National ID -->
          <div>
            <label class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">
              National ID
            </label>
            <input
              v-model="form.national_id"
              type="text"
              class="w-full rounded-xl border border-gray-200 bg-white px-4 py-3 text-sm text-gray-900 outline-none transition-all hover:border-indigo-300 focus:border-indigo-500 focus:ring-4 focus:ring-indigo-100 dark:border-gray-700 dark:bg-gray-950 dark:text-white dark:hover:border-indigo-500/60 dark:focus:border-indigo-500 dark:focus:ring-indigo-500/10"
              placeholder="Enter national ID"
            />
            <div v-if="form.errors.national_id" class="mt-2 text-sm text-red-500">
              {{ form.errors.national_id }}
            </div>
          </div>

          <!-- Gender -->
          <div>
            <label class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">
              Gender
            </label>
            <select
              v-model="form.gender"
              class="w-full rounded-xl border border-gray-200 bg-white px-4 py-3 text-sm text-gray-900 outline-none transition-all hover:border-indigo-300 focus:border-indigo-500 focus:ring-4 focus:ring-indigo-100 dark:border-gray-700 dark:bg-gray-950 dark:text-white dark:hover:border-indigo-500/60 dark:focus:border-indigo-500 dark:focus:ring-indigo-500/10"
            >
              <option value="">Select gender</option>
              <option value="male">Male</option>
              <option value="female">Female</option>
            </select>
            <div v-if="form.errors.gender" class="mt-2 text-sm text-red-500">
              {{ form.errors.gender }}
            </div>
          </div>

          <!-- Country -->
          <div>
            <label class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">
              Country
            </label>
            <select
              v-model="form.country_id"
              class="w-full rounded-xl border border-gray-200 bg-white px-4 py-3 text-sm text-gray-900 outline-none transition-all hover:border-indigo-300 focus:border-indigo-500 focus:ring-4 focus:ring-indigo-100 dark:border-gray-700 dark:bg-gray-950 dark:text-white dark:hover:border-indigo-500/60 dark:focus:border-indigo-500 dark:focus:ring-indigo-500/10"
            >
              <option value="">Select country</option>
              <option v-for="country in countries" :key="country.id" :value="country.id">
                {{ country.name }}
              </option>
            </select>
            <div v-if="form.errors.country_id" class="mt-2 text-sm text-red-500">
              {{ form.errors.country_id }}
            </div>
          </div>

          <!-- City -->
          <div>
            <label class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">
              City
            </label>
            <select
              v-model="form.city_id"
              class="w-full rounded-xl border border-gray-200 bg-white px-4 py-3 text-sm text-gray-900 outline-none transition-all hover:border-indigo-300 focus:border-indigo-500 focus:ring-4 focus:ring-indigo-100 dark:border-gray-700 dark:bg-gray-950 dark:text-white dark:hover:border-indigo-500/60 dark:focus:border-indigo-500 dark:focus:ring-indigo-500/10"
              :disabled="!form.country_id"
            >
              <option value="">{{ form.country_id ? 'Select city' : 'Select country first' }}</option>
              <option v-for="city in cities" :key="city.id" :value="city.id">
                {{ city.name }}
              </option>
            </select>
            <div v-if="form.errors.city_id" class="mt-2 text-sm text-red-500">
              {{ form.errors.city_id }}
            </div>
          </div>

          <!-- Image Section -->
          <div class="md:col-span-2">
            <div class="rounded-2xl border border-dashed border-gray-300 bg-gray-50 p-5 dark:border-gray-700 dark:bg-gray-950/50">
              <div class="mb-4">
                <label class="mb-3 block text-sm font-medium text-gray-700 dark:text-gray-300">
                  Current Image
                </label>

                <div v-if="client.image" class="flex items-center gap-4">
                  <img
                    :src="`/storage/${client.image}`"
                    alt="current image"
                    class="h-24 w-24 rounded-2xl object-cover ring-2 ring-white shadow-sm dark:ring-gray-800"
                  />
                  <div>
                    <p class="text-sm font-medium text-gray-800 dark:text-gray-200">
                      Current profile image
                    </p>
                    <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                      Upload a new one to replace it
                    </p>
                  </div>
                </div>

                <div
                  v-else
                  class="flex h-24 items-center justify-center rounded-2xl border border-dashed border-gray-300 bg-white text-sm text-gray-500 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-400"
                >
                  No image uploaded
                </div>
              </div>

              <div>
                <label class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">
                  New Image
                </label>
                <input
                  type="file"
                  @change="handleImageChange"
                  class="block w-full rounded-xl border border-gray-200 bg-white px-4 py-3 text-sm text-gray-700 file:mr-4 file:rounded-lg file:border-0 file:bg-indigo-600 file:px-4 file:py-2 file:text-sm file:font-medium file:text-white hover:file:bg-indigo-500 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 dark:file:bg-indigo-500 dark:hover:file:bg-indigo-400"
                />
                <div v-if="form.errors.image" class="mt-2 text-sm text-red-500">
                  {{ form.errors.image }}
                </div>
              </div>
            </div>
          </div>

          <!-- Active -->
          <div class="md:col-span-2">
            <label class="flex items-center gap-3 rounded-xl border border-gray-200 bg-gray-50 px-4 py-3 transition-all hover:border-indigo-300 dark:border-gray-800 dark:bg-gray-950 dark:hover:border-indigo-500/50">
              <input
                id="is_active"
                v-model="form.is_active"
                type="checkbox"
                class="h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500 dark:border-gray-700 dark:bg-gray-900"
              />
              <span class="text-sm font-medium text-gray-700 dark:text-gray-300">
                Active
              </span>
            </label>
          </div>

          <!-- Actions -->
          <div class="md:col-span-2 flex flex-wrap items-center gap-3 pt-2">
            <button
              type="submit"
              :disabled="form.processing"
              class="inline-flex items-center gap-2 rounded-xl bg-indigo-600 px-6 py-3 text-sm font-semibold text-white shadow-sm transition-all hover:bg-indigo-500 hover:shadow-md active:scale-95 disabled:cursor-not-allowed disabled:opacity-50 dark:bg-indigo-500 dark:hover:bg-indigo-400"
            >
              <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
              </svg>
              {{ form.processing ? 'Updating...' : 'Update Client' }}
            </button>

            <Link
              href="/manager/clients"
              class="inline-flex items-center gap-2 rounded-xl border border-gray-200 bg-white px-6 py-3 text-sm font-semibold text-gray-700 transition-all hover:bg-gray-50 hover:shadow-sm active:scale-95 dark:border-gray-800 dark:bg-gray-900 dark:text-gray-200 dark:hover:bg-gray-800"
            >
              Cancel
            </Link>
          </div>
        </form>
      </div>
    </div>
  </AppLayout>
</template>
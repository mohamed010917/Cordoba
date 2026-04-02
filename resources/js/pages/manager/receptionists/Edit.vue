<script setup>
import AppLayout from '@/layouts/AppLayout.vue'
import { Head, Link, useForm } from '@inertiajs/vue3'

const props = defineProps({
  receptionist: Object,
})

const form = useForm({
  name: props.receptionist.name || '',
  email: props.receptionist.email || '',
  password: '',
  password_confirmation: '',
  phone: props.receptionist.phone || '',
  national_id: props.receptionist.national_id || '',
  gender: props.receptionist.gender || '',
  
  image: null,
})

function submit() {
  form.put(`/manager/receptionists/${props.receptionist.id}`)
}

function handleImageChange(event) {
  const fileInput = event.target
  const selectedFile = fileInput.files[0]
  form.image = selectedFile
}
</script>

<template>
  <Head title="Edit Receptionist" />

  <AppLayout>
    <div class="p-6">
      <div class="mb-6 flex items-center justify-between">
        <div>
          <h1 class="text-2xl font-bold">Edit Receptionist</h1>
          <p class="text-sm text-gray-500">Update receptionist data</p>
        </div>

        <Link
          href="/manager/receptionists"
          class="rounded border px-4 py-2 text-sm hover:bg-gray-50"
        >
          Back
        </Link>
      </div>

      <form @submit.prevent="submit" class="p-6 sm:p-8">
        <!-- Account -->
        <div class="mb-8">
          <h3 class="mb-4 flex items-center gap-2 text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">
            <span class="h-px flex-1 bg-gray-200 dark:bg-gray-600" aria-hidden="true" />
            <span>Account</span>
            <span class="h-px flex-1 bg-gray-200 dark:bg-gray-600" aria-hidden="true" />
          </h3>

          <div class="grid grid-cols-1 gap-5 md:grid-cols-2">
            <div class="space-y-1.5">
              <label for="receptionist-name" class="block text-sm font-medium text-gray-700 dark:text-gray-200">Name</label>
              <input
                id="receptionist-name"
                v-model="form.name"
                type="text"
                autocomplete="name"
                class="w-full rounded-lg border border-gray-300 bg-white px-3 py-2.5 text-gray-900 shadow-sm transition-colors placeholder:text-gray-400 focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 dark:border-gray-600 dark:bg-gray-900/50 dark:text-white dark:placeholder:text-gray-500 dark:focus:border-indigo-400 dark:focus:ring-indigo-400/20"
              />
              <div v-if="form.errors.name" class="text-sm text-red-600 dark:text-red-400">
                {{ form.errors.name }}
              </div>
            </div>

            <div class="space-y-1.5">
              <label for="receptionist-email" class="block text-sm font-medium text-gray-700 dark:text-gray-200">Email</label>
              <input
                id="receptionist-email"
                v-model="form.email"
                type="email"
                autocomplete="email"
                class="w-full rounded-lg border border-gray-300 bg-white px-3 py-2.5 text-gray-900 shadow-sm transition-colors placeholder:text-gray-400 focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 dark:border-gray-600 dark:bg-gray-900/50 dark:text-white dark:placeholder:text-gray-500 dark:focus:border-indigo-400 dark:focus:ring-indigo-400/20"
              />
              <div v-if="form.errors.email" class="text-sm text-red-600 dark:text-red-400">
                {{ form.errors.email }}
              </div>
            </div>

            <div class="space-y-1.5">
              <label for="receptionist-password" class="block text-sm font-medium text-gray-700 dark:text-gray-200">Password</label>
              <input
                id="receptionist-password"
                v-model="form.password"
                type="password"
                autocomplete="new-password"
                class="w-full rounded-lg border border-gray-300 bg-white px-3 py-2.5 text-gray-900 shadow-sm transition-colors focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 dark:border-gray-600 dark:bg-gray-900/50 dark:text-white dark:focus:border-indigo-400 dark:focus:ring-indigo-400/20"
              />
              <div v-if="form.errors.password" class="text-sm text-red-600 dark:text-red-400">
                {{ form.errors.password }}
              </div>
            </div>

            <div class="space-y-1.5">
              <label for="receptionist-password-confirmation" class="block text-sm font-medium text-gray-700 dark:text-gray-200">Confirm password</label>
              <input
                id="receptionist-password-confirmation"
                v-model="form.password_confirmation"
                type="password"
                autocomplete="new-password"
                class="w-full rounded-lg border border-gray-300 bg-white px-3 py-2.5 text-gray-900 shadow-sm transition-colors focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 dark:border-gray-600 dark:bg-gray-900/50 dark:text-white dark:focus:border-indigo-400 dark:focus:ring-indigo-400/20"
              />
            </div>
          </div>
        </div>

        <!-- Profile -->
        <div class="mb-8">
          <h3 class="mb-4 flex items-center gap-2 text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">
            <span class="h-px flex-1 bg-gray-200 dark:bg-gray-600" aria-hidden="true" />
            <span>Profile</span>
            <span class="h-px flex-1 bg-gray-200 dark:bg-gray-600" aria-hidden="true" />
          </h3>

          <div class="grid grid-cols-1 gap-5 md:grid-cols-2">
            <div class="space-y-1.5">
              <label for="receptionist-phone" class="block text-sm font-medium text-gray-700 dark:text-gray-200">Phone</label>
              <input
                id="receptionist-phone"
                v-model="form.phone"
                type="text"
                autocomplete="tel"
                class="w-full rounded-lg border border-gray-300 bg-white px-3 py-2.5 text-gray-900 shadow-sm transition-colors focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 dark:border-gray-600 dark:bg-gray-900/50 dark:text-white dark:focus:border-indigo-400 dark:focus:ring-indigo-400/20"
              />
              <div v-if="form.errors.phone" class="text-sm text-red-600 dark:text-red-400">
                {{ form.errors.phone }}
              </div>
            </div>

            <div class="space-y-1.5">
              <label for="receptionist-national-id" class="block text-sm font-medium text-gray-700 dark:text-gray-200">National ID</label>
              <input
                id="receptionist-national-id"
                v-model="form.national_id"
                type="text"
                class="w-full rounded-lg border border-gray-300 bg-white px-3 py-2.5 text-gray-900 shadow-sm transition-colors focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 dark:border-gray-600 dark:bg-gray-900/50 dark:text-white dark:focus:border-indigo-400 dark:focus:ring-indigo-400/20"
              />
              <div v-if="form.errors.national_id" class="text-sm text-red-600 dark:text-red-400">
                {{ form.errors.national_id }}
              </div>
            </div>

            <div class="space-y-1.5">
              <label for="receptionist-gender" class="block text-sm font-medium text-gray-700 dark:text-gray-200">Gender</label>
              <select
                id="receptionist-gender"
                v-model="form.gender"
                class="w-full rounded-lg border border-gray-300 bg-white px-3 py-2.5 text-gray-900 shadow-sm transition-colors focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 dark:border-gray-600 dark:bg-gray-900/50 dark:text-white dark:focus:border-indigo-400 dark:focus:ring-indigo-400/20"
              >
                <option value="">Select gender</option>
                <option value="male">Male</option>
                <option value="female">Female</option>
              </select>
              <div v-if="form.errors.gender" class="text-sm text-red-600 dark:text-red-400">
                {{ form.errors.gender }}
              </div>
            </div>

        
          </div>
        </div>

        <!-- Image -->
        <div class="mb-8">
          <h3 class="mb-4 flex items-center gap-2 text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">
            <span class="h-px flex-1 bg-gray-200 dark:bg-gray-600" aria-hidden="true" />
            <span>Photo</span>
            <span class="h-px flex-1 bg-gray-200 dark:bg-gray-600" aria-hidden="true" />
          </h3>

          <div class="space-y-1.5">
            <label for="receptionist-image" class="block text-sm font-medium text-gray-700 dark:text-gray-200">Image</label>
            <input
              id="receptionist-image"
              type="file"
              @change="handleImageChange"
              class="block w-full cursor-pointer text-sm text-gray-600 file:mr-4 file:cursor-pointer file:rounded-lg file:border-0 file:bg-indigo-50 file:px-4 file:py-2.5 file:text-sm file:font-medium file:text-indigo-700 hover:file:bg-indigo-100 dark:text-gray-300 dark:file:bg-indigo-950/50 dark:file:text-indigo-300 dark:hover:file:bg-indigo-900/50"
            />
            <div v-if="form.errors.image" class="text-sm text-red-600 dark:text-red-400">
              {{ form.errors.image }}
            </div>
          </div>
        </div>

        <div class="flex flex-col-reverse gap-3 border-t border-gray-100 pt-6 sm:flex-row sm:items-center sm:justify-end sm:gap-4 dark:border-gray-700">
          <Link
            href="/manager/receptionists"
            class="inline-flex items-center justify-center rounded-lg border border-gray-300 bg-white px-5 py-2.5 text-sm font-medium text-gray-700 shadow-sm transition-colors hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-200 dark:hover:bg-gray-700 dark:focus:ring-offset-gray-900"
          >
            Cancel
          </Link>

          <button
            type="submit"
            :disabled="form.processing"
            class="inline-flex items-center justify-center rounded-lg bg-gray-900 px-5 py-2.5 text-sm font-medium text-white shadow-sm transition-colors hover:bg-gray-800 focus:outline-none focus:ring-2 focus:ring-gray-900 focus:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50 dark:bg-indigo-600 dark:hover:bg-indigo-500 dark:focus:ring-indigo-500"
          >
            <svg
              v-if="form.processing"
              class="-ml-1 mr-2 h-4 w-4 animate-spin text-white"
              xmlns="http://www.w3.org/2000/svg"
              fill="none"
              viewBox="0 0 24 24"
              aria-hidden="true"
            >
              <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" />
              <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z" />
            </svg>
            {{ form.processing ? 'Updating…' : 'Update' }}
          </button>
        </div>
      </form>
    </div>
  </AppLayout>
</template>
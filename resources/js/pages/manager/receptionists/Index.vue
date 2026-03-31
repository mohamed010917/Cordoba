<script setup>
import AppLayout from '@/layouts/AppLayout.vue'
import { Head, Link, router, usePage } from '@inertiajs/vue3'
import Swal from 'sweetalert2'
import { reactive } from 'vue'

const props = defineProps({
  receptionists: Object,
  filters: Object,
})
const page = usePage()

async function deleteReceptionist(id) {
  const result = await Swal.fire({
    title: 'Delete Receptionist?',
    text: 'This action cannot be undone.',
    icon: 'warning',
    showCancelButton: true,
    confirmButtonText: 'Yes, delete',
    cancelButtonText: 'Cancel',
  })
  if (!result.isConfirmed) return
  router.delete(`/manager/receptionists/${id}`)
}

async function toggleBan(receptionist) {
  const isBanned = receptionist.is_banned
  const result = await Swal.fire({
    title: isBanned ? 'Unban Receptionist?' : 'Ban Receptionist?',
    text: isBanned ? 'This will restore their access.' : 'This will revoke their access.',
    icon: 'warning',
    showCancelButton: true,
    confirmButtonText: 'Yes',
    cancelButtonText: 'Cancel',
  })
  if (!result.isConfirmed) return
  const url = isBanned
    ? `/manager/receptionists/${receptionist.id}/unban`
    : `/manager/receptionists/${receptionist.id}/ban`
  router.patch(url)
}


const filterForm = reactive({
  search: props.filters?.search || '',
  sort: props.filters?.sort || 'created_at',
  direction: props.filters?.direction || 'desc',
})

function applyFilters() {
  router.get('/manager/receptionists', filterForm, {
    preserveState: true,
    replace: true,
  })
}

function resetFilters() {
  filterForm.search = ''
  filterForm.sort = 'created_at'
  filterForm.direction = 'desc'
  filterForm.date_from = ''
  filterForm.date_to = ''

  applyFilters()
}

</script>

<template>
  <Head title="Receptionists" />

  <AppLayout>
    <div class="min-h-screen bg-gray-50 px-6 py-8 dark:bg-gray-950">

      <!-- Header -->
      <div class="mb-8 flex items-start justify-between">
        <div class="space-y-1">
          <div class="flex items-center gap-2.5">
            <div class="h-7 w-1 rounded-full bg-indigo-500"></div>
            <h1 class="text-2xl font-bold tracking-tight text-gray-900 dark:text-white">
              Receptionists
            </h1>
          </div>
          <p class="pl-3.5 text-sm text-gray-500 dark:text-gray-400">
            {{ receptionists.data.length }}
            staff member{{ receptionists.data.length !== 1 ? 's' : '' }} listed
          </p>
        </div>

        <Link
          href="/manager/receptionists/create"
          class="inline-flex items-center gap-2 rounded-xl bg-indigo-600 px-5 py-2.5 text-sm font-semibold text-white shadow-sm transition-all hover:bg-indigo-500 hover:shadow-md active:scale-95 dark:bg-indigo-500 dark:hover:bg-indigo-400"
        >
          <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
          </svg>
          Add Receptionist
        </Link>
      </div>

      <div class="mb-4 flex flex-col gap-3 lg:flex-row lg:items-end">
  <div class="w-full lg:w-72">
    <label class="mb-1 block text-sm font-medium">Search</label>
    <input
      v-model="filterForm.search"
      type="text"
      placeholder="Name, email, phone, national id..."
      class="w-full rounded border px-3 py-2"
      @keyup.enter="applyFilters"
    />
  </div>

  <div>
    <label class="mb-1 block text-sm font-medium">From Date</label>
    <input
      v-model="filterForm.date_from"
      type="date"
      class="rounded border px-3 py-2"
      @change="applyFilters"
    />
  </div>

  <div>
    <label class="mb-1 block text-sm font-medium">To Date</label>
    <input
      v-model="filterForm.date_to"
      type="date"
      class="rounded border px-3 py-2"
      @change="applyFilters"
    />
  </div>

  <div>
    <label class="mb-1 block text-sm font-medium">Sort By</label>
    <select
      v-model="filterForm.sort"
      class="rounded border px-3 py-2"
      @change="applyFilters"
    ><option value="created_at">Created At</option>
    <option value="name">Name</option>
      <option value="email">Email</option>
    </select>
  </div>

  <div>
    <label class="mb-1 block text-sm font-medium">Direction</label>
    <select
      v-model="filterForm.direction"
      class="rounded border px-3 py-2"
      @change="applyFilters"
    >
      <option value="desc">Descending</option>
      <option value="asc">Ascending</option>
    </select>
  </div>

  <div class="flex gap-2">
    <button
      @click="applyFilters"
      class="rounded bg-black px-4 py-2 text-white hover:bg-gray-800"
    >
      Search
    </button>

    <button
      @click="resetFilters"
      class="rounded border px-4 py-2 hover:bg-gray-50"
    >
      Reset
    </button>
  </div>
</div>
      <!-- Table Card -->
      <div class="overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-sm dark:border-gray-800 dark:bg-gray-900">
        <div class="overflow-x-auto">
          <table class="min-w-full">

            <thead>
              <tr class="border-b border-gray-100 bg-gray-50/80 dark:border-gray-800 dark:bg-gray-800/60">
                <th class="px-5 py-4 text-left text-xs font-semibold uppercase tracking-widest text-gray-400 dark:text-gray-500">Staff</th>
                <th class="px-5 py-4 text-left text-xs font-semibold uppercase tracking-widest text-gray-400 dark:text-gray-500">Contact</th>
                <th class="px-5 py-4 text-left text-xs font-semibold uppercase tracking-widest text-gray-400 dark:text-gray-500">Phone</th>
                <th class="px-5 py-4 text-left text-xs font-semibold uppercase tracking-widest text-gray-400 dark:text-gray-500">Joined</th>
                <th class="px-5 py-4 text-left text-xs font-semibold uppercase tracking-widest text-gray-400 dark:text-gray-500">Manager</th>
                <th class="px-5 py-4 text-left text-xs font-semibold uppercase tracking-widest text-gray-400 dark:text-gray-500">Status</th>
                <th class="px-5 py-4 text-right text-xs font-semibold uppercase tracking-widest text-gray-400 dark:text-gray-500">Actions</th>
              </tr>
            </thead>

            <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
              <tr
                v-for="receptionist in receptionists.data"
                :key="receptionist.id"
                class="group transition-colors hover:bg-indigo-50/40 dark:hover:bg-indigo-950/30"
              >
                <!-- Avatar + Name -->
                <td class="px-5 py-4">
                  <div class="flex items-center gap-3">
                    <div class="relative flex-shrink-0">
                      <img
                        v-if="receptionist.image"
                        :src="`/storage/${receptionist.image}`"
                        alt="user image"
                        class="h-10 w-10 rounded-full object-cover ring-2 ring-white shadow-sm dark:ring-gray-800"
                      />
                      <div
                        v-else
                        class="flex h-10 w-10 items-center justify-center rounded-full bg-gradient-to-br from-indigo-100 to-purple-100 text-xs font-bold text-indigo-600 ring-2 ring-white shadow-sm dark:from-indigo-900/60 dark:to-purple-900/60 dark:text-indigo-400 dark:ring-gray-800"
                      >
                        {{ receptionist.name?.charAt(0)?.toUpperCase() || '?' }}
                      </div>
                      <span
                        v-if="!receptionist.is_banned"
                        class="absolute bottom-0 right-0 h-2.5 w-2.5 rounded-full bg-emerald-400 ring-2 ring-white dark:ring-gray-900"
                      ></span>
                    </div>
                    <div>
                      <p class="text-sm font-semibold text-gray-900 dark:text-white">
                        {{ receptionist.name }}
                      </p>
                    </div>
                  </div>
                </td>

                <!-- Email -->
                <td class="px-5 py-4">
                  <span class="text-sm text-gray-600 dark:text-gray-400">{{ receptionist.email }}</span>
                </td>

                <!-- Phone -->
                <td class="px-5 py-4">
                  <span class="text-sm text-gray-600 dark:text-gray-400">{{ receptionist.phone || '—' }}</span>
                </td>

                <!-- Created At -->
                <td class="px-5 py-4">
                  <span class="text-sm text-gray-500 dark:text-gray-500">{{ receptionist.created_at }}</span>
                </td>

                <!-- Manager -->
                <td class="px-5 py-4">
                  <div v-if="receptionist.manager_name" class="flex items-center gap-1.5">
                    <div class="h-1.5 w-1.5 rounded-full bg-indigo-400 dark:bg-indigo-500"></div>
                    <span class="text-sm text-gray-700 dark:text-gray-300">{{ receptionist.manager_name }}</span>
                  </div>
                  <span v-else class="text-sm text-gray-400 dark:text-gray-600">—</span>
                </td>

                <!-- Status -->
                <td class="px-5 py-4">
                  <span
                    v-if="receptionist.is_banned"
                    class="inline-flex items-center gap-1.5 rounded-full bg-red-50 px-3 py-1 text-xs font-semibold text-red-600 ring-1 ring-inset ring-red-100 dark:bg-red-950/40 dark:text-red-400 dark:ring-red-900/50"
                  >
                    <span class="h-1.5 w-1.5 rounded-full bg-red-400 dark:bg-red-500"></span>
                    Banned
                  </span>
                  <span
                    v-else
                    class="inline-flex items-center gap-1.5 rounded-full bg-emerald-50 px-3 py-1 text-xs font-semibold text-emerald-700 ring-1 ring-inset ring-emerald-100 dark:bg-emerald-950/40 dark:text-emerald-400 dark:ring-emerald-900/50"
                  >
                    <span class="h-1.5 w-1.5 rounded-full bg-emerald-400 dark:bg-emerald-500"></span>
                    Active
                  </span>
                </td>

                <!-- Actions -->
                <td class="px-5 py-4">
                  <div class="flex items-center justify-end gap-2">
                    <Link
                      v-if="receptionist.can_edit"
                      :href="`/manager/receptionists/${receptionist.id}/edit`"
                      class="inline-flex items-center gap-1.5 rounded-lg border border-indigo-200 bg-indigo-50 px-3 py-1.5 text-xs font-semibold text-indigo-700 transition-all hover:bg-indigo-100 hover:border-indigo-300 dark:border-indigo-800 dark:bg-indigo-950/50 dark:text-indigo-400 dark:hover:bg-indigo-900/60 dark:hover:border-indigo-700"
                    >
                      <svg class="h-3 w-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                      </svg>
                      Edit
                    </Link>

                    <button
                      v-if="receptionist.can_delete"
                      @click="deleteReceptionist(receptionist.id)"
                      class="inline-flex items-center gap-1.5 rounded-lg border border-red-200 bg-red-50 px-3 py-1.5 text-xs font-semibold text-red-700 transition-all hover:bg-red-100 hover:border-red-300 dark:border-red-900 dark:bg-red-950/50 dark:text-red-400 dark:hover:bg-red-900/60 dark:hover:border-red-800"
                    >
                      <svg class="h-3 w-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                      </svg>
                      Delete
                    </button>

                    <button
                      @click="toggleBan(receptionist)"
                      class="inline-flex items-center gap-1.5 rounded-lg border px-3 py-1.5 text-xs font-semibold transition-all"
                      :class="receptionist.is_banned
                        ? 'border-emerald-200 bg-emerald-50 text-emerald-700 hover:bg-emerald-100 hover:border-emerald-300 dark:border-emerald-900 dark:bg-emerald-950/50 dark:text-emerald-400 dark:hover:bg-emerald-900/60 dark:hover:border-emerald-800'
                        : 'border-amber-200 bg-amber-50 text-amber-700 hover:bg-amber-100 hover:border-amber-300 dark:border-amber-900 dark:bg-amber-950/50 dark:text-amber-400 dark:hover:bg-amber-900/60 dark:hover:border-amber-800'"
                    >
                      <svg class="h-3 w-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path
                          v-if="receptionist.is_banned"
                          stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"
                        />
                        <path
                          v-else
                          stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"
                        />
                      </svg>
                      {{ receptionist.is_banned ? 'Unban' : 'Ban' }}
                    </button>
                  </div>
                </td>
              </tr>

              <!-- Empty State -->
              <tr v-if="!receptionists.data.length">
                <td colspan="7" class="px-5 py-20 text-center">
                  <div class="flex flex-col items-center gap-3">
                    <div class="flex h-14 w-14 items-center justify-center rounded-2xl bg-gray-100 dark:bg-gray-800">
                      <svg class="h-7 w-7 text-gray-400 dark:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z" />
                      </svg>
                    </div>
                    <div>
                      <p class="text-sm font-semibold text-gray-700 dark:text-gray-300">No receptionists found</p>
                      <p class="mt-0.5 text-xs text-gray-400 dark:text-gray-600">Add your first receptionist to get started</p>
                    </div>
                  </div>
                </td>
              </tr>
            </tbody>
          </table>
        </div>

        <!-- Pagination -->
        <div
          v-if="receptionists.links?.length"
          class="flex items-center justify-between border-t border-gray-100 bg-gray-50/50 px-5 py-3 dark:border-gray-800 dark:bg-gray-800/40"
        >
          <p class="text-xs text-gray-400 dark:text-gray-500">
            Showing {{ receptionists.data.length }} result{{ receptionists.data.length !== 1 ? 's' : '' }}
          </p>
          <div class="flex items-center gap-1">
            <template v-for="(link, index) in receptionists.links" :key="index">
              <Link
                v-if="link.url"
                :href="link.url"
                class="flex h-8 min-w-8 items-center justify-center rounded-lg px-2.5 text-xs font-medium transition-all"
                :class="link.active
                  ? 'bg-indigo-600 text-white shadow-sm dark:bg-indigo-500'
                  : 'text-gray-600 hover:bg-gray-100 dark:text-gray-400 dark:hover:bg-gray-800'"
                v-html="link.label"
              />
              <span
                v-else
                class="flex h-8 min-w-8 items-center justify-center rounded-lg px-2.5 text-xs text-gray-300 dark:text-gray-600"
                v-html="link.label"
              />
            </template>
          </div>
        </div>
      </div>

    </div>
  </AppLayout>
</template>
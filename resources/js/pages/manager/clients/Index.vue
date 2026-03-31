<script setup>
import AppLayout from '@/layouts/AppLayout.vue'
import { Head, Link, router } from '@inertiajs/vue3'
import Swal from 'sweetalert2'
import { reactive } from 'vue'

const props = defineProps({
  clients: Object,
  filters: Object,
})

async function deleteClient(id) {
  const result = await Swal.fire({
    title: 'Delete Client?',
    text: 'This action cannot be undone.',
    icon: 'warning',
    showCancelButton: true,
    confirmButtonText: 'Yes, delete',
    cancelButtonText: 'Cancel',
  })

  if (!result.isConfirmed) return

  router.delete(`/manager/clients/${id}`, {
    onSuccess: () => {
      Swal.fire({
        title: 'Deleted!',
        text: 'Client has been deleted successfully.',
        icon: 'success',
        timer: 1500,
        showConfirmButton: false,
      })
    },
  })
}

async function approveClient(id) {
  const result = await Swal.fire({
    title: 'Approve client?',
    text: 'This client will be approved and can use the system.',
    icon: 'question',
    showCancelButton: true,
    confirmButtonText: 'Yes, approve',
    cancelButtonText: 'Cancel',
    reverseButtons: true,
  })

  if (!result.isConfirmed) return

  router.patch(`/manager/clients/${id}/approve`, {
    onSuccess: () => {
      Swal.fire({
        title: 'Approved!',
        text: 'Client has been approved successfully.',
        icon: 'success',
        timer: 1500,
        showConfirmButton: false,
      })
    },
  })
}

const filterForm = reactive({
  search: props.filters?.search ?? '',
  sort: props.filters?.sort ?? 'latest',
  approval_status: props.filters?.approval_status ?? 'all',
  date_from: props.filters?.date_from ?? '',
  date_to: props.filters?.date_to ?? '',
})
function applyFilters() {
  router.get('/manager/clients', filterForm, {
    preserveState: true,
    replace: true,
  })
}
</script>

<template>
  <Head title="Clients" />

  <AppLayout>
    <div class="min-h-screen bg-gray-50 px-6 py-8 dark:bg-gray-950">
      <!-- Header -->
      <div class="mb-8 flex items-start justify-between">
        <div class="space-y-1">
          <div class="flex items-center gap-2.5">
            <div class="h-7 w-1 rounded-full bg-indigo-500"></div>
            <h1 class="text-2xl font-bold tracking-tight text-gray-900 dark:text-white">
              Clients
            </h1>
          </div>
          <p class="pl-3.5 text-sm text-gray-500 dark:text-gray-400">
            {{ clients.data.length }}
            client{{ clients.data.length !== 1 ? 's' : '' }} listed
          </p>
        </div>

        <Link
          href="/manager/clients/create"
          class="inline-flex items-center gap-2 rounded-xl bg-indigo-600 px-5 py-2.5 text-sm font-semibold text-white shadow-sm transition-all hover:bg-indigo-500 hover:shadow-md active:scale-95 dark:bg-indigo-500 dark:hover:bg-indigo-400"
        >
          <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
          </svg>
          Add Client
        </Link>
      </div>

      <div class="mb-4 flex flex-col gap-3 md:flex-row md:items-center">
  <input
    v-model="filterForm.search"
    type="text"
    placeholder="Search by name, email, phone..."
    class="w-full rounded border px-3 py-2 md:w-80"
    @keyup.enter="applyFilters"
  />

  <select
    v-model="filterForm.sort"
    class="rounded border px-3 py-2"
    @change="applyFilters"
  >
    <option value="created_at">Created At</option>
    <option value="name">Name</option>
    <option value="email">Email</option>
    <option value="approved_at">Approved At</option>
  </select>

  <select
    v-model="filterForm.direction"
    class="rounded border px-3 py-2"
    @change="applyFilters"
  >
    <option value="desc">Descending</option>
    <option value="asc">Ascending</option>
  </select>

  <select v-model="filterForm.approval_status" @change="applyFilters">
  <option value="all">All Clients</option>
  <option value="approved">Approved Clients</option>
  <option value="not_approved">Not Approved Clients</option>
</select>

<input
  type="date"
  v-model="filterForm.date_from"
  @change="applyFilters"
/>

<input
  type="date"
  v-model="filterForm.date_to"
  @change="applyFilters"
/>

  <button
    @click="applyFilters"
    class="rounded bg-black px-4 py-2 text-white hover:bg-gray-800"
  >
    Search
  </button>
</div>

      <!-- Table Card -->
      <div class="overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-sm dark:border-gray-800 dark:bg-gray-900">
        <div class="overflow-x-auto">
          <table class="min-w-full">
            <thead>
              <tr class="border-b border-gray-100 bg-gray-50/80 dark:border-gray-800 dark:bg-gray-800/60">
                <th class="px-5 py-4 text-left text-xs font-semibold uppercase tracking-widest text-gray-400 dark:text-gray-500">
                  Client
                </th>
                <th class="px-5 py-4 text-left text-xs font-semibold uppercase tracking-widest text-gray-400 dark:text-gray-500">
                  Contact
                </th>
                <th class="px-5 py-4 text-left text-xs font-semibold uppercase tracking-widest text-gray-400 dark:text-gray-500">
                  Phone
                </th>
                <th class="px-5 py-4 text-left text-xs font-semibold uppercase tracking-widest text-gray-400 dark:text-gray-500">
                  Gender
                </th>
                <th class="px-5 py-4 text-left text-xs font-semibold uppercase tracking-widest text-gray-400 dark:text-gray-500">
                  Country
                </th>
                <th class="px-5 py-4 text-left text-xs font-semibold uppercase tracking-widest text-gray-400 dark:text-gray-500">
                  Status
                </th>
                <th class="px-5 py-4 text-left text-xs font-semibold uppercase tracking-widest text-gray-400 dark:text-gray-500">
                  Approved By
                </th>
                <th class="px-5 py-4 text-left text-xs font-semibold uppercase tracking-widest text-gray-400 dark:text-gray-500">
                  Created At
                </th>
                <th class="px-5 py-4 text-right text-xs font-semibold uppercase tracking-widest text-gray-400 dark:text-gray-500">
                  Actions
                </th>
              </tr>
            </thead>

            <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
              <tr
                v-for="client in clients.data"
                :key="client.id"
                class="group transition-colors hover:bg-indigo-50/40 dark:hover:bg-indigo-950/30"
              >
                <!-- Avatar + Name -->
                <td class="px-5 py-4">
                  <div class="flex items-center gap-3">
                    <div class="relative flex-shrink-0">
                      <img
                        v-if="client.image"
                        :src="`/storage/${client.image}`"
                        alt="client image"
                        class="h-10 w-10 rounded-full object-cover ring-2 ring-white shadow-sm dark:ring-gray-800"
                      />
                      <div
                        v-else
                        class="flex h-10 w-10 items-center justify-center rounded-full bg-gradient-to-br from-indigo-100 to-purple-100 text-xs font-bold text-indigo-600 ring-2 ring-white shadow-sm dark:from-indigo-900/60 dark:to-purple-900/60 dark:text-indigo-400 dark:ring-gray-800"
                      >
                        {{ client.name?.charAt(0)?.toUpperCase() || '?' }}
                      </div>
                      <span
                        v-if="client.approved_at"
                        class="absolute bottom-0 right-0 h-2.5 w-2.5 rounded-full bg-emerald-400 ring-2 ring-white dark:ring-gray-900"
                      ></span>
                      <span
                        v-else
                        class="absolute bottom-0 right-0 h-2.5 w-2.5 rounded-full bg-amber-400 ring-2 ring-white dark:ring-gray-900"
                      ></span>
                    </div>

                    <div>
                      <p class="text-sm font-semibold text-gray-900 dark:text-white">
                        {{ client.name }}
                      </p>
                    </div>
                  </div>
                </td>

                <!-- Email -->
                <td class="px-5 py-4">
                  <span class="text-sm text-gray-600 dark:text-gray-400">
                    {{ client.email }}
                  </span>
                </td>

                <!-- Phone -->
                <td class="px-5 py-4">
                  <span class="text-sm text-gray-600 dark:text-gray-400">
                    {{ client.phone || '—' }}
                  </span>
                </td>

                <!-- Gender -->
                <td class="px-5 py-4">
                  <span class="text-sm text-gray-600 capitalize dark:text-gray-400">
                    {{ client.gender || '—' }}
                  </span>
                </td>

                <!-- Country -->
                <td class="px-5 py-4">
                  <span class="text-sm text-gray-700 dark:text-gray-300">
                    {{ client.country || '—' }}
                  </span>
                </td>

                <!-- Status -->
                <td class="px-5 py-4">
                  <span
                    v-if="client.approved_at"
                    class="inline-flex items-center gap-1.5 rounded-full bg-emerald-50 px-3 py-1 text-xs font-semibold text-emerald-700 ring-1 ring-inset ring-emerald-100 dark:bg-emerald-950/40 dark:text-emerald-400 dark:ring-emerald-900/50"
                  >
                    <span class="h-1.5 w-1.5 rounded-full bg-emerald-400 dark:bg-emerald-500"></span>
                    Approved
                  </span>

                  <span
                    v-else
                    class="inline-flex items-center gap-1.5 rounded-full bg-amber-50 px-3 py-1 text-xs font-semibold text-amber-700 ring-1 ring-inset ring-amber-100 dark:bg-amber-950/40 dark:text-amber-400 dark:ring-amber-900/50"
                  >
                    <span class="h-1.5 w-1.5 rounded-full bg-amber-400 dark:bg-amber-500"></span>
                    Pending
                  </span>
                </td>

                <!-- Approved By -->
                <td class="px-5 py-4">
                  <div v-if="client.approved_by" class="flex items-center gap-1.5">
                    <div class="h-1.5 w-1.5 rounded-full bg-indigo-400 dark:bg-indigo-500"></div>
                    <span class="text-sm text-gray-700 dark:text-gray-300">
                      {{ client.approved_by }}
                    </span>
                  </div>
                  <span v-else class="text-sm text-gray-400 dark:text-gray-600">—</span>
                </td>

                <!-- Created At -->
                <td class="px-5 py-4">
                  <span class="text-sm text-gray-500 dark:text-gray-500">
                    {{ client.created_at || '—' }}
                  </span>
                </td>

                <!-- Actions -->
                <td class="px-5 py-4">
                  <div class="flex items-center justify-end gap-2">
                    <button
                      v-if="!client.approved_at"
                      @click="approveClient(client.id)"
                      class="inline-flex items-center gap-1.5 rounded-lg border border-green-200 bg-green-50 px-3 py-1.5 text-xs font-semibold text-green-700 transition-all hover:bg-green-100 hover:border-green-300 dark:border-green-800 dark:bg-green-950/50 dark:text-green-400 dark:hover:bg-green-900/60 dark:hover:border-green-700"                    >
                      Approve
                    </button>
                    <Link
                      :href="`/manager/clients/${client.id}/edit`"
                      class="inline-flex items-center gap-1.5 rounded-lg border border-indigo-200 bg-indigo-50 px-3 py-1.5 text-xs font-semibold text-indigo-700 transition-all hover:bg-indigo-100 hover:border-indigo-300 dark:border-indigo-800 dark:bg-indigo-950/50 dark:text-indigo-400 dark:hover:bg-indigo-900/60 dark:hover:border-indigo-700"
                    >
                      <svg class="h-3 w-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                      </svg>
                      Edit
                    </Link>

                    <button
                      @click="deleteClient(client.id)"
                      class="inline-flex items-center gap-1.5 rounded-lg border border-red-200 bg-red-50 px-3 py-1.5 text-xs font-semibold text-red-700 transition-all hover:bg-red-100 hover:border-red-300 dark:border-red-900 dark:bg-red-950/50 dark:text-red-400 dark:hover:bg-red-900/60 dark:hover:border-red-800"
                    >
                      <svg class="h-3 w-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                      </svg>
                      Delete
                    </button>
                  </div>
                </td>
              </tr>

              <!-- Empty State -->
              <tr v-if="!clients.data.length">
                <td colspan="9" class="px-5 py-20 text-center">
                  <div class="flex flex-col items-center gap-3">
                    <div class="flex h-14 w-14 items-center justify-center rounded-2xl bg-gray-100 dark:bg-gray-800">
                      <svg class="h-7 w-7 text-gray-400 dark:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z" />
                      </svg>
                    </div>
                    <div>
                      <p class="text-sm font-semibold text-gray-700 dark:text-gray-300">No clients found</p>
                      <p class="mt-0.5 text-xs text-gray-400 dark:text-gray-600">Add your first client to get started</p>
                    </div>
                  </div>
                </td>
              </tr>
            </tbody>
          </table>
        </div>

        <!-- Pagination -->
        <div
          v-if="clients.links?.length"
          class="flex items-center justify-between border-t border-gray-100 bg-gray-50/50 px-5 py-3 dark:border-gray-800 dark:bg-gray-800/40"
        >
          <p class="text-xs text-gray-400 dark:text-gray-500">
            Showing {{ clients.data.length }} result{{ clients.data.length !== 1 ? 's' : '' }}
          </p>

          <div class="flex items-center gap-1">
            <template v-for="(link, index) in clients.links" :key="index">
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
<script setup>
import AppLayout from '@/layouts/AppLayout.vue'
import { Head, Link, router } from '@inertiajs/vue3'
import Swal from 'sweetalert2'
import { reactive, watch } from 'vue'

const props = defineProps({
  clients: Object,
  filters: Object,
  summary: {
    type: Object,
    default: () => ({ total: 0, approved: 0, pending: 0 }),
  },
})

const filterForm = reactive({
  search: props.filters?.search ?? '',
  sort: props.filters?.sort ?? 'latest',
  approval_status: props.filters?.approval_status ?? 'all',
  date_from: props.filters?.date_from ?? '',
  date_to: props.filters?.date_to ?? '',
})

watch(
  () => props.filters,
  (f) => {
    if (!f) {
      return
    }
    filterForm.search = f.search ?? ''
    filterForm.sort = f.sort ?? 'latest'
    filterForm.approval_status = f.approval_status ?? 'all'
    filterForm.date_from = f.date_from ?? ''
    filterForm.date_to = f.date_to ?? ''
  },
  { deep: true },
)

function applyFilters() {
  router.get('/manager/clients', filterForm, {
    preserveState: true,
    replace: true,
  })
}

async function deleteClient(id) {
  const result = await Swal.fire({
    title: 'Delete Client?',
    text: 'This action cannot be undone.',
    icon: 'warning',
    showCancelButton: true,
    confirmButtonText: 'Yes, delete',
    cancelButtonText: 'Cancel',
  })

  if (!result.isConfirmed) {
    return
  }

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

  if (!result.isConfirmed) {
    return
  }

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
</script>

<template>
  <Head title="Clients" />

  <AppLayout>
    <div class="min-h-screen bg-gradient-to-b from-gray-50 to-white px-4 py-8 dark:from-gray-950 dark:to-gray-900 sm:px-6 lg:px-8">
      <div class="mx-auto max-w-7xl space-y-8">
        <!-- Header -->
        <div class="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
          <div class="space-y-1">
            <div class="flex items-center gap-2.5">
              <div class="h-7 w-1 rounded-full bg-indigo-500"></div>
              <h1 class="text-2xl font-bold tracking-tight text-gray-900 dark:text-white">
                Clients
              </h1>
            </div>
            <p class="pl-3.5 text-sm text-gray-500 dark:text-gray-400">
              Manage guest accounts, approvals, and contact details.
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

        <!-- Stats -->
        <div class="grid gap-4 sm:grid-cols-3">
          <div
            class="rounded-2xl border border-gray-200/80 bg-white p-5 shadow-sm transition hover:-translate-y-0.5 hover:shadow-md dark:border-gray-800 dark:bg-gray-900/80"
          >
            <p class="text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">Total</p>
            <p class="mt-1 text-3xl font-bold text-gray-900 dark:text-white">{{ summary.total }}</p>
          </div>
          <div
            class="rounded-2xl border border-emerald-200/60 bg-emerald-50/50 p-5 shadow-sm transition hover:-translate-y-0.5 hover:shadow-md dark:border-emerald-900/40 dark:bg-emerald-950/30"
          >
            <p class="text-xs font-semibold uppercase tracking-wider text-emerald-700 dark:text-emerald-400">Approved</p>
            <p class="mt-1 text-3xl font-bold text-emerald-800 dark:text-emerald-200">{{ summary.approved }}</p>
          </div>
          <div
            class="rounded-2xl border border-amber-200/60 bg-amber-50/50 p-5 shadow-sm transition hover:-translate-y-0.5 hover:shadow-md dark:border-amber-900/40 dark:bg-amber-950/30"
          >
            <p class="text-xs font-semibold uppercase tracking-wider text-amber-700 dark:text-amber-400">Pending</p>
            <p class="mt-1 text-3xl font-bold text-amber-800 dark:text-amber-200">{{ summary.pending }}</p>
          </div>
        </div>

        <!-- Filters -->
        <div
          class="rounded-2xl border border-gray-200/80 bg-white p-4 shadow-sm dark:border-gray-800 dark:bg-gray-900/80"
        >
          <div class="grid gap-3 md:grid-cols-2 lg:grid-cols-12 lg:items-end">
            <div class="lg:col-span-4">
              <label class="mb-1 block text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">Search</label>
              <input
                v-model="filterForm.search"
                type="text"
                placeholder="Name, email, phone, national ID…"
                class="w-full rounded-xl border border-gray-200 bg-white px-3 py-2.5 text-sm text-gray-900 outline-none ring-indigo-500/30 transition focus:border-indigo-500 focus:ring-4 dark:border-gray-700 dark:bg-gray-950 dark:text-white"
                @keyup.enter="applyFilters"
              />
            </div>
            <div class="lg:col-span-2">
              <label class="mb-1 block text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">Sort</label>
              <select
                v-model="filterForm.sort"
                class="w-full rounded-xl border border-gray-200 bg-white px-3 py-2.5 text-sm text-gray-900 dark:border-gray-700 dark:bg-gray-950 dark:text-white"
                @change="applyFilters"
              >
                <option value="latest">Newest first</option>
                <option value="oldest">Oldest first</option>
                <option value="name_asc">Name A–Z</option>
                <option value="name_desc">Name Z–A</option>
              </select>
            </div>
            <div class="lg:col-span-2">
              <label class="mb-1 block text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">Approval</label>
              <select
                v-model="filterForm.approval_status"
                class="w-full rounded-xl border border-gray-200 bg-white px-3 py-2.5 text-sm text-gray-900 dark:border-gray-700 dark:bg-gray-950 dark:text-white"
                @change="applyFilters"
              >
                <option value="all">All</option>
                <option value="approved">Approved only</option>
                <option value="not_approved">Pending only</option>
              </select>
            </div>
            <div class="lg:col-span-2">
              <label class="mb-1 block text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">From</label>
              <input
                v-model="filterForm.date_from"
                type="date"
                class="w-full rounded-xl border border-gray-200 bg-white px-3 py-2.5 text-sm text-gray-900 dark:border-gray-700 dark:bg-gray-950 dark:text-white"
                @change="applyFilters"
              />
            </div>
            <div class="lg:col-span-2">
              <label class="mb-1 block text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">To</label>
              <input
                v-model="filterForm.date_to"
                type="date"
                class="w-full rounded-xl border border-gray-200 bg-white px-3 py-2.5 text-sm text-gray-900 dark:border-gray-700 dark:bg-gray-950 dark:text-white"
                @change="applyFilters"
              />
            </div>
          </div>
          <div class="mt-3 flex flex-wrap gap-2">
            <button
              type="button"
              class="rounded-xl bg-gray-900 px-4 py-2 text-sm font-semibold text-white transition hover:bg-gray-800 dark:bg-white dark:text-gray-900 dark:hover:bg-gray-200"
              @click="applyFilters"
            >
              Apply filters
            </button>
            <button
              type="button"
              class="rounded-xl border border-gray-200 px-4 py-2 text-sm font-semibold text-gray-700 transition hover:bg-gray-50 dark:border-gray-700 dark:text-gray-300 dark:hover:bg-gray-800"
              @click="
                filterForm.search = '';
                filterForm.sort = 'latest';
                filterForm.approval_status = 'all';
                filterForm.date_from = '';
                filterForm.date_to = '';
                applyFilters();
              "
            >
              Reset
            </button>
          </div>
        </div>

        <!-- Table Card -->
        <div
          class="overflow-hidden rounded-2xl border border-gray-200/80 bg-white shadow-sm dark:border-gray-800 dark:bg-gray-900/80"
        >
          <div class="overflow-x-auto">
            <table class="min-w-full">
              <thead>
                <tr
                  class="border-b border-gray-100 bg-gray-50/80 dark:border-gray-800 dark:bg-gray-800/60"
                >
                  <th
                    class="px-5 py-4 text-left text-xs font-semibold uppercase tracking-widest text-gray-400 dark:text-gray-500"
                  >
                    Client
                  </th>
                  <th
                    class="px-5 py-4 text-left text-xs font-semibold uppercase tracking-widest text-gray-400 dark:text-gray-500"
                  >
                    Contact
                  </th>
                  <th
                    class="px-5 py-4 text-left text-xs font-semibold uppercase tracking-widest text-gray-400 dark:text-gray-500"
                  >
                    Phone
                  </th>
                  <th
                    class="px-5 py-4 text-left text-xs font-semibold uppercase tracking-widest text-gray-400 dark:text-gray-500"
                  >
                    Gender
                  </th>
                  <th
                    class="px-5 py-4 text-left text-xs font-semibold uppercase tracking-widest text-gray-400 dark:text-gray-500"
                  >
                    Country
                  </th>
                  <th
                    class="px-5 py-4 text-left text-xs font-semibold uppercase tracking-widest text-gray-400 dark:text-gray-500"
                  >
                    City
                  </th>
                  <th
                    class="px-5 py-4 text-left text-xs font-semibold uppercase tracking-widest text-gray-400 dark:text-gray-500"
                  >
                    Status
                  </th>
                  <th
                    class="px-5 py-4 text-left text-xs font-semibold uppercase tracking-widest text-gray-400 dark:text-gray-500"
                  >
                    Approved by
                  </th>
                  <th
                    class="px-5 py-4 text-left text-xs font-semibold uppercase tracking-widest text-gray-400 dark:text-gray-500"
                  >
                    Created
                  </th>
                  <th
                    class="px-5 py-4 text-right text-xs font-semibold uppercase tracking-widest text-gray-400 dark:text-gray-500"
                  >
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
                  <td class="px-5 py-4">
                    <div class="flex items-center gap-3">
                      <div class="relative flex-shrink-0">
                        <img
                          v-if="client.image"
                          :src="`/storage/${client.image}`"
                          alt=""
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
                        <p
                          v-if="!client.is_active"
                          class="mt-0.5 text-[11px] font-semibold uppercase tracking-wide text-red-500"
                        >
                          Inactive
                        </p>
                      </div>
                    </div>
                  </td>
                  <td class="px-5 py-4">
                    <span class="text-sm text-gray-600 dark:text-gray-400">{{ client.email }}</span>
                  </td>
                  <td class="px-5 py-4">
                    <span class="text-sm text-gray-600 dark:text-gray-400">{{ client.phone || '—' }}</span>
                  </td>
                  <td class="px-5 py-4">
                    <span class="text-sm capitalize text-gray-600 dark:text-gray-400">{{ client.gender || '—' }}</span>
                  </td>
                  <td class="px-5 py-4">
                    <span class="text-sm text-gray-700 dark:text-gray-300">{{ client.country_name || '—' }}</span>
                  </td>
                  <td class="px-5 py-4">
                    <span class="text-sm text-gray-700 dark:text-gray-300">{{ client.city_name || '—' }}</span>
                  </td>
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
                  <td class="px-5 py-4">
                    <span class="text-sm text-gray-700 dark:text-gray-300">{{ client.approved_by_name || '—' }}</span>
                  </td>
                  <td class="px-5 py-4">
                    <span class="text-sm text-gray-500 dark:text-gray-500">{{ client.created_at || '—' }}</span>
                  </td>
                  <td class="px-5 py-4">
                    <div class="flex items-center justify-end gap-2">
                      <button
                        v-if="!client.approved_at"
                        type="button"
                        class="inline-flex items-center gap-1.5 rounded-lg border border-green-200 bg-green-50 px-3 py-1.5 text-xs font-semibold text-green-700 transition-all hover:bg-green-100 dark:border-green-800 dark:bg-green-950/50 dark:text-green-400 dark:hover:bg-green-900/60"
                        @click="approveClient(client.id)"
                      >
                        Approve
                      </button>
                      <Link
                        :href="`/manager/clients/${client.id}/edit`"
                        class="inline-flex items-center gap-1.5 rounded-lg border border-indigo-200 bg-indigo-50 px-3 py-1.5 text-xs font-semibold text-indigo-700 transition-all hover:bg-indigo-100 dark:border-indigo-800 dark:bg-indigo-950/50 dark:text-indigo-400 dark:hover:bg-indigo-900/60"
                      >
                        Edit
                      </Link>
                      <button
                        type="button"
                        class="inline-flex items-center gap-1.5 rounded-lg border border-red-200 bg-red-50 px-3 py-1.5 text-xs font-semibold text-red-700 transition-all hover:bg-red-100 dark:border-red-900 dark:bg-red-950/50 dark:text-red-400 dark:hover:bg-red-900/60"
                        @click="deleteClient(client.id)"
                      >
                        Delete
                      </button>
                    </div>
                  </td>
                </tr>

                <tr v-if="!clients.data.length">
                  <td colspan="10" class="px-5 py-20 text-center">
                    <div class="flex flex-col items-center gap-3">
                      <div class="flex h-14 w-14 items-center justify-center rounded-2xl bg-gray-100 dark:bg-gray-800">
                        <svg class="h-7 w-7 text-gray-400 dark:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="1.5"
                            d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"
                          />
                        </svg>
                      </div>
                      <div>
                        <p class="text-sm font-semibold text-gray-700 dark:text-gray-300">No clients found</p>
                        <p class="mt-0.5 text-xs text-gray-400 dark:text-gray-600">Try adjusting filters or add a new client.</p>
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
            class="flex flex-col gap-3 border-t border-gray-100 bg-gray-50/50 px-5 py-3 sm:flex-row sm:items-center sm:justify-between dark:border-gray-800 dark:bg-gray-800/40"
          >
            <p class="text-xs text-gray-400 dark:text-gray-500">
              Page {{ clients.current_page ?? '—' }} · {{ clients.data.length }} row(s) on this page
            </p>
            <div class="flex flex-wrap items-center gap-1">
              <template v-for="(link, index) in clients.links" :key="index">
                <Link
                  v-if="link.url"
                  :href="link.url"
                  class="flex h-8 min-w-8 items-center justify-center rounded-lg px-2.5 text-xs font-medium transition-all"
                  :class="
                    link.active
                      ? 'bg-indigo-600 text-white shadow-sm dark:bg-indigo-500'
                      : 'text-gray-600 hover:bg-gray-100 dark:text-gray-400 dark:hover:bg-gray-800'
                  "
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
    </div>
  </AppLayout>
</template>

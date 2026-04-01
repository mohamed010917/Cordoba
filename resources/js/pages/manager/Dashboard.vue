<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3'
import AppLayout from '@/layouts/AppLayout.vue'
import type { BreadcrumbItem } from '@/types'

type Stat = {
  total_clients: number
  approved_clients: number
  pending_clients: number
  inactive_clients: number
  total_receptionists: number
  active_receptionists: number
  total_reservations: number
  reservations_today: number
  my_rooms_total: number
  my_rooms_occupied: number
}

type ReservationRow = {
  id: number
  client_name: string | null
  client_email: string | null
  room_number: string | null
  floor_name: string | null
  paid_price_cents: number
  created_at: string | null
}

type ClientRow = {
  id: number
  name: string
  email: string
  country: string | null
  city: string | null
  approved_at: string | null
  created_at: string | null
}

type Trend = { date: string; count: number }

const props = defineProps<{
  stats: Stat
  latest_reservations: ReservationRow[]
  latest_clients: ClientRow[]
  reservation_trend: Trend[]
}>()

const breadcrumbs: BreadcrumbItem[] = [{ title: 'Dashboard', href: '/manager/dashboard' }]

function formatMoney(cents: number): string {
  return new Intl.NumberFormat(undefined, { style: 'currency', currency: 'USD' }).format(cents / 100)
}

function formatDate(iso: string | null): string {
  if (!iso) return '—'
  return new Date(iso).toLocaleString()
}

const maxTrend = Math.max(1, ...props.reservation_trend.map((t) => t.count))
</script>

<template>
  <Head title="Manager Dashboard" />

  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="min-h-screen bg-gradient-to-b from-gray-50 to-white px-4 py-8 dark:from-gray-950 dark:to-gray-900 sm:px-6 lg:px-8">
      <div class="mx-auto max-w-7xl space-y-8">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
          <div>
            <h1 class="text-2xl font-bold tracking-tight text-gray-900 dark:text-white">
              Hotel overview
            </h1>
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
              Monitor clients, reservations, and your floors at a glance.
            </p>
          </div>
          <div class="flex flex-wrap gap-2">
            <Link
              href="/manager/clients"
              class="inline-flex items-center gap-2 rounded-xl bg-indigo-600 px-4 py-2 text-sm font-semibold text-white shadow-sm transition hover:bg-indigo-500 dark:bg-indigo-500 dark:hover:bg-indigo-400"
            >
              Clients
            </Link>
            <Link
              href="/manager/rooms"
              class="inline-flex items-center gap-2 rounded-xl border border-gray-200 bg-white px-4 py-2 text-sm font-semibold text-gray-700 shadow-sm transition hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-200 dark:hover:bg-gray-800"
            >
              Rooms
            </Link>
          </div>
        </div>

        <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
          <div
            class="group rounded-2xl border border-gray-200/80 bg-white p-5 shadow-sm transition hover:-translate-y-0.5 hover:shadow-md dark:border-gray-800 dark:bg-gray-900/80"
          >
            <p class="text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">Total clients</p>
            <p class="mt-2 text-3xl font-bold text-gray-900 dark:text-white">{{ stats.total_clients }}</p>
            <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">All registered guests</p>
          </div>
          <div
            class="group rounded-2xl border border-emerald-200/60 bg-emerald-50/50 p-5 shadow-sm transition hover:-translate-y-0.5 hover:shadow-md dark:border-emerald-900/50 dark:bg-emerald-950/30"
          >
            <p class="text-xs font-semibold uppercase tracking-wider text-emerald-700 dark:text-emerald-400">Approved</p>
            <p class="mt-2 text-3xl font-bold text-emerald-800 dark:text-emerald-200">{{ stats.approved_clients }}</p>
            <p class="mt-1 text-xs text-emerald-700/80 dark:text-emerald-400/80">Ready to book</p>
          </div>
          <div
            class="group rounded-2xl border border-amber-200/60 bg-amber-50/50 p-5 shadow-sm transition hover:-translate-y-0.5 hover:shadow-md dark:border-amber-900/50 dark:bg-amber-950/30"
          >
            <p class="text-xs font-semibold uppercase tracking-wider text-amber-700 dark:text-amber-400">Pending</p>
            <p class="mt-2 text-3xl font-bold text-amber-800 dark:text-amber-200">{{ stats.pending_clients }}</p>
            <p class="mt-1 text-xs text-amber-700/80 dark:text-amber-400/80">Awaiting approval</p>
          </div>
          <div
            class="group rounded-2xl border border-gray-200/80 bg-white p-5 shadow-sm transition hover:-translate-y-0.5 hover:shadow-md dark:border-gray-800 dark:bg-gray-900/80"
          >
            <p class="text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">Inactive clients</p>
            <p class="mt-2 text-3xl font-bold text-gray-900 dark:text-white">{{ stats.inactive_clients }}</p>
            <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Disabled accounts</p>
          </div>
        </div>

        <div class="grid gap-4 lg:grid-cols-3">
          <div
            class="rounded-2xl border border-gray-200/80 bg-white p-5 shadow-sm dark:border-gray-800 dark:bg-gray-900/80"
          >
            <p class="text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">Receptionists</p>
            <p class="mt-2 text-2xl font-bold text-gray-900 dark:text-white">
              {{ stats.active_receptionists }}
              <span class="text-base font-medium text-gray-400">/ {{ stats.total_receptionists }}</span>
            </p>
            <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Active / total</p>
          </div>
          <div
            class="rounded-2xl border border-gray-200/80 bg-white p-5 shadow-sm dark:border-gray-800 dark:bg-gray-900/80"
          >
            <p class="text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">Reservations</p>
            <p class="mt-2 text-2xl font-bold text-gray-900 dark:text-white">{{ stats.total_reservations }}</p>
            <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">{{ stats.reservations_today }} created today</p>
          </div>
          <div
            class="rounded-2xl border border-indigo-200/60 bg-indigo-50/40 p-5 shadow-sm dark:border-indigo-900/50 dark:bg-indigo-950/30"
          >
            <p class="text-xs font-semibold uppercase tracking-wider text-indigo-700 dark:text-indigo-300">Your rooms</p>
            <p class="mt-2 text-2xl font-bold text-indigo-900 dark:text-indigo-100">
              {{ stats.my_rooms_occupied }}
              <span class="text-base font-medium text-indigo-400">/ {{ stats.my_rooms_total }}</span>
            </p>
            <p class="mt-1 text-xs text-indigo-700/80 dark:text-indigo-300/80">Occupied / total on your floors</p>
          </div>
        </div>

        <div class="grid gap-6 lg:grid-cols-5">
          <div
            class="rounded-2xl border border-gray-200/80 bg-white p-6 shadow-sm dark:border-gray-800 dark:bg-gray-900/80 lg:col-span-2"
          >
            <h2 class="text-sm font-semibold text-gray-900 dark:text-white">Reservation trend (7 days)</h2>
            <div class="mt-6 flex h-40 items-end gap-1">
              <div
                v-for="t in reservation_trend"
                :key="t.date"
                class="flex flex-1 flex-col items-center gap-2"
              >
                <div
                  class="w-full max-w-[28px] rounded-t-md bg-indigo-500/90 transition-all dark:bg-indigo-400"
                  :style="{ height: `${(t.count / maxTrend) * 100}%`, minHeight: t.count ? '4px' : '0' }"
                  :title="`${t.date}: ${t.count}`"
                />
                <span class="text-[10px] text-gray-400 dark:text-gray-500">{{ new Date(t.date).getDate() }}</span>
              </div>
            </div>
            <p class="mt-4 text-xs text-gray-500 dark:text-gray-400">Bars scale to the busiest day in this window.</p>
          </div>

          <div
            class="rounded-2xl border border-gray-200/80 bg-white p-0 shadow-sm dark:border-gray-800 dark:bg-gray-900/80 lg:col-span-3"
          >
            <div class="border-b border-gray-100 px-6 py-4 dark:border-gray-800">
              <h2 class="text-sm font-semibold text-gray-900 dark:text-white">Latest reservations</h2>
            </div>
            <div class="overflow-x-auto">
              <table class="min-w-full text-sm">
                <thead>
                  <tr class="border-b border-gray-100 bg-gray-50/80 text-left text-xs font-semibold uppercase tracking-wider text-gray-500 dark:border-gray-800 dark:bg-gray-800/50 dark:text-gray-400">
                    <th class="px-6 py-3">Guest</th>
                    <th class="px-6 py-3">Room</th>
                    <th class="px-6 py-3">Floor</th>
                    <th class="px-6 py-3">Paid</th>
                    <th class="px-6 py-3">When</th>
                  </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                  <tr
                    v-for="r in latest_reservations"
                    :key="r.id"
                    class="transition hover:bg-gray-50/80 dark:hover:bg-gray-800/40"
                  >
                    <td class="px-6 py-3">
                      <div class="font-medium text-gray-900 dark:text-white">{{ r.client_name }}</div>
                      <div class="text-xs text-gray-500 dark:text-gray-400">{{ r.client_email }}</div>
                    </td>
                    <td class="px-6 py-3 text-gray-700 dark:text-gray-300">{{ r.room_number ?? '—' }}</td>
                    <td class="px-6 py-3 text-gray-700 dark:text-gray-300">{{ r.floor_name ?? '—' }}</td>
                    <td class="px-6 py-3 text-gray-700 dark:text-gray-300">{{ formatMoney(r.paid_price_cents) }}</td>
                    <td class="px-6 py-3 text-xs text-gray-500 dark:text-gray-400">{{ formatDate(r.created_at) }}</td>
                  </tr>
                  <tr v-if="!latest_reservations.length">
                    <td colspan="5" class="px-6 py-12 text-center text-sm text-gray-500 dark:text-gray-400">
                      No reservations yet.
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>
        </div>

        <div
          class="rounded-2xl border border-gray-200/80 bg-white p-0 shadow-sm dark:border-gray-800 dark:bg-gray-900/80"
        >
          <div class="flex items-center justify-between border-b border-gray-100 px-6 py-4 dark:border-gray-800">
            <h2 class="text-sm font-semibold text-gray-900 dark:text-white">Latest clients</h2>
            <Link href="/manager/clients" class="text-xs font-semibold text-indigo-600 hover:text-indigo-500 dark:text-indigo-400">View all</Link>
          </div>
          <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
              <thead>
                <tr class="border-b border-gray-100 bg-gray-50/80 text-left text-xs font-semibold uppercase tracking-wider text-gray-500 dark:border-gray-800 dark:bg-gray-800/50 dark:text-gray-400">
                  <th class="px-6 py-3">Name</th>
                  <th class="px-6 py-3">Email</th>
                  <th class="px-6 py-3">Location</th>
                  <th class="px-6 py-3">Status</th>
                  <th class="px-6 py-3">Joined</th>
                </tr>
              </thead>
              <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                <tr
                  v-for="c in latest_clients"
                  :key="c.id"
                  class="transition hover:bg-gray-50/80 dark:hover:bg-gray-800/40"
                >
                  <td class="px-6 py-3 font-medium text-gray-900 dark:text-white">{{ c.name }}</td>
                  <td class="px-6 py-3 text-gray-600 dark:text-gray-400">{{ c.email }}</td>
                  <td class="px-6 py-3 text-gray-700 dark:text-gray-300">
                    <span v-if="c.city">{{ c.city }}</span>
                    <span v-if="c.city && c.country">, </span>
                    <span v-if="c.country">{{ c.country }}</span>
                    <span v-if="!c.city && !c.country">—</span>
                  </td>
                  <td class="px-6 py-3">
                    <span
                      v-if="c.approved_at"
                      class="inline-flex rounded-full bg-emerald-50 px-2.5 py-0.5 text-xs font-semibold text-emerald-700 ring-1 ring-emerald-100 dark:bg-emerald-950/50 dark:text-emerald-300 dark:ring-emerald-900/50"
                    >Approved</span>
                    <span
                      v-else
                      class="inline-flex rounded-full bg-amber-50 px-2.5 py-0.5 text-xs font-semibold text-amber-700 ring-1 ring-amber-100 dark:bg-amber-950/50 dark:text-amber-300 dark:ring-amber-900/50"
                    >Pending</span>
                  </td>
                  <td class="px-6 py-3 text-xs text-gray-500 dark:text-gray-400">{{ formatDate(c.created_at) }}</td>
                </tr>
                <tr v-if="!latest_clients.length">
                  <td colspan="5" class="px-6 py-12 text-center text-sm text-gray-500 dark:text-gray-400">
                    No clients yet.
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  </AppLayout>
</template>

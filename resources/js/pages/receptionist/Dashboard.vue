<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3'
import AppLayout from '@/layouts/AppLayout.vue'
import type { BreadcrumbItem } from '@/types'

type Stats = {
  pending_clients: number
  my_approved_clients: number
  my_reservations: number
}

type PendingRow = {
  id: number
  name: string
  email: string
  country: string | null
  city: string | null
  created_at: string | null
}

type ApprovedRow = {
  id: number
  name: string
  email: string
  country: string | null
  city: string | null
  approved_at: string | null
}

type ResRow = {
  id: number
  client_name: string | null
  room_number: string | null
  paid_price_cents: number
  created_at: string | null
}

defineProps<{
  stats: Stats
  recent_pending: PendingRow[]
  recent_approved: ApprovedRow[]
  recent_reservations: ResRow[]
}>()

const breadcrumbs: BreadcrumbItem[] = [{ title: 'Dashboard', href: '/receptionist/dashboard' }]

function formatMoney(cents: number): string {
  return new Intl.NumberFormat(undefined, { style: 'currency', currency: 'USD' }).format(cents / 100)
}

function formatDate(iso: string | null): string {
  if (!iso) return '—'
  return new Date(iso).toLocaleString()
}
</script>

<template>
  <Head title="Receptionist Dashboard" />

  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="min-h-screen bg-gradient-to-b from-gray-50 to-white px-4 py-8 dark:from-gray-950 dark:to-gray-900 sm:px-6 lg:px-8">
      <div class="mx-auto max-w-7xl space-y-8">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
          <div>
            <h1 class="text-2xl font-bold tracking-tight text-gray-900 dark:text-white">Front desk</h1>
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
              Pending approvals, your clients, and reservations in one place.
            </p>
          </div>
          <div class="flex flex-wrap gap-2">
            <Link
              href="/receptionist/clients/pending"
              class="inline-flex items-center gap-2 rounded-xl bg-amber-600 px-4 py-2 text-sm font-semibold text-white shadow-sm transition hover:bg-amber-500 dark:bg-amber-500 dark:hover:bg-amber-400"
            >
              Review pending
            </Link>
            <Link
              href="/receptionist/clients/approved"
              class="inline-flex items-center gap-2 rounded-xl border border-gray-200 bg-white px-4 py-2 text-sm font-semibold text-gray-700 shadow-sm transition hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-200 dark:hover:bg-gray-800"
            >
              My clients
            </Link>
            <Link
              href="/receptionist/clients/reservations"
              class="inline-flex items-center gap-2 rounded-xl border border-gray-200 bg-white px-4 py-2 text-sm font-semibold text-gray-700 shadow-sm transition hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-200 dark:hover:bg-gray-800"
            >
              Reservations
            </Link>
          </div>
        </div>

        <div class="grid gap-4 sm:grid-cols-3">
          <div
            class="rounded-2xl border border-amber-200/60 bg-amber-50/50 p-6 shadow-sm transition hover:-translate-y-0.5 hover:shadow-md dark:border-amber-900/50 dark:bg-amber-950/30"
          >
            <p class="text-xs font-semibold uppercase tracking-wider text-amber-800 dark:text-amber-300">Pending (queue)</p>
            <p class="mt-2 text-4xl font-bold text-amber-900 dark:text-amber-100">{{ stats.pending_clients }}</p>
            <p class="mt-1 text-xs text-amber-800/80 dark:text-amber-300/80">All guests awaiting approval</p>
          </div>
          <div
            class="rounded-2xl border border-emerald-200/60 bg-emerald-50/50 p-6 shadow-sm transition hover:-translate-y-0.5 hover:shadow-md dark:border-emerald-900/50 dark:bg-emerald-950/30"
          >
            <p class="text-xs font-semibold uppercase tracking-wider text-emerald-800 dark:text-emerald-300">Approved by you</p>
            <p class="mt-2 text-4xl font-bold text-emerald-900 dark:text-emerald-100">{{ stats.my_approved_clients }}</p>
            <p class="mt-1 text-xs text-emerald-800/80 dark:text-emerald-300/80">Clients you cleared</p>
          </div>
          <div
            class="rounded-2xl border border-indigo-200/60 bg-indigo-50/40 p-6 shadow-sm transition hover:-translate-y-0.5 hover:shadow-md dark:border-indigo-900/50 dark:bg-indigo-950/30"
          >
            <p class="text-xs font-semibold uppercase tracking-wider text-indigo-800 dark:text-indigo-300">Their reservations</p>
            <p class="mt-2 text-4xl font-bold text-indigo-900 dark:text-indigo-100">{{ stats.my_reservations }}</p>
            <p class="mt-1 text-xs text-indigo-800/80 dark:text-indigo-300/80">Bookings for clients you approved</p>
          </div>
        </div>

        <div class="grid gap-6 lg:grid-cols-2">
          <div
            class="rounded-2xl border border-gray-200/80 bg-white p-0 shadow-sm dark:border-gray-800 dark:bg-gray-900/80"
          >
            <div class="flex items-center justify-between border-b border-gray-100 px-6 py-4 dark:border-gray-800">
              <h2 class="text-sm font-semibold text-gray-900 dark:text-white">Recent pending</h2>
              <Link href="/receptionist/clients/pending" class="text-xs font-semibold text-indigo-600 dark:text-indigo-400">Open queue</Link>
            </div>
            <ul class="divide-y divide-gray-100 dark:divide-gray-800">
              <li
                v-for="p in recent_pending"
                :key="p.id"
                class="flex items-start justify-between gap-4 px-6 py-4 transition hover:bg-gray-50/80 dark:hover:bg-gray-800/40"
              >
                <div>
                  <p class="font-medium text-gray-900 dark:text-white">{{ p.name }}</p>
                  <p class="text-xs text-gray-500 dark:text-gray-400">{{ p.email }}</p>
                  <p class="mt-1 text-xs text-gray-600 dark:text-gray-300">
                    <span v-if="p.city">{{ p.city }}</span>
                    <span v-if="p.city && p.country">, </span>
                    <span v-if="p.country">{{ p.country }}</span>
                    <span v-if="!p.city && !p.country">—</span>
                  </p>
                </div>
                <span class="shrink-0 text-xs text-gray-400 dark:text-gray-500">{{ formatDate(p.created_at) }}</span>
              </li>
              <li v-if="!recent_pending.length" class="px-6 py-12 text-center text-sm text-gray-500 dark:text-gray-400">
                No pending clients.
              </li>
            </ul>
          </div>

          <div
            class="rounded-2xl border border-gray-200/80 bg-white p-0 shadow-sm dark:border-gray-800 dark:bg-gray-900/80"
          >
            <div class="flex items-center justify-between border-b border-gray-100 px-6 py-4 dark:border-gray-800">
              <h2 class="text-sm font-semibold text-gray-900 dark:text-white">Recently approved (yours)</h2>
              <Link href="/receptionist/clients/approved" class="text-xs font-semibold text-indigo-600 dark:text-indigo-400">View all</Link>
            </div>
            <ul class="divide-y divide-gray-100 dark:divide-gray-800">
              <li
                v-for="a in recent_approved"
                :key="a.id"
                class="flex items-start justify-between gap-4 px-6 py-4 transition hover:bg-gray-50/80 dark:hover:bg-gray-800/40"
              >
                <div>
                  <p class="font-medium text-gray-900 dark:text-white">{{ a.name }}</p>
                  <p class="text-xs text-gray-500 dark:text-gray-400">{{ a.email }}</p>
                  <p class="mt-1 text-xs text-gray-600 dark:text-gray-300">
                    <span v-if="a.city">{{ a.city }}</span>
                    <span v-if="a.city && a.country">, </span>
                    <span v-if="a.country">{{ a.country }}</span>
                  </p>
                </div>
                <span class="shrink-0 text-xs text-gray-400 dark:text-gray-500">{{ formatDate(a.approved_at) }}</span>
              </li>
              <li v-if="!recent_approved.length" class="px-6 py-12 text-center text-sm text-gray-500 dark:text-gray-400">
                You have not approved any clients yet.
              </li>
            </ul>
          </div>
        </div>

        <div
          class="rounded-2xl border border-gray-200/80 bg-white p-0 shadow-sm dark:border-gray-800 dark:bg-gray-900/80"
        >
          <div class="flex items-center justify-between border-b border-gray-100 px-6 py-4 dark:border-gray-800">
            <h2 class="text-sm font-semibold text-gray-900 dark:text-white">Recent reservations (your clients)</h2>
            <Link href="/receptionist/clients/reservations" class="text-xs font-semibold text-indigo-600 dark:text-indigo-400">Full list</Link>
          </div>
          <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
              <thead>
                <tr class="border-b border-gray-100 bg-gray-50/80 text-left text-xs font-semibold uppercase tracking-wider text-gray-500 dark:border-gray-800 dark:bg-gray-800/50 dark:text-gray-400">
                  <th class="px-6 py-3">Guest</th>
                  <th class="px-6 py-3">Room</th>
                  <th class="px-6 py-3">Paid</th>
                  <th class="px-6 py-3">Booked</th>
                </tr>
              </thead>
              <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                <tr
                  v-for="r in recent_reservations"
                  :key="r.id"
                  class="transition hover:bg-gray-50/80 dark:hover:bg-gray-800/40"
                >
                  <td class="px-6 py-3 font-medium text-gray-900 dark:text-white">{{ r.client_name }}</td>
                  <td class="px-6 py-3 text-gray-700 dark:text-gray-300">{{ r.room_number ?? '—' }}</td>
                  <td class="px-6 py-3 text-gray-700 dark:text-gray-300">{{ formatMoney(r.paid_price_cents) }}</td>
                  <td class="px-6 py-3 text-xs text-gray-500 dark:text-gray-400">{{ formatDate(r.created_at) }}</td>
                </tr>
                <tr v-if="!recent_reservations.length">
                  <td colspan="4" class="px-6 py-12 text-center text-sm text-gray-500 dark:text-gray-400">
                    No reservations for your approved clients yet.
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

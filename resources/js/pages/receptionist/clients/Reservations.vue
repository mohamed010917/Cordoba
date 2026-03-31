<script setup>
import AppLayout from '@/layouts/AppLayout.vue'
import { Head, Link, router } from '@inertiajs/vue3'
import { ref, watch } from 'vue'

const props = defineProps({
  reservations: Object,
  filters: Object,
})

const search = ref(props.filters?.search || '')
let searchTimeout = null

function formatPrice(priceInCents) {
  if (priceInCents === null || priceInCents === undefined) return '-'
  return (priceInCents / 100).toFixed(2)
}

watch(search, (value) => {
  clearTimeout(searchTimeout)

  searchTimeout = setTimeout(() => {
    router.get(
      route('receptionist.clients.reservations'),
      {
        search: value,
        sort: props.filters?.sort || 'created_at',
        direction: props.filters?.direction || 'desc',
      },
      {
        preserveState: true,
        replace: true,
        preserveScroll: true,
      }
    )
  }, 400)
})

function sortBy(column) {
  let direction = 'asc'

  if (props.filters?.sort === column) {
    direction = props.filters.direction === 'asc' ? 'desc' : 'asc'
  }

  router.get('/receptionist/clients/reservations',
    {
      search: search.value,
      sort: column,
      direction,
    },
    {
      preserveState: true,
      replace: true,
      preserveScroll: true,
    }
  )
}

function sortIcon(column) {
  if (props.filters?.sort !== column) return '↕'
  return props.filters?.direction === 'asc' ? '↑' : '↓'
}
</script>

<template>
  <Head title="Clients Reservations" />

  <AppLayout>
    <div class="min-h-screen bg-black px-6 py-8 text-white">
      <div
        class="mx-auto max-w-7xl rounded-2xl border border-white/10 bg-zinc-950/90 p-6 shadow-2xl shadow-cyan-500/5 backdrop-blur"
      >
        <div class="mb-8">
          <h1
            class="mb-2 text-3xl font-bold tracking-tight text-white transition duration-300 hover:translate-x-1 hover:text-cyan-400"
          >
            Clients Reservations
          </h1>
          <p
            class="text-sm text-zinc-400 transition duration-300 hover:translate-x-1 hover:text-white"
          >
            Reservations for clients approved by the current receptionist
          </p>
        </div>

        <div class="mb-6 flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
          <div class="w-full md:max-w-md">
            <input
              v-model="search"
              type="text"
              placeholder="Search by client name, email, room number, price..."
              class="w-full rounded-2xl border border-white/10 bg-zinc-900 px-4 py-3 text-sm text-white placeholder:text-zinc-500 outline-none transition duration-300 focus:border-cyan-400 focus:ring-2 focus:ring-cyan-400/20"
            />
          </div>

          <div class="text-sm text-zinc-500">
            Total: {{ reservations.total ?? reservations.data.length }}
          </div>
        </div>

        <div class="overflow-hidden rounded-2xl border border-white/10 bg-zinc-900 shadow-lg">
          <div class="overflow-x-auto">
            <table class="min-w-full">
              <thead class="bg-zinc-800/80">
                <tr class="border-b border-white/10">
                  <th
                    @click="sortBy('client_name')"
                    class="cursor-pointer px-4 py-4 text-left text-sm font-semibold text-zinc-300 transition hover:text-cyan-400"
                  >
                    Client Name {{ sortIcon('client_name') }}
                  </th>

                  <th
                    @click="sortBy('client_email')"
                    class="cursor-pointer px-4 py-4 text-left text-sm font-semibold text-zinc-300 transition hover:text-cyan-400"
                  >
                    Client Email {{ sortIcon('client_email') }}
                  </th>

                  <th
                    @click="sortBy('accompany_number')"
                    class="cursor-pointer px-4 py-4 text-left text-sm font-semibold text-zinc-300 transition hover:text-cyan-400"
                  >
                    Accompany Number {{ sortIcon('accompany_number') }}
                  </th>

                  <th
                    @click="sortBy('room_number')"
                    class="cursor-pointer px-4 py-4 text-left text-sm font-semibold text-zinc-300 transition hover:text-cyan-400"
                  >
                    Room Number {{ sortIcon('room_number') }}
                  </th>

                  <th
                    @click="sortBy('paid_price_cents')"
                    class="cursor-pointer px-4 py-4 text-left text-sm font-semibold text-zinc-300 transition hover:text-cyan-400"
                  >
                    Paid Price {{ sortIcon('paid_price_cents') }}
                  </th>

                  <th
                    @click="sortBy('created_at')"
                    class="cursor-pointer px-4 py-4 text-left text-sm font-semibold text-zinc-300 transition hover:text-cyan-400"
                  >
                    Created At {{ sortIcon('created_at') }}
                  </th>
                </tr>
              </thead>

              <tbody>
                <tr
                  v-for="reservation in reservations.data"
                  :key="reservation.id"
                  class="group border-b border-white/5 bg-zinc-950 transition duration-300 hover:scale-[1.005] hover:bg-zinc-800/70"
                >
                  <td class="px-4 py-4 text-sm text-zinc-200 transition duration-300 group-hover:text-cyan-400">
                    {{ reservation.client_name || '-' }}
                  </td>

                  <td class="px-4 py-4 text-sm text-zinc-200 transition duration-300 group-hover:text-cyan-400">
                    {{ reservation.client_email || '-' }}
                  </td>

                  <td class="px-4 py-4 text-sm text-zinc-300 transition duration-300 group-hover:text-white">
                    {{ reservation.accompany_number ?? '-' }}
                  </td>

                  <td class="px-4 py-4 text-sm text-zinc-300 transition duration-300 group-hover:text-white">
                    {{ reservation.room_number || '-' }}
                  </td>

                  <td class="px-4 py-4 text-sm font-medium text-emerald-400 transition duration-300 group-hover:scale-105">
                    {{ formatPrice(reservation.paid_price_cents) }}
                  </td>

                  <td class="px-4 py-4 text-sm text-zinc-400 transition duration-300 group-hover:text-zinc-200">
                    {{ reservation.created_at || '-' }}
                  </td>
                </tr>

                <tr v-if="!reservations.data.length">
                  <td colspan="6" class="px-4 py-10 text-center text-sm text-zinc-500">
                    No reservations found.
                  </td>
                </tr>
              </tbody>
            </table>
          </div>

          <div
            v-if="reservations.links?.length"
            class="flex flex-wrap gap-2 border-t border-white/10 bg-zinc-950 px-4 py-4"
          >
            <template v-for="(link, index) in reservations.links" :key="index">
              <Link
                v-if="link.url"
                :href="link.url"
                class="rounded-xl border border-white/10 px-3 py-1.5 text-sm transition duration-300 hover:-translate-y-0.5 hover:border-cyan-400 hover:text-cyan-400"
                :class="link.active ? 'border-cyan-400 bg-cyan-500 font-semibold text-black' : 'bg-zinc-900 text-zinc-300 hover:bg-zinc-800'"
                v-html="link.label"
              />
              <span
                v-else
                class="rounded-xl border border-white/5 bg-zinc-900 px-3 py-1.5 text-sm text-zinc-600"
                v-html="link.label"
              />
            </template>
          </div>
        </div>
      </div>
    </div>
  </AppLayout>
</template>
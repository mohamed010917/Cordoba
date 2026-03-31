<script setup>
import AppLayout from '@/layouts/AppLayout.vue'
import { Head, Link, router } from '@inertiajs/vue3'
import { reactive } from 'vue'

const props = defineProps({
  clients: Object,
  filters: {
    type: Object,
    default: () => ({}),
  },
})

const filterForm = reactive({
  search: props.filters?.search || '',
  sort: props.filters?.sort || 'approved_at',
  direction: props.filters?.direction || 'desc',
})

function applyFilters() {
  router.get('/receptionist/clients/approved', filterForm, {
    preserveState: true,
    replace: true,
  })
}

function resetFilters() {
  filterForm.search = ''
  filterForm.sort = 'approved_at'
  filterForm.direction = 'desc'

  applyFilters()
}

</script>

<template>
  <Head title="My Approved Clients" />

  <AppLayout>
    <div class="min-h-screen bg-black px-6 py-8 text-white">
      <div
        class="mx-auto max-w-7xl rounded-2xl border border-white/10 bg-zinc-950/90 p-6 shadow-2xl shadow-cyan-500/5 backdrop-blur"
      >
        <div class="mb-8">
          <h1
            class="mb-2 text-3xl font-bold tracking-tight text-white transition duration-300 hover:translate-x-1 hover:text-cyan-400"
          >
            My Approved Clients
          </h1>
          <p
            class="text-sm text-zinc-400 transition duration-300 hover:translate-x-1 hover:text-white"
          >
            Clients approved by the current receptionist
          </p>
        </div>


      <div class="mb-4 flex flex-col gap-3 md:flex-row md:items-end">
        <div class="w-full md:w-80">
          <label class="mb-1 block text-sm font-medium">Search</label>
          <input
            v-model="filterForm.search"
            type="text"
            placeholder="Name, email, country..."
            class="w-full rounded border px-3 py-2"
            @keyup.enter="applyFilters"
          />
        </div>

        <div>
          <label class="mb-1 block text-sm font-medium">Sort By</label>
          <select
            v-model="filterForm.sort"
            class="rounded border px-3 py-2"
            @change="applyFilters"
          >
            <option value="approved_at">Approved At</option>
            <option value="created_at">Created At</option>
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
        
        <div class="overflow-hidden rounded-2xl border border-white/10 bg-zinc-900 shadow-lg">
          <div class="overflow-x-auto">
            <table class="min-w-full">
              <thead class="bg-zinc-800/80">
                <tr class="border-b border-white/10">
                  <th class="px-4 py-4 text-left text-sm font-semibold text-zinc-300 transition hover:text-cyan-400">Image</th>
                  <th class="px-4 py-4 text-left text-sm font-semibold text-zinc-300 transition hover:text-cyan-400">Name</th>
                  <th class="px-4 py-4 text-left text-sm font-semibold text-zinc-300 transition hover:text-cyan-400">Email</th>
                  <th class="px-4 py-4 text-left text-sm font-semibold text-zinc-300 transition hover:text-cyan-400">Phone</th>
                  <th class="px-4 py-4 text-left text-sm font-semibold text-zinc-300 transition hover:text-cyan-400">Gender</th>
                  <th class="px-4 py-4 text-left text-sm font-semibold text-zinc-300 transition hover:text-cyan-400">Country</th>
                  <th class="px-4 py-4 text-left text-sm font-semibold text-zinc-300 transition hover:text-cyan-400">Approved At</th>
                </tr>
              </thead>

              <tbody>
                <tr
                  v-for="client in clients.data"
                  :key="client.id"
                  class="group border-b border-white/5 bg-zinc-950 transition duration-300 hover:scale-[1.005] hover:bg-zinc-800/70"
                >
                  <td class="px-4 py-4">
                    <img
                      v-if="client.image"
                      :src="`/storage/${client.image}`"
                      alt="client image"
                      class="h-11 w-11 rounded-full border border-white/10 object-cover transition duration-300 group-hover:scale-110 group-hover:shadow-lg group-hover:shadow-cyan-500/20"
                    />
                    <div
                      v-else
                      class="flex h-11 w-11 items-center justify-center rounded-full border border-white/10 bg-zinc-800 text-xs text-zinc-400 transition duration-300 group-hover:scale-110 group-hover:text-white"
                    >
                      N/A
                    </div>
                  </td>

                  <td class="px-4 py-4 text-sm text-zinc-200 transition duration-300 group-hover:text-cyan-400">
                    {{ client.name }}
                  </td>
                  <td class="px-4 py-4 text-sm text-zinc-200 transition duration-300 group-hover:text-cyan-400">
                    {{ client.email }}
                  </td>
                  <td class="px-4 py-4 text-sm text-zinc-300 transition duration-300 group-hover:text-white">
                    {{ client.phone || '-' }}
                  </td>
                  <td class="px-4 py-4 text-sm capitalize text-zinc-300 transition duration-300 group-hover:text-white">
                    {{ client.gender || '-' }}
                  </td>
                  <td class="px-4 py-4 text-sm text-zinc-300 transition duration-300 group-hover:text-white">
                    {{ client.country || '-' }}
                  </td>
                  <td class="px-4 py-4 text-sm text-zinc-400 transition duration-300 group-hover:text-zinc-200">
                    {{ client.approved_at || '-' }}
                  </td>
                </tr>

                <tr v-if="!clients.data.length">
                  <td colspan="7" class="px-4 py-10 text-center text-sm text-zinc-500">
                    No approved clients found.
                  </td>
                </tr>
              </tbody>
            </table>
          </div>

          <div
            v-if="clients.links?.length"
            class="flex flex-wrap gap-2 border-t border-white/10 bg-zinc-950 px-4 py-4"
          >
            <template v-for="(link, index) in clients.links" :key="index">
              <Link
                v-if="link.url"
                :href="link.url"
                class="rounded-xl border border-white/10 px-3 py-1.5 text-sm transition duration-300 hover:-translate-y-0.5 hover:border-cyan-400 hover:text-cyan-400"
                :class="link.active ? 'bg-cyan-500 text-black border-cyan-400 font-semibold' : 'bg-zinc-900 text-zinc-300 hover:bg-zinc-800'"
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
<script setup>
import AppLayout from '@/layouts/AppLayout.vue'
import Swal from 'sweetalert2'
import { Head, Link, router, usePage } from '@inertiajs/vue3'

const props = defineProps({
    clients: Object,
})

const page = usePage()

function approveClient(id) {
  Swal.fire({
    title: 'Approve Client?',
    text: 'Are you sure you want to approve this client?',
    icon: 'question',
    showCancelButton: true,
    confirmButtonText: 'OK',
    cancelButtonText: 'Cancel',
    reverseButtons: true,
    background: '#111827',
    color: '#fff',
    confirmButtonColor: '#16a34a',
    cancelButtonColor: '#374151',
  }).then((result) => {
    if (result.isConfirmed) {
      router.patch(`/receptionist/clients/${id}/approve`)
    }
  })
}
</script>

<template>
  <Head title="Pending Clients" />

  <AppLayout>
    <div class="min-h-screen bg-black px-6 py-8 text-white">
      <div
        class="mx-auto max-w-7xl rounded-2xl border border-white/10 bg-zinc-950/90 p-6 shadow-2xl shadow-cyan-500/5 backdrop-blur"
      >
        <div class="mb-8">
          <h1
            class="mb-2 text-3xl font-bold tracking-tight text-white transition duration-300 hover:translate-x-1 hover:text-cyan-400"
          >
            Pending Clients
          </h1>
          <p
            class="text-sm text-zinc-400 transition duration-300 hover:translate-x-1 hover:text-white"
          >
            Approve registered clients waiting for review
          </p>
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
                  <th class="px-4 py-4 text-left text-sm font-semibold text-zinc-300 transition hover:text-cyan-400">Created At</th>
                  <th class="px-4 py-4 text-left text-sm font-semibold text-zinc-300 transition hover:text-cyan-400">Actions</th>
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
                    {{ client.created_at }}
                  </td>

                  <td class="px-4 py-4">
                    <button
                      @click="approveClient(client.id)"
                      class="rounded-xl bg-green-600 px-4 py-2 text-xs font-semibold text-white transition duration-300 hover:scale-105 hover:bg-green-500 hover:shadow-lg hover:shadow-green-500/30 active:scale-95"
                    >
                      Approve
                    </button>
                  </td>
                </tr>

                <tr v-if="!clients.data.length">
                  <td colspan="8" class="px-4 py-10 text-center text-sm text-zinc-500">
                    No pending clients found.
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
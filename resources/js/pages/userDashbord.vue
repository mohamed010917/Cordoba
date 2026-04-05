<template>
        <Head title="Dashboard" />

    <AppLayout :breadcrumbs="breadcrumbs">

        <div class="min-h-screen bg-gray-50 dark:bg-gray-950 text-gray-900 dark:text-gray-100 p-6 transition-colors duration-300">
      
          <!-- Top Bar -->
          <div class="flex items-start justify-between mb-6">
            <div>
              <span class="block text-xs font-semibold uppercase tracking-widest text-gray-400 dark:text-gray-500 mb-1">
                {{ timeGreeting }},
              </span>
              <h1 class="text-3xl font-bold text-gray-900 dark:text-gray-100 tracking-tight">
                {{ user.name }}
              </h1>
            </div>
          </div>
      
          <!-- Status Banners -->
          <div
            v-if="!user.is_approved"
            class="flex items-center gap-3 px-4 py-3 mb-5 rounded-xl bg-amber-50 dark:bg-amber-950 border border-amber-200 dark:border-amber-800 text-amber-700 dark:text-amber-300 text-sm font-medium"
          >
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="shrink-0">
              <circle cx="12" cy="12" r="10"/><path d="M12 8v4M12 16h.01"/>
            </svg>
            Your account is awaiting approval. You'll be notified once activated.
          </div>
      
          <div
            v-if="user.is_banned"
            class="flex items-center gap-3 px-4 py-3 mb-5 rounded-xl bg-red-50 dark:bg-red-950 border border-red-200 dark:border-red-800 text-red-700 dark:text-red-300 text-sm font-medium"
          >
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="shrink-0">
              <circle cx="12" cy="12" r="10"/><path d="M4.93 4.93l14.14 14.14"/>
            </svg>
            Your account has been suspended. Please contact support.
          </div>
      
          <!-- Stats Grid -->
          <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
            <div
              v-for="s in stats"
              :key="s.label"
              class="flex items-center gap-3 bg-white dark:bg-gray-900 border border-gray-100 dark:border-gray-800 rounded-2xl p-4 hover:-translate-y-0.5 transition-transform duration-150"
            >
              <div
                class="w-11 h-11 rounded-xl flex items-center justify-center shrink-0"
                :class="s.iconClass"
              >
                <component :is="s.icon" />
              </div>
              <div class="flex-1 min-w-0">
                <span class="block text-xl font-bold text-gray-900 dark:text-gray-100 leading-tight tracking-tight">
                  {{ s.value }}
                </span>
                <span class="block text-xs text-gray-400 dark:text-gray-500 mt-0.5">{{ s.label }}</span>
              </div>
              <span class="text-xs font-semibold whitespace-nowrap" :class="s.badgeClass">{{ s.badge }}</span>
            </div>
          </div>
      
          <!-- Two Column -->
          <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 mb-4">
      
            <!-- Active Reservation -->
            <div class="bg-white dark:bg-gray-900 border border-gray-100 dark:border-gray-800 rounded-2xl p-5">
              <div class="flex items-center justify-between mb-4">
                <span class="text-xs font-semibold uppercase tracking-widest text-gray-400 dark:text-gray-500">
                  Active Reservation
                </span>
                <router-link to="/reservations" class="text-xs font-medium text-indigo-600 dark:text-indigo-400 hover:underline">
                  View all
                </router-link>
              </div>
      
              <div v-if="activeReservation" class="flex gap-3 items-start bg-gray-50 dark:bg-gray-800 rounded-xl p-4">
                <div class="w-14 h-14 rounded-xl bg-indigo-50 dark:bg-indigo-950 flex items-center justify-center text-indigo-600 dark:text-indigo-400 shrink-0">
                  <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                    <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/>
                    <polyline points="9 22 9 12 15 12 15 22"/>
                  </svg>
                </div>
                <div class="flex-1 min-w-0">
                  <p class="text-sm font-semibold text-gray-900 dark:text-gray-100 truncate mb-2">
                    {{ activeReservation.room.name }} — Room {{ activeReservation.room.number }}
                  </p>
            

                </div>
              </div>
      
              <div v-else class="flex flex-col items-center gap-3 py-8 text-gray-400 dark:text-gray-600 text-center">
                <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.2">
                  <rect x="3" y="4" width="18" height="18" rx="2"/><path d="M16 2v4M8 2v4M3 10h18"/>
                </svg>
                <p class="text-sm m-0">No active reservations</p>
                <button
                  class="px-5 py-2 bg-indigo-600 hover:bg-indigo-500 text-white text-xs font-semibold rounded-lg transition-opacity cursor-pointer border-0"
                  @click="$router.push('/rooms')"
                >
                  Book a Room
                </button>
              </div>
            </div>
      
            <!-- Profile Snapshot -->
            <div class="bg-white dark:bg-gray-900 border border-gray-100 dark:border-gray-800 rounded-2xl p-5">
              <div class="flex items-center justify-between mb-4">
                <span class="text-xs font-semibold uppercase tracking-widest text-gray-400 dark:text-gray-500">Profile</span>
                <router-link to="/profile" class="text-xs font-medium text-indigo-600 dark:text-indigo-400 hover:underline">
                  Edit
                </router-link>
              </div>
      
              <div class="flex gap-4 items-start">
                <!-- Avatar -->
                <div class="relative shrink-0">
                  <img
                    v-if="user.image"
                    :src="user.image"
                    :alt="user.name"
                    class="w-16 h-16 rounded-full object-cover border-2 border-gray-100 dark:border-gray-800"
                  />
                  <div
                    v-else
                    class="w-16 h-16 rounded-full bg-indigo-600 text-white flex items-center justify-center text-xl font-bold"
                  >
                    {{ initials }}
                  </div>
                  <span
                    class="absolute -bottom-1 left-1/2 -translate-x-1/2 text-[10px] font-bold px-2 py-0.5 rounded-full whitespace-nowrap"
                    :class="user.is_approved
                      ? 'bg-emerald-100 dark:bg-emerald-950 text-emerald-700 dark:text-emerald-300'
                      : 'bg-amber-100 dark:bg-amber-950 text-amber-700 dark:text-amber-300'"
                  >
                    {{ user.is_approved ? 'Active' : 'Pending' }}
                  </span>
                </div>
      
                <!-- Fields -->
                <div class="flex-1 min-w-0">
                  <div
                    v-for="(f, i) in profileFields"
                    :key="f.label"
                    class="flex justify-between items-center py-1.5 gap-2"
                    :class="i < profileFields.length - 1 ? 'border-b border-gray-100 dark:border-gray-800' : ''"
                  >
                    <span class="text-xs text-gray-400 dark:text-gray-500 shrink-0">{{ f.label }}</span>
                    <span class="text-xs font-medium text-gray-900 dark:text-gray-100 text-right truncate max-w-[160px]">
                      {{ f.value || '—' }}
                    </span>
                  </div>
                </div>
              </div>
            </div>
          </div>
      
          <!-- Recent Reservations Table -->
          <div class="bg-white dark:bg-gray-900 border border-gray-100 dark:border-gray-800 rounded-2xl p-5">
            <div class="flex flex-wrap items-center justify-between gap-3 mb-4">
              <span class="text-xs font-semibold uppercase tracking-widest text-gray-400 dark:text-gray-500">
                Recent Reservations
              </span>
              <div class="flex flex-wrap gap-1.5">
                <button
                  v-for="f in ['All', 'Confirmed', 'Pending', 'Cancelled']"
                  :key="f"
                  class="px-3 py-1 rounded-full text-xs font-medium border transition-all duration-150 cursor-pointer"
                  :class="activeFilter === f
                    ? 'bg-indigo-600 text-white border-indigo-600'
                    : 'bg-transparent text-gray-500 dark:text-gray-400 border-gray-200 dark:border-gray-700 hover:border-indigo-400 hover:text-indigo-500 dark:hover:text-indigo-400'"
                  @click="activeFilter = f"
                >
                  {{ f }}
                </button>
              </div>
            </div>
      
            <div class="overflow-x-auto">
              <table class="w-full min-w-[520px] text-sm border-collapse">
                <thead>
                  <tr>
                    <th class="text-left px-3 py-2 text-[11px] font-semibold uppercase tracking-wider text-gray-400 dark:text-gray-500 border-b border-gray-100 dark:border-gray-800">Room</th>
                    <th class="text-left px-3 py-2 text-[11px] font-semibold uppercase tracking-wider text-gray-400 dark:text-gray-500 border-b border-gray-100 dark:border-gray-800">Check-in</th>
                    <th class="text-left px-3 py-2 text-[11px] font-semibold uppercase tracking-wider text-gray-400 dark:text-gray-500 border-b border-gray-100 dark:border-gray-800">Check-out</th>
                    <th class="text-left px-3 py-2 text-[11px] font-semibold uppercase tracking-wider text-gray-400 dark:text-gray-500 border-b border-gray-100 dark:border-gray-800">Nights</th>
                    <th class="text-left px-3 py-2 text-[11px] font-semibold uppercase tracking-wider text-gray-400 dark:text-gray-500 border-b border-gray-100 dark:border-gray-800">Total</th>
                    <th class="text-left px-3 py-2 text-[11px] font-semibold uppercase tracking-wider text-gray-400 dark:text-gray-500 border-b border-gray-100 dark:border-gray-800">Status</th>
                  </tr>
                </thead>
                <tbody>
                  <tr
                    v-for="r in filteredReservations"
                    :key="r.id"
                    class="group hover:bg-gray-50 dark:hover:bg-gray-800 transition-colors"
                  >
                    <td class="px-3 py-3 border-b border-gray-100 dark:border-gray-800 group-last:border-b-0">
                      <div class="flex items-center gap-2 font-medium text-gray-900 dark:text-gray-100">
                        <span class="w-2 h-2 rounded-full bg-indigo-500 shrink-0 inline-block"></span>
                        {{ r.room }}
                      </div>
                    </td>
                    <td class="px-3 py-3 text-gray-600 dark:text-gray-400 border-b border-gray-100 dark:border-gray-800 group-last:border-b-0">{{ r.checkIn }}</td>
                    <td class="px-3 py-3 text-gray-600 dark:text-gray-400 border-b border-gray-100 dark:border-gray-800 group-last:border-b-0">{{ r.checkOut }}</td>
                    <td class="px-3 py-3 text-gray-600 dark:text-gray-400 border-b border-gray-100 dark:border-gray-800 group-last:border-b-0">{{ r.nights }}</td>
                    <td class="px-3 py-3 font-semibold text-gray-900 dark:text-gray-100 border-b border-gray-100 dark:border-gray-800 group-last:border-b-0">${{ r.total }}</td>
                    <td class="px-3 py-3 border-b border-gray-100 dark:border-gray-800 group-last:border-b-0">
                      <span
                        class="inline-block px-2.5 py-0.5 rounded-full text-[11px] font-semibold"
                        :class="{
                          'bg-emerald-50 dark:bg-emerald-950 text-emerald-700 dark:text-emerald-300': r.status === 'Confirmed',
                          'bg-amber-50 dark:bg-amber-950 text-amber-700 dark:text-amber-300': r.status === 'Pending',
                          'bg-red-50 dark:bg-red-950 text-red-700 dark:text-red-300': r.status === 'Cancelled',
                        }"
                      >
                        {{ r.status }}
                      </span>
                    </td>
                  </tr>
                </tbody>
              </table>
              <div
                v-if="filteredReservations.length === 0"
                class="text-center py-8 text-sm text-gray-400 dark:text-gray-600"
              >
                No reservations found.
              </div>
            </div>
          </div>
      
        </div>
    </AppLayout>
  
</template>

<script setup lang="ts">
import { Head, usePage } from '@inertiajs/vue3'
import { ref, computed } from 'vue'
import AppLayout from '@/layouts/AppLayout.vue';
import { dashboard } from '@/routes';
import type { BreadcrumbItem } from '@/types';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Dashboard',
        href: dashboard(), 
    },
];


// --- Mock user from your User model ---
const page = usePage()
const user = page.props.auth.user ;
const totalStays = page.props.totalStays ;
const completedStays = page.props.completedStays ;
const upcomingStays = page.props.upcomingStays ;
const loyaltyStatus = page.props.loyaltyStatus ;

const initials = computed(() =>
  user.name.split(' ').map(n => n[0]).join('').slice(0, 2).toUpperCase()
)

const timeGreeting = computed(() => {
  const h = new Date().getHours()
  if (h < 12) return 'Good morning'
  if (h < 17) return 'Good afternoon'
  return 'Good evening'
})

const profileFields = computed(() => [
  { label: 'Email',      value: user.email },
  { label: 'Phone',      value: user.phone },
  { label: 'Gender',     value: user.gender },
  { label: 'Country',    value: user.country_id },
  { label: 'City',       value: user.city_id },
  { label: 'Last Login', value: user.last_login_at },
])

// --- Icons ---
const IconCalendar = { template: `<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2"/><path d="M16 2v4M8 2v4M3 10h18"/></svg>` }
const IconCheck    = { template: `<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="20 6 9 17 4 12"/></svg>` }
const IconClock    = { template: `<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><path d="M12 6v6l4 2"/></svg>` }
const IconStar     = { template: `<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>` }

const stats = [
  { label: 'Total Stays',    value: totalStays || 0 ,    badge: '+2 this year', icon: IconCalendar, iconClass: 'bg-indigo-50 dark:bg-indigo-950 text-indigo-600 dark:text-indigo-400',   badgeClass: 'text-indigo-500 dark:text-indigo-400' },
  { label: 'Completed',      value: completedStays || 0,    badge: '83% rate',     icon: IconCheck,    iconClass: 'bg-emerald-50 dark:bg-emerald-950 text-emerald-600 dark:text-emerald-400', badgeClass: 'text-emerald-600 dark:text-emerald-400' },
  { label: 'Upcoming',       value: upcomingStays || 0,     badge: 'Next: Apr 10', icon: IconClock,    iconClass: 'bg-orange-50 dark:bg-orange-950 text-orange-600 dark:text-orange-400',   badgeClass: 'text-orange-500 dark:text-orange-400' },
  { label: 'Loyalty Points', value: loyaltyStatus || 0, badge: 'Gold tier',    icon: IconStar,     iconClass: 'bg-yellow-50 dark:bg-yellow-950 text-yellow-600 dark:text-yellow-400',   badgeClass: 'text-yellow-600 dark:text-yellow-400' },
]

// --- Active Reservation ---
const activeReservation = page.props.activeReservation || null

// --- Reservations Table ---
const reservations = ref([
  { id: 1, room: 'Deluxe Ocean Suite',  checkIn: 'Apr 10', checkOut: 'Apr 15', nights: 5, total: '1,250', status: 'Confirmed' },
  { id: 2, room: 'Standard King Room',  checkIn: 'Feb 20', checkOut: 'Feb 23', nights: 3, total: '510',   status: 'Confirmed' },
  { id: 3, room: 'Executive Suite',     checkIn: 'Jan 5',  checkOut: 'Jan 8',  nights: 3, total: '870',   status: 'Confirmed' },
  { id: 4, room: 'Twin Garden View',    checkIn: 'Dec 22', checkOut: 'Dec 26', nights: 4, total: '680',   status: 'Confirmed' },
  { id: 5, room: 'Penthouse Suite',     checkIn: 'Nov 14', checkOut: 'Nov 15', nights: 1, total: '450',   status: 'Cancelled' },
])

const activeFilter = ref('All')
const filteredReservations = computed(() => {
  if (activeFilter.value === 'All') return reservations.value
  return reservations.value.filter(r => r.status === activeFilter.value)
})
</script>
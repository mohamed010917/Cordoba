<script setup lang="ts">
import { ref, computed } from 'vue'
import { Head, Link, router, usePage } from '@inertiajs/vue3'
import AppLayout from '@/layouts/AppLayout.vue'
import { BreadcrumbItem } from '@/types'

// ── Custom v-click-outside directive ──
const vClickOutside = {
    mounted(el: any, binding: any) {
        el._clickOutsideHandler = (event: MouseEvent) => {
            if (!el.contains(event.target as Node)) {
                binding.value(event)
            }
        }
        document.addEventListener('mousedown', el._clickOutsideHandler)
    },
    unmounted(el: any) {
        document.removeEventListener('mousedown', el._clickOutsideHandler)
    },
}

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'managers', href: '/admin/managers' },
]

const page = usePage()

const filters = ref({
    search: '',
    ban: '',
    active: '',
})

const hasActiveFilters = computed(() =>
    filters.value.search || filters.value.ban || filters.value.active
)

const clearFilters = () => {
    filters.value = { search: '', ban: '', active: '' }
    router.get('/admin/managers', {}, { preserveState: true, replace: true })
}

const applyFilters = () => {
    const query: Record<string, string> = {}
    if (filters.value.search) query.search = filters.value.search
    if (filters.value.ban)    query.ban    = filters.value.ban
    if (filters.value.active) query.active = filters.value.active
    router.get('/admin/managers', query, { preserveState: true, replace: true })
}

const toggleMenu = (user: any) => {
    page.props.managers.data.forEach((u: any) => { if (u.id !== user.id) u.open = false })
    user.open = !user.open
}

const closeMenu = (user: any) => { user.open = false }

const toggleBan = (user: any) => {
    router.post(`/admin/users/${user.id}/toggle-ban`, {}, {
        onSuccess: () => console.log('Ban toggled'),
        onError:   (e) => console.error(e),
    })
}

const deleteUser = (user: any) => {
  
    router.post(`/admin/managers/${user.id}`, { _method: 'delete' }, {
        onSuccess: () => console.log('Deleted'),
        onError:   (e) => console.error(e),
    })
}

const changeRole = (user: any, role: string) => {
    router.post(`/admin/users/${user.id}/toggle-role`, { role }, {
        onSuccess: () => console.log('Role changed'),
        onError:   (e) => console.error(e),
    })
}

const getRoleBadge = (role: string) => ({
    admin:   'badge-purple',
    manager: 'badge-blue',
    user:    'badge-gray',
}[role] ?? 'badge-gray')

const formatRole = (role: string) => ({
    admin:   'Administrator',
    manager: 'Manager',
    user:    'Regular User',
}[role] ?? role)

const getBanBadge   = (banned: boolean)  => banned ? 'badge-red'   : 'badge-green'
const getActiveBadge = (active: boolean) => active ? 'badge-green' : 'badge-gray'
</script>

<template>
    <Head title="managers Management" />
    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="px-4 sm:px-6 lg:px-8 py-6 space-y-5">

            <!-- ── Header ── -->
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900 dark:text-white tracking-tight">
                        managers Management
                    </h1>
                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-0.5">
                        Manage managers, roles, and account status
                    </p>
                </div>
                <Link
                    href="/admin/managers/create"
                    class="inline-flex items-center gap-2 px-4 py-2 bg-indigo-600 hover:bg-indigo-700 active:bg-indigo-800 text-white text-sm font-medium rounded-lg shadow-sm transition-colors"
                >
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    Add User
                </Link>
            </div>

            <!-- ── Filters ── -->
            <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-4 shadow-sm">
                <div class="flex flex-col sm:flex-row flex-wrap gap-3">

                    <!-- Search -->
                    <div class="relative flex-1 min-w-[180px]">
                        <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                        <input
                            v-model="filters.search"
                            type="text"
                            placeholder="Search name or email…"
                            @keyup.enter="applyFilters"
                            class="w-full pl-9 pr-4 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition"
                        />
                    </div>

                    <!-- Ban Filter -->
                    <div class="relative min-w-[140px]">
                        <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/>
                        </svg>
                        <select
                            v-model="filters.ban"
                            class="w-full pl-9 pr-8 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition appearance-none"
                        >
                            <option value="">All Ban Status</option>
                            <option value="1">Banned</option>
                            <option value="0">Not Banned</option>
                        </select>
                        <svg class="absolute right-2 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                        </svg>
                    </div>

                    <!-- Active Filter -->
                    <div class="relative min-w-[140px]">
                        <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <select
                            v-model="filters.active"
                            class="w-full pl-9 pr-8 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition appearance-none"
                        >
                            <option value="">All Status</option>
                            <option value="1">Active</option>
                            <option value="0">Inactive</option>
                        </select>
                        <svg class="absolute right-2 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                        </svg>
                    </div>

                    <!-- Actions -->
                    <div class="flex gap-2">
                        <button
                            @click="applyFilters"
                            class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-lg transition-colors"
                        >
                            Search
                        </button>
                        <button
                            v-if="hasActiveFilters"
                            @click="clearFilters"
                            class="px-4 py-2 text-sm text-gray-600 dark:text-gray-300 hover:text-red-600 dark:hover:text-red-400 border border-gray-300 dark:border-gray-600 rounded-lg hover:border-red-400 dark:hover:border-red-500 transition-colors"
                        >
                            Clear
                        </button>
                    </div>
                </div>
            </div>

            <!-- ── Table (fully responsive via horizontal scroll) ── -->
            <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full min-w-[700px] text-sm">
                        <thead>
                            <tr class="bg-gray-50 dark:bg-gray-700/60 border-b border-gray-200 dark:border-gray-700">
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                    User
                                </th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                    Email
                                </th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                    Role
                                </th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                    Ban
                                </th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                    Status
                                </th>
                                <th class="px-4 py-3 text-right text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                    Actions
                                </th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 dark:divide-gray-700">

                            <!-- Empty state -->
                            <tr v-if="!page.props.managers?.data?.length">
                                <td colspan="6" class="px-4 py-12 text-center text-gray-400 dark:text-gray-500">
                                    <svg class="w-10 h-10 mx-auto mb-3 opacity-40" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    </svg>
                                    No managers found
                                </td>
                            </tr>

                            <tr
                                v-for="user in page.props.managers?.data"
                                :key="user.id"
                                class="hover:bg-gray-50 dark:hover:bg-gray-700/40 transition-colors"
                            >
                                <!-- User -->
                                <td class="px-4 py-3">
                                    <div class="flex items-center gap-3">
                                        <img
                                            :src="user.image || ''"
                                            :alt="user.name"
                                            class="w-9 h-9 rounded-full object-cover ring-2 ring-gray-200 dark:ring-gray-600 flex-shrink-0"
                                            @error="(e: any) => e.target.src = ''"
                                        />
                                        <span class="font-medium text-gray-900 dark:text-white whitespace-nowrap">
                                            {{ user.name }}
                                        </span>
                                    </div>
                                </td>

                                <!-- Email -->
                                <td class="px-4 py-3 text-gray-500 dark:text-gray-400 whitespace-nowrap">
                                    {{ user.email }}
                                </td>

                                <!-- Role -->
                                <td class="px-4 py-3">
                                    <span :class="['badge', getRoleBadge(user.role)]">
                                        {{ formatRole(user.role) }}
                                    </span>
                                </td>

                                <!-- Ban -->
                                <td class="px-4 py-3">
                                    <span :class="['badge', getBanBadge(user.is_banned)]">
                                        <span class="w-1.5 h-1.5 rounded-full inline-block mr-1.5"
                                              :class="user.is_banned ? 'bg-red-500' : 'bg-green-500'"></span>
                                        {{ user.is_banned ? 'Banned' : 'Clean' }}
                                    </span>
                                </td>

                                <!-- Active -->
                                <td class="px-4 py-3">
                                    <span :class="['badge', getActiveBadge(user.is_active)]">
                                        <span class="w-1.5 h-1.5 rounded-full inline-block mr-1.5"
                                              :class="user.is_active ? 'bg-green-500' : 'bg-gray-400'"></span>
                                        {{ user.is_active ? 'Active' : 'Inactive' }}
                                    </span>
                                </td>

                                <!-- Actions -->
                                <td class="px-4 py-3 text-right">
                                    <div class="relative inline-block">
                                        <button
                                            @click="toggleMenu(user)"
                                            class="p-1.5 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors"
                                            :class="{ 'bg-gray-100 dark:bg-gray-700': user.open }"
                                            title="Actions"
                                        >
                                            <svg class="w-5 h-5 text-gray-500 dark:text-gray-400" fill="currentColor" viewBox="0 0 24 24">
                                                <circle cx="12" cy="5"  r="1.5"/>
                                                <circle cx="12" cy="12" r="1.5"/>
                                                <circle cx="12" cy="19" r="1.5"/>
                                            </svg>
                                        </button>

                                        <!-- Dropdown -->
                                        <Transition
                                            enter-active-class="transition ease-out duration-100"
                                            enter-from-class="opacity-0 scale-95"
                                            enter-to-class="opacity-100 scale-100"
                                            leave-active-class="transition ease-in duration-75"
                                            leave-from-class="opacity-100 scale-100"
                                            leave-to-class="opacity-0 scale-95"
                                        >
                                            <div
                                                v-if="user.open"
                                                v-click-outside="() => closeMenu(user)"
                                                class="absolute right-0 mt-1 w-52 bg-white dark:bg-gray-800 rounded-xl shadow-xl border border-gray-200 dark:border-gray-700 z-50 overflow-hidden origin-top-right"
                                            >
                                                <!-- View -->
                                                <Link
                                                    :href="`/admin/managers/${user.id}`"
                                                    class="flex items-center gap-2.5 px-4 py-2.5 text-sm text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors"
                                                >
                                                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                                    </svg>
                                                    View Details
                                                </Link>

                                                <!-- Edit -->
                                                <Link
                                                    :href="`/admin/managers/${user.id}/edit`"
                                                    class="flex items-center gap-2.5 px-4 py-2.5 text-sm text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors"
                                                >
                                                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                                    </svg>
                                                    Edit User
                                                </Link>

                                                <!-- Ban / Unban -->
                                                <button
                                                    @click="toggleBan(user)"
                                                    class="flex items-center gap-2.5 w-full px-4 py-2.5 text-sm text-yellow-600 dark:text-yellow-400 hover:bg-yellow-50 dark:hover:bg-yellow-900/20 transition-colors"
                                                >
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/>
                                                    </svg>
                                                    {{ user.is_banned ? 'Unban User' : 'Ban User' }}
                                                </button>

                                                <!-- Change Role -->
                                                <div class="border-t border-gray-100 dark:border-gray-700">
                                                    <div class="relative px-4 py-2.5">
                                                        <svg class="absolute left-7 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                                        </svg>
                                                        <select
                                                            @change="changeRole(user, ($event.target as HTMLSelectElement).value)"
                                                            class="w-full pl-7 pr-6 py-0 text-sm bg-transparent text-gray-700 dark:text-gray-200 focus:outline-none cursor-pointer appearance-none"
                                                        >
                                                            <option disabled selected value="">Change Role…</option>
                                                            <option value="admin">Administrator</option>
                                                            <option value="manager">Manager</option>
                                                            <option value="user">Regular User</option>
                                                        </select>
                                                    </div>
                                                </div>

                                                <!-- Delete -->
                                                <div class="border-t border-gray-100 dark:border-gray-700">
                                                    <button
                                                        @click="deleteUser(user)"
                                                        class="flex items-center gap-2.5 w-full px-4 py-2.5 text-sm text-red-600 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-900/20 transition-colors"
                                                    >
                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                                        </svg>
                                                        Delete User
                                                    </button>
                                                </div>
                                            </div>
                                        </Transition>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- ── Pagination ── -->
            <div class="flex flex-col sm:flex-row items-center justify-between gap-3">
                <p class="text-sm text-gray-500 dark:text-gray-400">
                    Showing
                    <span class="font-medium text-gray-700 dark:text-gray-200">{{ page.props.managers?.from ?? 0 }}</span>
                    –
                    <span class="font-medium text-gray-700 dark:text-gray-200">{{ page.props.managers?.to ?? 0 }}</span>
                    of
                    <span class="font-medium text-gray-700 dark:text-gray-200">{{ page.props.managers?.total ?? 0 }}</span>
                    managers
                </p>

                <div class="flex flex-wrap gap-1">
                    <template v-for="link in page.props.managers?.links" :key="link.label">
                        <Link
                            v-if="typeof link.url === 'string'"
                            :href="link.url"
                            class="inline-flex items-center px-3 py-1.5 rounded-lg text-sm font-medium border transition-colors"
                            :class="link.active
                                ? 'bg-indigo-600 border-indigo-600 text-white'
                                : 'border-gray-300 dark:border-gray-600 text-gray-600 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700'"
                            v-html="link.label"
                        />
                        <span
                            v-else
                            v-html="link.label"
                            class="px-3 py-1.5 text-sm text-gray-400 dark:text-gray-600 select-none"
                        />
                    </template>
                </div>
            </div>

        </div>
    </AppLayout>
</template>


<script setup lang="ts">
import { ref, computed } from 'vue'
import { Head, Link, router, usePage } from '@inertiajs/vue3'
import AppLayout from '@/layouts/AppLayout.vue';
import { BreadcrumbItem } from '@/types';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'users',
        href: "/admin/users", 
    },
];


const page = usePage()

const filters = ref({
    search: '',
    role: '',
    active: ''
})

const hasActiveFilters = computed(() => {
    return filters.value.search || filters.value.role || filters.value.active
})

const clearFilters = () => {
    filters.value = {
        search: '',
        role: '',
        active: ''
    }
    router.get('/admin/users', {}, {
        preserveState: true,
        replace: true,
    }) ;
}

const sort = (field) => {
    // Implement sorting logic
    console.log('Sort by:', field)
}

const toggleMenu = (user) => {
    // Close all other menus
    if (page.props.users.data) {
        page.props.users.data.forEach(u => {
            if (u.id !== user.id) {
                u.open = false
            }
        })
    }
    user.open = !user.open
}

const closeMenu = (user) => {
    user.open = false
}

const toggleBan = (user : object) => {
    router.post(`/admin/users/${user.id}/toggle-ban`, {}, {
        onSuccess: () => {
            console.log('User ban status toggled successfully')
        },
        onError: (errors) => {
            console.error('Error toggling ban status:', errors)
        }
    })

}

const deleteUser = (user : object ) => {
    router.post(`/admin/users/${user.id }`, {
        _method: 'delete'
    }, {
        onSuccess: () => {
            console.log('User deleted successfully')
        },
        onError: (errors) => {
            console.error('Error deleting user:', errors)
        }
    })
}

const changeRole = (user : object, role ) => {
   router.post(`/admin/users/${user.id}/toggle-role`, {
        role
    }, {
        onSuccess: () => {
            console.log('User role changed successfully')
        },
        onError: (errors) => {
            console.error('Error changing user role:', errors)
        }
    })
}

const handleImageError = (event) => {
    event.target.src = ""
}

const getRoleBadgeClass = (role) => {
    const classes = {
        admin: 'bg-purple-100 text-purple-800 dark:bg-purple-900/30 dark:text-purple-300',
        manager: 'bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-300',
        user: 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300'
    }
    return classes[role] || classes.user
}

const getStatusBadgeClass = (isBanned) => {
    return isBanned
        ? 'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-300'
        : 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-300'
}

const formatRole = (role) => {
    const roles = {
        admin: 'Administrator',
        manager: 'Manager',
        user: 'Regular User'
    }
    return roles[role] || role
}

const applyFilters = function() {
    const query = {}
    if (filters.value.search) query.search = filters.value.search
    if (filters.value.role) query.role = filters.value.role
    if (filters.value.active) query.active = filters.value.active

    router.get('/admin/users', query , {
        preserveState: true,
        replace: true
    }) ;
}
</script>

<template>
    <Head title="Users" />
    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="p-4 sm:p-6 lg:p-8 space-y-6">
            <!-- Header Section -->
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Users Management</h1>
                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                        Manage all users, their roles, and account status
                    </p>
                </div>
                <Link
                    href="/admin/users/create"
                    class="inline-flex items-center px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg transition-colors duration-200 shadow-sm"
                >
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    Add User
                </Link>
            </div>

            <!-- 🔍 Enhanced Filters Section -->
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-4">
                <div class="flex flex-col lg:flex-row gap-4">
                    <!-- Search -->
                    <div class="flex-1 relative">
                        <svg class="absolute left-3 top-1/2 transform -translate-y-1/2 w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                        <input
                            v-model="filters.search"
                            type="text"
                            placeholder="Search by name, email..."
                            class="w-full pl-10 pr-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-700 dark:text-white"
                        />
                    </div>

                    <!-- Role Filter -->
                    <div class="relative">
                        <svg class="absolute left-3 top-1/2 transform -translate-y-1/2 w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                        </svg>
                        <select
                            v-model="filters.role"
                            class="pl-10 pr-8 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-700 dark:text-white appearance-none"
                        >
                            <option value="">All Roles</option>
                            <option value="admin">Administrator</option>
                            <option value="manager">Manager</option>
                            <option value="user">Regular User</option>
                        </select>
                    </div>

                    <!-- Status Filter -->
                    <div class="relative">
                        <svg class="absolute left-3 top-1/2 transform -translate-y-1/2 w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <select
                            v-model="filters.active"
                            class="pl-10 pr-8 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-700 dark:text-white appearance-none"
                        >
                            <option value="">All Status</option>
                            <option value="1">Active</option>
                            <option value="0">Inactive</option>
                        </select>
                    </div>

                    <!-- Clear Filters -->
                    <button
                        @click="clearFilters"
                        class="px-4 py-2 text-gray-600 dark:text-gray-300 hover:text-gray-900 dark:hover:text-white transition-colors"
                        v-if="hasActiveFilters"
                    >

                        Clear Filters
                    </button>
                          <button
                        @click="applyFilters"
                        class="px-4 py-2 text-gray-600 dark:text-gray-300 hover:text-gray-900 dark:hover:text-white transition-colors"
                        v-if="hasActiveFilters"
                    >
                         Filter
                    </button>
                </div>
            </div>

            <!-- 💻 DESKTOP TABLE - Enhanced -->
            <div class="hidden lg:block bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead>
                            <tr class="bg-gray-50 dark:bg-gray-700 border-b border-gray-200 dark:border-gray-600">
                                <th @click="sort('name')" class="px-6 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-300 uppercase tracking-wider cursor-pointer hover:bg-gray-100 dark:hover:bg-gray-600 transition-colors">
                                    <div class="flex items-center space-x-1">
                                        <span>User</span>
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16V4m0 0L3 8m4-4l4 4m6 0v12m0 0l4-4m-4 4l-4-4" />
                                        </svg>
                                    </div>
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-300 uppercase tracking-wider">Email</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-300 uppercase tracking-wider">Avatar</th>
                                <th @click="sort('role')" class="px-6 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-300 uppercase tracking-wider cursor-pointer hover:bg-gray-100 dark:hover:bg-gray-600 transition-colors">
                                    Role
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-300 uppercase tracking-wider">bann</th>

                                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-300 uppercase tracking-wider">Status</th>
                                <th class="px-6 py-3 text-right text-xs font-semibold text-gray-500 dark:text-gray-300 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                            <tr
                                v-for="user in page.props.users.data"
                                :key="user.id"
                                class="hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors duration-150"
                            >

                                <td class="px-6 py-4">
                                    <div class="font-medium text-gray-900 dark:text-white">{{ user.name }}</div>
                                </td>
                                <td class="px-6 py-4 text-gray-600 dark:text-gray-300">
                                    {{ user.email  }}
                                </td>
                                <td class="px-6 py-4">
                                    <img
                                        :src="user.image"
                                        :alt="user.name"
                                        class="w-10 h-10 rounded-full object-cover ring-2 ring-gray-200 dark:ring-gray-600"
                                       
                                    />
                                </td>
                                <td class="px-6 py-4">
                                    <span :class="getRoleBadgeClass(user.role)" class="badge">
                                        {{ formatRole(user.role) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4">
                                    <span :class="getStatusBadgeClass(user.is_banned)" class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium">
                                        <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                            <circle cx="10" cy="10" r="8" />
                                        </svg>
                                        {{ user.is_banned ? 'Banned' : 'not bann' }}
                                    </span>
                                </td>
                                <td class="px-6 py-4">
                                    <span :class="getStatusBadgeClass(user.is_banned)" class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium">
                                        <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                            <circle cx="10" cy="10" r="8" />
                                        </svg>
                                        {{ user.is_active ? 'active' : 'un active' }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <div class="relative inline-block">
                                        <button
                                            @click="toggleMenu(user)"
                                            class="p-2 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors duration-200"
                                            :class="{ 'bg-gray-100 dark:bg-gray-700': user.open }"
                                        >
                                            <svg class="w-5 h-5 text-gray-500 dark:text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 5v.01M12 12v.01M12 19v.01M12 6a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2z" />
                                            </svg>
                                        </button>

                                        <div
                                            v-if="user.open"
                                            @click.away="closeMenu(user)"
                                            class="absolute right-0 mt-2 w-56 bg-white dark:bg-gray-800 rounded-lg shadow-lg border border-gray-200 dark:border-gray-700 z-50 overflow-hidden"
                                        >
                                            <Link
                                                :href="`/admin/users/${user.id}/edit`"
                                                class="flex items-center px-4 py-3 text-sm text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors"
                                            >
                                                <svg class="w-4 h-4 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                                </svg>
                                                Edit User
                                            </Link>

                                            <Link
                                                :href="`/admin/users/${user.id}`"
                                                class="flex items-center px-4 py-3 text-sm text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors"
                                            >
                                                <svg class="w-4 h-4 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                                </svg>
                                                View Details
                                            </Link>

                                            <button
                                                @click="toggleBan(user)"
                                                class="flex items-center w-full px-4 py-3 text-sm text-yellow-600 dark:text-yellow-400 hover:bg-yellow-50 dark:hover:bg-yellow-900/20 transition-colors"
                                            >
                                                <svg class="w-4 h-4 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636" />
                                                </svg>
                                                {{ user.is_banned ? 'Unban User' : 'Ban User' }}
                                            </button>

                                            <div class="border-t border-gray-200 dark:border-gray-700">
                                                <select
                                                    @change="changeRole(user, $event.target.value)"
                                                    class="w-full px-4 py-3 text-sm bg-white dark:bg-gray-800 text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors cursor-pointer"
                                                >
                                                    <option disabled selected>Change Role</option>
                                                    <option value="admin">Administrator</option>
                                                    <option value="manager">Manager</option>
                                                    <option value="user">Regular User</option>
                                                </select>
                                            </div>

                                            <button
                                                @click="deleteUser(user)"
                                                class="flex items-center w-full px-4 py-3 text-sm text-red-600 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-900/20 transition-colors border-t border-gray-200 dark:border-gray-700"
                                            >
                                                <svg class="w-4 h-4 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                </svg>
                                                Delete User
                                            </button>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- 📱 MOBILE CARDS - Enhanced -->
            <div class="lg:hidden space-y-3">
                <div
                    v-for="user in page.props.users.data"
                    :key="user.id"
                    class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden hover:shadow-md transition-shadow duration-200"
                >
                                    <div class="p-4">
                        <div class="flex items-start justify-between">
                            <div class="flex items-center space-x-3">
                                <img
                                    :src="user.image"
                                    :alt="user.name"
                                    class="w-12 h-12 rounded-full object-cover ring-2 ring-gray-200 dark:ring-gray-600"
                                   
                                />
                                <div>
                                    <h3 class="font-semibold text-gray-900 dark:text-white">{{ user.name }}</h3>
                                    <p class="text-sm text-gray-500 dark:text-gray-400">{{ user.email }}</p>
                                </div>
                            </div>

                            <div class="relative">
                                <button
                                    @click="toggleMenu(user)"
                                    class="p-2 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors"
                                >
                                    <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 5v.01M12 12v.01M12 19v.01M12 6a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2z" />
                                    </svg>
                                </button>

                                <div
                                    v-if="user.open"
                                    @click.away="closeMenu(user)"
                                    class="absolute right-0 mt-2 w-48 bg-white dark:bg-gray-800 rounded-lg shadow-lg border border-gray-200 dark:border-gray-700 z-50"
                                >
                                    <Link :href="`/users/${user.id}/edit`" class="flex items-center px-4 py-2 text-sm hover:bg-gray-50 dark:hover:bg-gray-700">
                                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                        </svg>
                                        Edit
                                    </Link>
                                    <button @click="toggleBan(user)" class="flex items-center w-full px-4 py-2 text-sm text-yellow-600 hover:bg-yellow-50">
                                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728L5.636 5.636" />
                                        </svg>
                                        {{ user.is_banned ? 'Unban' : 'Ban' }}
                                    </button>
                                    <button @click="deleteUser(user)" class="flex items-center w-full px-4 py-2 text-sm text-red-600 hover:bg-red-50">
                                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                        </svg>
                                        Delete
                                    </button>
                                </div>
                            </div>
                        </div>

                        <div class="mt-3 pt-3 border-t border-gray-200 dark:border-gray-700">
                            <div class="flex justify-between items-center text-sm">
                                <span :class="getRoleBadgeClass(user.role)" class="badge">
                                    {{ formatRole(user.role) }}
                                </span>
                                <span :class="getStatusBadgeClass(user.is_banned)" class="inline-flex items-center px-2 py-1 rounded-full text-xs">
                                    <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                        <circle cx="10" cy="10" r="8" />
                                    </svg>
                                    {{ user.is_banned ? 'Banned' : 'Active' }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- 🔢 Enhanced Pagination -->
            <div class="flex flex-col sm:flex-row items-center justify-between gap-4 mt-6">
                <div class="text-sm text-gray-600 dark:text-gray-400">
                    Showing {{ page.props.users.from || 0 }} to {{ page.props.users.to || 0 }} of {{ page.props.users.total || 0 }} users
                </div>
                
                <div class="flex flex-wrap gap-2">
                    <template v-for="link in page.props.users.links" :key="link.label">
                        <Link
                                v-if="typeof link.url === 'string'"
                                :href="link.url"
                                class="inline-flex items-center px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-sm font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors duration-200"
                                :class="{
                                    'bg-indigo-50 dark:bg-indigo-900/30 border-indigo-500 text-indigo-600 dark:text-indigo-400': link.active
                                }"
                                v-html="link.label"
                            />
                            <span
                                v-else
                                v-html="link.label"
                                class="px-3 py-2 text-gray-400 dark:text-gray-600"
                            />
                    </template>
                </div>
            </div>
        </div>
    </AppLayout>
</template>


<style scoped>


/* Smooth transitions */
.transition-colors {
    transition-property: background-color, border-color, color, fill, stroke;
    transition-timing-function: cubic-bezier(0.4, 0, 0.2, 1);
    transition-duration: 150ms;
}
</style>


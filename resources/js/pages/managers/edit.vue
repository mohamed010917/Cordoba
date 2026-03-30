<script setup lang="ts">
import { ref, nextTick } from 'vue'
import { Head, useForm, usePage } from '@inertiajs/vue3'
import AppLayout from '@/layouts/AppLayout.vue'

const page = usePage()
const user  = page.props.user
const countries = page.props.countries

const showPassword = ref(false)
const imagePreview = ref<string | null>(user.image ?? null)
const toast = ref<string | null>(null)

const form = useForm({
    name: user.name || '',
    email: user.email || '',
    password: '',
    role: user.role || 'user',
    phone: user.phone || '',
    national_id: user.national_id || '',
    gender: user.gender || '',
    country_id: user.country_id || '',
    image: null as File | null,
    is_active: user.is_active ?? true
})

const submit = () => {
    form.transform((data) => ({
        ...data,
        _method: 'put',
        is_active: data.is_active ? 1 : 0
    })).post(`/admin/managers/${user.id}`, {
        forceFormData: true,
        preserveScroll: true,
        onError: async () => {
            await nextTick()
            document.querySelector('.error-input')?.focus()
            showToast('Fix errors ❌')
        },
        onSuccess: () => showToast('Updated 🎉')
    })
}

const handleFile = (e: Event) => {
    const file = (e.target as HTMLInputElement).files?.[0]
    if (file) {
        form.image = file
        imagePreview.value = URL.createObjectURL(file)
    }
}

const showToast = (msg: string) => {
    toast.value = msg
    setTimeout(() => (toast.value = null), 2500)
}
</script>

<template>
    <Head title="Edit User" />

    <AppLayout>

        <!-- Toast -->
        <transition name="fade">
            <div v-if="toast"
                class="fixed top-5 right-5 z-50 px-4 py-2 rounded-xl bg-black text-white shadow-lg">
                {{ toast }}
            </div>
        </transition>

        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-8">

            <!-- Header -->
            <div class="mb-8">
                <h1 class="text-2xl sm:text-3xl font-bold text-gray-800 dark:text-white">
                    Edit User
                </h1>
                <p class="text-gray-500 dark:text-gray-400">
                    Update user information
                </p>
            </div>

            <!-- Card -->
            <form @submit.prevent="submit"
                class="bg-white dark:bg-gray-900 border dark:border-gray-800 shadow-xl rounded-3xl p-6 sm:p-8 space-y-8">

                <!-- Avatar -->
                <div class="flex flex-col items-center">
                    <div class="relative">
                        <img v-if="imagePreview"
                            :src="imagePreview"
                            class="w-28 h-28 rounded-full object-cover border-4 border-indigo-500 shadow" />

                        <div v-else
                            class="w-28 h-28 flex items-center justify-center rounded-full bg-gray-200 dark:bg-gray-700 text-3xl font-bold">
                            {{ form.name?.charAt(0) }}
                        </div>

                        <label for="file"
                            class="absolute bottom-0 right-0 bg-indigo-600 text-white p-2 rounded-full cursor-pointer shadow">
                            ✏️
                        </label>

                        <input id="file" type="file" hidden @change="handleFile" />
                    </div>
                </div>

                <!-- Form -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                    <!-- Name -->
                    <div>
                        <label class="label">Name</label>
                        <input v-model="form.name"
                            class="input"
                            :class="{ 'error-input': form.errors.name }" />
                        <p v-if="form.errors.name" class="error">{{ form.errors.name }}</p>
                    </div>

                    <!-- Email -->
                    <div>
                        <label class="label">Email</label>
                        <input v-model="form.email"
                            class="input"
                            :class="{ 'error-input': form.errors.email }" />
                        <p v-if="form.errors.email" class="error">{{ form.errors.email }}</p>
                    </div>

                    <!-- Password -->
                    <div class="relative">
                        <label class="label">Password</label>
                        <input
                            v-model="form.password"
                            :type="showPassword ? 'text' : 'password'"
                            class="input pr-10"
                        />
                        <button type="button"
                            @click="showPassword = !showPassword"
                            class="absolute right-3 top-9 text-gray-400">
                            👁️
                        </button>
                    </div>

                    <!-- Phone -->
                    <div>
                        <label class="label">Phone</label>
                        <input v-model="form.phone" class="input" />
                    </div>

                    <!-- Role -->
                    <div>
                        <label class="label">Role</label>
                        <select v-model="form.role" class="input">
                            <option value="admin">Admin</option>
                            <option value="manager">Manager</option>
                            <option value="user">User</option>
                        </select>
                    </div>
                                        <!-- National ID -->
                    <div>
                        <label class="label">National ID</label>
                        <input
                            v-model="form.national_id"
                            class="input"
                            :class="{ 'error-input': form.errors.national_id }"
                        />
                        <p v-if="form.errors.national_id" class="error">
                            {{ form.errors.national_id }}
                        </p>
                    </div>

                    <!-- Gender -->
                    <div>
                        <label class="label">Gender</label>
                        <select
                            v-model="form.gender"
                            class="input"
                            :class="{ 'error-input': form.errors.gender }"
                        >
                            <option value="">Select</option>
                            <option value="male">Male</option>
                            <option value="female">Female</option>
                            <option value="other">Other</option>
                        </select>

                        <p v-if="form.errors.gender" class="error">
                            {{ form.errors.gender }}
                        </p>
                    </div>

                    <!-- Country -->
                    <div>
                        <label class="label">Country</label>
                        <select v-model="form.country_id" class="input">
                            <option value="">Select</option>
                            <option v-for="c in countries" :key="c.id" :value="c.id">
                                {{ c.name }}
                            </option>
                        </select>
                    </div>

                    <!-- Active -->
                    <div class="flex items-center gap-3 mt-4">
                        <input type="checkbox" v-model="form.is_active"
                            class="w-5 h-5 accent-indigo-600" />
                        <span class="text-gray-700 dark:text-gray-300">Active</span>
                    </div>

                </div>

                <!-- Actions -->
                <div class="flex flex-col sm:flex-row justify-end gap-3 pt-6 border-t dark:border-gray-800">

                    <button type="button"
                        @click="$inertia.visit('/admin/managers')"
                        class="btn-secondary">
                        Cancel
                    </button>

                    <button type="submit"
                        :disabled="form.processing"
                        class="btn-primary flex items-center justify-center gap-2">

                        <span v-if="form.processing"
                            class="w-4 h-4 border-2 border-white border-t-transparent rounded-full animate-spin"></span>

                        <span>{{ form.processing ? 'Updating...' : 'Update User' }}</span>
                    </button>

                </div>

            </form>
        </div>
    </AppLayout>
</template>

<style scoped>
/* Inputs */
.input {
    width: 100%;
    padding: 10px 12px;
    border-radius: 12px;
    border: 1px solid #d1d5db;
    background: white;
    transition: 0.2s;
}

.dark .input {
    background: #111827;
    border-color: #374151;
    color: white;
}

.input:focus {
    border-color: #6366f1;
    box-shadow: 0 0 0 3px rgba(99,102,241,0.2);
    outline: none;
}

/* Label */
.label {
    font-size: 13px;
    margin-bottom: 5px;
    display: block;
    color: #6b7280;
}

/* Buttons */
.btn-primary {
    background: #6366f1;
    color: white;
    padding: 10px 18px;
    border-radius: 12px;
}

.btn-primary:hover {
    background: #4f46e5;
}

.btn-secondary {
    background: #e5e7eb;
    padding: 10px 18px;
    border-radius: 12px;
}

.dark .btn-secondary {
    background: #374151;
    color: white;
}

/* Errors */
.error {
    color: red;
    font-size: 12px;
}

.error-input {
    border-color: red !important;
}

/* Animations */
.fade-enter-active, .fade-leave-active {
    transition: 0.3s;
}
.fade-enter-from, .fade-leave-to {
    opacity: 0;
    transform: translateY(-10px);
}
</style>
<script setup lang="ts">
import { ref, nextTick } from 'vue'
import { Head, useForm, usePage } from '@inertiajs/vue3'
import AppLayout from '@/layouts/AppLayout.vue'
import { BreadcrumbItem } from '@/types'

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Create/managers',
        href: "/admin/managers/create",
    },
]

const page = usePage()
const countries = page.props.countries

/* UI States */
const showPassword = ref(false)
const imagePreview = ref<string | null>(null)
const toast = ref<string | null>(null)

/* Form */
const form = useForm({
    name: '',
    email: '',
    password: '',
    role: 'manager',
    phone: '',
    national_id: '',
    gender: '',
    country_id: '',
    image: null as File | null,
    is_active: true
})

/* Submit */
const submit = () => {
    form.post('/admin/managers', {
        onError: async () => {
            await nextTick()
            const el = document.querySelector('.error-input') as HTMLElement
            if (el) el.focus()
            showToast('Please fix errors ❌')
        },
        onSuccess: () => {
            showToast('User created successfully 🎉')
        }
    })
}

/* Image */
const handleFile = (e: Event) => {
    const target = e.target as HTMLInputElement
    const file = target.files?.[0]

    if (file) {
        form.image = file
        imagePreview.value = URL.createObjectURL(file)
    }
}

/* Toast */
const showToast = (msg: string) => {
    toast.value = msg
    setTimeout(() => (toast.value = null), 3000)
}
</script>
<template>
    <Head title="Create User" />

    <AppLayout :breadcrumbs="breadcrumbs">

        <!-- 🔔 Toast -->
        <div v-if="toast"
            class="fixed top-5 right-5 bg-black text-white px-4 py-2 rounded-lg shadow-lg z-50 animate-fade-in">
            {{ toast }}
        </div>

        <div class="p-4 sm:p-6 lg:p-8 max-w-6xl mx-auto animate-fade-in">

            <!-- Header -->
            <div class="mb-8">
                <h1 class="text-3xl font-bold text-gray-900 dark:text-white">
                    Create managers
                </h1>
                <p class="text-gray-500 dark:text-gray-400 mt-1">
                    Add a new manager to your system
                </p>
            </div>

            <!-- Card -->
            <form @submit.prevent="submit"
                class="bg-white/80 dark:bg-gray-900/80 backdrop-blur rounded-3xl shadow-xl border p-6 sm:p-8 space-y-8">

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                    <!-- Name -->
                    <div>
                        <label class="form-label">Name</label>
                        <input v-model="form.name"
                            class="form-input"
                            :class="{ 'error-input': form.errors.name }" />
                        <p v-if="form.errors.name" class="error-text">{{ form.errors.name }}</p>
                    </div>

                    <!-- Email -->
                    <div>
                        <label class="form-label">Email</label>
                        <input v-model="form.email"
                            class="form-input"
                            :class="{ 'error-input': form.errors.email }" />
                        <p v-if="form.errors.email" class="error-text">{{ form.errors.email }}</p>
                    </div>

                    <!-- Password -->
                    <div class="relative">
                        <label class="form-label">Password</label>

                        <input
                            v-model="form.password"
                            :type="showPassword ? 'text' : 'password'"
                            class="form-input pr-10"
                            :class="{ 'error-input': form.errors.password }"
                        />

                        <button type="button"
                            @click="showPassword = !showPassword"
                            class="absolute right-3 top-9">
                            👁️
                        </button>

                        <p v-if="form.errors.password" class="error-text">
                            {{ form.errors.password }}
                        </p>
                    </div>

                    <!-- Phone -->
                    <div>
                        <label class="form-label">Phone</label>
                        <input v-model="form.phone" class="form-input" />
                    </div>

                    <!-- National ID -->
                    <div>
                        <label class="form-label">National ID</label>
                        <input v-model="form.national_id" class="form-input" />
                    </div>

                    <!-- Gender -->
                    <div>
                        <label class="form-label">Gender</label>
                        <select v-model="form.gender" class="form-input">
                            <option value="">Select</option>
                            <option value="male">Male</option>
                            <option value="female">Female</option>
                            <option value="other">Other</option>
                        </select>
                    </div>

       

                    <!-- Country -->
                    <div>
                        <label class="form-label">Country</label>
                        <select v-model="form.country_id"
                            class="form-input"
                            :class="{ 'error-input': form.errors.country_id }">
                            <option value="">Select</option>
                            <option v-for="c in countries" :key="c.id" :value="c.id">
                                {{ c.name }}
                            </option>
                        </select>
                        <p v-if="form.errors.country_id" class="error-text">
                            {{ form.errors.country_id }}
                        </p>
                    </div>

                    <!-- Active -->
                    <div class="flex items-center gap-3">
                        <input type="checkbox" v-model="form.is_active" />
                        <span>Active</span>
                    </div>

                    <!-- Image -->
                    <div class="md:col-span-2">
                        <label class="form-label">Profile Image</label>

                        <div class="border-2 border-dashed rounded-xl p-6 text-center">
                            <input type="file" @change="handleFile" hidden id="file" />
                            <label for="file" class="cursor-pointer">
                                Upload Image
                            </label>

                            <img v-if="imagePreview"
                                :src="imagePreview"
                                class="mt-4 w-24 h-24 rounded-full mx-auto shadow" />
                        </div>
                    </div>

                </div>

                <!-- Buttons -->
                <div class="flex justify-end gap-3 pt-6 border-t">
                    <button type="button"
                        @click="$inertia.visit('/admin/managers')"
                        class="btn-secondary">
                        Cancel
                    </button>

                    <button type="submit"
                        :disabled="form.processing"
                        class="btn-primary">
                        <span v-if="form.processing">Creating...</span>
                        <span v-else>Create User</span>
                    </button>
                </div>

            </form>
        </div>
    </AppLayout>
</template>

<style>
/* Animation */
@keyframes fadeIn {
    from { opacity: 0; transform: translateY(10px); }
    to { opacity: 1; transform: translateY(0); }
}

.animate-fade-in {
    animation: fadeIn 0.4s ease;
}

/* Inputs */
.form-input {
    width: 100%;
    padding: 10px 12px;
    border-radius: 12px;
    border: 1px solid #d1d5db;
    background: white;
    transition: all 0.2s ease;
}

.dark .form-input {
    background: #1f2937;
    border-color: #374151;
    color: white;
}

.form-input:focus {
    outline: none;
    border-color: #6366f1;
    box-shadow: 0 0 0 3px rgba(99,102,241,0.2);
}

/* Label */
.form-label {
    display: block;
    margin-bottom: 6px;
    font-size: 13px;
    font-weight: 500;
    color: #6b7280;
}

/* Buttons */
.btn-primary {
    padding: 10px 18px;
    background: #6366f1;
    color: white;
    border-radius: 12px;
    transition: all 0.2s;
}

.btn-primary:hover {
    background: #4f46e5;
    transform: translateY(-1px);
}

.btn-secondary {
    padding: 10px 18px;
    background: #e5e7eb;
    border-radius: 12px;
}

.dark .btn-secondary {
    background: #374151;
    color: white;
}
.animate-fade-in {
    animation: fadeIn 0.4s ease;
}

@keyframes fadeIn {
    from { opacity: 0; transform: translateY(10px); }
    to { opacity: 1; transform: translateY(0); }
}

.form-input {
    width: 100%;
    padding: 10px;
    border-radius: 12px;
    border: 1px solid #ccc;
}

.form-label {
    font-size: 13px;
    margin-bottom: 5px;
    display: block;
}

.btn-primary {
    background: #6366f1;
    color: white;
    padding: 10px 18px;
    border-radius: 12px;
}

.btn-secondary {
    background: #e5e7eb;
    padding: 10px 18px;
    border-radius: 12px;
}

.error-input {
    border-color: red !important;
}

.error-text {
    color: red;
    font-size: 12px;
    margin-top: 4px;
}
</style>
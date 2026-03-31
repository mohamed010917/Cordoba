<script setup lang="ts">
import { Head, useForm, usePage } from '@inertiajs/vue3'
import { ref, onMounted, watch, nextTick } from 'vue'  
import AppLayout from '@/layouts/AppLayout.vue'

const countries = ref([])
const cities = ref([])

const page = usePage()
const user = page.props.user

onMounted(() => {
  fetch('/api/countries')
    .then(res => res.json())
    .then(data => { countries.value = data })
    .catch(err => console.error('Error fetching countries:', err))


  if (user.country_id) {
    loadCities()
  }
})

function loadCities() {
  if (!form.country_id) return

  fetch(`/api/cities/${form.country_id}`)
    .then(res => res.json())
    .then(data => { cities.value = data })
    .catch(err => console.error('Error fetching cities:', err))
}

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
    city_id: user.city_id || '', 
    image: null as File | null,
    is_active: user.is_active ?? true
})

watch(() => form.country_id, (newVal) => {
  if (newVal) {
    loadCities()
  } else {
    cities.value = []     
    form.city_id = ''       
  }
})

const submit = () => {
    form.transform((data) => ({
        ...data,
        _method: 'put',
        is_active: data.is_active ? 1 : 0
    })).post(`/admin/users/${user.id}`, {
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

        <transition name="fade">
            <div v-if="toast"
                class="fixed top-5 right-5 z-50 px-4 py-2 rounded-xl bg-black text-white shadow-lg">
                {{ toast }}
            </div>
        </transition>

        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-8">

            <div class="mb-8">
                <h1 class="text-2xl sm:text-3xl font-bold text-gray-800 dark:text-white">Edit User</h1>
                <p class="text-gray-500 dark:text-gray-400">Update user information</p>
            </div>

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
                        <input id="file" type="file" accept="image/*" hidden @change="handleFile" />
                    </div>
                
                    <p v-if="form.errors.image" class="error mt-2">{{ form.errors.image }}</p>
                </div>

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
                        <label class="label">Password <span class="text-gray-400 text-xs">(leave blank to keep current)</span></label>
                        <input
                            v-model="form.password"
                            :type="showPassword ? 'text' : 'password'"
                            class="input pr-10"
                            :class="{ 'error-input': form.errors.password }"
                        />
                        <button type="button"
                            @click="showPassword = !showPassword"
                            class="absolute right-3 top-9 text-gray-400">
                            👁️
                        </button>
                      
                        <p v-if="form.errors.password" class="error">{{ form.errors.password }}</p>
                    </div>

                    <!-- Phone -->
                    <div>
                        <label class="label">Phone</label>
                        <input v-model="form.phone"
                            class="input"
                            :class="{ 'error-input': form.errors.phone }" />
                      
                        <p v-if="form.errors.phone" class="error">{{ form.errors.phone }}</p>
                    </div>

                    <!-- Role -->
                    <div>
                        <label class="label">Role</label>
                        <select v-model="form.role"
                            class="input"
                            :class="{ 'error-input': form.errors.role }">
                            <option value="admin">Admin</option>
                            <option value="manager">Manager</option>
                            <option value="user">User</option>
                        </select>
                        <!-- ✅ Added missing role error display -->
                        <p v-if="form.errors.role" class="error">{{ form.errors.role }}</p>
                    </div>

                    <!-- National ID -->
                    <div>
                        <label class="label">National ID</label>
                        <input v-model="form.national_id"
                            class="input"
                            :class="{ 'error-input': form.errors.national_id }" />
                        <p v-if="form.errors.national_id" class="error">{{ form.errors.national_id }}</p>
                    </div>

                    <!-- Gender -->
                    <div>
                        <label class="label">Gender</label>
                        <select v-model="form.gender"
                            class="input"
                            :class="{ 'error-input': form.errors.gender }">
                            <option value="">Select</option>
                            <option value="male">Male</option>
                            <option value="female">Female</option>
                            <option value="other">Other</option>
                        </select>
                        <p v-if="form.errors.gender" class="error">{{ form.errors.gender }}</p>
                    </div>

                    <!-- Country -->
                    <div class="space-y-1">
                        <label class="label">Country</label>
                        <select v-model="form.country_id"
                                class="input"
                                :class="{ 'error-input': form.errors.country_id }">
                            <option value="">Select Country</option>
                            <option v-for="country in countries"
                                    :key="country.id"
                                    :value="country.id">
                                {{ country.name }}
                            </option>
                        </select>
                        <p v-if="form.errors.country_id" class="error">{{ form.errors.country_id }}</p>
                    </div>

                    <!-- City -->
                    <div class="space-y-1">
                        <label class="label">City</label>
                        <select v-model="form.city_id"  
                                class="input"
                                :class="{ 'error-input': form.errors.city_id }"
                                :disabled="!cities.length">  
                            <option value="">Select City</option>
                            <option v-for="city in cities"
                                    :key="city.id"
                                    :value="city.id">
                                {{ city.name }}
                            </option>
                        </select>
                        <p v-if="form.errors.city_id" class="error">{{ form.errors.city_id }}</p>
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
                        @click="$inertia.visit('/admin/users')"
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
.input:disabled {
    opacity: 0.5;          
    cursor: not-allowed;
}
.label {
    font-size: 13px;
    margin-bottom: 5px;
    display: block;
    color: #6b7280;
}
.btn-primary {
    background: #6366f1;
    color: white;
    padding: 10px 18px;
    border-radius: 12px;
    transition: 0.2s;
}
.btn-primary:hover { background: #4f46e5; }
.btn-primary:disabled { opacity: 0.6; cursor: not-allowed; } 
.btn-secondary {
    background: #e5e7eb;
    padding: 10px 18px;
    border-radius: 12px;
}
.dark .btn-secondary {
    background: #374151;
    color: white;
}
.error { color: red; font-size: 12px; }
.error-input { border-color: red !important; }
.fade-enter-active, .fade-leave-active { transition: 0.3s; }
.fade-enter-from, .fade-leave-to { opacity: 0; transform: translateY(-10px); }
</style>
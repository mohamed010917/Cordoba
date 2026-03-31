<script setup lang="ts">
import { ref, nextTick, onMounted, watch } from 'vue'
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

/* UI States */
const showPassword = ref(false)
const imagePreview = ref<string | null>(null)
const toast = ref<string | null>(null)

/* ✅ API Data */
const countries = ref([])
const cities = ref([])

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
    city_id: '', // ✅ مهم
    image: null as File | null,
    is_active: true
})

/* Submit */
const submit = () => {
    form.post('/admin/managers', {
        forceFormData: true,
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

/* ✅ Load Countries */
onMounted(() => {
  fetch('/api/countries')
    .then(res => res.json())
    .then(data => {
      countries.value = data
    })
    .catch(err => console.error('Error:', err))
})

/* ✅ Load Cities */
function loadCities() {
  if (!form.country_id) {
    cities.value = []
    return
  }

  fetch(`/api/cities/${form.country_id}`)
    .then(res => res.json())
    .then(data => {
      cities.value = data
    })
    .catch(err => console.error('Error:', err))
}

/* ✅ Watch Country */
watch(() => form.country_id, (newVal) => {
  if (newVal) {
    loadCities()
  } else {
    cities.value = []
  }
})
</script>

<template>
    <Head title="Create User" />

    <AppLayout :breadcrumbs="breadcrumbs">

        <!-- Toast -->
        <div v-if="toast"
            class="fixed top-5 right-5 bg-black text-white px-4 py-2 rounded-lg shadow-lg z-50">
            {{ toast }}
        </div>

        <div class="p-4 max-w-6xl mx-auto">

            <h1 class="text-3xl font-bold mb-6">
                Create managers
            </h1>

            <form @submit.prevent="submit"
                class="bg-white dark:bg-gray-900 rounded-3xl shadow-xl p-6 space-y-6">

                <div class="grid md:grid-cols-2 gap-4">

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
                    <div>
                        <label class="form-label">Password</label>
                        <input v-model="form.password"
                            :type="showPassword ? 'text' : 'password'"
                            class="form-input"
                            :class="{ 'error-input': form.errors.password }" />
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

                    <!-- City -->
                    <div>
                        <label class="form-label">City</label>
                        <select v-model="form.city_id"
                            class="form-input"
                            :class="{ 'error-input': form.errors.city_id }">
                            <option value="">Select</option>
                            <option v-for="c in cities" :key="c.id" :value="c.id">
                                {{ c.name }}
                            </option>
                        </select>
                        <p v-if="form.errors.city_id" class="error-text">
                            {{ form.errors.city_id }}
                        </p>
                    </div>

                    <!-- Active -->
                    <div class="flex items-center gap-2">
                        <input type="checkbox" v-model="form.is_active" />
                        <span>Active</span>
                    </div>

                    <!-- Image -->
                    <div class="md:col-span-2">
                        <input type="file" @change="handleFile" />
                        <img v-if="imagePreview"
                            :src="imagePreview"
                            class="mt-3 w-20 h-20 rounded-full" />
                    </div>

                </div>

                <div class="flex justify-end gap-3 pt-4">
                    <button type="button"
                        @click="$inertia.visit('/admin/managers')"
                        class="btn-secondary">
                        Cancel
                    </button>

                    <button type="submit"
                        :disabled="form.processing"
                        class="btn-primary">
                        {{ form.processing ? 'Creating...' : 'Create User' }}
                    </button>
                </div>

            </form>
        </div>
    </AppLayout>
</template>

<style>
.form-input {
    width: 100%;
    padding: 10px;
    border-radius: 10px;
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
    border-radius: 10px;
}

.btn-secondary {
    background: #e5e7eb;
    padding: 10px 18px;
    border-radius: 10px;
}

.error-input {
    border-color: red !important;
}

.error-text {
    color: red;
    font-size: 12px;
}
</style>
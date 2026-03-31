<script setup lang="ts">
import { ref, nextTick, onMounted, watch } from 'vue'
import { Head, useForm, usePage } from '@inertiajs/vue3'
import AppLayout from '@/layouts/AppLayout.vue'

const page = usePage()
const user  = page.props.user

const showPassword = ref(false)
const imagePreview = ref<string | null>(user.image ?? null)
const toast = ref<string | null>(null)

// ✅ API data
const countries = ref([])
const cities = ref([])

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

// ✅ submit
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

// ✅ image preview
const handleFile = (e: Event) => {
    const file = (e.target as HTMLInputElement).files?.[0]
    if (file) {
        form.image = file
        imagePreview.value = URL.createObjectURL(file)
    }
}

// ✅ toast
const showToast = (msg: string) => {
    toast.value = msg
    setTimeout(() => (toast.value = null), 2500)
}

// ✅ load countries
onMounted(() => {
  fetch('/api/countries')
    .then(res => res.json())
    .then(data => {
      countries.value = data

      // لو edit → حمل المدن
      if (form.country_id) {
        loadCities()
      }
    })
    .catch(err => console.error(err))
})

// ✅ load cities
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
    .catch(err => console.error(err))
}

// ✅ watch country
watch(() => form.country_id, (newVal) => {
  if (newVal) {
    loadCities()
  } else {
    cities.value = []
  }
})
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

        <div class="max-w-6xl mx-auto px-4 py-8">

            <h1 class="text-2xl font-bold mb-6">Edit User</h1>

            <form @submit.prevent="submit"
                class="bg-white dark:bg-gray-900 shadow-xl rounded-3xl p-6 space-y-6">

                <!-- Avatar -->
                <div class="flex justify-center">
                    <div class="relative">
                        <img v-if="imagePreview"
                             :src="imagePreview"
                             class="w-28 h-28 rounded-full object-cover border-4 border-indigo-500" />

                        <div v-else
                             class="w-28 h-28 flex items-center justify-center rounded-full bg-gray-200">
                            {{ form.name?.charAt(0) }}
                        </div>

                        <input type="file" hidden id="file" @change="handleFile" />
                        <label for="file"
                               class="absolute bottom-0 right-0 bg-indigo-600 text-white p-2 rounded-full cursor-pointer">
                            ✏️
                        </label>
                    </div>
                </div>

                <!-- Form -->
                <div class="grid md:grid-cols-2 gap-4">

                    <div>
                        <label>Name</label>
                        <input v-model="form.name" class="input"
                               :class="{ 'error-input': form.errors.name }" />
                        <p v-if="form.errors.name" class="error">{{ form.errors.name }}</p>
                    </div>

                    <div>
                        <label>Email</label>
                        <input v-model="form.email" class="input"
                               :class="{ 'error-input': form.errors.email }" />
                        <p v-if="form.errors.email" class="error">{{ form.errors.email }}</p>
                    </div>

                    <div>
                        <label>Password</label>
                        <input v-model="form.password"
                               :type="showPassword ? 'text' : 'password'"
                               class="input" />
                    </div>

                    <div>
                        <label>Phone</label>
                        <input v-model="form.phone" class="input" />
                    </div>

                    <div>
                        <label>Country</label>
                        <select v-model="form.country_id" class="input">
                            <option value="">Select Country</option>
                            <option v-for="c in countries" :key="c.id" :value="c.id">
                                {{ c.name }}
                            </option>
                        </select>
                        <p v-if="form.errors.country_id" class="error">
                            {{ form.errors.country_id }}
                        </p>
                    </div>

                    <div>
                        <label>City</label>
                        <select v-model="form.city_id" class="input">
                            <option value="">Select City</option>
                            <option v-for="c in cities" :key="c.id" :value="c.id">
                                {{ c.name }}
                            </option>
                        </select>
                        <p v-if="form.errors.city_id" class="error">
                            {{ form.errors.city_id }}
                        </p>
                    </div>

                </div>

                <!-- Submit -->
                <button type="submit"
                        :disabled="form.processing"
                        class="btn-primary w-full">
                    {{ form.processing ? 'Updating...' : 'Update User' }}
                </button>

            </form>
        </div>
    </AppLayout>
</template>

<style scoped>
.input {
    width: 100%;
    padding: 10px;
    border: 1px solid #ccc;
    border-radius: 10px;
}

.error {
    color: red;
    font-size: 12px;
}

.error-input {
    border-color: red;
}

.btn-primary {
    background: #6366f1;
    color: white;
    padding: 12px;
    border-radius: 10px;
}

.fade-enter-active, .fade-leave-active {
    transition: 0.3s;
}
.fade-enter-from, .fade-leave-to {
    opacity: 0;
}
</style>
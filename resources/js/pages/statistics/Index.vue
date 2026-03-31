<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue'
import { Head, usePage } from '@inertiajs/vue3'
import Chart from 'chart.js/auto'
import { computed, onBeforeUnmount, onMounted, ref } from 'vue'

type User = {
    role: string
}

type ChartPayload = {
    labels: string[]
    data: number[]
}

const page = usePage<{ auth: { user: User } }>()

const rolePrefix = computed(() => {
    return page.props.auth.user.role === 'admin' ? 'admin' : 'manager'
})

const currentYear = new Date().getFullYear()
const yearOptions = Array.from({ length: 8 }, (_, index) => currentYear - index)

const years = ref({
    gender: currentYear,
    revenue: currentYear,
    countries: currentYear,
    topClients: currentYear,
})

const loading = ref({
    gender: false,
    revenue: false,
    countries: false,
    topClients: false,
})

const genderCanvas = ref<HTMLCanvasElement | null>(null)
const revenueCanvas = ref<HTMLCanvasElement | null>(null)
const countriesCanvas = ref<HTMLCanvasElement | null>(null)
const topClientsCanvas = ref<HTMLCanvasElement | null>(null)

let genderChart: Chart | null = null
let revenueChart: Chart | null = null
let countriesChart: Chart | null = null
let topClientsChart: Chart | null = null

function endpoint(path: string, year: number): string {
    return `/${rolePrefix.value}/statistics/${path}?filter[year]=${year}`
}

async function fetchChartData(url: string): Promise<ChartPayload> {
    const response = await fetch(url, {
        credentials: 'same-origin',
        headers: {
            Accept: 'application/json',
        },
    })

    if (!response.ok) {
        throw new Error('Unable to fetch chart data.')
    }

    return response.json()
}

async function loadGender(): Promise<void> {
    loading.value.gender = true

    try {
        const payload = await fetchChartData(endpoint('gender', years.value.gender))

        genderChart?.destroy()

        if (!genderCanvas.value) {
            return
        }

        genderChart = new Chart(genderCanvas.value, {
            type: 'pie',
            data: {
                labels: payload.labels,
                datasets: [
                    {
                        data: payload.data,
                    },
                ],
            },
        })
    } finally {
        loading.value.gender = false
    }
}

async function loadRevenue(): Promise<void> {
    loading.value.revenue = true

    try {
        const payload = await fetchChartData(endpoint('revenue', years.value.revenue))

        revenueChart?.destroy()

        if (!revenueCanvas.value) {
            return
        }

        revenueChart = new Chart(revenueCanvas.value, {
            type: 'line',
            data: {
                labels: payload.labels,
                datasets: [
                    {
                        label: 'Revenue (USD)',
                        data: payload.data,
                        borderWidth: 2,
                        tension: 0.25,
                    },
                ],
            },
        })
    } finally {
        loading.value.revenue = false
    }
}

async function loadCountries(): Promise<void> {
    loading.value.countries = true

    try {
        const payload = await fetchChartData(endpoint('countries', years.value.countries))

        countriesChart?.destroy()

        if (!countriesCanvas.value) {
            return
        }

        countriesChart = new Chart(countriesCanvas.value, {
            type: 'pie',
            data: {
                labels: payload.labels,
                datasets: [
                    {
                        data: payload.data,
                    },
                ],
            },
        })
    } finally {
        loading.value.countries = false
    }
}

async function loadTopClients(): Promise<void> {
    loading.value.topClients = true

    try {
        const payload = await fetchChartData(endpoint('top-clients', years.value.topClients))

        topClientsChart?.destroy()

        if (!topClientsCanvas.value) {
            return
        }

        topClientsChart = new Chart(topClientsCanvas.value, {
            type: 'pie',
            data: {
                labels: payload.labels,
                datasets: [
                    {
                        data: payload.data,
                    },
                ],
            },
        })
    } finally {
        loading.value.topClients = false
    }
}

onMounted(async () => {
    await Promise.all([loadGender(), loadRevenue(), loadCountries(), loadTopClients()])
})

onBeforeUnmount(() => {
    genderChart?.destroy()
    revenueChart?.destroy()
    countriesChart?.destroy()
    topClientsChart?.destroy()
})
</script>

<template>
    <AppLayout>
        <Head title="Statistics" />

        <div class="space-y-6 px-4 py-6 md:px-6">
            <h1 class="text-2xl font-semibold">Statistics</h1>

            <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
                <section class="rounded-lg border p-4">
                    <div class="mb-4 flex items-center justify-between gap-3">
                        <h2 class="font-medium">Male/Female Reservation</h2>
                        <select v-model.number="years.gender" class="rounded-md border px-2 py-1 text-sm" @change="loadGender">
                            <option v-for="year in yearOptions" :key="`gender-${year}`" :value="year">{{ year }}</option>
                        </select>
                    </div>
                    <p v-if="loading.gender" class="mb-2 text-sm text-muted-foreground">Loading...</p>
                    <canvas ref="genderCanvas" />
                </section>

                <section class="rounded-lg border p-4">
                    <div class="mb-4 flex items-center justify-between gap-3">
                        <h2 class="font-medium">Reservations Revenue</h2>
                        <select v-model.number="years.revenue" class="rounded-md border px-2 py-1 text-sm" @change="loadRevenue">
                            <option v-for="year in yearOptions" :key="`revenue-${year}`" :value="year">{{ year }}</option>
                        </select>
                    </div>
                    <p v-if="loading.revenue" class="mb-2 text-sm text-muted-foreground">Loading...</p>
                    <canvas ref="revenueCanvas" />
                </section>

                <section class="rounded-lg border p-4">
                    <div class="mb-4 flex items-center justify-between gap-3">
                        <h2 class="font-medium">Reservations Countries</h2>
                        <select v-model.number="years.countries" class="rounded-md border px-2 py-1 text-sm" @change="loadCountries">
                            <option v-for="year in yearOptions" :key="`countries-${year}`" :value="year">{{ year }}</option>
                        </select>
                    </div>
                    <p v-if="loading.countries" class="mb-2 text-sm text-muted-foreground">Loading...</p>
                    <canvas ref="countriesCanvas" />
                </section>

                <section class="rounded-lg border p-4">
                    <div class="mb-4 flex items-center justify-between gap-3">
                        <h2 class="font-medium">Top Reservations Clients</h2>
                        <select v-model.number="years.topClients" class="rounded-md border px-2 py-1 text-sm" @change="loadTopClients">
                            <option v-for="year in yearOptions" :key="`top-clients-${year}`" :value="year">{{ year }}</option>
                        </select>
                    </div>
                    <p v-if="loading.topClients" class="mb-2 text-sm text-muted-foreground">Loading...</p>
                    <canvas ref="topClientsCanvas" />
                </section>
            </div>
        </div>
    </AppLayout>
</template>

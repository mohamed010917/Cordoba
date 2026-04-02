<script setup lang="ts">
import { computed } from 'vue'
import { Head, Link, usePage } from '@inertiajs/vue3'
import { Badge } from '@/components/ui/badge'
import { Button } from '@/components/ui/button'
import {
    Card,
    CardContent,
    CardDescription,
    CardHeader,
    CardTitle,
} from '@/components/ui/card'
import {
    ArrowRight,
    BedDouble,
    CalendarCheck2,
    KeyRound,
    LayoutDashboard,
    ShieldCheck,
    Sparkles,
    Star,
} from 'lucide-vue-next'
import { dashboard, home, login, pendingApproval, register } from '@/routes'
import rooms from '@/routes/rooms'
import reservations from '@/routes/reservations'

type RoomPreview = {
    id: number
    number: string
    capacity: number
    price_cents: number
    floor?: {
        number: number | null
    } | null
}

type AuthUser = {
    role?: string | null
    is_approved?: boolean | null
} | null

const props = withDefaults(
    defineProps<{
        canRegister: boolean
        availableRoomsCount: number
        featuredRooms: RoomPreview[]
    }>(),
    {
        canRegister: true,
        availableRoomsCount: 0,
        featuredRooms: () => [],
    },
)

const page = usePage<{ auth: { user: AuthUser } }>()

const authUser = computed(() => page.props.auth.user)
const isLoggedIn = computed(() => authUser.value !== null)
const isApprovedClient = computed(
    () => authUser.value?.role === 'user' && Boolean(authUser.value?.is_approved),
)
const isPendingClient = computed(
    () => authUser.value?.role === 'user' && !Boolean(authUser.value?.is_approved),
)
const isStaff = computed(() =>
    ['admin', 'manager', 'receptionist'].includes(authUser.value?.role ?? ''),
)

const formatPrice = (cents: number): string => {
    return new Intl.NumberFormat('en-US', {
        style: 'currency',
        currency: 'USD',
    }).format(cents / 100)
}

const headlineAction = computed(() => {
    if (isApprovedClient.value) {
        return {
            href: dashboard(),
            label: 'Go to dashboard',
        }
    }

    if (isPendingClient.value) {
        return {
            href: pendingApproval(),
            label: 'Check approval status',
        }
    }

    if (isStaff.value) {
        return {
            href: dashboard(),
            label: 'Open staff dashboard',
        }
    }

    return {
        href: login(),
        label: 'Log in to continue',
    }
})

const secondaryAction = computed(() => {
    if (isApprovedClient.value) {
        return {
            href: rooms.index(),
            label: 'Browse all rooms',
        }
    }

    if (isPendingClient.value) {
        return {
            href: pendingApproval(),
            label: 'Pending approval page',
        }
    }

    if (props.canRegister) {
        return {
            href: register(),
            label: 'Create an account',
        }
    }

    return {
        href: login(),
        label: 'View login',
    }
})

const roomAction = (room: RoomPreview) => {
    if (isApprovedClient.value) {
        return {
            href: reservations.create(room.id),
            label: 'Reserve this room',
        }
    }

    if (isPendingClient.value) {
        return {
            href: pendingApproval(),
            label: 'Waiting for approval',
        }
    }

    return {
        href: login(),
        label: 'Log in to reserve',
    }
}

const overviewCards = [
    {
        icon: ShieldCheck,
        title: 'Controlled access',
        description:
            'Clients register first, then the hotel team approves them before booking features unlock.',
    },
    {
        icon: CalendarCheck2,
        title: 'Queued updates',
        description:
            'Approval and reminder emails are sent in the background so the system stays responsive.',
    },
    {
        icon: KeyRound,
        title: 'Simple reservations',
        description:
            'Approved guests can browse rooms, reserve quickly, and manage bookings from one place.',
    },
]

const journeySteps = [
    {
        step: '01',
        title: 'Register',
        description:
            'Guests create an account with their contact and location details.',
    },
    {
        step: '02',
        title: 'Wait for approval',
        description:
            'The receptionist or manager reviews the account and approves it.',
    },
    {
        step: '03',
        title: 'Browse rooms',
        description:
            'Approved users see available rooms, floors, and pricing at a glance.',
    },
    {
        step: '04',
        title: 'Reserve and pay',
        description:
            'Reservation creation and Stripe payment stay inside the booking flow.',
    },
]
</script>

<template>
    <Head title="Cordoba Hotel System" />

    <div class="relative min-h-screen overflow-hidden bg-[#f7f1ea] text-slate-950">
        <div class="pointer-events-none absolute inset-0">
            <div class="absolute -left-28 top-0 h-80 w-80 rounded-full bg-amber-200/50 blur-3xl" />
            <div class="absolute right-0 top-24 h-96 w-96 rounded-full bg-rose-200/40 blur-3xl" />
            <div class="absolute bottom-0 left-1/3 h-72 w-72 rounded-full bg-sky-200/35 blur-3xl" />
        </div>

        <header class="relative border-b border-white/60 bg-white/70 backdrop-blur-xl">
            <div class="mx-auto flex max-w-7xl items-center justify-between px-6 py-4 lg:px-8">
                <Link :href="home()" class="flex items-center gap-3">
                    <span
                        class="flex h-11 w-11 items-center justify-center rounded-2xl bg-slate-950 text-sm font-semibold text-white shadow-lg shadow-slate-900/20"
                    >
                        HS
                    </span>
                    <div>
                        <p class="text-sm font-semibold uppercase tracking-[0.24em] text-slate-500">
                            Cordoba Hotel
                        </p>
                        <p class="text-base font-medium text-slate-900">Simple Hotel System</p>
                    </div>
                </Link>

                <nav class="flex items-center gap-3">
                    <Badge
                        v-if="isPendingClient"
                        variant="outline"
                        class="border-amber-300 bg-amber-50 px-3 py-1 text-amber-800"
                    >
                        Pending approval
                    </Badge>
                    <Badge
                        v-else-if="isApprovedClient"
                        variant="outline"
                        class="border-emerald-300 bg-emerald-50 px-3 py-1 text-emerald-800"
                    >
                        Approved client
                    </Badge>
                    <Badge
                        v-else-if="isStaff"
                        variant="outline"
                        class="border-sky-300 bg-sky-50 px-3 py-1 text-sky-800"
                    >
                        Staff portal
                    </Badge>

                    <template v-if="isLoggedIn">
                        <Button variant="outline" as-child>
                            <Link :href="headlineAction.href">{{ headlineAction.label }}</Link>
                        </Button>
                    </template>
                    <template v-else>
                        <Button variant="outline" as-child>
                            <Link :href="login()">Log in</Link>
                        </Button>
                        <Button v-if="props.canRegister" as-child class="hidden sm:inline-flex">
                            <Link :href="register()">Register</Link>
                        </Button>
                    </template>
                </nav>
            </div>
        </header>

        <main class="relative mx-auto max-w-7xl px-6 pb-16 pt-10 lg:px-8 lg:pb-24 lg:pt-14">
            <section
                class="overflow-hidden rounded-[2rem] border border-white/80 bg-white/85 shadow-[0_30px_80px_-40px_rgba(15,23,42,0.35)] backdrop-blur"
            >
                <div class="grid gap-0 lg:grid-cols-[1.08fr_0.92fr]">
                    <div class="space-y-8 p-8 sm:p-10 lg:p-14">
                        <div class="flex flex-wrap items-center gap-3">
                            <Badge class="bg-slate-950 px-4 py-1.5 text-white">
                                <Sparkles class="mr-2 h-3.5 w-3.5" />
                                Hotel landing page
                            </Badge>
                            <Badge variant="outline" class="border-slate-200 bg-slate-50 px-4 py-1.5">
                                {{ availableRoomsCount }} available rooms
                            </Badge>
                        </div>

                        <div class="space-y-5">
                            <h1
                                class="max-w-3xl text-4xl font-semibold tracking-tight text-slate-950 sm:text-5xl xl:text-6xl"
                            >
                                A clean hotel workflow for guests, staff, and reservations.
                            </h1>
                            <p class="max-w-2xl text-lg leading-8 text-slate-600">
                                Simple Hotel System keeps the booking experience clear:
                                register, wait for approval, browse available rooms, and complete reservations
                                through a staff-controlled flow.
                            </p>
                        </div>

                        <div class="flex flex-wrap gap-3">
                            <Button as-child class="shadow-lg shadow-slate-900/10">
                                <Link :href="headlineAction.href">
                                    {{ headlineAction.label }}
                                    <ArrowRight class="ml-2 h-4 w-4" />
                                </Link>
                            </Button>
                            <Button variant="outline" as-child>
                                <Link :href="secondaryAction.href">{{ secondaryAction.label }}</Link>
                            </Button>
                        </div>

                        <div class="grid gap-4 pt-2 sm:grid-cols-3">
                            <Card class="border-slate-200/80 bg-slate-50/90 shadow-none">
                                <CardHeader class="space-y-2 pb-3">
                                <CardDescription>Workflow</CardDescription>
                                    <CardTitle class="text-2xl">Approval first</CardTitle>
                                </CardHeader>
                                <CardContent class="text-sm leading-6 text-slate-600">
                                    Pending clients see a waiting page until the hotel team approves them.
                                </CardContent>
                            </Card>
                            <Card class="border-slate-200/80 bg-slate-50/90 shadow-none">
                                <CardHeader class="space-y-2 pb-3">
                                <CardDescription>Availability</CardDescription>
                                <CardTitle class="text-2xl">{{ availableRoomsCount }}</CardTitle>
                                </CardHeader>
                                <CardContent class="text-sm leading-6 text-slate-600">
                                    Rooms are pulled from the live inventory in real time.
                                </CardContent>
                            </Card>
                            <Card class="border-slate-200/80 bg-slate-50/90 shadow-none">
                                <CardHeader class="space-y-2 pb-3">
                                <CardDescription>Messages</CardDescription>
                                <CardTitle class="text-2xl">Queued</CardTitle>
                                </CardHeader>
                                <CardContent class="text-sm leading-6 text-slate-600">
                                    Approval and reminder emails stay async through the queue.
                                </CardContent>
                            </Card>
                        </div>
                    </div>

                    <aside class="border-t border-slate-200/80 bg-slate-950 p-8 text-white lg:border-l lg:border-t-0 lg:p-10">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm uppercase tracking-[0.24em] text-slate-400">
                                    Current access
                                </p>
                                <h2 class="mt-2 text-2xl font-semibold">
                                    {{ isPendingClient ? 'Waiting for approval' : isApprovedClient ? 'Ready for bookings' : isStaff ? 'Staff workspace' : 'Guest entry' }}
                                </h2>
                            </div>
                            <div class="rounded-2xl bg-white/10 p-3">
                                <LayoutDashboard class="h-6 w-6" />
                            </div>
                        </div>

                        <div class="mt-8 space-y-4 rounded-3xl border border-white/10 bg-white/5 p-5">
                            <div class="flex items-start gap-3">
                                <ShieldCheck class="mt-0.5 h-5 w-5 text-emerald-300" />
                                <div>
                                    <p class="font-medium">Approved clients unlock the guest flow</p>
                                    <p class="mt-1 text-sm leading-6 text-slate-300">
                                        Booking links stay hidden until the hotel team approves the account.
                                    </p>
                                </div>
                            </div>
                            <div class="flex items-start gap-3">
                                <BedDouble class="mt-0.5 h-5 w-5 text-amber-300" />
                                <div>
                                    <p class="font-medium">Rooms stay visible as live inventory</p>
                                    <p class="mt-1 text-sm leading-6 text-slate-300">
                                        The homepage previews the current available rooms straight from the database.
                                    </p>
                                </div>
                            </div>
                            <div class="flex items-start gap-3">
                                <CalendarCheck2 class="mt-0.5 h-5 w-5 text-sky-300" />
                                <div>
                                    <p class="font-medium">Reservations remain simple</p>
                                    <p class="mt-1 text-sm leading-6 text-slate-300">
                                        Clients reserve only after approval, while staff keep the control flow clear.
                                    </p>
                                </div>
                            </div>
                        </div>

                        <div class="mt-8 grid gap-4 sm:grid-cols-2">
                            <div class="rounded-3xl border border-white/10 bg-white/5 p-5">
                                <p class="text-sm uppercase tracking-[0.2em] text-slate-400">Access label</p>
                                <p class="mt-3 text-xl font-semibold">{{ isLoggedIn ? 'Authenticated' : 'Public' }}</p>
                                <p class="mt-1 text-sm leading-6 text-slate-300">
                                    {{ isLoggedIn ? 'Role-aware actions are shown automatically.' : 'Login and registration actions are ready.' }}
                                </p>
                            </div>
                            <div class="rounded-3xl border border-white/10 bg-white/5 p-5">
                                <p class="text-sm uppercase tracking-[0.2em] text-slate-400">Hotel mode</p>
                                <p class="mt-3 text-xl font-semibold">Guest first</p>
                                <p class="mt-1 text-sm leading-6 text-slate-300">
                                    The design keeps the public landing page warm, simple, and product-focused.
                                </p>
                            </div>
                        </div>
                    </aside>
                </div>
            </section>

            <section class="mt-10 grid gap-6 lg:grid-cols-3">
                <Card
                    v-for="card in overviewCards"
                    :key="card.title"
                    class="border-white/80 bg-white/80 shadow-[0_18px_40px_-30px_rgba(15,23,42,0.25)]"
                >
                    <CardHeader>
                        <div class="mb-4 flex h-12 w-12 items-center justify-center rounded-2xl bg-slate-950 text-white">
                            <component :is="card.icon" class="h-5 w-5" />
                        </div>
                        <CardTitle>{{ card.title }}</CardTitle>
                    </CardHeader>
                    <CardContent class="text-sm leading-7 text-slate-600">
                        {{ card.description }}
                    </CardContent>
                </Card>
            </section>

            <section class="mt-14">
                <div class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
                    <div>
                        <p class="text-sm font-semibold uppercase tracking-[0.24em] text-slate-500">
                            Featured rooms
                        </p>
                        <h2 class="mt-2 text-3xl font-semibold tracking-tight text-slate-950">
                            Selected from live availability
                        </h2>
                        <p class="mt-3 max-w-2xl text-slate-600">
                            These are pulled from the current room inventory so the home page reflects the
                            live hotel state instead of a static mockup.
                        </p>
                    </div>
                    <Button variant="outline" as-child>
                        <Link :href="rooms.index()">
                            View all rooms
                        </Link>
                    </Button>
                </div>

                <div class="mt-6 grid gap-6 md:grid-cols-2 xl:grid-cols-3">
                    <Card
                        v-for="room in featuredRooms"
                        :key="room.id"
                        class="overflow-hidden border-slate-200/80 bg-white/85 shadow-[0_20px_50px_-35px_rgba(15,23,42,0.28)]"
                    >
                        <div class="h-2 bg-gradient-to-r from-amber-400 via-rose-400 to-sky-400" />
                        <CardHeader class="space-y-4">
                            <div class="flex items-start justify-between gap-4">
                                <div>
                                    <CardDescription>Room {{ room.number }}</CardDescription>
                                    <CardTitle class="mt-1 text-2xl tracking-tight">
                                        Floor {{ room.floor?.number ?? 'TBA' }}
                                    </CardTitle>
                                </div>
                                <Badge variant="secondary" class="bg-emerald-50 text-emerald-800">
                                    Available
                                </Badge>
                            </div>
                        </CardHeader>
                        <CardContent class="space-y-5">
                            <div class="flex items-center gap-2 text-sm text-slate-600">
                                <BedDouble class="h-4 w-4 text-slate-500" />
                                Capacity for {{ room.capacity }} guests
                            </div>
                            <div>
                                <p class="text-3xl font-semibold text-slate-950">
                                    {{ formatPrice(room.price_cents) }}
                                </p>
                                <p class="text-sm text-slate-500">per night</p>
                            </div>
                            <Button as-child class="w-full">
                                <Link :href="roomAction(room).href">
                                    {{ roomAction(room).label }}
                                    <ArrowRight class="ml-2 h-4 w-4" />
                                </Link>
                            </Button>
                        </CardContent>
                    </Card>

                    <div
                        v-if="featuredRooms.length === 0"
                        class="rounded-[1.75rem] border border-dashed border-slate-300 bg-white/70 p-8 md:col-span-2 xl:col-span-3"
                    >
                        <div class="flex flex-col items-center justify-center gap-4 py-10 text-center">
                            <Star class="h-10 w-10 text-amber-500" />
                            <div class="max-w-xl space-y-2">
                                <h3 class="text-xl font-semibold text-slate-950">No featured rooms yet</h3>
                                <p class="text-slate-600">
                                    The landing page is ready, but the room inventory is still empty. Add a few
                                    rooms from the manager or admin area and they will appear here automatically.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <section class="mt-14 grid gap-6 lg:grid-cols-[0.92fr_1.08fr]">
                <Card class="border-white/80 bg-white/85 shadow-[0_20px_50px_-35px_rgba(15,23,42,0.28)]">
                    <CardHeader>
                            <CardDescription>Guest journey</CardDescription>
                        <CardTitle class="text-2xl">How the flow works</CardTitle>
                    </CardHeader>
                    <CardContent class="space-y-4">
                        <div
                            v-for="step in journeySteps"
                            :key="step.step"
                            class="flex gap-4 rounded-2xl border border-slate-200/80 bg-slate-50/90 p-4"
                        >
                            <div
                                class="flex h-12 w-12 shrink-0 items-center justify-center rounded-2xl bg-slate-950 text-sm font-semibold text-white"
                            >
                                {{ step.step }}
                            </div>
                            <div>
                                <p class="text-base font-semibold text-slate-950">{{ step.title }}</p>
                                <p class="mt-1 text-sm leading-6 text-slate-600">{{ step.description }}</p>
                            </div>
                        </div>
                    </CardContent>
                </Card>

                <Card class="overflow-hidden border-slate-200/80 bg-slate-950 text-white shadow-[0_20px_50px_-35px_rgba(15,23,42,0.28)]">
                    <div class="h-2 bg-gradient-to-r from-amber-400 via-rose-400 to-sky-400" />
                    <CardHeader class="space-y-4">
                        <Badge class="w-fit bg-white/10 px-3 py-1 text-white">
                            {{ isPendingClient ? 'Pending approval' : isApprovedClient ? 'Approved guest' : isStaff ? 'Team access' : 'New visitor' }}
                        </Badge>
                        <CardTitle class="text-2xl text-white">
                            {{ isPendingClient ? 'Your account is waiting for hotel approval.' : 'Everything is ready for the next booking step.' }}
                        </CardTitle>
                        <CardDescription class="max-w-2xl text-slate-300">
                            {{ isPendingClient ? 'You can sign in, but booking features stay hidden until approval is completed.' : 'Use the buttons above to log in, register, or continue to the dashboard when your role allows it.' }}
                        </CardDescription>
                    </CardHeader>
                    <CardContent class="flex flex-wrap gap-3">
                        <Button v-if="isPendingClient" variant="secondary" as-child>
                            <Link :href="pendingApproval()">
                                Pending approval page
                            </Link>
                        </Button>
                        <Button v-else-if="isApprovedClient" variant="secondary" as-child>
                            <Link :href="dashboard()">Open dashboard</Link>
                        </Button>
                        <Button v-else-if="isStaff" variant="secondary" as-child>
                            <Link :href="dashboard()">Open dashboard</Link>
                        </Button>
                        <Button v-else-if="props.canRegister" variant="secondary" as-child>
                            <Link :href="register()">Create account</Link>
                        </Button>
                        <Button variant="outline" class="border-white/15 text-white hover:bg-white/10" as-child>
                            <Link :href="login()">Log in</Link>
                        </Button>
                    </CardContent>
                </Card>
            </section>
        </main>
    </div>
</template>

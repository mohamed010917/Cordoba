<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';

const tips = [
    'Your profile is saved and waiting for approval from the hotel team.',
    'You will receive an email as soon as your account is approved.',
    'After approval, reservation and room booking features will be unlocked automatically.',
];
</script>

<template>
    <Head title="Pending Approval" />

    <AppLayout>
        <div class="mx-auto flex min-h-[70vh] max-w-4xl items-center px-6 py-10">
            <div class="relative w-full overflow-hidden rounded-[28px] border border-amber-200/70 bg-gradient-to-br from-amber-50 via-white to-stone-100 shadow-[0_24px_80px_rgba(120,53,15,0.12)]">
                <div class="absolute -top-20 right-0 h-56 w-56 rounded-full bg-amber-300/25 blur-3xl"></div>
                <div class="absolute bottom-0 left-0 h-48 w-48 rounded-full bg-orange-200/30 blur-3xl"></div>

                <div class="relative grid gap-8 p-8 md:grid-cols-[1.2fr_0.8fr] md:p-12">
                    <div class="space-y-6">
                        <div class="inline-flex items-center gap-2 rounded-full border border-amber-300/70 bg-white/80 px-4 py-2 text-sm font-semibold text-amber-800 shadow-sm">
                            <span class="h-2.5 w-2.5 rounded-full bg-amber-500"></span>
                            Account Pending Approval
                        </div>

                        <div class="space-y-4">
                            <h1 class="max-w-xl font-serif text-4xl leading-tight font-semibold tracking-tight text-stone-900 md:text-5xl">
                                Your hotel account is almost ready.
                            </h1>
                            <p class="max-w-2xl text-base leading-7 text-stone-600 md:text-lg">
                                Your registration was successful, but your client access is still waiting for review by the hotel team. Once they approve your account, you will be able to browse rooms and make reservations.
                            </p>
                        </div>

                        <div class="grid gap-3">
                            <div
                                v-for="tip in tips"
                                :key="tip"
                                class="rounded-2xl border border-stone-200/80 bg-white/85 px-4 py-4 text-sm leading-6 text-stone-700 shadow-sm"
                            >
                                {{ tip }}
                            </div>
                        </div>

                        <div class="flex flex-wrap items-center gap-3">
                            <Link
                                href="/settings/profile"
                                class="inline-flex items-center justify-center rounded-full bg-stone-900 px-5 py-3 text-sm font-semibold text-white transition hover:bg-stone-800"
                            >
                                Review Profile
                            </Link>
                            <Link
                                href="/"
                                class="inline-flex items-center justify-center rounded-full border border-stone-300 bg-white px-5 py-3 text-sm font-semibold text-stone-700 transition hover:border-stone-400 hover:bg-stone-50"
                            >
                                Back to Home
                            </Link>
                        </div>
                    </div>

                    <div class="flex items-center">
                        <div class="w-full rounded-[24px] border border-stone-200/80 bg-white/90 p-6 shadow-sm">
                            <p class="text-xs font-semibold tracking-[0.35em] text-amber-700 uppercase">
                                What Happens Next
                            </p>
                            <div class="mt-6 space-y-5">
                                <div class="flex gap-4">
                                    <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-amber-100 text-sm font-bold text-amber-800">1</div>
                                    <div>
                                        <p class="font-semibold text-stone-900">Hotel team review</p>
                                        <p class="mt-1 text-sm leading-6 text-stone-600">
                                            A receptionist, manager, or admin will review your registration details.
                                        </p>
                                    </div>
                                </div>

                                <div class="flex gap-4">
                                    <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-amber-100 text-sm font-bold text-amber-800">2</div>
                                    <div>
                                        <p class="font-semibold text-stone-900">Approval email</p>
                                        <p class="mt-1 text-sm leading-6 text-stone-600">
                                            You will receive an email notification as soon as your account is approved.
                                        </p>
                                    </div>
                                </div>

                                <div class="flex gap-4">
                                    <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-amber-100 text-sm font-bold text-amber-800">3</div>
                                    <div>
                                        <p class="font-semibold text-stone-900">Booking access unlocked</p>
                                        <p class="mt-1 text-sm leading-6 text-stone-600">
                                            After approval, your reservation history and room booking features will be available automatically.
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>

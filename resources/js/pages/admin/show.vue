<script setup lang="ts">
import { Head, usePage, router } from '@inertiajs/vue3'
import AppLayout from '@/layouts/AppLayout.vue'
import { ref, computed } from 'vue'

const page = usePage()
const user = page.props.user

const loading = ref<string | null>(null)

const approveUser = () => {
    loading.value = 'approve'
    router.post(`/admin/users/${user.id}/approve`, {}, { onFinish: () => loading.value = null })
}
const banUser = () => {
    loading.value = 'ban'
    router.post(`/admin/users/${user.id}/ban`, {}, { onFinish: () => loading.value = null })
}
const activateUser = () => {
    loading.value = 'activate'
    router.post(`/admin/users/${user.id}/activate`, {}, { onFinish: () => loading.value = null })
}
const deleteUser = () => {
    if (confirm('Are you sure you want to delete this user? This action cannot be undone.')) {
        loading.value = 'delete'
        router.delete(`/admin/users/${user.id}`, { onFinish: () => loading.value = null })
    }
}

const statusConfig = computed(() => {
    if (user.banned_at) return { label: 'Banned', class: 'status-banned', dot: '#ef4444' }
    if (user.is_active && user.approved_at) return { label: 'Active', class: 'status-active', dot: '#22c55e' }
    if (!user.approved_at) return { label: 'Pending', class: 'status-pending', dot: '#f59e0b' }
    return { label: 'Inactive', class: 'status-inactive', dot: '#6b7280' }
})

const initials = computed(() => {
    return user.name?.split(' ').map((n: string) => n[0]).slice(0, 2).join('').toUpperCase() || '?'
})
</script>

<template>
    <Head title="User Details" />
    <AppLayout>
        <div class="ud-root">

            <!-- BREADCRUMB -->
            <nav class="ud-breadcrumb">
                <span>Admin</span>
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="m9 18 6-6-6-6"/></svg>
                <span>Users</span>
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="m9 18 6-6-6-6"/></svg>
                <span class="ud-breadcrumb__current">{{ user.name }}</span>
            </nav>

            <!-- HERO CARD -->
            <div class="ud-hero">
                <div class="ud-hero__bg">
                    <div class="ud-hero__blob ud-hero__blob--1"></div>
                    <div class="ud-hero__blob ud-hero__blob--2"></div>
                </div>

                <div class="ud-hero__inner">
                    <!-- Avatar -->
                    <div class="ud-avatar-wrap">
                        <img v-if="user.image" :src="user.image" class="ud-avatar" :alt="user.name" />
                        <div v-else class="ud-avatar ud-avatar--placeholder">{{ initials }}</div>
                        <span class="ud-avatar__dot" :style="{ background: statusConfig.dot }"></span>
                    </div>

                    <!-- Identity -->
                    <div class="ud-hero__identity">
                        <div class="ud-hero__name-row">
                            <h1 class="ud-hero__name">{{ user.name }}</h1>
                            <span class="ud-role-badge">{{ user.role }}</span>
                        </div>
                        <p class="ud-hero__email">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="4" width="20" height="16" rx="2"/><path d="m22 7-8.97 5.7a1.94 1.94 0 0 1-2.06 0L2 7"/></svg>
                            {{ user.email }}
                        </p>
                        <div class="ud-badges">
                            <span :class="['ud-badge', statusConfig.class]">
                                <span class="ud-badge__dot"></span>
                                {{ statusConfig.label }}
                            </span>
                            <span v-if="user.approved_at" class="ud-badge ud-badge--approved">
                                <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M20 6 9 17l-5-5"/></svg>
                                Approved
                            </span>
                            <span v-if="user.banned_at" class="ud-badge ud-badge--banned">
                                <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="12" r="10"/><path d="m4.9 4.9 14.2 14.2"/></svg>
                                Banned
                            </span>
                        </div>
                    </div>

                    <!-- Quick Stats -->
                    <div class="ud-hero__stats">
                        <div class="ud-stat">
                            <span class="ud-stat__val">{{ user.country?.name || '—' }}</span>
                            <span class="ud-stat__lbl">Country</span>
                        </div>
                        <div class="ud-stat__div"></div>
                        <div class="ud-stat">
                            <span class="ud-stat__val">{{ user.gender || '—' }}</span>
                            <span class="ud-stat__lbl">Gender</span>
                        </div>
                        <div class="ud-stat__div"></div>
                        <div class="ud-stat">
                            <span class="ud-stat__val">{{ user.is_active ? 'Yes' : 'No' }}</span>
                            <span class="ud-stat__lbl">Active</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- INFO GRID -->
            <div class="ud-grid">

                <!-- Personal Info -->
                <div class="ud-card">
                    <div class="ud-card__header">
                        <div class="ud-card__icon ud-card__icon--indigo">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                        </div>
                        <h2 class="ud-card__title">Personal Info</h2>
                    </div>
                    <div class="ud-card__body">
                        <div class="ud-row">
                            <span class="ud-row__label">Phone</span>
                            <span class="ud-row__value">{{ user.phone || '—' }}</span>
                        </div>
                        <div class="ud-row">
                            <span class="ud-row__label">National ID</span>
                            <span class="ud-row__value ud-row__value--mono">{{ user.national_id || '—' }}</span>
                        </div>
                        <div class="ud-row">
                            <span class="ud-row__label">Gender</span>
                            <span class="ud-row__value">{{ user.gender || '—' }}</span>
                        </div>
                        <div class="ud-row">
                            <span class="ud-row__label">Country</span>
                            <span class="ud-row__value">{{ user.country?.name || '—' }}</span>
                        </div>
                    </div>
                </div>

                <!-- Relations -->
                <div class="ud-card">
                    <div class="ud-card__header">
                        <div class="ud-card__icon ud-card__icon--violet">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87M16 3.13a4 4 0 0 1 0 7.75"/></svg>
                        </div>
                        <h2 class="ud-card__title">Relations</h2>
                    </div>
                    <div class="ud-card__body">
                        <div class="ud-row">
                            <span class="ud-row__label">Created By</span>
                            <span class="ud-row__value">{{ user.created_by_manager?.name || '—' }}</span>
                        </div>
                        <div class="ud-row">
                            <span class="ud-row__label">Approved By</span>
                            <span class="ud-row__value">{{ user.approved_by?.name || '—' }}</span>
                        </div>
                        <div class="ud-row">
                            <span class="ud-row__label">Banned By</span>
                            <span class="ud-row__value">{{ user.banned_by?.name || '—' }}</span>
                        </div>
                    </div>
                </div>

                <!-- Dates -->
                <div class="ud-card">
                    <div class="ud-card__header">
                        <div class="ud-card__icon ud-card__icon--cyan">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2"/><path d="M16 2v4M8 2v4M3 10h18"/></svg>
                        </div>
                        <h2 class="ud-card__title">Dates</h2>
                    </div>
                    <div class="ud-card__body">
                        <div class="ud-row">
                            <span class="ud-row__label">Created</span>
                            <span class="ud-row__value ud-row__value--mono">{{ user.created_at || '—' }}</span>
                        </div>
                        <div class="ud-row">
                            <span class="ud-row__label">Approved</span>
                            <span class="ud-row__value ud-row__value--mono">{{ user.approved_at || '—' }}</span>
                        </div>
                        <div class="ud-row">
                            <span class="ud-row__label">Banned</span>
                            <span class="ud-row__value ud-row__value--mono">{{ user.banned_at || '—' }}</span>
                        </div>
                    </div>
                </div>

            </div>

            <!-- ACTIONS -->
            <div class="ud-actions">
                <p class="ud-actions__label">Actions</p>
                <div class="ud-actions__btns">

                    <button @click="approveUser" class="ud-btn ud-btn--approve" :disabled="!!loading">
                        <svg v-if="loading !== 'approve'" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M20 6 9 17l-5-5"/></svg>
                        <span class="ud-btn__spinner" v-else></span>
                        Approve
                    </button>

                    <button @click="activateUser" class="ud-btn ud-btn--activate" :disabled="!!loading">
                        <svg v-if="loading !== 'activate'" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M13 2 3 14h9l-1 8 10-12h-9l1-8z"/></svg>
                        <span class="ud-btn__spinner" v-else></span>
                        Activate
                    </button>

                    <button @click="banUser" class="ud-btn ud-btn--ban" :disabled="!!loading">
                        <svg v-if="loading !== 'ban'" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="12" r="10"/><path d="m4.9 4.9 14.2 14.2"/></svg>
                        <span class="ud-btn__spinner" v-else></span>
                        Ban User
                    </button>

                    <button @click="deleteUser" class="ud-btn ud-btn--delete" :disabled="!!loading">
                        <svg v-if="loading !== 'delete'" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6"/><path d="M10 11v6M14 11v6"/><path d="M9 6V4h6v2"/></svg>
                        <span class="ud-btn__spinner" v-else></span>
                        Delete
                    </button>

                </div>
            </div>

        </div>
    </AppLayout>
</template>

<style scoped>
.ud-root {
    --ud-radius: 16px;
    --ud-gap: 20px;
    --ud-accent: #6366f1;
    --ud-accent-violet: #8b5cf6;
    --ud-accent-cyan: #06b6d4;
    --ud-bg: #f1f5f9;
    --ud-surface: #ffffff;
    --ud-surface-2: #f8fafc;
    --ud-border: rgba(0,0,0,0.07);
    --ud-text-primary: #0f172a;
    --ud-text-secondary: #64748b;
    --ud-shadow: 0 4px 24px rgba(0,0,0,0.07);
    --ud-shadow-hover: 0 8px 32px rgba(0,0,0,0.12);

    max-width: 1100px;
    margin: 0 auto;
    padding: 28px 20px 48px;
    font-family: 'Segoe UI', system-ui, sans-serif;
}

.dark .ud-root {
    --ud-bg: #0b0f1a;
    --ud-surface: #111827;
    --ud-surface-2: #1a2235;
    --ud-border: rgba(255,255,255,0.07);
    --ud-text-primary: #f1f5f9;
    --ud-text-secondary: #94a3b8;
    --ud-shadow: 0 4px 24px rgba(0,0,0,0.4);
    --ud-shadow-hover: 0 8px 32px rgba(0,0,0,0.5);
}

/* Breadcrumb */
.ud-breadcrumb {
    display: flex;
    align-items: center;
    gap: 6px;
    font-size: 13px;
    color: var(--ud-text-secondary);
    margin-bottom: 20px;
}
.ud-breadcrumb svg { opacity: 0.5; }
.ud-breadcrumb__current { color: var(--ud-text-primary); font-weight: 600; }

/* Hero */
.ud-hero {
    position: relative;
    overflow: hidden;
    background: var(--ud-surface);
    border-radius: var(--ud-radius);
    box-shadow: var(--ud-shadow);
    border: 1px solid var(--ud-border);
    margin-bottom: var(--ud-gap);
}
.ud-hero__bg { position: absolute; inset: 0; pointer-events: none; overflow: hidden; }
.ud-hero__blob { position: absolute; border-radius: 50%; filter: blur(60px); opacity: 0.12; }
.ud-hero__blob--1 { width: 320px; height: 320px; top: -100px; left: -60px; background: var(--ud-accent); }
.ud-hero__blob--2 { width: 220px; height: 220px; bottom: -80px; right: 40px; background: var(--ud-accent-violet); }
.dark .ud-hero__blob--1 { opacity: 0.2; }
.dark .ud-hero__blob--2 { opacity: 0.15; }

.ud-hero__inner {
    position: relative;
    z-index: 1;
    display: flex;
    align-items: center;
    flex-wrap: wrap;
    gap: 24px;
    padding: 28px 28px 24px;
}

/* Avatar */
.ud-avatar-wrap { position: relative; flex-shrink: 0; }
.ud-avatar {
    width: 96px; height: 96px;
    border-radius: 50%;
    object-fit: cover;
    border: 3px solid var(--ud-accent);
    box-shadow: 0 0 0 6px rgba(99,102,241,0.12);
    display: block;
}
.ud-avatar--placeholder {
    display: flex;
    align-items: center;
    justify-content: center;
    background: linear-gradient(135deg, var(--ud-accent), var(--ud-accent-violet));
    color: #fff;
    font-size: 28px;
    font-weight: 700;
    letter-spacing: -1px;
}
.ud-avatar__dot {
    position: absolute;
    bottom: 4px; right: 4px;
    width: 16px; height: 16px;
    border-radius: 50%;
    border: 3px solid var(--ud-surface);
}

/* Identity */
.ud-hero__identity { flex: 1; min-width: 200px; }
.ud-hero__name-row {
    display: flex;
    align-items: center;
    flex-wrap: wrap;
    gap: 10px;
    margin-bottom: 6px;
}
.ud-hero__name {
    font-size: 24px;
    font-weight: 700;
    color: var(--ud-text-primary);
    margin: 0;
    letter-spacing: -0.5px;
    line-height: 1.2;
}
.ud-role-badge {
    background: linear-gradient(135deg, var(--ud-accent), var(--ud-accent-violet));
    color: #fff;
    font-size: 11px;
    font-weight: 600;
    padding: 3px 10px;
    border-radius: 99px;
    letter-spacing: 0.5px;
    text-transform: uppercase;
}
.ud-hero__email {
    display: flex;
    align-items: center;
    gap: 6px;
    color: var(--ud-text-secondary);
    font-size: 14px;
    margin: 0 0 12px;
}
.ud-badges { display: flex; flex-wrap: wrap; gap: 8px; }
.ud-badge {
    display: inline-flex;
    align-items: center;
    gap: 5px;
    padding: 4px 12px;
    border-radius: 99px;
    font-size: 12px;
    font-weight: 600;
}
.ud-badge__dot { width: 7px; height: 7px; border-radius: 50%; background: currentColor; }
.status-active   { background: rgba(34,197,94,0.12);  color: #16a34a; }
.status-pending  { background: rgba(245,158,11,0.12); color: #d97706; }
.status-banned   { background: rgba(239,68,68,0.12);  color: #dc2626; }
.status-inactive { background: rgba(107,114,128,0.12);color: #6b7280; }
.ud-badge--approved { background: rgba(99,102,241,0.12); color: var(--ud-accent); }
.ud-badge--banned   { background: rgba(239,68,68,0.12);  color: #dc2626; }

/* Stats */
.ud-hero__stats {
    display: flex;
    align-items: center;
    gap: 16px;
    margin-left: auto;
    padding: 16px 20px;
    background: var(--ud-surface-2);
    border-radius: 12px;
    border: 1px solid var(--ud-border);
}
.ud-stat { text-align: center; }
.ud-stat__val { display: block; font-size: 15px; font-weight: 700; color: var(--ud-text-primary); line-height: 1.2; }
.ud-stat__lbl { display: block; font-size: 11px; color: var(--ud-text-secondary); margin-top: 2px; text-transform: uppercase; letter-spacing: 0.5px; }
.ud-stat__div { width: 1px; height: 32px; background: var(--ud-border); }

/* Grid */
.ud-grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: var(--ud-gap);
    margin-bottom: var(--ud-gap);
}

/* Card */
.ud-card {
    background: var(--ud-surface);
    border-radius: var(--ud-radius);
    box-shadow: var(--ud-shadow);
    border: 1px solid var(--ud-border);
    overflow: hidden;
    transition: box-shadow 0.25s ease, transform 0.25s ease;
}
.ud-card:hover { box-shadow: var(--ud-shadow-hover); transform: translateY(-3px); }
.ud-card__header {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 18px 20px 14px;
    border-bottom: 1px solid var(--ud-border);
}
.ud-card__icon {
    width: 36px; height: 36px;
    border-radius: 10px;
    display: flex; align-items: center; justify-content: center;
    flex-shrink: 0;
}
.ud-card__icon--indigo { background: rgba(99,102,241,0.12); color: var(--ud-accent); }
.ud-card__icon--violet { background: rgba(139,92,246,0.12); color: var(--ud-accent-violet); }
.ud-card__icon--cyan   { background: rgba(6,182,212,0.12);  color: var(--ud-accent-cyan); }
.ud-card__title { font-size: 14px; font-weight: 700; color: var(--ud-text-primary); margin: 0; }
.ud-card__body { padding: 8px 0; }

/* Row */
.ud-row {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 10px 20px;
    transition: background 0.15s;
    gap: 12px;
}
.ud-row:hover { background: var(--ud-surface-2); }
.ud-row__label {
    font-size: 12px;
    color: var(--ud-text-secondary);
    font-weight: 500;
    text-transform: uppercase;
    letter-spacing: 0.4px;
    flex-shrink: 0;
}
.ud-row__value { font-size: 13px; color: var(--ud-text-primary); font-weight: 500; text-align: right; word-break: break-all; }
.ud-row__value--mono { font-family: 'SF Mono', 'Fira Code', monospace; font-size: 12px; color: var(--ud-text-secondary); }

/* Actions */
.ud-actions {
    background: var(--ud-surface);
    border-radius: var(--ud-radius);
    box-shadow: var(--ud-shadow);
    border: 1px solid var(--ud-border);
    padding: 20px 24px;
}
.ud-actions__label {
    font-size: 11px; font-weight: 700;
    text-transform: uppercase; letter-spacing: 0.8px;
    color: var(--ud-text-secondary); margin: 0 0 14px;
}
.ud-actions__btns { display: flex; flex-wrap: wrap; gap: 10px; }

/* Buttons */
.ud-btn {
    display: inline-flex;
    align-items: center;
    gap: 7px;
    padding: 10px 20px;
    border-radius: 10px;
    font-size: 13px;
    font-weight: 600;
    border: none;
    cursor: pointer;
    transition: transform 0.18s ease, opacity 0.18s ease, box-shadow 0.18s ease;
}
.ud-btn:disabled { opacity: 0.6; cursor: not-allowed; }
.ud-btn:not(:disabled):hover { transform: translateY(-2px); }

.ud-btn--approve  { background: linear-gradient(135deg,#22c55e,#16a34a); color:#fff; box-shadow:0 4px 14px rgba(34,197,94,0.3); }
.ud-btn--activate { background: linear-gradient(135deg,#6366f1,#4f46e5); color:#fff; box-shadow:0 4px 14px rgba(99,102,241,0.3); }
.ud-btn--ban      { background: linear-gradient(135deg,#f59e0b,#d97706); color:#fff; box-shadow:0 4px 14px rgba(245,158,11,0.3); }
.ud-btn--delete   { background: linear-gradient(135deg,#ef4444,#dc2626); color:#fff; box-shadow:0 4px 14px rgba(239,68,68,0.3); }

.ud-btn--approve:not(:disabled):hover  { box-shadow:0 6px 20px rgba(34,197,94,0.4); }
.ud-btn--activate:not(:disabled):hover { box-shadow:0 6px 20px rgba(99,102,241,0.4); }
.ud-btn--ban:not(:disabled):hover      { box-shadow:0 6px 20px rgba(245,158,11,0.4); }
.ud-btn--delete:not(:disabled):hover   { box-shadow:0 6px 20px rgba(239,68,68,0.4); }

.ud-btn__spinner {
    width: 14px; height: 14px;
    border: 2px solid rgba(255,255,255,0.4);
    border-top-color: #fff;
    border-radius: 50%;
    animation: ud-spin 0.7s linear infinite;
}
@keyframes ud-spin { to { transform: rotate(360deg); } }

/* Responsive */
@media (max-width: 900px) { .ud-grid { grid-template-columns: 1fr 1fr; } }
@media (max-width: 580px) { .ud-grid { grid-template-columns: 1fr; } }

@media (max-width: 700px) {
    .ud-hero__inner { flex-direction: column; text-align: center; padding: 24px 20px; }
    .ud-hero__identity { min-width: unset; }
    .ud-hero__name-row, .ud-hero__email, .ud-badges { justify-content: center; }
    .ud-hero__stats { width: 100%; justify-content: center; margin-left: 0; }
}

@media (max-width: 480px) {
    .ud-root { padding: 16px 12px 40px; }
    .ud-actions__btns { flex-direction: column; }
    .ud-btn { width: 100%; justify-content: center; }
}
</style>
<script setup lang="ts">
import {  Link, usePage } from '@inertiajs/vue3';
import {  BookAIcon, Flower2Icon, LayoutGrid, Magnet, User2Icon } from 'lucide-vue-next';

import { computed } from 'vue';
import {  Users } from 'lucide-vue-next';
import { route } from 'ziggy-js';

import NavMain from '@/components/NavMain.vue';
import NavUser from '@/components/NavUser.vue';
import {
    Sidebar,
    SidebarContent,
    SidebarFooter,
   
} from '@/components/ui/sidebar';
import { dashboard } from '@/routes';
import type { NavItem } from '@/types';

const page = usePage() ;

const role = page.props.auth.user.role ;

const admin: NavItem[] = [
    {
        title: 'Dashboard',
        href: "/admin/dashboard" ,
        icon: LayoutGrid,
    },

    {
        title: 'users',
        href: '/admin/users',
        icon: User2Icon,
    },
    {
        title: 'Mangers',
        href: '/admin/managers',
        icon: User2Icon,
    },
    {
        title: 'floors',
        href:"/admin/floors",
        icon: Flower2Icon,
    },
       {
        title: 'rooms',
        href: "/admin/rooms",
        icon: Magnet,
    },
    {
        title: 'Receptionists',
        href: '/admin/receptionists',
        icon: Users,
    },
];


const manager: NavItem[] = [
    {
        title: 'Dashboard',
        href: "/manager/dashboard",
        icon: LayoutGrid,
    },
       {
        title: 'floors',
        href:"/manager/floors",
        icon: Flower2Icon,
    },
       {
        title: 'rooms',
        href: "/manager/rooms",
        icon: Magnet,
    },
    {
        title: 'Receptionists',
        href: '/manager/receptionists',
        icon: Users,
    },
    {
        title: 'Clients',
        href: '/manager/clients',
        icon: Users,

    },
];

const user: NavItem[] = [
    {
        title: 'Dashboard',
        href: dashboard(),
        icon: LayoutGrid,
    },
      {
        title: 'My Reservations',
        href: "/my-reservations",
        icon: BookAIcon,
    },
    {
        title: 'rooms',
        href: "/rooms",
        icon: User2Icon,
    }
];

const receptionist: NavItem[] = [
    {
        title: 'Dashboard',
        href: '/receptionist/dashboard',
        icon: LayoutGrid,
    },
    {
        title: 'Pending Clients',
        href: '/receptionist/clients/pending',
        icon: Users,
    },
    {
        title: 'My Approved Clients',
        href: '/receptionist/clients/approved',
        icon: Users,
    },
    {
        title: 'Clients Reservations',
        href: '/receptionist/clients/reservations',
        icon: Users,
    },
];
const items = computed(() => {

    if (role === 'admin') {
return admin
}

    if (role === 'manager') {
return manager
}

    if (role === 'admin') return admin
    if (role === 'manager') return manager

   
    if (role === 'receptionist') {
return receptionist
}

    return user
})
</script>

<template>
    <Sidebar collapsible="icon" variant="inset">


        <SidebarContent>

            <NavMain :items="items" />
        </SidebarContent>

        <SidebarFooter>
          
            <NavUser />
        </SidebarFooter>
    </Sidebar>
    <slot />
</template>

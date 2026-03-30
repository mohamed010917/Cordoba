<script setup lang="ts">
import {  Link, usePage } from '@inertiajs/vue3';
import {  BookAIcon, Flower2Icon, LayoutGrid, Magnet, User2Icon } from 'lucide-vue-next';

import { computed } from 'vue';
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
];


const manger: NavItem[] = [
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
        href: "/receptionist/dashboard",
        icon: LayoutGrid,
    },
];

const items = computed(() => {
    if (role === 'admin') {
return admin
}

    if (role === 'manager') {
return manger
}
   
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

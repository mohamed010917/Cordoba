<script setup lang="ts">
import { Link, usePage } from '@inertiajs/vue3';
import { BookOpen, FolderGit2, LayoutGrid } from 'lucide-vue-next';
import AppLogo from '@/components/AppLogo.vue';
import NavFooter from '@/components/NavFooter.vue';
import NavMain from '@/components/NavMain.vue';
import NavUser from '@/components/NavUser.vue';
import {
    Sidebar,
    SidebarContent,
    SidebarFooter,
    SidebarHeader,
    SidebarMenu,
    SidebarMenuButton,
    SidebarMenuItem,
} from '@/components/ui/sidebar';
import { dashboard } from '@/routes';
import type { NavItem } from '@/types';
import { computed } from 'vue';

const page = usePage() ;

const role = page.props.auth.user.role ;

const admin: NavItem[] = [
    {
        title: 'Dashboard',
        href: dashboard(),
        icon: LayoutGrid,
    },
];


const manger: NavItem[] = [
    {
        title: 'Dashboard',
        href: dashboard(),
        icon: LayoutGrid,
    },
];
const user: NavItem[] = [
    {
        title: 'Dashboard',
        href: dashboard(),
        icon: LayoutGrid,
    },
];


const items = computed(() => {
    if (role === 'admin') return admin
    if (role === 'manager') return manger
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

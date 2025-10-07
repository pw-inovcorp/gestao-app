<script setup>
import { Link, usePage } from '@inertiajs/vue3'
import { computed } from 'vue'

const props = defineProps({
    open: Boolean
})

const emit = defineEmits(['close'])

const page = usePage()

const isActive = (route) => {
    return page.url.startsWith(route)
}


const isAdmin = computed(() => {
    return page.props.auth.user?.role === 'admin'
})

const menuItems = computed(() => {
    const items = [
        {
            name: 'Início',
            route: '/home',
            icon: "m2.25 12 8.954-8.955c.44-.439 1.152-.439 1.591 0L21.75 12M4.5 9.75v10.125c0 .621.504 1.125 1.125 1.125H9.75v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21h4.125c.621 0 1.125-.504 1.125-1.125V9.75M8.25 21h8.25",
            permission: null
        },

        {
            name: 'Entidades',
            route: '/entidades',
            icon: "M2.25 21h19.5m-18-18v18m10.5-18v18m6-13.5V21M6.75 6.75h.75m-.75 3h.75m-.75 3h.75m3-6h.75m-.75 3h.75m-.75 3h.75M6.75 21v-3.375c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21M3 3h12m-.75 4.5H21m-3.75 3.75h.008v.008h-.008v-.008Zm0 3h.008v.008h-.008v-.008Zm0 3h.008v.008h-.008v-.008Z",
            permission: null
        },

        {
            name: 'Utilizadores',
            route: '/utilizadores',
            icon: "M15 19.128a9.38 9.38 0 0 0 2.625.372 9.337 9.337 0 0 0 4.121-.952 4.125 4.125 0 0 0-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 0 1 8.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0 1 11.964-3.07M12 6.375a3.375 3.375 0 1 1-6.75 0 3.375 3.375 0 0 1 6.75 0Zm8.25 2.25a2.625 2.625 0 1 1-5.25 0 2.625 2.625 0 0 1 5.25 0Z",
            permission: 'admin'
        },
    ]
    return items.filter(item => {
        if (item.permission === null) return true
        if (item.permission === 'admin') return isAdmin.value
        return false
    })
})


</script>

<template>
    <aside :class="[
        'fixed lg:sticky lg:top-0 inset-y-0 left-0 z-50 w-64 h-screen bg-slate-900',
        'transition-transform',
        open ? 'translate-x-0' : '-translate-x-full lg:translate-x-0'
    ]">
        <div class="h-16 flex items-center justify-between px-6 border-b border-slate-800">
            <Link href="/home" class="text-xl font-bold text-white hover:text-blue-400">
                Gestão-App
            </Link>
            <button @click="emit('close')" class="lg:hidden text-slate-400 hover:text-white">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
                </svg>
            </button>
        </div>

        <nav class="p-4 space-y-1">
            <Link
                v-for="item in menuItems"
                :key="item.route"
                :href="item.route"
                :class="[
                    'flex items-center gap-3 px-4 py-2 rounded-md',
                    isActive(item.route)
                        ? 'bg-slate-700 text-white font-medium'
                        : 'text-slate-400 hover:bg-slate-800 hover:text-white'
                ]"
            >
                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" :d="item.icon" />
                </svg>
                <span>{{ item.name }}</span>
            </Link>
        </nav>
    </aside>
</template>

<script setup>
import { router, usePage } from '@inertiajs/vue3'
import { Button } from '@/components/ui/button'
import Sidebar from '@/components/Sidebar.vue'
import { ref } from 'vue'

const page = usePage()
const sidebarOpen = ref(false)

const logout = () => {
    router.post('/logout')
}
</script>

<template>
    <div class="flex min-h-screen bg-slate-50">

        <div
            v-if="sidebarOpen"
            @click="sidebarOpen = false"
            class="fixed inset-0 bg-black/50 z-40 lg:hidden"
        />


        <Sidebar :open="sidebarOpen" @close="sidebarOpen = false" />


        <div class="flex-1 flex flex-col">
            <header class="h-16 bg-white border-b border-slate-200 flex items-center justify-between px-4 lg:px-6 sticky top-0 z-30">
                <div class="flex items-center gap-3">
                    <button @click="sidebarOpen = !sidebarOpen" class="lg:hidden text-slate-700">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" />
                        </svg>
                    </button>
                    <h1 class="text-lg font-semibold text-slate-900">Dashboard</h1>
                </div>

                <div class="flex items-center gap-4">
                    <span class="text-sm text-slate-600 hidden sm:inline">
                        {{ page.props.auth.user.name }}
                    </span>
                    <Button variant="ghost" size="sm" @click="logout">
                        Sair
                    </Button>
                </div>
            </header>

            <main class="flex-1 p-4 lg:p-6">
                <slot />
            </main>
        </div>
    </div>
</template>

<script setup>
    import { computed, watch } from 'vue'
    import { usePage, router } from '@inertiajs/vue3'
    import { Button } from '@/components/ui/button'
    import Sidebar from '@/components/Sidebar.vue'
    import { ref } from 'vue'

    const page = usePage()
    const sidebarOpen = ref(false)

    const showSuccess = ref(true)
    const showError = ref(true)

    const flashSuccess = computed(() => page.props.flash?.success)
    const flashError = computed(() => page.props.flash?.error)


    watch(flashSuccess, () => showSuccess.value = true)
    watch(flashError, () => showError.value = true)

    const closeSuccess = () => showSuccess.value = false
    const closeError = () => showError.value = false

    const logout = () => {
        router.post('/logout')
    }
</script>

<template>
    <div class="flex h-screen overflow-hidden bg-slate-50">

        <div
            v-if="sidebarOpen"
            @click="sidebarOpen = false"
            class="fixed inset-0 bg-black/50 z-40 lg:hidden"
        />

        <Sidebar :open="sidebarOpen" @close="sidebarOpen = false" />

        <div class="flex-1 flex flex-col min-w-0 overflow-hidden">

            <header class="h-16 bg-white border-b border-slate-200 flex items-center justify-between px-4 lg:px-6 shrink-0 z-30">
                <div class="flex items-center gap-3 min-w-0">
                    <button @click="sidebarOpen = !sidebarOpen" class="lg:hidden text-slate-700 shrink-0">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" />
                        </svg>
                    </button>
                    <h1 class="text-lg font-semibold text-slate-900 truncate">Dashboard</h1>
                </div>

                <div class="flex items-center gap-2 sm:gap-4 shrink-0">
                    <span class="text-sm text-slate-600 hidden sm:inline truncate max-w-[150px]">
                        {{ page.props.auth.user.name }}
                    </span>
                    <Button variant="ghost" size="sm" @click="logout">Sair</Button>
                </div>
            </header>


            <div class="flex-1 overflow-y-auto overflow-x-hidden">

                <div v-if="flashSuccess && showSuccess" class="px-4 lg:px-6 pt-4">
                    <div class="max-w-7xl mx-auto bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-md relative">
                        <span class="pr-8 block">{{ flashSuccess }}</span>
                        <button
                            class="absolute top-3 right-3 text-green-600 hover:text-green-800"
                            @click="closeSuccess"
                            aria-label="Fechar"
                        >
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="h-5 w-5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>
                </div>

                <div v-if="flashError && showError" class="px-4 lg:px-6 pt-4">
                    <div class="max-w-7xl mx-auto bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-md relative">
                        <span class="pr-8 block">{{ flashError }}</span>
                        <button
                            class="absolute top-3 right-3 text-red-600 hover:text-red-800"
                            @click="closeError"
                            aria-label="Fechar"
                        >
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="h-5 w-5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>
                </div>

                <!-- Main Content -->
                <main class="p-4 lg:p-6">
                    <slot />
                </main>
            </div>
        </div>
    </div>
</template>

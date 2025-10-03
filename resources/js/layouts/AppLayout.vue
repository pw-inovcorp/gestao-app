<script setup>
    import { router, usePage } from '@inertiajs/vue3'


    const page = usePage()

    const logout = () => {
        router.post('/logout')
    }
</script>

<template>
    <div class="min-h-screen flex flex-col">
        <main class="flex-grow flex items-center justify-center">
            <slot />
        </main>
    </div>
</template>

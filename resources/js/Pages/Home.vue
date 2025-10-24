<script setup>
import AppLayout from '@/layouts/AppLayout.vue'
import WelcomeHeader from '@/components/WelcomeHeader.vue'
import WeeklyCalendar from '@/components/WeeklyCalendar.vue'
</script>

<template>
    <AppLayout>
        <div class="space-y-6 max-w-7xl mx-auto">
            <WelcomeHeader />

            <WeeklyCalendar />
        </div>
    </AppLayout>
</template>

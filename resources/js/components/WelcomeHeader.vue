<script setup>
import { computed } from 'vue'
import { Card, CardContent } from '@/components/ui/card'
import { usePage } from '@inertiajs/vue3'

const page = usePage()
const user = computed(() => page.props.auth?.user)

const currentDate = computed(() => {
    const date = new Date()
    return date.toLocaleDateString('pt-PT', {
        weekday: 'long',
        year: 'numeric',
        month: 'long',
        day: 'numeric'
    })
})

const greeting = computed(() => {
    const hour = new Date().getHours()
    if (hour < 12) return 'Bom dia'
    if (hour < 19) return 'Boa tarde'
    return 'Boa noite'
})
</script>

<template>
    <Card>
        <CardContent class="pt-6">
            <h1 class="text-2xl sm:text-3xl font-bold text-foreground mb-2">
                {{ greeting }}, {{ user?.name }}!
            </h1>
            <p class="text-sm sm:text-base text-muted-foreground capitalize">
                {{ currentDate }}
            </p>
        </CardContent>
    </Card>
</template>

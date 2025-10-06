<script setup>
import AppLayout from '@/layouts/AppLayout.vue'
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card'
import { usePage } from '@inertiajs/vue3'

const page = usePage()
</script>

<template>
    <AppLayout>
        <Card class="max-w-2xl mx-auto">
            <CardHeader>
                <CardTitle class="text-xl sm:text-2xl">
                    Bem-vindo, {{ page.props.auth.user.name }}!
                </CardTitle>
            </CardHeader>
            <CardContent>
                <p class="text-sm sm:text-base text-muted-foreground">
                    A implementar...
                </p>
            </CardContent>
        </Card>
    </AppLayout>
</template>

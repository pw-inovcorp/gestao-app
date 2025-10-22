<script setup>
    import { useForm, Link } from '@inertiajs/vue3'
    import GuestLayout from '@/layouts/GuestLayout.vue'
    import { Button } from '@/components/ui/button'
    import { Input } from '@/components/ui/input'
    import { Label } from '@/components/ui/label'
    import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card'
    import AppLogo from '@/components/AppLogo.vue'

    defineProps({
        status: String
    })

    const form = useForm({
        email: ''
    })

    const submit = () => {
        form.post('/forgot-password')
}
</script>

<template>
    <GuestLayout>
        <Card class="w-full max-w-md">
            <CardHeader class="text-center space-y-2">
                <AppLogo variant="auth" />
                <CardTitle class="text-2xl font-bold">Gestão-App</CardTitle>
                <CardDescription>Recuperar Password</CardDescription>
            </CardHeader>
            <CardContent>

                <p class="text-sm text-gray-500 mb-6">
                    Esqueceu a sua password? Não há problema. Indique o seu email e enviaremos um link para redefinir a password.
                </p>


                <form @submit.prevent="submit" class="space-y-6">
                    <div class="space-y-2">
                        <Label for="email">Email</Label>
                        <Input
                            id="email"
                            v-model="form.email"
                            type="email"
                            placeholder="seu@email.com"
                            required
                        />
                        <p v-if="form.errors.email" class="text-sm text-red-600">
                            {{ form.errors.email }}
                        </p>
                    </div>

                    <Button type="submit" class="w-full" :disabled="form.processing">
                        <span v-if="form.processing">A enviar...</span>
                        <span v-else>Enviar link de recuperação</span>
                    </Button>
                </form>


                <div class="relative my-6">
                    <div class="absolute inset-0 flex items-center">
                        <span class="w-full border-t" />
                    </div>
                    <div class="relative flex justify-center text-xs uppercase">
                        <span class="bg-background px-2 text-gray-500">
                            Lembrou-se?
                        </span>
                    </div>
                </div>

                <Link href="/login">
                    <Button variant="outline" class="w-full">
                        Voltar ao login
                    </Button>
                </Link>
            </CardContent>
        </Card>
    </GuestLayout>
</template>

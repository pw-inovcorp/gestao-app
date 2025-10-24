<script setup>
    import { useForm, Link } from '@inertiajs/vue3'
    import GuestLayout from '@/layouts/GuestLayout.vue'
    import { Button } from '@/components/ui/button'
    import { Input } from '@/components/ui/input'
    import { Label } from '@/components/ui/label'
    import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card'
    import AppLogo from '@/components/AppLogo.vue'

    const form = useForm({
        name: '',
        email: '',
        password: '',
        password_confirmation: ''
    })

    const submit = () => {
        form.post('/register', {
            onFinish: () => form.reset('password', 'password_confirmation'),
        })
}
</script>

<template>
    <GuestLayout>
        <Card class="w-full max-w-md">
            <CardHeader class="text-center space-y-2">
                <AppLogo variant="auth"/>
                <CardTitle class="text-2xl font-bold">Gestão-App</CardTitle>
                <CardDescription>Entre na sua conta</CardDescription>
            </CardHeader>
            <CardContent>
                <form @submit.prevent="submit" class="space-y-6">

                    <div class="space-y-2">
                        <Label for="name">Nome completo</Label>
                        <Input
                            id="name"
                            v-model="form.name"
                            type="text"
                            placeholder="João Silva"
                            required
                        />
                        <p v-if="form.errors.name" class="text-sm text-red-600">
                            {{ form.errors.name }}
                        </p>
                    </div>


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


                    <div class="space-y-2">
                        <Label for="password">Password</Label>
                        <Input
                            id="password"
                            v-model="form.password"
                            type="password"
                            required
                        />
                        <p v-if="form.errors.password" class="text-sm text-red-600">
                            {{ form.errors.password }}
                        </p>
                    </div>


                    <div class="space-y-2">
                        <Label for="password_confirmation">Confirmar password</Label>
                        <Input
                            id="password_confirmation"
                            v-model="form.password_confirmation"
                            type="password"
                            placeholder="tem que ser igual ao password"
                            required
                        />
                    </div>


                    <Button type="submit" class="w-full" :disabled="form.processing">
                        <span v-if="form.processing">A criar conta...</span>
                        <span v-else>Criar conta</span>
                    </Button>
                </form>


                <div class="relative my-6">
                    <div class="absolute inset-0 flex items-center">
                        <span class="w-full border-t" />
                    </div>
                    <div class="relative flex justify-center text-xs uppercase">
                        <span class="bg-background px-2 text-gray-500">
                            Já tem conta?
                        </span>
                    </div>
                </div>


                <Link href="/login">
                    <Button variant="outline" class="w-full">
                        Entrar na conta
                    </Button>
                </Link>
            </CardContent>
        </Card>
    </GuestLayout>
</template>

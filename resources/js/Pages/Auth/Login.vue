<script setup>
    import { useForm, Link } from '@inertiajs/vue3'
    import GuestLayout from '@/layouts/GuestLayout.vue'
    import { Button } from '@/components/ui/button'
    import { Input } from '@/components/ui/input'
    import { Label } from '@/components/ui/label'
    import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card'
    import { Checkbox } from '@/components/ui/checkbox'
    import AppLogo from '@/components/AppLogo.vue'

    const form = useForm({
        email: '',
        password: '',
        remember: false
    })

    const submit = () => {
        form.post('/login', {
            onFinish: () => form.reset('password'),
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
                        <Label for="email">Email</Label>
                        <Input
                            id="email"
                            v-model="form.email"
                            type="email"
                            placeholder="seu@email.com"
                            required
                            autocomplete="email"
                        />
                        <p v-if="form.errors.email" class="text-sm text-red-600">
                            {{ form.errors.email }}
                        </p>
                    </div>


                    <div class="space-y-2">
                        <div class="flex items-center justify-between">
                            <Label for="password">Password</Label>
                            <Link
                                href="/forgot-password"
                                class="text-sm text-gray-900 hover:underline">

                                Esqueceu a password?
                            </Link>
                        </div>
                        <Input
                            id="password"
                            v-model="form.password"
                            type="password"
                            placeholder="••••••••"
                            required
                            autocomplete="current-password"
                        />
                        <p v-if="form.errors.password" class="text-sm text-red-600">
                            {{ form.errors.password }}
                        </p>
                    </div>

                    <div class="flex items-center space-x-2">
                        <Checkbox id="remember" v-model:checked="form.remember" />
                        <Label for="remember" class="text-sm font-normal cursor-pointer">
                            Lembrar-me
                        </Label>
                    </div>

                    <!-- Submit Button -->
                    <Button type="submit" class="w-full" :disabled="form.processing">
                        <span v-if="form.processing">A entrar...</span>
                        <span v-else>Entrar</span>
                    </Button>
                </form>

                <!-- Divider -->
                <div class="relative my-6">
                    <div class="absolute inset-0 flex items-center">
                        <span class="w-full border-t" />
                    </div>
                    <div class="relative flex justify-center text-xs uppercase">
                        <span class="bg-background px-2 text-gray-500">
                            Não tem conta?
                        </span>
                    </div>
                </div>

                <!-- Register Link -->
                <Link href="/register">
                    <Button variant="outline" class="w-full">
                        Criar nova conta
                    </Button>
                </Link>
            </CardContent>
        </Card>
    </GuestLayout>
</template>

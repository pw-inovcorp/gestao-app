<script setup>
    import AppLayout from '@/layouts/AppLayout.vue'
    import { useForm, Link } from '@inertiajs/vue3'
    import { Button } from '@/components/ui/button'
    import { Input } from '@/components/ui/input'
    import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card'
    import {
        FormControl,
        FormDescription,
        FormField,
        FormItem,
        FormLabel,
        FormMessage,
    } from '@/components/ui/form'
    import {
        Select,
        SelectContent,
        SelectGroup,
        SelectItem,
        SelectTrigger,
        SelectValue,
    } from '@/components/ui/select'
    import { toTypedSchema } from '@vee-validate/zod'
    import * as z from 'zod'
    import { useForm as useVeeForm } from 'vee-validate'

    const props = defineProps({
        user: Object
    })

    const formSchema = toTypedSchema(z.object({
        name: z.string().min(2, 'Nome deve ter no mínimo 2 caracteres').max(255),
        email: z.string().email('Email inválido'),
        mobile: z.string().optional(),
        password: z.string().min(8, 'Password deve ter no mínimo 8 caracteres').optional().or(z.literal('')),
        password_confirmation: z.string().min(8, 'Confirmação de password deve ter no mínimo 8 caracteres'),
        role: z.enum(['admin', 'user']),
        status: z.enum(['active', 'inactive']),
    }).refine((data) => data.password === data.password_confirmation, {
        message: "As passwords não coincidem",
        path: ["password_confirmation"],
    }))

    const form = useVeeForm({
        validationSchema: formSchema,
        initialValues: {
            name: props.user.name,
            email: props.user.email,
            mobile: props.user.mobile || '',
            password: '',
            password_confirmation: '',
            role: props.user.role,
            status: props.user.status,
        },
    })

    const inertiaForm = useForm({
        name: props.user.name,
        email: props.user.email,
        mobile: props.user.mobile || '',
        password: '',
        password_confirmation: '',
        role: props.user.role,
        status: props.user.status
    })

    const onSubmit = form.handleSubmit((values) => {
        inertiaForm.transform(() => values)
            .patch(`/utilizadores/${props.user.id}`)
    })
</script>

<template>
    <AppLayout>
        <div class="max-w-4xl mx-auto space-y-6">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold">Editar Utilizador</h1>
                    <p class="text-slate-600 mt-1">Atualizar dados do utilizador</p>
                </div>
                <Link href="/utilizadores">
                    <Button variant="outline">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                        </svg>
                        Voltar
                    </Button>
                </Link>
            </div>

            <Card>
                <CardHeader>
                    <CardTitle>Dados do Utilizador</CardTitle>
                    <CardDescription>Atualize os dados do utilizador</CardDescription>
                </CardHeader>
                <CardContent>
                    <form @submit="onSubmit" class="space-y-6">
                        <FormField v-slot="{ componentField }" name="name">
                            <FormItem>
                                <FormLabel>Nome completo *</FormLabel>
                                <FormControl>
                                    <Input type="text" placeholder="João Silva" v-bind="componentField" />
                                </FormControl>
                                <FormMessage />
                            </FormItem>
                        </FormField>

                        <FormField v-slot="{ componentField }" name="email">
                            <FormItem>
                                <FormLabel>Email *</FormLabel>
                                <FormControl>
                                    <Input type="email" placeholder="joao@exemplo.com" v-bind="componentField" />
                                </FormControl>
                                <FormMessage />
                            </FormItem>
                        </FormField>

                        <FormField v-slot="{ componentField }" name="mobile">
                            <FormItem>
                                <FormLabel>Telemóvel</FormLabel>
                                <FormControl>
                                    <Input type="text" placeholder="912345678" v-bind="componentField" />
                                </FormControl>
                                <FormMessage />
                            </FormItem>
                        </FormField>

                        <FormField v-slot="{ componentField }" name="password">
                            <FormItem>
                                <FormLabel>Password</FormLabel>
                                <FormControl>
                                    <Input type="password" placeholder="Deixe vazio para manter a atual" v-bind="componentField" />
                                </FormControl>
                                <FormDescription>Deixe vazio se não quiser alterar a password</FormDescription>
                                <FormMessage />
                            </FormItem>
                        </FormField>

                        <FormField v-slot="{ componentField }" name="password_confirmation">
                            <FormItem>
                                <FormLabel>Confirmar Password *</FormLabel>
                                <FormControl>
                                    <Input type="password" placeholder="••••••••" v-bind="componentField" />
                                </FormControl>
                                <FormMessage />
                            </FormItem>
                        </FormField>

                        <div class="grid grid-cols-2 gap-4">
                            <FormField v-slot="{ componentField }" name="role">
                                <FormItem>
                                    <FormLabel>Role *</FormLabel>
                                    <Select v-bind="componentField">
                                        <FormControl>
                                            <SelectTrigger>
                                                <SelectValue placeholder="Selecione o role" />
                                            </SelectTrigger>
                                        </FormControl>
                                        <SelectContent>
                                            <SelectGroup>
                                                <SelectItem value="user">User</SelectItem>
                                                <SelectItem value="admin">Admin</SelectItem>
                                            </SelectGroup>
                                        </SelectContent>
                                    </Select>
                                    <FormMessage />
                                </FormItem>
                            </FormField>

                            <FormField v-slot="{ componentField }" name="status">
                                <FormItem>
                                    <FormLabel>Status *</FormLabel>
                                    <Select v-bind="componentField">
                                        <FormControl>
                                            <SelectTrigger>
                                                <SelectValue placeholder="Selecione o status" />
                                            </SelectTrigger>
                                        </FormControl>
                                        <SelectContent>
                                            <SelectGroup>
                                                <SelectItem value="active">Ativo</SelectItem>
                                                <SelectItem value="inactive">Inativo</SelectItem>
                                            </SelectGroup>
                                        </SelectContent>
                                    </Select>
                                    <FormMessage />
                                </FormItem>
                            </FormField>
                        </div>

                        <div class="flex gap-4 pt-4">
                            <Button type="submit" :disabled="inertiaForm.processing" class="flex-1">
                                <span v-if="inertiaForm.processing">A guardar...</span>
                                <span v-else>Atualizar Utilizador</span>
                            </Button>
                            <Link href="/utilizadores" class="flex-1">
                                <Button type="button" variant="outline" class="w-full">
                                    Cancelar
                                </Button>
                            </Link>
                        </div>
                    </form>
                </CardContent>
            </Card>
        </div>
    </AppLayout>
</template>

<script setup>
import AppLayout from '@/layouts/AppLayout.vue'
import CompanySettingForm from '@/components/CompanySettingForm.vue'
import { useForm, Link } from '@inertiajs/vue3'
import { Button } from '@/components/ui/button'
import { Card, CardContent } from '@/components/ui/card'
import { toTypedSchema } from '@vee-validate/zod'
import * as z from 'zod'
import { useForm as useVeeForm } from 'vee-validate'
import { ref } from 'vue'

const props = defineProps({
    settings: Object
})

const companyFormRef = ref(null)

const formSchema = toTypedSchema(z.object({
    nome: z.string().min(2, 'Nome deve ter no mínimo 2 caracteres').max(255),
    morada: z.string().optional(),
    codigo_postal: z.string().regex(/^\d{4}-\d{3}$/, 'Formato inválido (XXXX-XXX)').optional().or(z.literal('')),
    localidade: z.string().optional(),
    numero_contribuinte: z.string().length(9, 'Deve ter exatamente 9 dígitos')
}))

const veeForm = useVeeForm({
    validationSchema: formSchema,
    initialValues: {
        nome: props.settings.nome || '',
        morada: props.settings.morada || '',
        codigo_postal: props.settings.codigo_postal || '',
        localidade: props.settings.localidade || '',
        numero_contribuinte: props.settings.numero_contribuinte || ''
    }
})

const inertiaForm = useForm({
    nome: props.settings.nome || '',
    morada: props.settings.morada || '',
    codigo_postal: props.settings.codigo_postal || '',
    localidade: props.settings.localidade || '',
    numero_contribuinte: props.settings.numero_contribuinte || '',
    logo: null
})

const onSubmit = veeForm.handleSubmit((values) => {
    const data = {
        _method: 'PATCH',
        nome: values.nome,
        morada: values.morada,
        codigo_postal: values.codigo_postal,
        localidade: values.localidade,
        numero_contribuinte: values.numero_contribuinte
    }

    if (companyFormRef.value?.imageFile) {
        data.logo = companyFormRef.value.imageFile
    }

    if (companyFormRef.value?.imageRemoved) {
        data.remove_logo = '1'
    }

    inertiaForm.transform(() => data).post('/configuracoes/empresa', {
        forceFormData: true,
        onSuccess: () => {
            veeForm.resetForm()
            companyFormRef.value?.removeImage()
        },
        onError: (errors) => console.error('Erros:', errors)
    })
})

const quickLinks = [
    {
        title: 'Artigos',
        description: 'Gerir artigos do sistema',
        route: '/artigos',
        icon: 'M20.25 7.5l-.625 10.632a2.25 2.25 0 01-2.247 2.118H6.622a2.25 2.25 0 01-2.247-2.118L3.75 7.5M10 11.25h4M3.375 7.5h17.25c.621 0 1.125-.504 1.125-1.125v-1.5c0-.621-.504-1.125-1.125-1.125H3.375c-.621 0-1.125.504-1.125 1.125v1.5c0 .621.504 1.125 1.125 1.125z',
        iconColor: 'text-blue-600'
    },
    {
        title: 'Permissões',
        description: 'Gerir grupos de permissões',
        route: '/permissoes',
        icon: 'M16.5 10.5V6.75a4.5 4.5 0 10-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 002.25-2.25v-6.75a2.25 2.25 0 00-2.25-2.25H6.75a2.25 2.25 0 00-2.25 2.25v6.75a2.25 2.25 0 002.25 2.25z',
        iconColor: 'text-purple-600'
    },
    {
        title: 'Logs',
        description: 'Histórico de atividades',
        route: '/logs',
        icon: 'M12 6.042A8.967 8.967 0 006 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 016 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 016-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0018 18a8.967 8.967 0 00-6 2.292m0-14.25v14.25',
        iconColor: 'text-green-600'
    },

    {
        title: 'Calendário: Tipos',
        description: 'Criar/Editar Tipos do Calendário',
        route: '/configuracoes/calendario/tipos',
        icon:'M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m3.75 9v6m3-3H9m1.5-12H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z',
        iconColor: 'text-orange-600'
    },

    {
        title: 'Calendário: Ações',
        description: 'Criar/Editar Ações do Calendário',
        route: '/configuracoes/calendario/acoes',
        icon:'M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z',
        iconColor: 'text-indigo-600'
    },

    {
        title: 'Taxa IVA',
        description: 'Criar/Editar Taxas IVA',
        route: '/configuracoes/iva',
        icon:'M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m0 11.625h4.5m-4.5 2.25h4.5m2.121 1.527c-1.171 1.464-3.07 1.464-4.242 0-1.172-1.465-1.172-3.84 0-5.304 1.171-1.464 3.07-1.464 4.242 0M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z',
        iconColor: 'text-slate-600'
    },
]
</script>

<template>
    <AppLayout>
        <div class="max-w-7xl mx-auto space-y-6">
            <div>
                <h1 class="text-3xl font-bold">Configurações</h1>
                <p class="text-slate-600 mt-1">Gerir configurações da empresa e do sistema</p>
            </div>

            <form @submit.prevent="onSubmit" class="space-y-6">
                <CompanySettingForm ref="companyFormRef" :existing-logo="settings.logo" />

                <div class="pt-4">
                    <Button type="submit" :disabled="inertiaForm.processing">
                        <svg v-if="!inertiaForm.processing" class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                        {{ inertiaForm.processing ? 'A guardar...' : 'Guardar Configurações' }}
                    </Button>
                </div>
            </form>

            <div>
                <h2 class="text-lg font-semibold mb-4">Configurações Avançadas</h2>
                <div class="grid md:grid-cols-3 gap-4">
                    <Link
                        v-for="link in quickLinks"
                        :key="link.route"
                        :href="link.route"
                        class="block"
                    >
                        <Card class="cursor-pointer transition-all hover:bg-slate-100 border h-full">
                            <CardContent class="p-10">
                                <div class="flex items-start gap-4">
                                    <div class="p-3 rounded-lg bg-slate-50 shrink-0">
                                        <svg :class="['w-6 h-6', link.iconColor]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" :d="link.icon" />
                                        </svg>
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <h3 class="font-semibold text-slate-900">{{ link.title }}</h3>
                                        <p class="text-sm text-slate-600 mt-1">{{ link.description }}</p>
                                    </div>
                                </div>
                            </CardContent>
                        </Card>
                    </Link>
                </div>
            </div>
        </div>
    </AppLayout>
</template>

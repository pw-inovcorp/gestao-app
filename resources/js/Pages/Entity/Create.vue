<script setup>
import AppLayout from '@/layouts/AppLayout.vue'
import EntityForm from '@/components/EntityForm.vue'
import { useForm, Link } from '@inertiajs/vue3'
import { Button } from '@/components/ui/button'
import { toTypedSchema } from '@vee-validate/zod'
import * as z from 'zod'
import { useForm as useVeeForm } from 'vee-validate'

const props = defineProps({
    countries: Array
})

const formSchema = toTypedSchema(z.object({
    tipo_cliente: z.boolean().default(false),
    tipo_fornecedor: z.boolean().default(false),
    nome: z.string().min(2, 'Nome deve ter no mínimo 2 caracteres').max(255),
    nif: z.string().min(9, 'NIF deve ter 9 dígitos').max(9),
    country_id: z.string().min(1, 'Selecione um país'),
    morada: z.string().optional(),
    codigo_postal: z.string().regex(/^\d{4}-\d{3}$/, 'Formato: XXXX-XXX').optional().or(z.literal('')),
    localidade: z.string().optional(),
    email: z.string().email('Email inválido').optional().or(z.literal('')),
    telefone: z.string().optional(),
    telemovel: z.string().optional(),
    website: z.string().url('URL inválida').optional().or(z.literal('')),
    observacoes: z.string().optional(),
    consentimento_rgpd: z.boolean().default(false),
    estado: z.enum(['ativo', 'inativo'])
}).refine((data) => data.tipo_cliente || data.tipo_fornecedor, {
    message: "Selecione pelo menos um tipo (Cliente ou Fornecedor)",
    path: ["tipo_cliente"],
}))

const veeForm = useVeeForm({
    validationSchema: formSchema,
    initialValues: {
        tipo_cliente: false,
        tipo_fornecedor: false,
        nome: '',
        nif: '',
        country_id: '',
        morada: '',
        codigo_postal: '',
        localidade: '',
        email: '',
        telefone: '',
        telemovel: '',
        website: '',
        observacoes: '',
        consentimento_rgpd: false,
        estado: 'ativo'
    },
})

const inertiaForm = useForm({
    is_cliente: false,
    is_fornecedor: false,
    nome: '',
    nif: '',
    country_id: '',
    morada: '',
    codigo_postal: '',
    localidade: '',
    email: '',
    telefone: '',
    telemovel: '',
    website: '',
    observacoes: '',
    consentimento_rgpd: false,
    estado: 'ativo'
})

const onSubmit = veeForm.handleSubmit((values) => {
    inertiaForm.transform(() => ({
        is_cliente: values.tipo_cliente,
        is_fornecedor: values.tipo_fornecedor,
        nome: values.nome,
        nif: values.nif,
        country_id: values.country_id,
        morada: values.morada,
        codigo_postal: values.codigo_postal,
        localidade: values.localidade,
        email: values.email,
        telefone: values.telefone,
        telemovel: values.telemovel,
        website: values.website,
        observacoes: values.observacoes,
        consentimento_rgpd: values.consentimento_rgpd,
        estado: values.estado
    })).post('/entidades', {
        onSuccess: () => veeForm.resetForm(),
        onError: (errors) => console.error('Erros:', errors)
    })
})
</script>

<template>
    <AppLayout>
        <div class="max-w-4xl mx-auto space-y-6">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold">Nova Entidade</h1>
                    <p class="text-slate-600 mt-1">Criar nova entidade (Cliente/Fornecedor)</p>
                </div>
                <Link href="/entidades">
                    <Button variant="outline">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                        </svg>
                        Voltar
                    </Button>
                </Link>
            </div>

            <form @submit.prevent="onSubmit" class="space-y-6">
                <EntityForm :countries="countries" />

                <div class="flex gap-4">
                    <Button type="submit" :disabled="inertiaForm.processing" class="flex-1">
                        <span v-if="inertiaForm.processing">A guardar...</span>
                        <span v-else>Criar Entidade</span>
                    </Button>
                    <Link href="/entidades" class="flex-1">
                        <Button type="button" variant="outline" class="w-full">Cancelar</Button>
                    </Link>
                </div>
            </form>
        </div>
    </AppLayout>
</template>

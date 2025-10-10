<script setup>
import AppLayout from '@/layouts/AppLayout.vue'
import ContactForm from '@/components/ContactForm.vue'
import { useForm, Link } from '@inertiajs/vue3'
import { Button } from '@/components/ui/button'
import { toTypedSchema } from '@vee-validate/zod'
import * as z from 'zod'
import { useForm as useVeeForm } from 'vee-validate'

const props = defineProps({
    contact: Object,
    entities: Array,
    contactFunctions: Array
})

const formSchema = toTypedSchema(z.object({
    entity_id: z.string().min(1, 'Selecione uma entidade'),
    nome: z.string().min(2, 'Nome deve ter no mínimo 2 caracteres').max(255),
    apelido: z.string().min(2, 'Apelido deve ter no mínimo 2 caracteres').max(255),
    contact_function_id: z.string().optional(),
    telefone: z.string().optional(),
    telemovel: z.string().optional(),
    email: z.string().email('Email inválido').optional().or(z.literal('')),
    observacoes: z.string().optional(),
    consentimento_rgpd: z.boolean().default(false),
    estado: z.enum(['ativo', 'inativo'])
}))

const veeForm = useVeeForm({
    validationSchema: formSchema,
    initialValues: {
        entity_id: props.contact.entity_id?.toString() || '',
        nome: props.contact.nome || '',
        apelido: props.contact.apelido || '',
        contact_function_id: props.contact.contact_function_id
            ? props.contact.contact_function_id.toString()
            : '',
        telefone: props.contact.telefone || '',
        telemovel: props.contact.telemovel || '',
        email: props.contact.email || '',
        observacoes: props.contact.observacoes || '',
        consentimento_rgpd: !!props.contact.consentimento_rgpd,
        estado: props.contact.estado || 'ativo'
    }
})

const inertiaForm = useForm({
    entity_id: props.contact.entity_id,
    nome: props.contact.nome,
    apelido: props.contact.apelido,
    contact_function_id: props.contact.contact_function_id,
    telefone: props.contact.telefone || '',
    telemovel: props.contact.telemovel || '',
    email: props.contact.email || '',
    observacoes: props.contact.observacoes || '',
    consentimento_rgpd: props.contact.consentimento_rgpd,
    estado: props.contact.estado
})

const onSubmit = veeForm.handleSubmit((values) => {
    inertiaForm.transform(() => ({
        entity_id: values.entity_id,
        nome: values.nome,
        apelido: values.apelido,
        contact_function_id: values.contact_function_id || null,
        telefone: values.telefone,
        telemovel: values.telemovel,
        email: values.email,
        observacoes: values.observacoes,
        consentimento_rgpd: values.consentimento_rgpd,
        estado: values.estado
    })).patch(`/contactos/${props.contact.id}`, {
        onError: (errors) => console.error('Erros:', errors)
    })
})
</script>

<template>
    <AppLayout>
        <div class="max-w-4xl mx-auto space-y-6">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold">Editar Contacto</h1>
                    <p class="text-slate-600 mt-1">
                        Atualizar dados de {{ contact.nome }} {{ contact.apelido }}
                    </p>
                </div>
                <Link href="/contactos">
                    <Button variant="outline">
                        <svg
                            class="w-4 h-4 mr-2"
                            fill="none"
                            stroke="currentColor"
                            viewBox="0 0 24 24"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M10 19l-7-7m0 0l7-7m-7 7h18"
                            />
                        </svg>
                        Voltar
                    </Button>
                </Link>
            </div>

            <form @submit.prevent="onSubmit" class="space-y-6">
                <ContactForm
                    :entities="entities"
                    :contact-functions="contactFunctions"
                />

                <div class="flex gap-4">
                    <Button
                        type="submit"
                        :disabled="inertiaForm.processing"
                        class="flex-1"
                    >
                        <span v-if="inertiaForm.processing">A guardar...</span>
                        <span v-else>Atualizar Contacto</span>
                    </Button>
                    <Link href="/contactos" class="flex-1">
                        <Button type="button" variant="outline" class="w-full">
                            Cancelar
                        </Button>
                    </Link>
                </div>
            </form>
        </div>
    </AppLayout>
</template>

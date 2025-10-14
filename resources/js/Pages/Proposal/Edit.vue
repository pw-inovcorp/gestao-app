<script setup>
import AppLayout from '@/layouts/AppLayout.vue'
import ProposalForm from '@/components/ProposalForm.vue'
import { useForm, Link } from '@inertiajs/vue3'
import { Button } from '@/components/ui/button'
import { toTypedSchema } from '@vee-validate/zod'
import * as z from 'zod'
import { useForm as useVeeForm } from 'vee-validate'
import { ref } from 'vue'

const props = defineProps({
    proposal: Object,
    clients: Array,
    articles: Array,
    suppliers: Array
})

const proposalFormRef = ref(null)


const formSchema = toTypedSchema(z.object({
    cliente_id: z.string().min(1, 'Selecione um cliente'),
    validade: z.string().min(1, 'A data de validade é obrigatória'),
    estado: z.enum(['rascunho', 'fechado'])
}))


const existingItems = props.proposal.items?.map(item => ({
    article_id: item.article_id,
    article: item.article,
    fornecedor_id: item.fornecedor_id,
    quantidade: item.quantidade,
    preco_unitario: parseFloat(item.preco_unitario),
    preco_custo: parseFloat(item.preco_custo || 0),
    subtotal: parseFloat(item.subtotal)
})) || []

const veeForm = useVeeForm({
    validationSchema: formSchema,
    initialValues: {
        cliente_id: props.proposal.cliente_id?.toString() || '',
        validade: props.proposal.validade || '',
        estado: props.proposal.estado || 'rascunho'
    },
})


const inertiaForm = useForm({
    cliente_id: props.proposal.cliente_id,
    validade: props.proposal.validade,
    estado: props.proposal.estado,
    items: []
})


const onSubmit = veeForm.handleSubmit((values) => {
    const items = proposalFormRef.value?.items || []

    if (items.length === 0) {
        alert('Deve adicionar pelo menos um artigo à proposta')
        return
    }

    inertiaForm.transform(() => ({
        cliente_id: values.cliente_id,
        validade: values.validade,
        estado: values.estado,
        items: items.map(item => ({
            article_id: item.article_id,
            fornecedor_id: item.fornecedor_id,
            quantidade: item.quantidade,
            preco_unitario: item.preco_unitario,
            preco_custo: item.preco_custo
        }))
    })).patch(`/propostas/${props.proposal.id}`, {
        onError: (errors) => {
            alert('Erros no formulário:\n' + 'O preço unitário não pode ser nulo')
        }
    })
})
</script>

<template>
    <AppLayout>
        <div class="max-w-6xl mx-auto space-y-6">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold">Editar Proposta</h1>
                    <p class="text-slate-600 mt-1">Atualizar proposta {{ proposal.numero }}</p>
                </div>
                <Link href="/propostas">
                    <Button variant="outline">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                        </svg>
                        Voltar
                    </Button>
                </Link>
            </div>

            <form @submit.prevent="onSubmit" class="space-y-6">
                <ProposalForm
                    ref="proposalFormRef"
                    :clients="clients"
                    :articles="articles"
                    :suppliers="suppliers"
                    :existing-items="existingItems"
                />

                <div class="flex gap-4">
                    <Button type="submit" :disabled="inertiaForm.processing" class="flex-1">
                        <span v-if="inertiaForm.processing">A guardar...</span>
                        <span v-else>Atualizar Proposta</span>
                    </Button>
                    <Link href="/propostas" class="flex-1">
                        <Button type="button" variant="outline" class="w-full">Cancelar</Button>
                    </Link>
                </div>
            </form>
        </div>
    </AppLayout>
</template>

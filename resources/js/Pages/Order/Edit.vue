<script setup>
import AppLayout from '@/layouts/AppLayout.vue'
import OrderForm from '@/components/OrderForm.vue'
import { useForm, Link } from '@inertiajs/vue3'
import { Button } from '@/components/ui/button'
import { toTypedSchema } from '@vee-validate/zod'
import * as z from 'zod'
import { useForm as useVeeForm } from 'vee-validate'
import { ref } from 'vue'

const props = defineProps({
    clients: Array,
    articles: Array,
    suppliers: Array,
    order: Object
})

const orderFormRef = ref(null)


const formSchema = toTypedSchema(z.object({
    cliente_id: z.string().min(1, 'Selecione um cliente'),
    estado: z.enum(['rascunho', 'fechado'])
}))
const veeForm = useVeeForm({
    validationSchema: formSchema,
    initialValues: {
        cliente_id: props.proposal.cliente_id?.toString() || '',
        estado: props.proposal.estado || 'rascunho'
    },
})

const inertiaForm = useForm({
    cliente_id: props.proposal.cliente_id,
    estado: props.proposal.estado,
    items: []
})


const onSubmit = veeForm.handleSubmit((values) => {
    const items = orderFormRef.value?.items || []

    if (items.length === 0) {
        alert('Deve adicionar pelo menos um artigo à encomenda')
        return
    }

    inertiaForm.transform(() => ({
        cliente_id: values.cliente_id,
        estado: values.estado,
        items: items.map(item => ({
            article_id: item.article_id,
            fornecedor_id: item.fornecedor_id,
            quantidade: item.quantidade,
            preco_unitario: item.preco_unitario,
            preco_custo: item.preco_custo
        }))
    })).patch(`/encomendas/${props.order.id}`, {
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
                    <h1 class="text-3xl font-bold">Editar Encomenda</h1>
                    <p class="text-slate-600 mt-1">Editar encomenda {{order.numero}}</p>
                </div>
                <Link href="/encomendas">
                    <Button variant="outline">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                        </svg>
                        Voltar
                    </Button>
                </Link>
            </div>

            <form @submit.prevent="onSubmit" class="space-y-6">
                <OrderForm
                    ref="orderFormRef"
                    :clients="clients"
                    :articles="articles"
                    :suppliers="suppliers"
                />

                <div class="flex gap-4">
                    <Button type="submit" :disabled="inertiaForm.processing" class="flex-1">
                        <span v-if="inertiaForm.processing">A guardar...</span>
                        <span v-else>Atualizar encomenda</span>
                    </Button>
                    <Link href="/encomendas" class="flex-1">
                        <Button type="button" variant="outline" class="w-full">Cancelar</Button>
                    </Link>
                </div>
            </form>
        </div>
    </AppLayout>
</template>

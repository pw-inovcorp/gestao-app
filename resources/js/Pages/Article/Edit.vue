<script setup>
import AppLayout from '@/layouts/AppLayout.vue'
import ArticleForm from '@/components/ArticleForm.vue'
import { useForm, Link } from '@inertiajs/vue3'
import { Button } from '@/components/ui/button'
import { toTypedSchema } from '@vee-validate/zod'
import * as z from 'zod'
import { useForm as useVeeForm } from 'vee-validate'
import { ref } from 'vue'

const props = defineProps({
    article: Object,
    ivaRates: Array
})

const articleFormRef = ref(null)

const formSchema = toTypedSchema(z.object({
    nome: z.string().min(2, 'Nome deve ter no mínimo 2 caracteres').max(255),
    descricao: z.string().optional(),
    preco: z.string().refine((val) => !isNaN(parseFloat(val)) && parseFloat(val) >= 0, {
        message: 'Preço deve ser um número válido e não negativo'
    }),
    iva_rate_id: z.string().min(1, 'Selecione uma taxa de IVA'),
    observacoes: z.string().optional(),
    estado: z.enum(['ativo', 'inativo'])
}))

const veeForm = useVeeForm({
    validationSchema: formSchema,
    initialValues: {
        nome: props.article.nome || '',
        descricao: props.article.descricao || '',
        preco: props.article.preco?.toString() || '',
        iva_rate_id: props.article.iva_rate_id?.toString() || '',
        observacoes: props.article.observacoes || '',
        estado: props.article.estado || 'ativo'
    },
})


const inertiaForm = useForm({
    nome: props.article.nome || '',
    descricao: props.article.descricao || '',
    preco: props.article.preco || 0,
    iva_rate_id: props.article.iva_rate_id || null,
    foto: props.article.foto ? `/storage/${props.article.foto}` : null,
    observacoes: props.article.observacoes || '',
    estado: props.article.estado || 'ativo'
})

const onSubmit = veeForm.handleSubmit((values) => {
    inertiaForm.transform(() => ({
        nome: values.nome,
        descricao: values.descricao,
        preco: parseFloat(values.preco),
        iva_rate_id: values.iva_rate_id,
        foto: articleFormRef.value?.imageFile || null,
        observacoes: values.observacoes,
        estado: values.estado
    })).patch('/artigos', {
        onSuccess: () => {
            veeForm.resetForm()
            articleFormRef.value?.removeImage()
        },
        onError: (errors) => console.error('Erros:', errors)
    })
})
</script>

<template>
    <AppLayout>
        <div class="max-w-4xl mx-auto space-y-6">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold">Editar Artigo</h1>
                    <p class="text-slate-600 mt-1">Atualizar dados do artigo {{ article.nome }}</p>
                </div>
                <Link href="/artigos">
                    <Button variant="outline">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                        </svg>
                        Voltar
                    </Button>
                </Link>
            </div>

            <form @submit.prevent="onSubmit" class="space-y-6">
                <ArticleForm ref="articleFormRef" :iva-rates="ivaRates" />

                <div class="flex gap-4">
                    <Button type="submit" :disabled="inertiaForm.processing" class="flex-1">
                        <span v-if="inertiaForm.processing">A guardar...</span>
                        <span v-else>Criar Artigo</span>
                    </Button>
                    <Link href="/artigos" class="flex-1">
                        <Button type="button" variant="outline" class="w-full">Cancelar</Button>
                    </Link>
                </div>
            </form>
        </div>
    </AppLayout>
</template>

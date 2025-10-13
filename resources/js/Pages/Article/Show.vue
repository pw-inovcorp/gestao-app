<script setup>
import AppLayout from '@/layouts/AppLayout.vue'
import { Button } from '@/components/ui/button'
import { Badge } from '@/components/ui/badge'
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card'
import { Link, router } from '@inertiajs/vue3'
import { ref, computed } from 'vue'

const props = defineProps({
    article: Object
})

const deleting = ref(false)

const formatPrice = (price) => {
    return new Intl.NumberFormat('pt-PT', {
        style: 'currency',
        currency: 'EUR'
    }).format(price)
}

const formatDate = (date) => {
    return new Date(date).toLocaleDateString('pt-PT', {
        day: '2-digit',
        month: '2-digit',
        year: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
    })
}

const precoComIva = computed(() => {
    const preco = parseFloat(props.article.preco) || 0
    const taxa = props.article.iva_rate?.taxa || 0
    const precoTotal = preco + (preco * taxa / 100)
    return precoTotal
})

const deleteArticle = () => {
    if (confirm('Tem a certeza que deseja eliminar este artigo?')) {
        deleting.value = true
        router.delete(`/artigos/${props.article.id}`, {
            onFinish: () => {
                deleting.value = false
            }
        })
    }
}
</script>

<template>
    <AppLayout>
        <div class="max-w-5xl mx-auto space-y-6">
            <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-4">
                <div class="flex-1 min-w-0">
                    <div class="flex items-center gap-3 mb-2">
                        <h1 class="text-2xl sm:text-3xl font-bold">{{ article.nome }}</h1>
                        <Badge :variant="article.estado === 'ativo' ? 'default' : 'destructive'">
                            {{ article.estado }}
                        </Badge>
                    </div>
                    <p class="text-slate-600">Detalhes do artigo</p>
                </div>

                <div class="flex flex-wrap gap-2">
                    <Link href="/artigos">
                        <Button variant="outline" size="sm">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                            </svg>
                            Voltar
                        </Button>
                    </Link>

                    <Link :href="`/artigos/${article.id}/editar`">
                        <Button size="sm">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L6.832 19.82a4.5 4.5 0 0 1-1.897 1.13l-2.685.8.8-2.685a4.5 4.5 0 0 1 1.13-1.897L16.863 4.487Zm0 0L19.5 7.125" />
                            </svg>
                            Editar
                        </Button>
                    </Link>

                    <Button
                        variant="destructive"
                        size="sm"
                        @click="deleteArticle"
                        :disabled="deleting"
                    >
                        <svg v-if="!deleting" class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0" />
                        </svg>
                        <svg v-else class="w-4 h-4 mr-2 animate-spin" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        {{ deleting ? 'A eliminar...' : 'Eliminar' }}
                    </Button>
                </div>
            </div>


            <div class="grid gap-6 md:grid-cols-2">
                <Card>
                    <CardHeader class="pb-3">
                        <div class="flex items-center gap-2">
                            <svg class="w-5 h-5 text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="m2.25 15.75 5.159-5.159a2.25 2.25 0 0 1 3.182 0l5.159 5.159m-1.5-1.5 1.409-1.409a2.25 2.25 0 0 1 3.182 0l2.909 2.909m-18 3.75h16.5a1.5 1.5 0 0 0 1.5-1.5V6a1.5 1.5 0 0 0-1.5-1.5H3.75A1.5 1.5 0 0 0 2.25 6v12a1.5 1.5 0 0 0 1.5 1.5Zm10.5-11.25h.008v.008h-.008V8.25Zm.375 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Z" />
                            </svg>
                            <CardTitle class="text-lg">Fotografia</CardTitle>
                        </div>
                    </CardHeader>
                    <CardContent class="pt-0">
                        <div v-if="article.foto" class="w-full aspect-square rounded-lg overflow-hidden">
                            <img
                                :src="`/storage/${article.foto}`"
                                :alt="article.nome"
                                class="w-full h-full object-cover"
                            />
                        </div>
                        <div v-else class="w-full aspect-square rounded-lg bg-slate-100 flex items-center justify-center">
                            <div class="text-center">
                                <svg class="w-20 h-20 mx-auto text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m2.25 15.75 5.159-5.159a2.25 2.25 0 0 1 3.182 0l5.159 5.159m-1.5-1.5 1.409-1.409a2.25 2.25 0 0 1 3.182 0l2.909 2.909m-18 3.75h16.5a1.5 1.5 0 0 0 1.5-1.5V6a1.5 1.5 0 0 0-1.5-1.5H3.75A1.5 1.5 0 0 0 2.25 6v12a1.5 1.5 0 0 0 1.5 1.5Zm10.5-11.25h.008v.008h-.008V8.25Zm.375 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Z" />
                                </svg>
                                <p class="text-slate-500 mt-2">Sem fotografia</p>
                            </div>
                        </div>
                    </CardContent>
                </Card>


                <Card>
                    <CardHeader class="pb-3">
                        <div class="flex items-center gap-2">
                            <svg class="w-5 h-5 text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="m11.25 11.25.041-.02a.75.75 0 0 1 1.063.852l-.708 2.836a.75.75 0 0 0 1.063.853l.041-.021M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9-3.75h.008v.008H12V8.25Z" />
                            </svg>
                            <CardTitle class="text-lg">Informação Geral</CardTitle>
                        </div>
                    </CardHeader>
                    <CardContent class="space-y-4 pt-0">
                        <div>
                            <label class="label">Referência</label>
                            <p class="font-medium text-lg">{{ article.referencia }}</p>
                        </div>

                        <div>
                            <label class="label">Estado</label>
                            <div class="mt-1">
                                <Badge :variant="article.estado === 'ativo' ? 'default' : 'destructive'">
                                    {{ article.estado }}
                                </Badge>
                            </div>
                        </div>

                        <div>
                            <label class="label">Nome</label>
                            <p class="font-medium">{{ article.nome }}</p>
                        </div>

                        <div v-if="article.descricao">
                            <label class="label">Descrição</label>
                            <p class="text-slate-700">{{ article.descricao }}</p>
                        </div>

                        <div class="grid grid-cols-2 gap-4 pt-2 border-t">
                            <div>
                                <label class="label">Preço Unitário</label>
                                <p class="font-medium text-lg">{{ formatPrice(article.preco) }}</p>
                            </div>

                            <div>
                                <label class="label">Taxa IVA</label>
                                <p class="font-medium text-lg">
                                    {{ article.iva_rate ? article.iva_rate.taxa + '%' : '-' }}
                                </p>
                            </div>
                        </div>

                        <div class="bg-slate-50 p-4 rounded-lg border">
                            <label class="label">Preço com IVA</label>
                            <p class="font-bold text-2xl">{{ formatPrice(precoComIva) }}</p>
                        </div>
                    </CardContent>
                </Card>
            </div>


            <Card class="overflow-hidden">
                <CardHeader>
                    <div class="flex items-center gap-2">
                        <svg class="w-5 h-5 text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15.666 3.888A2.25 2.25 0 0 0 13.5 2.25h-3c-1.03 0-1.9.693-2.166 1.638m7.332 0c.055.194.084.4.084.612v0a.75.75 0 0 1-.75.75H9a.75.75 0 0 1-.75-.75v0c0-.212.03-.418.084-.612m7.332 0c.646.049 1.288.11 1.927.184 1.1.128 1.907 1.077 1.907 2.185V19.5a2.25 2.25 0 0 1-2.25 2.25H6.75A2.25 2.25 0 0 1 4.5 19.5V6.257c0-1.108.806-2.057 1.907-2.185a48.208 48.208 0 0 1 1.927-.184" />
                        </svg>
                        <CardTitle class="text-lg">Observações</CardTitle>
                    </div>
                </CardHeader>
                <CardContent class="pt-0 overflow-hidden">
                    <p class="text-sm text-slate-700 break-words break-all overflow-wrap-anywhere">
                        {{ article.observacoes || 'Sem observações registadas.' }}
                    </p>
                </CardContent>
            </Card>


            <Card>
                <CardHeader>
                    <div class="flex items-center gap-2">
                        <svg class="w-5 h-5 text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                        </svg>
                        <CardTitle class="text-lg">Informações do Sistema</CardTitle>
                    </div>
                </CardHeader>
                <CardContent>
                    <div class="grid md:grid-cols-2 gap-4">
                        <div>
                            <label class="label">Criado em</label>
                            <p class="text-sm text-slate-600">{{ formatDate(article.created_at) }}</p>
                        </div>

                        <div>
                            <label class="label">Atualizado em</label>
                            <p class="text-sm text-slate-600">{{ formatDate(article.updated_at) }}</p>
                        </div>
                    </div>
                </CardContent>
            </Card>
        </div>
    </AppLayout>
</template>

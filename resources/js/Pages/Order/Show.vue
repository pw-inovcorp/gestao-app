<script setup>
import AppLayout from '@/layouts/AppLayout.vue'
import {Button} from '@/components/ui/button'
import {Badge} from '@/components/ui/badge'
import {Card, CardContent, CardHeader, CardTitle} from '@/components/ui/card'
import {Table, TableBody, TableCell, TableHead, TableHeader, TableRow} from '@/components/ui/table'
import {Link, router} from '@inertiajs/vue3'
import {computed, ref} from 'vue'

const props = defineProps({
    order: Object
})

const deleting = ref(false)

const formatPrice = (price) => {
    return new Intl.NumberFormat('pt-PT', {
        style: 'currency',
        currency: 'EUR'
    }).format(price)
}

const formatDate = (date) => {
    if (!date) return '-'
    return new Date(date).toLocaleDateString('pt-PT', {
        day: '2-digit',
        month: '2-digit',
        year: 'numeric'
    })
}

const formatDateTime = (date) => {
    return new Date(date).toLocaleDateString('pt-PT', {
        day: '2-digit',
        month: '2-digit',
        year: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
    })
}

const totalWithoutIva = computed(() => {
    return props.order.items?.reduce((sum, item) => sum + parseFloat(item.subtotal), 0) || 0
})

const totalIva = computed(() => {
    let sum = 0
    for (const item of props.order.items || []) {
        sum += item.subtotal * ((item.article?.iva_rate?.taxa ?? 0) / 100)
    }
    return sum
})

const totalWithIva = computed(() => {
    return totalWithoutIva.value + totalIva.value
})

const canEdit = computed(() => props.order.estado === 'rascunho')
const canDelete = computed(() => props.order.estado === 'rascunho')

const canConvert = computed(() => {
    if (props.order.estado !== 'fechado') return false

    return props.order.items?.some(item => item.fornecedor_id)
})

const downloadPDF = () => {
    window.open(`/encomendas/${props.order.id}/pdf`, '_blank')
}


const deleteOrder = () => {
    if (confirm(`Tem a certeza que deseja eliminar a encomenda ${props.order.numero}?`)) {
        deleting.value = true
        router.delete(`/encomendas/${props.order.id}`, {
            onFinish: () => {
                deleting.value = false
            }
        })
    }
}

const convertToSupplierOrders = () => {
    if (confirm('Deseja criar encomendas de fornecedor a partir desta encomenda?')) {
        router.post(`/encomendas/${props.order.id}/converter-fornecedor`)
    }
}
</script>

<template>
    <AppLayout>
        <div class="max-w-7xl mx-auto space-y-6">
            <!-- Header -->
            <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-4">
                <div class="flex-1 min-w-0">
                    <div class="flex items-center gap-3 mb-2">
                        <h1 class="text-2xl sm:text-3xl font-bold">{{ order.numero }}</h1>
                        <Badge :variant="order.estado === 'fechado' ? 'default' : 'secondary'">
                            {{ order.estado }}
                        </Badge>
                    </div>
                    <p class="text-slate-600">Encomenda para {{ order.client?.nome }}</p>
                    <p v-if="order.proposal" class="text-sm text-slate-500 mt-1">
                        Convertida da proposta
                        <Link :href="`/propostas/${order.proposal.id}`" class="text-blue-600 hover:underline">
                            {{ order.proposal.numero }}
                        </Link>
                    </p>
                </div>

                <div class="flex flex-wrap gap-2">
                    <Link href="/encomendas">
                        <Button variant="outline" size="sm">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                            </svg>
                            Voltar
                        </Button>
                    </Link>

                    <Button variant="outline" size="sm" @click="downloadPDF">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 8.25H7.5a2.25 2.25 0 0 0-2.25 2.25v9a2.25 2.25 0 0 0 2.25 2.25h9a2.25 2.25 0 0 0 2.25-2.25v-9a2.25 2.25 0 0 0-2.25-2.25H15M9 12l3 3m0 0 3-3m-3 3V2.25" />
                        </svg>
                        Baixar PDF
                    </Button>

                    <Button
                        v-if="canConvert"
                        variant="default"
                        size="sm"
                        @click="convertToSupplierOrders"
                    >
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0 3.181 3.183a8.25 8.25 0 0 0 13.803-3.7M4.031 9.865a8.25 8.25 0 0 1 13.803-3.7l3.181 3.182m0-4.991v4.99" />
                        </svg>
                        Converter p/ Fornecedor
                    </Button>

                    <Link v-if="canEdit" :href="`/encomendas/${order.id}/editar`">
                        <Button size="sm">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L6.832 19.82a4.5 4.5 0 0 1-1.897 1.13l-2.685.8.8-2.685a4.5 4.5 0 0 1 1.13-1.897L16.863 4.487Zm0 0L19.5 7.125" />
                            </svg>
                            Editar
                        </Button>
                    </Link>

                    <Button
                        v-if="canDelete"
                        variant="destructive"
                        size="sm"
                        @click="deleteOrder"
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

            <div class="grid gap-6 md:grid-cols-3">
                <Card>
                    <CardHeader class="pb-3">
                        <div class="flex items-center gap-2">
                            <svg class="w-5 h-5 text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 9h3.75M15 12h3.75M15 15h3.75M4.5 19.5h15a2.25 2.25 0 0 0 2.25-2.25V6.75A2.25 2.25 0 0 0 19.5 4.5h-15a2.25 2.25 0 0 0-2.25 2.25v10.5A2.25 2.25 0 0 0 4.5 19.5Zm6-10.125a1.875 1.875 0 1 1-3.75 0 1.875 1.875 0 0 1 3.75 0Zm1.294 6.336a6.721 6.721 0 0 1-3.17.789 6.721 6.721 0 0 1-3.168-.789 3.376 3.376 0 0 1 6.338 0Z" />
                            </svg>
                            <CardTitle class="text-lg">Cliente</CardTitle>
                        </div>
                    </CardHeader>
                    <CardContent class="space-y-3">
                        <div>
                            <p class="font-medium text-lg">{{ order.client?.nome }}</p>
                            <p class="text-sm text-slate-500">NIF: {{ order.client?.nif }}</p>
                        </div>
                        <div v-if="order.client?.email" class="text-sm">
                            <a :href="`mailto:${order.client.email}`" class="text-blue-600 hover:underline">
                                {{ order.client.email }}
                            </a>
                        </div>
                        <div v-if="order.client?.telemovel" class="text-sm">
                            <a :href="`tel:${order.client.telemovel}`" class="text-blue-600 hover:underline">
                                {{ order.client.telemovel }}
                            </a>
                        </div>
                    </CardContent>
                </Card>

                <Card>
                    <CardHeader class="pb-3">
                        <div class="flex items-center gap-2">
                            <svg class="w-5 h-5 text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 0 1 2.25-2.25h13.5A2.25 2.25 0 0 1 21 7.5v11.25m-18 0A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75m-18 0v-7.5A2.25 2.25 0 0 1 5.25 9h13.5A2.25 2.25 0 0 1 21 11.25v7.5" />
                            </svg>
                            <CardTitle class="text-lg">Datas</CardTitle>
                        </div>
                    </CardHeader>
                    <CardContent class="space-y-3">
                        <div>
                            <label class="label">Data da Encomenda</label>
                            <p class="font-medium">{{ formatDate(order.data_encomenda) }}</p>
                        </div>
                        <div>
                            <label class="label">Criada em</label>
                            <p class="text-sm text-slate-600">{{ formatDate(order.created_at) }}</p>
                        </div>
                    </CardContent>
                </Card>

                <Card>
                    <CardHeader class="pb-3">
                        <div class="flex items-center gap-2">
                            <svg class="w-5 h-5 text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 18.75a60.07 60.07 0 0 1 15.797 2.101c.727.198 1.453-.342 1.453-1.096V18.75M3.75 4.5v.75A.75.75 0 0 1 3 6h-.75m0 0v-.375c0-.621.504-1.125 1.125-1.125H20.25M2.25 6v9m18-10.5v.75c0 .414.336.75.75.75h.75m-1.5-1.5h.375c.621 0 1.125.504 1.125 1.125v9.75c0 .621-.504 1.125-1.125 1.125h-.375m1.5-1.5H21a.75.75 0 0 0-.75.75v.75m0 0H3.75m0 0h-.375a1.125 1.125 0 0 1-1.125-1.125V15m1.5 1.5v-.75A.75.75 0 0 0 3 15h-.75M15 10.5a3 3 0 1 1-6 0 3 3 0 0 1 6 0Zm3 0h.008v.008H18V10.5Zm-12 0h.008v.008H6V10.5Z" />
                            </svg>
                            <CardTitle class="text-lg">Valores</CardTitle>
                        </div>
                    </CardHeader>
                    <CardContent class="space-y-3">
                        <div>
                            <label class="label">Total sem IVA</label>
                            <p class="font-medium">{{ formatPrice(totalWithoutIva) }}</p>
                        </div>
                        <div>
                            <label class="label">Total IVA</label>
                            <p class="font-medium">{{ formatPrice(totalIva) }}</p>
                        </div>
                        <div class="pt-2 border-t">
                            <label class="label">Total com IVA</label>
                            <p class="font-bold text-xl">{{ formatPrice(totalWithIva) }}</p>
                        </div>
                    </CardContent>
                </Card>
            </div>

            <Card>
                <CardHeader>
                    <CardTitle>Artigos da Encomenda</CardTitle>
                </CardHeader>
                <CardContent>
                    <div class="overflow-x-auto">
                        <Table>
                            <TableHeader>
                                <TableRow>
                                    <TableHead>Artigo</TableHead>
                                    <TableHead>Fornecedor</TableHead>
                                    <TableHead class="text-right">Quantidade</TableHead>
                                    <TableHead class="text-right">Preço Unit.</TableHead>
                                    <TableHead class="text-right">IVA</TableHead>
                                    <TableHead class="text-right">Subtotal</TableHead>
                                    <TableHead class="text-right">Total c/ IVA</TableHead>
                                </TableRow>
                            </TableHeader>
                            <TableBody>
                                <TableRow v-for="item in order.items" :key="item.id">
                                    <TableCell>
                                        <div class="font-medium">{{ item.article.nome }}</div>
                                        <div class="text-sm text-slate-500">{{ item.article.referencia }}</div>
                                    </TableCell>
                                    <TableCell>
                                        {{ item.supplier?.nome || '-' }}
                                    </TableCell>
                                    <TableCell class="text-right">{{ item.quantidade }}</TableCell>
                                    <TableCell class="text-right">{{ formatPrice(item.preco_unitario) }}</TableCell>
                                    <TableCell class="text-right">{{ item.article.iva_rate?.taxa || 0 }}%</TableCell>
                                    <TableCell class="text-right font-medium">{{ formatPrice(item.subtotal) }}</TableCell>
                                    <TableCell class="text-right font-medium">
                                        {{ formatPrice(parseFloat(item.subtotal) * (1 + (item.article.iva_rate?.taxa || 0) / 100)) }}
                                    </TableCell>
                                </TableRow>
                            </TableBody>
                        </Table>
                    </div>

                    <div class="mt-6 flex justify-end">
                        <div class="w-full md:w-96 space-y-2">
                            <div class="flex justify-between text-sm">
                                <span class="text-slate-600">Subtotal (sem IVA):</span>
                                <span class="font-medium">{{ formatPrice(totalWithoutIva) }}</span>
                            </div>
                            <div class="flex justify-between text-sm">
                                <span class="text-slate-600">Total IVA:</span>
                                <span class="font-medium">{{ formatPrice(totalIva) }}</span>
                            </div>
                            <div class="flex justify-between text-lg font-bold pt-2 border-t">
                                <span>Total:</span>
                                <span>{{ formatPrice(totalWithIva) }}</span>
                            </div>
                        </div>
                    </div>
                </CardContent>
            </Card>

        </div>
    </AppLayout>
</template>

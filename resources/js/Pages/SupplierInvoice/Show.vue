<script setup>
import AppLayout from '@/layouts/AppLayout.vue'
import { Button } from '@/components/ui/button'
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card'
import { Link, router } from '@inertiajs/vue3'
import { ref } from 'vue'
import { Label } from '@/components/ui/label'

const props = defineProps({
    invoice: Object
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

const deleteInvoice = () => {
    if (confirm(`Tem a certeza que deseja eliminar a fatura ${props.invoice.numero}?`)) {
        deleting.value = true
        router.delete(`/faturas-fornecedor/${props.invoice.id}`, {
            onFinish: () => {
                deleting.value = false
            }
        })
    }
}

const downloadDocument = (path, type) => {
    window.open(`/faturas-fornecedor/${props.invoice.id}/download/${type}`, '_blank')
}
</script>

<template>
    <AppLayout>
        <div class="max-w-4xl mx-auto space-y-6">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold">Fatura {{ invoice.numero }}</h1>
                    <p class="text-slate-600 mt-1">{{ invoice.supplier?.nome }}</p>
                </div>
               <div class="flex flex-wrap gap-2">
                   <Link href="/faturas-fornecedor">
                       <Button variant="outline">
                           <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                               <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                           </svg>
                           Voltar
                       </Button>
                   </Link>
                   <Button
                       v-if="invoice.estado === 'pendente_pagamento'"
                       variant="destructive"
                       @click="deleteInvoice"
                       :disabled="deleting"
                   >
                       <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                           <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                       </svg>
                       {{ deleting ? 'A eliminar...' : 'Eliminar Fatura' }}
                   </Button>
               </div>
            </div>

            <div class="grid gap-6 md:grid-cols-2">
                <Card>
                    <CardHeader>
                        <CardTitle>Informações da Fatura</CardTitle>
                    </CardHeader>
                    <CardContent class="space-y-3">
                        <div class="flex justify-between">
                            <span class="label">Número:</span>
                            <span class="font-medium">{{ invoice.numero }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="label">Data Fatura:</span>
                            <span class="font-medium">{{ formatDate(invoice.data_fatura) }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="label">Data Vencimento:</span>
                            <span class="font-medium">{{ formatDate(invoice.data_vencimento) }}</span>
                        </div>
                        <div class="flex justify-between pt-3 border-t">
                            <span class="label">Valor Total:</span>
                            <span class="font-bold text-lg">{{ formatPrice(invoice.valor_total) }}</span>
                        </div>
                    </CardContent>
                </Card>

                <Card>
                    <CardHeader>
                        <CardTitle>Fornecedor</CardTitle>
                    </CardHeader>
                    <CardContent class="space-y-3">
                        <div>
                            <p class="font-medium">{{ invoice.supplier?.nome }}</p>
                            <p class="text-sm text-slate-500">NIF: {{ invoice.supplier?.nif }}</p>
                        </div>
                        <div v-if="invoice.supplier?.email" class="text-sm">
                            <span class="label">Email:</span>
                            <a :href="`mailto:${invoice.supplier.email}`" class="text-blue-600 hover:underline ml-2">
                                {{ invoice.supplier.email }}
                            </a>
                        </div>
                        <div v-if="invoice.supplier?.telemovel" class="text-sm">
                            <span class="label">Telemóvel:</span>
                            <a :href="`tel:${invoice.supplier.telemovel}`" class="text-blue-600 hover:underline ml-2">
                                {{ invoice.supplier.telemovel }}
                            </a>
                        </div>
                    </CardContent>
                </Card>
            </div>

            <Card>
                <CardHeader>
                    <CardTitle>Estado e Pagamento</CardTitle>
                </CardHeader>
                <CardContent class="space-y-4">
                    <div class="flex items-center gap-3">
                        <span class="label">Estado:</span>
                        <span :class="[
                            'px-3 py-1 rounded-full text-sm font-medium',
                            invoice.estado === 'paga' ? 'bg-green-100 text-green-800' : 'bg-amber-100 text-amber-800'
                        ]">
                            {{ invoice.estado === 'paga' ? 'Paga' : 'Pendente de Pagamento' }}
                        </span>
                    </div>

                    <div v-if="invoice.data_pagamento" class="flex justify-between">
                        <span class="label">Data Pagamento:</span>
                        <span class="font-medium">{{ formatDate(invoice.data_pagamento) }}</span>
                    </div>

                    <div v-if="invoice.supplier_order" class="flex justify-between">
                        <span class="label">Encomenda Fornecedor:</span>
                        <Link :href="`/encomendas-fornecedor/${invoice.supplier_order.id}`" class="text-blue-600 hover:underline">
                            {{ invoice.supplier_order.numero }}
                        </Link>
                    </div>
                </CardContent>
            </Card>

            <Card v-if="invoice.documento || invoice.comprovativo_pagamento">
                <CardHeader>
                    <CardTitle>Documentos</CardTitle>
                </CardHeader>
                <CardContent class="space-y-3">
                    <div v-if="invoice.documento" class="flex items-center justify-between p-3 bg-slate-50 rounded-md">
                        <div class="flex items-center gap-3">
                            <svg class="w-8 h-8 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                            <div>
                                <p class="font-medium text-sm">Documento da Fatura</p>
                                <p class="text-xs text-slate-500">Ficheiro anexado</p>
                            </div>
                        </div>
                        <Button variant="ghost" size="sm" @click="downloadDocument(invoice.documento, 'documento')">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                        </Button>
                    </div>

                    <div v-if="invoice.comprovativo_pagamento" class="flex items-center justify-between p-3 bg-green-50 rounded-md">
                        <div class="flex items-center gap-3">
                            <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <div>
                                <p class="font-medium text-sm">Comprovativo de Pagamento</p>
                                <p class="text-xs text-slate-500">Ficheiro enviado</p>
                            </div>
                        </div>
                        <Button variant="ghost" size="sm" @click="downloadDocument(invoice.comprovativo_pagamento, 'comprovativo')">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                        </Button>
                    </div>
                </CardContent>
            </Card>
        </div>
    </AppLayout>
</template>

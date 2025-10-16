<script setup>
import AppLayout from '@/layouts/AppLayout.vue'
import { Button } from '@/components/ui/button'
import { Badge } from '@/components/ui/badge'
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from '@/components/ui/table'
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card'
import Pagination from '@/components/Pagination.vue'
import { Link } from '@inertiajs/vue3'
import { ref } from 'vue'
import {
    Select,
    SelectContent,
    SelectGroup,
    SelectItem,
    SelectTrigger,
    SelectValue,
} from '@/components/ui/select'
import { router } from '@inertiajs/vue3'

const props = defineProps({
    supplierOrders: Object
})

const deletingOrderId = ref(null)


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

const updateStatus = (supplierOrder, newStatus) => {
    if (supplierOrder.estado === newStatus) return

    const message = newStatus === 'fechado'
        ? `Ao fechar a encomenda ${supplierOrder.numero}, a data será definida. Confirma?`
        : `Tem a certeza que deseja voltar a encomenda ${supplierOrder.numero} ao estado de rascunho?`

    if (confirm(message)) {
        router.patch(`/encomendas-fornecedor/${supplierOrder.id}/status`, {
            estado: newStatus
        }, {
            preserveScroll: true
        })
    }
}
const deleteSupplierOrder = (supplierOrder) => {
    if (supplierOrder.estado !== 'rascunho') {
        alert('Apenas encomendas em rascunho podem ser eliminadas.')
        return
    }

    if (confirm(`Tem a certeza que deseja eliminar a encomenda ${supplierOrder.numero}?`)) {
        deletingOrderId.value = supplierOrder.id
        router.delete(`/encomendas-fornecedor/${supplierOrder.id}`, {
            onFinish: () => {
                deletingOrderId.value = null
            }
        })
    }
}
</script>

<template>
    <AppLayout>
        <div class="max-w-7xl mx-auto space-y-6">
            <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-4">
                <div>
                    <h1 class="text-2xl sm:text-3xl font-bold">Encomendas - Fornecedores</h1>
                    <p class="text-slate-600 mt-1">Gerir encomendas de fornecedores</p>
                </div>
            </div>

            <Card>
                <CardHeader>
                    <CardTitle>Lista de Encomendas - Fornecedores</CardTitle>
                    <CardDescription>Total: {{ supplierOrders.total }} encomendas</CardDescription>
                </CardHeader>
                <CardContent class="space-y-4">
                    <div class="hidden md:block overflow-x-auto">
                        <Table>
                            <TableHeader>
                                <TableRow>
                                    <TableHead>Número</TableHead>
                                    <TableHead>Fornecedor</TableHead>
                                    <TableHead>Encomenda Origem</TableHead>
                                    <TableHead>Data Criação</TableHead>
                                    <TableHead>Valor Total</TableHead>
                                    <TableHead>Estado</TableHead>
                                    <TableHead class="text-right">Ações</TableHead>
                                </TableRow>
                            </TableHeader>
                            <TableBody>
                                <TableRow v-for="supplierOrder in supplierOrders.data" :key="supplierOrder.id">
                                    <TableCell class="font-medium">{{ supplierOrder.numero }}</TableCell>
                                    <TableCell>{{ supplierOrder.supplier?.nome || '-' }}</TableCell>
                                    <TableCell>
                                        <Link
                                            v-if="supplierOrder.order"
                                            :href="`/encomendas/${supplierOrder.order.id}`"
                                            class="text-blue-600 hover:underline"
                                        >
                                            {{ supplierOrder.order.numero }}
                                        </Link>
                                        <span v-else>-</span>
                                    </TableCell>
                                    <TableCell>{{ formatDate(supplierOrder.created_at) }}</TableCell>
                                    <TableCell class="font-medium">{{ formatPrice(supplierOrder.valor_total) }}</TableCell>
                                    <TableCell>
                                        <Select
                                            :model-value="supplierOrder.estado"
                                            @update:model-value="(value) => updateStatus(supplierOrder, value)"
                                        >
                                            <SelectTrigger class="w-32">
                                                <SelectValue />
                                            </SelectTrigger>
                                            <SelectContent>
                                                <SelectGroup>
                                                    <SelectItem value="rascunho">
                                                        <div class="flex items-center gap-2">
                                                            <div class="w-2 h-2 rounded-full bg-slate-400"></div>
                                                            Rascunho
                                                        </div>
                                                    </SelectItem>
                                                    <SelectItem value="fechado">
                                                        <div class="flex items-center gap-2">
                                                            <div class="w-2 h-2 rounded-full bg-green-500"></div>
                                                            Fechado
                                                        </div>
                                                    </SelectItem>
                                                </SelectGroup>
                                            </SelectContent>
                                        </Select>
                                    </TableCell>
                                    <TableCell>
                                        <div class="flex justify-end gap-2">
                                            <Link :href="`/encomendas-fornecedor/${supplierOrder.id}`">
                                                <Button variant="ghost" size="sm" title="Ver Detalhes">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                                    </svg>
                                                </Button>
                                            </Link>

                                            <Button
                                                v-if="supplierOrder.estado === 'rascunho'"
                                                variant="ghost"
                                                size="sm"
                                                class="text-red-600 hover:text-red-700 hover:bg-red-50"
                                                @click="deleteSupplierOrder(supplierOrder)"
                                                :disabled="deletingOrderId === supplierOrder.id"
                                                title="Eliminar"
                                            >
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0" />
                                                </svg>
                                            </Button>
                                            <Button v-else variant="ghost" size="sm" disabled title="Apenas rascunhos podem ser eliminados">
                                                <svg class="w-4 h-4 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0" />
                                                </svg>
                                            </Button>

                                        </div>
                                    </TableCell>
                                </TableRow>
                            </TableBody>
                        </Table>
                    </div>

                    <!-- Mobile Cards -->
                    <div class="md:hidden space-y-4">
                        <Card v-for="supplierOrder in supplierOrders.data" :key="supplierOrder.id" class="p-4">
                            <div class="space-y-3">
                                <div class="flex items-start justify-between pb-3 border-b">
                                    <div class="flex-1 min-w-0">
                                        <p class="font-medium text-lg">{{ supplierOrder.numero }}</p>
                                        <p class="text-sm text-slate-500 truncate">{{ supplierOrder.supplier?.nome || '-' }}</p>
                                        <div class="mt-2">
                                            <Select
                                                :model-value="supplierOrder.estado"
                                                @update:model-value="(value) => updateStatus(supplierOrder, value)"
                                            >
                                                <SelectTrigger class="w-32">
                                                    <SelectValue />
                                                </SelectTrigger>
                                                <SelectContent>
                                                    <SelectGroup>
                                                        <SelectItem value="rascunho">Rascunho</SelectItem>
                                                        <SelectItem value="fechado">Fechado</SelectItem>
                                                    </SelectGroup>
                                                </SelectContent>
                                            </Select>
                                        </div>
                                    </div>
                                    <Link :href="`/encomendas-fornecedor/${supplierOrder.id}`">
                                        <Button variant="ghost" size="sm" title="Ver Detalhes">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                            </svg>
                                        </Button>
                                    </Link>

                                    <Button
                                        v-if="supplierOrder.estado === 'rascunho'"
                                        variant="ghost"
                                        size="sm"
                                        class="text-red-600 hover:text-red-700 hover:bg-red-50"
                                        @click="deleteSupplierOrder(supplierOrder)"
                                        :disabled="deletingOrderId === supplierOrder.id"
                                        title="Eliminar"
                                    >
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0" />
                                        </svg>
                                    </Button>
                                    <Button v-else variant="ghost" size="sm" disabled title="Apenas rascunhos podem ser eliminados">
                                        <svg class="w-4 h-4 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0" />
                                        </svg>
                                    </Button>
                                </div>

                                <div class="space-y-2 text-sm">
                                    <div class="flex justify-between">
                                        <span class="text-slate-500">Origem:</span>
                                        <Link
                                            v-if="supplierOrder.order"
                                            :href="`/encomendas/${supplierOrder.order.id}`"
                                            class="text-blue-600 hover:underline"
                                        >
                                            {{ supplierOrder.order.numero }}
                                        </Link>
                                        <span v-else>-</span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span class="text-slate-500">Criado:</span>
                                        <span>{{ formatDate(supplierOrder.created_at) }}</span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span class="text-slate-500">Valor Total:</span>
                                        <span class="font-medium">{{ formatPrice(supplierOrder.valor_total) }}</span>
                                    </div>
                                </div>
                            </div>
                        </Card>
                    </div>

                    <Pagination :pagination="supplierOrders" />
                </CardContent>
            </Card>
        </div>
    </AppLayout>
</template>

<script setup>
import AppLayout from '@/layouts/AppLayout.vue'
import { Button } from '@/components/ui/button'
import { Badge } from '@/components/ui/badge'
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from '@/components/ui/table'
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card'
import Pagination from '@/components/Pagination.vue'
import { Link } from '@inertiajs/vue3'

const props = defineProps({
    orders: Object
})

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
</script>

<template>
    <AppLayout>
        <div class="max-w-7xl mx-auto space-y-6">
            <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-4">
                <div>
                    <h1 class="text-2xl sm:text-3xl font-bold">Encomendas</h1>
                    <p class="text-slate-600 mt-1">Gerir encomendas de clientes</p>
                </div>


            </div>

            <Card>
                <CardHeader>
                    <CardTitle>Lista de Encomendas</CardTitle>
                    <CardDescription>Total: {{ orders.total }} encomendas</CardDescription>
                </CardHeader>
                <CardContent class="space-y-4">
                    <div class="hidden md:block overflow-x-auto">
                        <Table>
                            <TableHeader>
                                <TableRow>
                                    <TableHead>Número</TableHead>
                                    <TableHead>Cliente</TableHead>
                                    <TableHead>Data Criação</TableHead>
                                    <TableHead>Data Encomenda</TableHead>
                                    <TableHead>Valor Total</TableHead>
                                    <TableHead>Estado</TableHead>
                                    <TableHead class="text-right">Ações</TableHead>
                                </TableRow>
                            </TableHeader>
                            <TableBody>
                                <TableRow v-for="order in orders.data" :key="order.id">
                                    <TableCell class="font-medium">{{ order.numero }}</TableCell>
                                    <TableCell>{{ order.client?.nome || '-' }}</TableCell>
                                    <TableCell>{{ formatDate(order.created_at) }}</TableCell>
                                    <TableCell>{{ formatDate(order.data_encomenda) }}</TableCell>
                                    <TableCell class="font-medium">{{ formatPrice(order.valor_total) }}</TableCell>
                                    <TableCell>
                                        <Badge :variant="order.estado === 'fechado' ? 'default' : 'secondary'">
                                            {{ order.estado }}
                                        </Badge>
                                    </TableCell>
                                    <TableCell>
                                        <div class="flex justify-end gap-2">


                                        </div>
                                    </TableCell>
                                </TableRow>
                            </TableBody>
                        </Table>
                    </div>

                    <!-- Mobile Cards -->
                    <div class="md:hidden space-y-4">
                        <Card v-for="order in orders.data" :key="order.id" class="p-4">
                            <div class="space-y-3">
                                <div class="flex items-start justify-between pb-3 border-b">
                                    <div class="flex-1 min-w-0">
                                        <p class="font-medium text-lg">{{ order.numero }}</p>
                                        <p class="text-sm text-slate-500 truncate">{{ order.client?.nome || '-' }}</p>
                                        <Badge :variant="order.estado === 'fechado' ? 'default' : 'secondary'" class="mt-2">
                                            {{ order.estado }}
                                        </Badge>
                                    </div>
                                    <div class="flex justify-end gap-2">
                                        <Link :href="`/encomendas/${order.id}`">
                                            <Button variant="ghost" size="sm" title="Ver Detalhes">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                                </svg>
                                            </Button>
                                        </Link>
                                    </div>
                                </div>

                                <div class="space-y-2 text-sm">
                                    <div class="flex justify-between">
                                        <span class="text-slate-500">Criado:</span>
                                        <span>{{ formatDate(order.created_at) }}</span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span class="text-slate-500">Data Encomenda:</span>
                                        <span>{{ formatDate(order.data_encomenda) }}</span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span class="text-slate-500">Valor Total:</span>
                                        <span class="font-medium">{{ formatPrice(order.valor_total) }}</span>
                                    </div>
                                </div>
                            </div>
                        </Card>
                    </div>

                    <Pagination :pagination="orders" />
                </CardContent>
            </Card>
        </div>
    </AppLayout>
</template>

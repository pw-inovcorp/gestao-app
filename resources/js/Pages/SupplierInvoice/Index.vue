<script setup>
import AppLayout from '@/layouts/AppLayout.vue'
import { Button } from '@/components/ui/button'
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from '@/components/ui/table'
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card'
import Pagination from '@/components/Pagination.vue'
import PaymentProofModal from '@/components/PaymentProofModal.vue'
import { Link, router } from '@inertiajs/vue3'
import { ref } from 'vue'
import {
    Select,
    SelectContent,
    SelectGroup,
    SelectItem,
    SelectTrigger,
    SelectValue
} from '@/components/ui/select'

const props = defineProps({
    invoices: Object
})

const showModal = ref(false)
const selectedInvoice = ref(null)

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

const updateStatus = (invoice, newStatus) => {
    if (invoice.estado === newStatus) return

    if (newStatus === 'paga') {
        router.patch(`/faturas-fornecedor/${invoice.id}/status`, {
            estado: newStatus
        }, {
            preserveScroll: true,
            onSuccess: () => {
                selectedInvoice.value = invoice
                showModal.value = true
            }
        })
        return
    }

    if (confirm(`Voltar a fatura ${invoice.numero} ao estado pendente?`)) {
        router.patch(`/faturas-fornecedor/${invoice.id}/status`, {
            estado: newStatus
        }, {
            preserveScroll: true
        })
    }
}

const closeModal = () => {
    showModal.value = false
    selectedInvoice.value = null
}
</script>

<template>
    <AppLayout>
        <div class="max-w-7xl mx-auto space-y-6">
            <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-4">
                <div>
                    <h1 class="text-2xl sm:text-3xl font-bold">Faturas de Fornecedores</h1>
                    <p class="text-slate-600 mt-1">Gerir faturas e pagamentos a fornecedores</p>
                </div>
                <Link href="/faturas-fornecedor/criar">
                    <Button class="w-full sm:w-auto">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                        </svg>
                        Nova Fatura
                    </Button>
                </Link>
            </div>

            <Card>
                <CardHeader>
                    <CardTitle>Lista de Faturas</CardTitle>
                    <CardDescription>Total: {{ invoices.total }} faturas</CardDescription>
                </CardHeader>
                <CardContent class="space-y-4">
                    <div class="hidden md:block overflow-x-auto">
                        <Table>
                            <TableHeader>
                                <TableRow>
                                    <TableHead>Número</TableHead>
                                    <TableHead>Data</TableHead>
                                    <TableHead>Fornecedor</TableHead>
                                    <TableHead class="text-right">Valor Total</TableHead>
                                    <TableHead>Estado</TableHead>
                                </TableRow>
                            </TableHeader>
                            <TableBody>
                                <TableRow v-for="invoice in invoices.data" :key="invoice.id">
                                    <TableCell class="font-medium">{{ invoice.numero }}</TableCell>
                                    <TableCell>{{ formatDate(invoice.data_fatura) }}</TableCell>
                                    <TableCell>{{ invoice.supplier?.nome || '-' }}</TableCell>
                                    <TableCell class="text-right font-medium">
                                        {{ formatPrice(invoice.valor_total) }}
                                    </TableCell>
                                    <TableCell>
                                        <Select
                                            :model-value="invoice.estado"
                                            @update:model-value="(value) => updateStatus(invoice, value)"
                                        >
                                            <SelectTrigger class="w-40">
                                                <SelectValue />
                                            </SelectTrigger>
                                            <SelectContent>
                                                <SelectGroup>
                                                    <SelectItem value="pendente_pagamento">
                                                        <div class="flex items-center gap-2">
                                                            <div class="w-2 h-2 rounded-full bg-amber-500"></div>
                                                            Pendente
                                                        </div>
                                                    </SelectItem>
                                                    <SelectItem value="paga">
                                                        <div class="flex items-center gap-2">
                                                            <div class="w-2 h-2 rounded-full bg-green-500"></div>
                                                            Paga
                                                        </div>
                                                    </SelectItem>
                                                </SelectGroup>
                                            </SelectContent>
                                        </Select>
                                    </TableCell>
                                </TableRow>
                            </TableBody>
                        </Table>
                    </div>

                    <!-- Mobile Cards -->
                    <div class="md:hidden space-y-4">
                        <Card v-for="invoice in invoices.data" :key="invoice.id" class="p-4">
                            <div class="space-y-3">
                                <div class="flex items-start justify-between pb-3 border-b">
                                    <div class="flex-1 min-w-0">
                                        <p class="font-medium text-lg">{{ invoice.numero }}</p>
                                        <p class="text-sm text-slate-500 truncate">{{ invoice.supplier?.nome || '-' }}</p>
                                        <div class="mt-2">
                                            <Select
                                                :model-value="invoice.estado"
                                                @update:model-value="(value) => updateStatus(invoice, value)"
                                            >
                                                <SelectTrigger class="w-40">
                                                    <SelectValue />
                                                </SelectTrigger>
                                                <SelectContent>
                                                    <SelectGroup>
                                                        <SelectItem value="pendente_pagamento">
                                                            <div class="flex items-center gap-2">
                                                                <div class="w-2 h-2 rounded-full bg-amber-500"></div>
                                                                Pendente
                                                            </div>
                                                        </SelectItem>
                                                        <SelectItem value="paga">
                                                            <div class="flex items-center gap-2">
                                                                <div class="w-2 h-2 rounded-full bg-green-500"></div>
                                                                Paga
                                                            </div>
                                                        </SelectItem>
                                                    </SelectGroup>
                                                </SelectContent>
                                            </Select>
                                        </div>
                                    </div>
                                </div>

                                <div class="space-y-2 text-sm">
                                    <div class="flex justify-between">
                                        <span class="text-slate-500">Data:</span>
                                        <span>{{ formatDate(invoice.data_fatura) }}</span>
                                    </div>
                                    <div class="flex justify-between font-medium">
                                        <span class="text-slate-500">Valor Total:</span>
                                        <span>{{ formatPrice(invoice.valor_total) }}</span>
                                    </div>
                                </div>
                            </div>
                        </Card>
                    </div>

                    <Pagination :pagination="invoices" />
                </CardContent>
            </Card>

            <PaymentProofModal
                :open="showModal"
                :invoice="selectedInvoice"
                @close="closeModal"
            />
        </div>
    </AppLayout>
</template>

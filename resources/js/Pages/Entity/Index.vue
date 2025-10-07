<script setup>
import AppLayout from '@/layouts/AppLayout.vue'
import { Button } from '@/components/ui/button'
import { Badge } from '@/components/ui/badge'
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from '@/components/ui/table'
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card'

import Pagination from '@/components/Pagination.vue'
import { Link, router } from '@inertiajs/vue3'
import { ref } from 'vue'

const props = defineProps({
    entities: Object,
    filter: String
})

const changeFilter = (newFilter) => {
    router.get('/entidades', { filter: newFilter }, {
        preserveState: true,
        preserveScroll: true
    })
}
</script>

<template>
    <AppLayout>
        <div class="max-w-7xl mx-auto space-y-6">
            <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-4">
                <div>
                    <h1 class="text-2xl sm:text-3xl font-bold">Entidades</h1>
                    <p class="text-slate-600 mt-1">Gerir clientes e fornecedores</p>
                </div>
            </div>

            <Card>
                <CardHeader>
                    <div class="flex flex-col gap-4">
                        <div class="flex flex-col sm:flex-row sm:justify-between sm:items-start gap-4">
                            <div>
                                <CardTitle>Lista de Entidades</CardTitle>
                                <CardDescription>Total: {{ entities.total }} entidades</CardDescription>
                            </div>

                            <!-- Filtros como botões -->
                            <div class="flex flex-wrap gap-2">
                                <Button
                                    :variant="filter === 'todos' ? 'default' : 'outline'"
                                    size="sm"
                                    @click="changeFilter('todos')"
                                >
                                    Todos
                                </Button>
                                <Button
                                    :variant="filter === 'clientes' ? 'default' : 'outline'"
                                    size="sm"
                                    @click="changeFilter('clientes')"
                                >
                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                                    </svg>
                                    Clientes
                                </Button>
                                <Button
                                    :variant="filter === 'fornecedores' ? 'default' : 'outline'"
                                    size="sm"
                                    @click="changeFilter('fornecedores')"
                                >
                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                                    </svg>
                                    Fornecedores
                                </Button>
                            </div>
                        </div>
                    </div>
                </CardHeader>
                <CardContent class="space-y-4">
                    <!-- Desktop Table -->
                    <div class="hidden md:block overflow-x-auto">
                        <Table>
                            <TableHeader>
                                <TableRow>
                                    <TableHead>NIF</TableHead>
                                    <TableHead>Nome</TableHead>
                                    <TableHead>Telefone</TableHead>
                                    <TableHead>Telemóvel</TableHead>
                                    <TableHead>Website</TableHead>
                                    <TableHead>Email</TableHead>
                                    <TableHead>Tipo</TableHead>
                                </TableRow>
                            </TableHeader>
                            <TableBody>
                                <TableRow v-for="entity in entities.data" :key="entity.id">
                                    <TableCell class="font-medium">{{ entity.nif }}</TableCell>
                                    <TableCell>{{ entity.nome }}</TableCell>
                                    <TableCell>{{ entity.telefone || '-' }}</TableCell>
                                    <TableCell>{{ entity.telemovel || '-' }}</TableCell>
                                    <TableCell>
                                        <a v-if="entity.website" :href="entity.website" target="_blank" class="text-blue-600 hover:underline">
                                            {{ entity.website }}
                                        </a>
                                        <span v-else>-</span>
                                    </TableCell>
                                    <TableCell>{{ entity.email || '-' }}</TableCell>
                                    <TableCell>
                                        <div class="flex gap-1">
                                            <Badge v-if="entity.is_cliente" variant="secondary">
                                                Cliente
                                            </Badge>
                                            <Badge v-if="entity.is_fornecedor" variant="default">
                                                Fornecedor
                                            </Badge>
                                            <span v-if="!entity.is_cliente && !entity.is_fornecedor">-</span>
                                        </div>
                                    </TableCell>
                                </TableRow>
                            </TableBody>
                        </Table>
                    </div>

                    <!-- Mobile Cards -->
                    <div class="md:hidden space-y-4">
                        <Card v-for="entity in entities.data" :key="entity.id" class="p-4">
                            <div class="flex items-start justify-between mb-3">
                                <div class="flex-1 min-w-0">
                                    <p class="font-medium text-lg">{{ entity.nome }}</p>
                                    <p class="text-sm text-slate-500">NIF: {{ entity.nif }}</p>
                                    <div class="flex gap-1 mt-2">
                                        <Badge v-if="entity.is_cliente" variant="secondary">
                                            Cliente
                                        </Badge>
                                        <Badge v-if="entity.is_fornecedor" variant="outline">
                                            Fornecedor
                                        </Badge>
                                    </div>
                                </div>
                            </div>
                            <div class="space-y-2 text-sm border-t pt-3">
                                <div class="flex justify-between">
                                    <span class="text-slate-500">Telefone:</span>
                                    <span>{{ entity.telefone || '-' }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-slate-500">Telemóvel:</span>
                                    <span>{{ entity.telemovel || '-' }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-slate-500">Email:</span>
                                    <span class="truncate ml-2">{{ entity.email || '-' }}</span>
                                </div>
                                <div v-if="entity.website" class="flex justify-between">
                                    <span class="text-slate-500">Website:</span>
                                    <a :href="entity.website" target="_blank" class="text-blue-600 hover:underline truncate ml-2">
                                        {{ entity.website }}
                                    </a>
                                </div>
                            </div>
                        </Card>
                    </div>

                    <Pagination :pagination="entities" />
                </CardContent>
            </Card>
        </div>
    </AppLayout>
</template>

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

const deletingEntityId = ref(null)

const changeFilter = (newFilter) => {
    router.get('/entidades', { filter: newFilter }, {
        preserveState: true,
        preserveScroll: true
    })
}

const deleteEntity = (entityId) => {
    if (confirm('Tem a certeza que deseja eliminar esta entidade?')) {
        deletingEntityId.value = entityId
        router.delete(`/entidades/${entityId}`, {
            onFinish: () => {
                deletingEntityId.value = null
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
                    <h1 class="text-2xl sm:text-3xl font-bold">Entidades</h1>
                    <p class="text-slate-600 mt-1">Gerir clientes e fornecedores</p>
                </div>

                <Link href="/entidades/criar">
                    <Button class="w-full sm:w-auto">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                        </svg>
                        Nova Entidade
                    </Button>
                </Link>
            </div>

            <Card>
                <CardHeader>
                    <div class="flex flex-col gap-4">
                        <div class="flex flex-col sm:flex-row sm:justify-between sm:items-start gap-4">
                            <div>
                                <CardTitle class="mb-1.5">Lista de Entidades</CardTitle>
                                <CardDescription>Total: {{ entities.total }} entidades</CardDescription>
                            </div>


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
                                    <TableHead class="text-right">Ações</TableHead>
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

                                            <Badge v-if="entity.is_cliente && entity.is_fornecedor" variant="default">
                                                C/F
                                            </Badge>

                                            <Badge v-else-if="entity.is_cliente" variant="default">
                                                Cliente
                                            </Badge>

                                            <Badge v-else-if="entity.is_fornecedor" variant="default">
                                                Fornecedor
                                            </Badge>
                                            <span v-else>-</span>
                                        </div>
                                    </TableCell>
                                    <TableCell>
                                        <div class="flex justify-end gap-2">
                                            <Link :href="`/entidades/${entity.id}`">
                                                <Button variant="ghost" size="sm" title="Ver Detalhes">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                                    </svg>
                                                </Button>
                                            </Link>
                                            <Link :href="`/entidades/${entity.id}/editar`">
                                                <Button variant="ghost" size="sm" title="Editar">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L6.832 19.82a4.5 4.5 0 0 1-1.897 1.13l-2.685.8.8-2.685a4.5 4.5 0 0 1 1.13-1.897L16.863 4.487Zm0 0L19.5 7.125" />
                                                    </svg>
                                                </Button>
                                            </Link>
                                            <Button
                                                variant="ghost"
                                                size="sm"
                                                class="text-red-600 hover:text-red-700 hover:bg-red-50"
                                                @click="deleteEntity(entity.id)"
                                                :disabled="deletingEntityId === entity.id"
                                                title="Eliminar"
                                            >
                                                <svg v-if="deletingEntityId !== entity.id" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0" />
                                                </svg>
                                                <svg v-else class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24">
                                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
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
                        <Card v-for="entity in entities.data" :key="entity.id" class="p-4">
                            <div class="flex items-start justify-between mb-3">
                                <div class="flex-1 min-w-0">
                                    <Link :href="`/entidades/${entity.id}`" class="hover:underline hover:text-blue-600">
                                        <p class="font-medium text-lg">{{ entity.nome }}</p>
                                    </Link>
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
                                <div class="flex gap-1 ml-2 shrink-0">
                                    <Link :href="`/entidades/${entity.id}`">
                                        <Button variant="ghost" size="sm" title="Ver Detalhes">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                            </svg>
                                        </Button>
                                    </Link>
                                    <Link :href="`/entidades/${entity.id}/editar`">
                                        <Button variant="ghost" size="sm">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L6.832 19.82a4.5 4.5 0 0 1-1.897 1.13l-2.685.8.8-2.685a4.5 4.5 0 0 1 1.13-1.897L16.863 4.487Zm0 0L19.5 7.125" />
                                            </svg>
                                        </Button>
                                    </Link>
                                    <Button
                                        variant="ghost"
                                        size="sm"
                                        class="text-red-600"
                                        @click="deleteEntity(entity.id)"
                                        :disabled="deletingEntityId === entity.id"
                                    >
                                        <svg v-if="deletingEntityId !== entity.id" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0" />
                                        </svg>
                                        <svg v-else class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24">
                                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                        </svg>
                                    </Button>
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

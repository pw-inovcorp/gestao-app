<script setup>
import AppLayout from '@/layouts/AppLayout.vue'
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from '@/components/ui/table'
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card'
import Pagination from '@/components/Pagination.vue'

const props = defineProps({
    contacts: Object
})
</script>

<template>
    <AppLayout>
        <div class="max-w-7xl mx-auto space-y-6">
            <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-4">
                <div>
                    <h1 class="text-2xl sm:text-3xl font-bold">Contactos</h1>
                    <p class="text-slate-600 mt-1">Gerir contactos das entidades</p>
                </div>
            </div>

            <Card>
                <CardHeader>
                    <CardTitle>Lista de Contactos</CardTitle>
                    <CardDescription>Total: {{ contacts.total }} contactos</CardDescription>
                </CardHeader>
                <CardContent class="space-y-4">

                    <div class="hidden md:block overflow-x-auto">
                        <Table>
                            <TableHeader>
                                <TableRow>
                                    <TableHead>Nome</TableHead>
                                    <TableHead>Apelido</TableHead>
                                    <TableHead>Função</TableHead>
                                    <TableHead>Entidade</TableHead>
                                    <TableHead>Telefone</TableHead>
                                    <TableHead>Telemóvel</TableHead>
                                    <TableHead>Email</TableHead>
                                </TableRow>
                            </TableHeader>
                            <TableBody>
                                <TableRow v-for="contact in contacts.data" :key="contact.id">
                                    <TableCell class="font-medium">{{ contact.nome }}</TableCell>
                                    <TableCell>{{ contact.apelido }}</TableCell>
                                    <TableCell>{{ contact.contactFunction?.nome || '-' }}</TableCell>
                                    <TableCell>{{ contact.entity?.nome || '-' }}</TableCell>
                                    <TableCell>{{ contact.telefone || '-' }}</TableCell>
                                    <TableCell>{{ contact.telemovel || '-' }}</TableCell>
                                    <TableCell>
                                        <a
                                            v-if="contact.email"
                                            :href="`mailto:${contact.email}`"
                                            class="text-blue-600 hover:underline truncate block max-w-[200px]"
                                        >
                                            {{ contact.email }}
                                        </a>
                                        <span v-else>-</span>
                                    </TableCell>
                                </TableRow>
                            </TableBody>
                        </Table>
                    </div>

                    <!-- Mobile Cards -->
                    <div class="md:hidden space-y-4">
                        <Card v-for="contact in contacts.data" :key="contact.id" class="p-4">
                            <div class="space-y-3">
                                <div class="flex items-start justify-between border-b pb-3">
                                    <div class="flex-1 min-w-0">
                                        <p class="font-medium text-lg truncate">
                                            {{ contact.nome }} {{ contact.apelido }}
                                        </p>
                                        <p class="text-sm text-slate-500">{{ contact.contactFunction?.nome || '-' }}</p>
                                    </div>
                                </div>

                                <div class="space-y-2 text-sm">
                                    <div class="flex justify-between">
                                        <span class="text-slate-500">Entidade:</span>
                                        <span class="font-medium truncate ml-2">{{ contact.entity?.nome || '-' }}</span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span class="text-slate-500">Telefone:</span>
                                        <span>{{ contact.telefone || '-' }}</span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span class="text-slate-500">Telemóvel:</span>
                                        <span>{{ contact.telemovel || '-' }}</span>
                                    </div>
                                    <div v-if="contact.email" class="flex justify-between">
                                        <span class="text-slate-500">Email:</span>
                                        <a
                                            :href="`mailto:${contact.email}`"
                                            class="text-blue-600 hover:underline truncate ml-2"
                                        >
                                            {{ contact.email }}
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </Card>
                    </div>

                    <Pagination :pagination="contacts" />
                </CardContent>
            </Card>
        </div>
    </AppLayout>
</template>

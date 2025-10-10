<script setup>
import AppLayout from '@/layouts/AppLayout.vue'
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from '@/components/ui/table'
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card'
import Pagination from '@/components/Pagination.vue'
import {Link, router} from "@inertiajs/vue3";
import {Button} from "@/components/ui/button/index.js";
import {ref} from "vue";


const deletingContactId = ref(null)

const props = defineProps({
    contacts: Object
})

const deleteContact = (contactId) => {
    if (confirm('Tem a certeza que deseja eliminar este contacto?')) {
        deletingContactId.value = contactId
        router.delete(`/contactos/${contactId}`, {
            onFinish: () => {
                deletingContactId.value = null
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
                    <h1 class="text-2xl sm:text-3xl font-bold">Contactos</h1>
                    <p class="text-slate-600 mt-1">Gerir contactos das entidades</p>
                </div>

                <Link href="/contactos/criar">
                    <Button class="w-full sm:w-auto">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                        </svg>
                        Novo Contacto
                    </Button>
                </Link>
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
                                    <TableHead class="text-right">Ações</TableHead>
                                </TableRow>
                            </TableHeader>
                            <TableBody>
                                <TableRow v-for="contact in contacts.data" :key="contact.id">
                                    <TableCell class="font-medium">{{ contact.nome }}</TableCell>
                                    <TableCell>{{ contact.apelido }}</TableCell>
                                    <TableCell>{{ contact.contact_function?.nome || '-' }}</TableCell>
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
                                    <TableCell>
                                        <div class="flex justify-end gap-2">

                                            <Link :href="`/contactos/${contact.id}`">
                                                <Button variant="ghost" size="sm" title="Ver Detalhes">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                                    </svg>
                                                </Button>
                                            </Link>
                                            <Link :href="`/contactos/${contact.id}/editar`">
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
                                                @click="deleteContact(contact.id)"
                                                :disabled="deletingContactId === contact.id"
                                                title="Eliminar"
                                            >
                                                <svg v-if="deletingContactId !== contact.id" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
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
                        <Card v-for="contact in contacts.data" :key="contact.id" class="p-4">
                            <div class="space-y-3">
                                <div class="flex items-start justify-between border-b pb-3">
                                    <div class="flex-1 min-w-0">
                                        <p class="font-medium text-lg truncate">
                                            {{ contact.nome }} {{ contact.apelido }}
                                        </p>
                                        <p class="text-sm text-slate-500">{{ contact.entity?.nome || '-' }}</p>
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

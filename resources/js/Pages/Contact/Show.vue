<script setup>
import AppLayout from '@/layouts/AppLayout.vue'
import { Button } from '@/components/ui/button'
import { Badge } from '@/components/ui/badge'
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card'
import { Link, router } from '@inertiajs/vue3'
import { ref } from 'vue'

const props = defineProps({
    contact: Object
})

const deleting = ref(false)

const formatDate = (date) => {
    return new Date(date).toLocaleDateString('pt-PT', {
        day: '2-digit',
        month: '2-digit',
        year: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
    })
}

const deleteContact = () => {
    if (confirm('Tem a certeza que deseja eliminar este contacto?')) {
        deleting.value = true
        router.delete(`/contactos/${props.contact.id}`, {
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
                        <h1 class="text-2xl sm:text-3xl font-bold">{{ contact.nome }} {{ contact.apelido }}</h1>
                        <Badge :variant="contact.estado === 'ativo' ? 'default' : 'destructive'">
                            {{ contact.estado }}
                        </Badge>
                    </div>
                    <p class="text-slate-600">Detalhes do contacto</p>
                </div>

                <div class="flex flex-wrap gap-2">
                    <Link href="/contactos">
                        <Button variant="outline" size="sm">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                            </svg>
                            Voltar
                        </Button>
                    </Link>

                    <Link :href="`/contactos/${contact.id}/editar`">
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
                        @click="deleteContact"
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


            <div class="space-y-6">
                <div class="grid gap-6 md:grid-cols-2">

                    <Card>
                        <CardHeader class="pb-3">
                            <div class="flex items-center gap-2">
                                <svg class="w-5 h-5 text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 9h3.75M15 12h3.75M15 15h3.75M4.5 19.5h15a2.25 2.25 0 0 0 2.25-2.25V6.75A2.25 2.25 0 0 0 19.5 4.5h-15a2.25 2.25 0 0 0-2.25 2.25v10.5A2.25 2.25 0 0 0 4.5 19.5Zm6-10.125a1.875 1.875 0 1 1-3.75 0 1.875 1.875 0 0 1 3.75 0Zm1.294 6.336a6.721 6.721 0 0 1-3.17.789 6.721 6.721 0 0 1-3.168-.789 3.376 3.376 0 0 1 6.338 0Z" />
                                </svg>
                                <CardTitle class="text-lg">Informações Básicas</CardTitle>
                            </div>
                        </CardHeader>
                        <CardContent class="space-y-3 pt-0">
                            <div>
                                <label class="label">Número</label>
                                <p class="font-medium">{{ contact.numero }}</p>
                            </div>

                            <div>
                                <label class="label">Nome Completo</label>
                                <p class="font-medium">{{ contact.nome }} {{ contact.apelido }}</p>
                            </div>

                            <div>
                                <label class="label">Função</label>
                                <p>{{ contact.contact_function?.nome || '-' }}</p>
                            </div>

                            <div>
                                <label class="label">Entidade</label>
                                <p class="font-medium">{{ contact.entity?.nome || '-' }}</p>
                            </div>

                            <div>
                                <label class="label">Estado</label>
                                <div class="mt-1">
                                    <Badge :variant="contact.estado === 'ativo' ? 'default' : 'destructive'">
                                        {{ contact.estado }}
                                    </Badge>
                                </div>
                            </div>
                        </CardContent>
                    </Card>

                    <Card>
                        <CardHeader class="pb-3">
                            <div class="flex items-center gap-2">
                                <svg class="w-5 h-5 text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 6.75c0 8.284 6.716 15 15 15h2.25a2.25 2.25 0 0 0 2.25-2.25v-1.372c0-.516-.351-.966-.852-1.091l-4.423-1.106c-.44-.11-.902.055-1.173.417l-.97 1.293c-.282.376-.769.542-1.21.38a12.035 12.035 0 0 1-7.143-7.143c-.162-.441.004-.928.38-1.21l1.293-.97c.363-.271.527-.734.417-1.173L6.963 3.102a1.125 1.125 0 0 0-1.091-.852H4.5A2.25 2.25 0 0 0 2.25 4.5v2.25Z" />
                                </svg>
                                <CardTitle class="text-lg">Contactos</CardTitle>
                            </div>
                        </CardHeader>
                        <CardContent class="space-y-3 pt-0">
                            <div>
                                <label class="label">Email</label>
                                <p>
                                    <a v-if="contact.email" :href="`mailto:${contact.email}`" class="text-blue-600 hover:underline break-all">
                                        {{ contact.email }}
                                    </a>
                                    <span v-else>-</span>
                                </p>
                            </div>

                            <div>
                                <label class="label">Telefone</label>
                                <p>
                                    <a v-if="contact.telefone" :href="`tel:${contact.telefone}`" class="text-blue-600 hover:underline">
                                        {{ contact.telefone }}
                                    </a>
                                    <span v-else>-</span>
                                </p>
                            </div>

                            <div>
                                <label class="label">Telemóvel</label>
                                <p>
                                    <a v-if="contact.telemovel" :href="`tel:${contact.telemovel}`" class="text-blue-600 hover:underline">
                                        {{ contact.telemovel }}
                                    </a>
                                    <span v-else>-</span>
                                </p>
                            </div>
                        </CardContent>
                    </Card>
                </div>

                <div class="grid gap-6 md:grid-cols-2">

                    <Card class="overflow-hidden">
                        <CardHeader class="pb-3">
                            <div class="flex items-center gap-2">
                                <svg class="w-5 h-5 text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15.666 3.888A2.25 2.25 0 0 0 13.5 2.25h-3c-1.03 0-1.9.693-2.166 1.638m7.332 0c.055.194.084.4.084.612v0a.75.75 0 0 1-.75.75H9a.75.75 0 0 1-.75-.75v0c0-.212.03-.418.084-.612m7.332 0c.646.049 1.288.11 1.927.184 1.1.128 1.907 1.077 1.907 2.185V19.5a2.25 2.25 0 0 1-2.25 2.25H6.75A2.25 2.25 0 0 1 4.5 19.5V6.257c0-1.108.806-2.057 1.907-2.185a48.208 48.208 0 0 1 1.927-.184" />
                                </svg>
                                <CardTitle class="text-lg">Observações</CardTitle>
                            </div>
                        </CardHeader>
                        <CardContent class="pt-0 overflow-hidden">
                            <p class="text-sm text-slate-700 break-words break-all overflow-wrap-anywhere">
                                {{ contact.observacoes || 'Sem observações registadas.' }}
                            </p>
                        </CardContent>
                    </Card>

                    <Card>
                        <CardHeader class="pb-3">
                            <div class="flex items-center gap-2">
                                <svg class="w-5 h-5 text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9.594 3.94c.09-.542.56-.94 1.11-.94h2.593c.55 0 1.02.398 1.11.94l.213 1.281c.063.374.313.686.645.87.074.04.147.083.22.127.325.196.72.257 1.075.124l1.217-.456a1.125 1.125 0 0 1 1.37.49l1.296 2.247a1.125 1.125 0 0 1-.26 1.431l-1.003.827c-.293.241-.438.613-.43.992a7.723 7.723 0 0 1 0 .255c-.008.378.137.75.43.991l1.004.827c.424.35.534.955.26 1.43l-1.298 2.247a1.125 1.125 0 0 1-1.369.491l-1.217-.456c-.355-.133-.75-.072-1.076.124a6.47 6.47 0 0 1-.22.128c-.331.183-.581.495-.644.869l-.213 1.281c-.09.543-.56.94-1.11.94h-2.594c-.55 0-1.019-.398-1.11-.94l-.213-1.281c-.062-.374-.312-.686-.644-.87a6.52 6.52 0 0 1-.22-.127c-.325-.196-.72-.257-1.076-.124l-1.217.456a1.125 1.125 0 0 1-1.369-.49l-1.297-2.247a1.125 1.125 0 0 1 .26-1.431l1.004-.827c.292-.24.437-.613.43-.991a6.932 6.932 0 0 1 0-.255c.007-.38-.138-.751-.43-.992l-1.004-.827a1.125 1.125 0 0 1-.26-1.43l1.297-2.247a1.125 1.125 0 0 1 1.37-.491l1.216.456c.356.133.751.072 1.076-.124.072-.044.146-.086.22-.128.332-.183.582-.495.644-.869l.214-1.28Z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                                </svg>
                                <CardTitle class="text-lg">Definições</CardTitle>
                            </div>
                        </CardHeader>
                        <CardContent class="space-y-3 pt-0">
                            <div>
                                <label class="label">Consentimento RGPD</label>
                                <div class="mt-1">
                                    <Badge :variant="contact.consentimento_rgpd ? 'default' : 'secondary'">
                                        {{ contact.consentimento_rgpd ? 'Sim' : 'Não' }}
                                    </Badge>
                                </div>
                            </div>

                            <div>
                                <label class="label">Criado em</label>
                                <p class="text-sm text-slate-600">{{ formatDate(contact.created_at) }}</p>
                            </div>

                            <div>
                                <label class="label">Atualizado em</label>
                                <p class="text-sm text-slate-600">{{ formatDate(contact.updated_at) }}</p>
                            </div>
                        </CardContent>
                    </Card>
                </div>
            </div>
        </div>
    </AppLayout>
</template>

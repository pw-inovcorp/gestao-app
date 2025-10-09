<script setup>
import AppLayout from '@/layouts/AppLayout.vue'
import { Button } from '@/components/ui/button'
import { Badge } from '@/components/ui/badge'
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card'
import { Link, router } from '@inertiajs/vue3'
import { ref } from 'vue'

const props = defineProps({
    entity: Object
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

const deleteEntity = () => {
    if (confirm('Tem a certeza que deseja eliminar esta entidade?')) {
        deleting.value = true
        router.delete(`/entidades/${props.entity.id}`, {
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
            <!-- Header -->
            <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-4">
                <div class="flex-1 min-w-0">
                    <div class="flex items-center gap-3 mb-2">
                        <h1 class="text-2xl sm:text-3xl font-bold">{{ entity.nome }}</h1>
                        <Badge :variant="entity.estado === 'ativo' ? 'default' : 'destructive'">
                            {{ entity.estado }}
                        </Badge>
                    </div>
                    <p class="text-slate-600">Detalhes da entidade</p>
                </div>

                <div class="flex flex-wrap gap-2">
                    <Link href="/entidades">
                        <Button variant="outline" size="sm">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                            </svg>
                            Voltar
                        </Button>
                    </Link>

                    <Link :href="`/entidades/${entity.id}/editar`">
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
                        @click="deleteEntity"
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
                <div class="grid gap-6 md:grid-cols-3">

                    <Card>
                        <CardHeader class="pb-3">
                            <div class="flex items-center gap-2">
                                <svg class="w-5 h-5 text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z" />
                                </svg>
                                <CardTitle class="text-lg">Informações Básicas</CardTitle>
                            </div>
                        </CardHeader>
                        <CardContent class="space-y-3 pt-0">
                            <div>
                                <label class="label">Número</label>
                                <p class="font-medium">{{ entity.numero }}</p>
                            </div>

                            <div>
                                <label class="label">NIF</label>
                                <p class="font-medium">{{ entity.nif }}</p>
                            </div>

                            <div>
                                <label class="label">Nome</label>
                                <p>{{ entity.nome }}</p>
                            </div>

                            <div>
                                <label class="label">Tipo de Entidade</label>
                                <div class="flex gap-2 mt-1">
                                    <Badge v-if="entity.is_cliente" variant="default">
                                        Cliente
                                    </Badge>
                                    <Badge v-if="entity.is_fornecedor" variant="secondary">
                                        Fornecedor
                                    </Badge>
                                </div>
                            </div>

                            <div>
                                <label class="label">Estado</label>
                                <div class="mt-1">
                                    <Badge :variant="entity.estado === 'ativo' ? 'default' : 'destructive'">
                                        {{ entity.estado }}
                                    </Badge>
                                </div>
                            </div>
                        </CardContent>
                    </Card>

                    <Card>
                        <CardHeader class="pb-3">
                            <div class="flex items-center gap-2">
                                <svg class="w-5 h-5 text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1 1 15 0Z" />
                                </svg>
                                <CardTitle class="text-lg">Morada</CardTitle>
                            </div>
                        </CardHeader>
                        <CardContent class="space-y-3 pt-0">
                            <div>
                                <label class="label">Endereço</label>
                                <p>{{ entity.morada || '-' }}</p>
                            </div>

                            <div>
                                <label class="label">Código Postal</label>
                                <p>{{ entity.codigo_postal || '-' }}</p>
                            </div>

                            <div>
                                <label class="label">Localidade</label>
                                <p>{{ entity.localidade || '-' }}</p>
                            </div>

                            <div>
                                <label class="label">País</label>
                                <p>{{ entity.country?.nome || '-' }}</p>
                            </div>
                        </CardContent>
                    </Card>


                    <Card>
                        <CardHeader class="pb-3">
                            <div class="flex items-center gap-2">
                                <svg class="w-5 h-5 text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 0 1-2.25 2.25h-15a2.25 2.25 0 0 1-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0 0 19.5 4.5h-15a2.25 2.25 0 0 0-2.25 2.25m19.5 0v.243a2.25 2.25 0 0 1-1.07 1.916l-7.5 4.615a2.25 2.25 0 0 1-2.36 0L3.32 8.91a2.25 2.25 0 0 1-1.07-1.916V6.75" />
                                </svg>
                                <CardTitle class="text-lg">Contactos</CardTitle>
                            </div>
                        </CardHeader>
                        <CardContent class="space-y-3 pt-0">
                            <div>
                                <label class="label">Email</label>
                                <p>
                                    <a v-if="entity.email" :href="`mailto:${entity.email}`" class="text-blue-600 hover:underline break-all">
                                        {{ entity.email }}
                                    </a>
                                    <span v-else>-</span>
                                </p>
                            </div>

                            <div>
                                <label class="label">Telefone</label>
                                <p>
                                    <a v-if="entity.telefone" :href="`tel:${entity.telefone}`" class="text-blue-600 hover:underline">
                                        {{ entity.telefone }}
                                    </a>
                                    <span v-else>-</span>
                                </p>
                            </div>

                            <div>
                                <label class="label">Telemóvel</label>
                                <p>
                                    <a v-if="entity.telemovel" :href="`tel:${entity.telemovel}`" class="text-blue-600 hover:underline">
                                        {{ entity.telemovel }}
                                    </a>
                                    <span v-else>-</span>
                                </p>
                            </div>

                            <div>
                                <label class="label">Website</label>
                                <p>
                                    <a v-if="entity.website" :href="entity.website" target="_blank" class="text-blue-600 hover:underline break-all">
                                        {{ entity.website }}
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
                            <p class="text-sm text-slate-700 break-words break-all overflow-wrap-anywhere">{{ entity.observacoes || 'Sem observações registadas.' }}</p>
                        </CardContent>
                    </Card>

                    <Card>
                        <CardHeader class="pb-3">
                            <div class="flex items-center gap-2">
                                <svg class="w-5 h-5 text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M10.343 3.94c.09-.542.56-.94 1.11-.94h1.093c.55 0 1.02.398 1.11.94l.149.894c.07.424.384.764.78.93.398.164.855.142 1.205-.108l.737-.527a1.125 1.125 0 0 1 1.45.12l.773.774c.39.389.44 1.002.12 1.45l-.527.737c-.25.35-.272.806-.107 1.204.165.397.505.71.93.78l.893.15c.543.09.94.559.94 1.109v1.094c0 .55-.397 1.02-.94 1.11l-.894.149c-.424.07-.764.383-.929.78-.165.398-.143.854.107 1.204l.527.738c.32.447.269 1.06-.12 1.45l-.774.773a1.125 1.125 0 0 1-1.449.12l-.738-.527c-.35-.25-.806-.272-1.203-.107-.398.165-.71.505-.781.929l-.149.894c-.09.542-.56.94-1.11.94h-1.094c-.55 0-1.019-.398-1.11-.94l-.148-.894c-.071-.424-.384-.764-.781-.93-.398-.164-.854-.142-1.204.108l-.738.527c-.447.32-1.06.269-1.45-.12l-.773-.774a1.125 1.125 0 0 1-.12-1.45l.527-.737c.25-.35.272-.806.108-1.204-.165-.397-.506-.71-.93-.78l-.894-.15c-.542-.09-.94-.56-.94-1.109v-1.094c0-.55.398-1.02.94-1.11l.894-.149c.424-.07.765-.383.93-.78.165-.398.143-.854-.108-1.204l-.526-.738a1.125 1.125 0 0 1 .12-1.45l.773-.773a1.125 1.125 0 0 1 1.45-.12l.737.527c.35.25.807.272 1.204.107.397-.165.71-.505.78-.929l.15-.894Z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                                </svg>
                                <CardTitle class="text-lg">Definições</CardTitle>
                            </div>
                        </CardHeader>
                        <CardContent class="space-y-3 pt-0">
                            <div>
                                <label class="label">Consentimento RGPD</label>
                                <div class="mt-1">
                                    <Badge :variant="entity.consentimento_rgpd ? 'default' : 'secondary'">
                                        {{ entity.consentimento_rgpd ? 'Sim' : 'Não' }}
                                    </Badge>
                                </div>
                            </div>

                            <div>
                                <label class="label">Criado em</label>
                                <p class="text-sm text-slate-600">{{ formatDate(entity.created_at) }}</p>
                            </div>

                            <div>
                                <label class="label">Atualizado em</label>
                                <p class="text-sm text-slate-600">{{ formatDate(entity.updated_at) }}</p>
                            </div>
                        </CardContent>
                    </Card>
                </div>
            </div>
        </div>
    </AppLayout>
</template>

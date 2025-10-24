<script setup>
import { ref } from 'vue'
import { router, useForm } from '@inertiajs/vue3'
import AppLayout from '@/layouts/AppLayout.vue'
import { Button } from '@/components/ui/button'
import { Input } from '@/components/ui/input'
import { Label } from '@/components/ui/label'
import { Textarea } from '@/components/ui/textarea'
import { Dialog, DialogContent, DialogHeader, DialogTitle } from '@/components/ui/dialog'
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card'
import { Badge } from '@/components/ui/badge'

const props = defineProps({
    types: Array,
})

const showModal = ref(false)
const editingType = ref(null)

const form = useForm({
    nome: '',
    cor: '#3b82f6',
    descricao: '',
    estado: 'ativo',
})

function openCreate() {
    editingType.value = null
    form.reset()
    form.cor = '#3b82f6'
    form.estado = 'ativo'
    showModal.value = true
}

function openEdit(type) {
    editingType.value = type
    form.nome = type.nome
    form.cor = type.cor
    form.descricao = type.descricao || ''
    form.estado = type.estado
    showModal.value = true
}

function save() {
    if (editingType.value) {
        form.patch(`/configuracoes/calendario/tipos/${editingType.value.id}`, {
            preserveScroll: true,
            onSuccess: () => {
                showModal.value = false
            },
        })
    } else {
        form.post('/configuracoes/calendario/tipos', {
            preserveScroll: true,
            onSuccess: () => {
                showModal.value = false
            },
        })
    }
}

function deleteType(type) {
    if (!confirm(`Eliminar o tipo "${type.nome}"?`)) return

    router.delete(`/configuracoes/calendario/tipos/${type.id}`, {
        preserveScroll: true,
    })
}

function closeModal() {
    showModal.value = false
    editingType.value = null
}
</script>

<template>
    <AppLayout>
        <div class="p-6">
            <div class="flex justify-between items-center mb-6">
                <div>
                    <h1 class="text-2xl font-bold">Tipos de Calendário</h1>
                    <p class="text-sm text-muted-foreground mt-1">
                        Configure os tipos de eventos do calendário
                    </p>
                </div>
                <Button @click="openCreate">
                    + Novo Tipo
                </Button>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                <Card v-for="type in types" :key="type.id">
                    <CardHeader class="pb-3">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-3">
                                <div
                                    class="w-4 h-4 rounded-full"
                                    :style="{ backgroundColor: type.cor }"
                                />
                                <CardTitle class="text-lg">{{ type.nome }}</CardTitle>
                            </div>
                            <Badge :variant="type.estado === 'ativo' ? 'default' : 'secondary'">
                                {{ type.estado }}
                            </Badge>
                        </div>
                        <CardDescription v-if="type.descricao" class="mt-2">
                            {{ type.descricao }}
                        </CardDescription>
                    </CardHeader>
                    <CardContent>
                        <div class="flex gap-2">
                            <Button
                                @click="openEdit(type)"
                                variant="outline"
                                size="sm"
                                class="flex-1"
                            >
                                Editar
                            </Button>
                            <Button
                                @click="deleteType(type)"
                                variant="destructive"
                                size="sm"
                            >
                                ×
                            </Button>
                        </div>
                    </CardContent>
                </Card>
            </div>

            <div v-if="!types || types.length === 0" class="text-center py-12">
                <p class="text-muted-foreground mb-4">Nenhum tipo cadastrado</p>
                <Button @click="openCreate">
                    + Criar Primeiro Tipo
                </Button>
            </div>

<!--            Modal-->
            <Dialog :open="showModal" @update:open="(val) => !val && closeModal()">
                <DialogContent>
                    <DialogHeader>
                        <DialogTitle>
                            {{ editingType ? 'Editar Tipo' : 'Novo Tipo' }}
                        </DialogTitle>
                    </DialogHeader>

                    <form @submit.prevent="save" class="space-y-4">
                        <div>
                            <Label for="nome">Nome *</Label>
                            <Input
                                id="nome"
                                v-model="form.nome"
                                placeholder="Ex: Reunião"
                                required
                            />
                            <p v-if="form.errors.nome" class="text-sm text-destructive mt-1">
                                {{ form.errors.nome }}
                            </p>
                        </div>

                        <div>
                            <Label for="cor">Cor *</Label>
                            <div class="flex gap-2">
                                <Input
                                    id="cor"
                                    v-model="form.cor"
                                    type="color"
                                    class="w-20 h-10"
                                    required
                                />
                                <Input
                                    v-model="form.cor"
                                    placeholder="#3b82f6"
                                    class="flex-1"
                                />
                            </div>
                            <p v-if="form.errors.cor" class="text-sm text-destructive mt-1">
                                {{ form.errors.cor }}
                            </p>
                        </div>

                        <div>
                            <Label for="descricao">Descrição</Label>
                            <Textarea
                                id="descricao"
                                v-model="form.descricao"
                                placeholder="Descrição opcional..."
                                rows="3"
                            />
                        </div>

                        <div>
                            <Label for="estado">Estado *</Label>
                            <select
                                id="estado"
                                v-model="form.estado"
                                class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm"
                                required
                            >
                                <option value="ativo">Ativo</option>
                                <option value="inativo">Inativo</option>
                            </select>
                        </div>

                        <div class="flex justify-end gap-2 pt-4">
                            <Button
                                type="button"
                                @click="closeModal"
                                variant="outline"
                                :disabled="form.processing"
                            >
                                Cancelar
                            </Button>
                            <Button
                                type="submit"
                                :disabled="form.processing"
                            >
                                {{ form.processing ? 'A guardar...' : 'Guardar' }}
                            </Button>
                        </div>
                    </form>
                </DialogContent>
            </Dialog>
        </div>
    </AppLayout>
</template>

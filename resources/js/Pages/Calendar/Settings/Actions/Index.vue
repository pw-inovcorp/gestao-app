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
    actions: Array,
})

const showModal = ref(false)
const editingAction = ref(null)

const form = useForm({
    nome: '',
    descricao: '',
    estado: 'ativo',
})

function openCreate() {
    editingAction.value = null
    form.reset()
    form.estado = 'ativo'
    showModal.value = true
}

function openEdit(action) {
    editingAction.value = action
    form.nome = action.nome
    form.descricao = action.descricao || ''
    form.estado = action.estado
    showModal.value = true
}

function save() {
    if (editingAction.value) {
        form.patch(`/configuracoes/calendario/accoes/${editingAction.value.id}`, {
            preserveScroll: true,
            onSuccess: () => {
                showModal.value = false
            },
        })
    } else {
        form.post('/configuracoes/calendario/accoes', {
            preserveScroll: true,
            onSuccess: () => {
                showModal.value = false
            },
        })
    }
}

function deleteAction(action) {
    if (!confirm(`Eliminar a ação "${action.nome}"?`)) return

    router.delete(`/configuracoes/calendario/accoes/${action.id}`, {
        preserveScroll: true,
    })
}

function closeModal() {
    showModal.value = false
    editingAction.value = null
}
</script>

<template>
    <AppLayout>
        <div class="p-6">
            <div class="flex justify-between items-center mb-6">
                <div>
                    <h1 class="text-2xl font-bold">Ações de Calendário</h1>
                    <p class="text-sm text-muted-foreground mt-1">
                        Configure as ações disponíveis para eventos
                    </p>
                </div>
                <Button @click="openCreate">
                    + Nova Ação
                </Button>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                <Card v-for="action in actions" :key="action.id">
                    <CardHeader class="pb-3">
                        <div class="flex items-center justify-between">
                            <CardTitle class="text-lg">{{ action.nome }}</CardTitle>
                            <Badge :variant="action.estado === 'ativo' ? 'default' : 'secondary'">
                                {{ action.estado }}
                            </Badge>
                        </div>
                        <CardDescription v-if="action.descricao" class="mt-2">
                            {{ action.descricao }}
                        </CardDescription>
                    </CardHeader>
                    <CardContent>
                        <div class="flex gap-2">
                            <Button
                                @click="openEdit(action)"
                                variant="outline"
                                size="sm"
                                class="flex-1"
                            >
                                Editar
                            </Button>
                            <Button
                                @click="deleteAction(action)"
                                variant="destructive"
                                size="sm"
                            >
                                ×
                            </Button>
                        </div>
                    </CardContent>
                </Card>
            </div>

            <div v-if="!actions || actions.length === 0" class="text-center py-12">
                <p class="text-muted-foreground mb-4">Nenhuma ação cadastrada</p>
                <Button @click="openCreate">
                    + Criar Primeira Ação
                </Button>
            </div>

<!--            Modal-->
            <Dialog :open="showModal" @update:open="(val) => !val && closeModal()">
                <DialogContent>
                    <DialogHeader>
                        <DialogTitle>
                            {{ editingAction ? 'Editar Ação' : 'Nova Ação' }}
                        </DialogTitle>
                    </DialogHeader>

                    <form @submit.prevent="save" class="space-y-4">
                        <div>
                            <Label for="nome">Nome *</Label>
                            <Input
                                id="nome"
                                v-model="form.nome"
                                placeholder="Ex: Apresentar proposta"
                                required
                            />
                            <p v-if="form.errors.nome" class="text-sm text-destructive mt-1">
                                {{ form.errors.nome }}
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

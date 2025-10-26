<script setup>
import { ref } from 'vue'
import { router, useForm } from '@inertiajs/vue3'
import AppLayout from '@/layouts/AppLayout.vue'
import { Button } from '@/components/ui/button/index.js'
import { Input } from '@/components/ui/input/index.js'
import { Label } from '@/components/ui/label/index.js'
import { Textarea } from '@/components/ui/textarea/index.js'
import { Dialog, DialogContent, DialogHeader, DialogTitle } from '@/components/ui/dialog/index.js'
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card/index.js'
import { Badge } from '@/components/ui/badge/index.js'

const props = defineProps({
    ivaRates: Array,
})

const showModal = ref(false)
const editingRate = ref(null)

const form = useForm({
    nome: '',
    taxa: '',
    descricao: '',
    estado: 'ativo',
})

function openCreate() {
    editingRate.value = null
    form.reset()
    form.estado = 'ativo'
    showModal.value = true
}

function openEdit(rate) {
    editingRate.value = rate
    form.nome = rate.nome
    form.taxa = rate.taxa
    form.descricao = rate.descricao || ''
    form.estado = rate.estado
    showModal.value = true
}

function save() {
    if (editingRate.value) {
        form.patch(`/configuracoes/iva/${editingRate.value.id}`, {
            preserveScroll: true,
            onSuccess: () => {
                showModal.value = false
            },
        })
    } else {
        form.post('/configuracoes/iva', {
            preserveScroll: true,
            onSuccess: () => {
                showModal.value = false
            },
        })
    }
}

function deleteRate(rate) {
    if (!confirm(`Eliminar a taxa "${rate.nome}"?`)) return

    router.delete(`/configuracoes/iva/${rate.id}`, {
        preserveScroll: true,
    })
}

function closeModal() {
    showModal.value = false
    editingRate.value = null
}
</script>

<template>
    <AppLayout>
        <div class="p-6">
            <div class="flex justify-between items-center mb-6">
                <div>
                    <h1 class="text-2xl font-bold">Taxas de IVA</h1>
                    <p class="text-sm text-muted-foreground mt-1">
                        Configure as taxas de IVA disponíveis
                    </p>
                </div>
                <Button @click="openCreate">
                    + Nova Taxa
                </Button>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                <Card v-for="rate in ivaRates" :key="rate.id">
                    <CardHeader class="pb-3">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-3">
                                <div class="text-2xl font-bold text-primary">
                                    {{ rate.taxa }}%
                                </div>
                                <CardTitle class="text-lg">{{ rate.nome }}</CardTitle>
                            </div>
                            <Badge :variant="rate.estado === 'ativo' ? 'default' : 'secondary'">
                                {{ rate.estado }}
                            </Badge>
                        </div>
                        <CardDescription v-if="rate.descricao" class="mt-2">
                            {{ rate.descricao }}
                        </CardDescription>
                    </CardHeader>
                    <CardContent>
                        <div class="flex gap-2">
                            <Button
                                @click="openEdit(rate)"
                                variant="outline"
                                size="sm"
                                class="flex-1"
                            >
                                Editar
                            </Button>
                            <Button
                                @click="deleteRate(rate)"
                                variant="destructive"
                                size="sm"
                            >
                                ×
                            </Button>
                        </div>
                    </CardContent>
                </Card>
            </div>

            <div v-if="!ivaRates || ivaRates.length === 0" class="text-center py-12">
                <p class="text-muted-foreground mb-4">Nenhuma taxa cadastrada</p>
                <Button @click="openCreate">
                    + Criar Primeira Taxa
                </Button>
            </div>

            <!-- Modal -->
            <Dialog :open="showModal" @update:open="(val) => !val && closeModal()">
                <DialogContent>
                    <DialogHeader>
                        <DialogTitle>
                            {{ editingRate ? 'Editar Taxa de IVA' : 'Nova Taxa de IVA' }}
                        </DialogTitle>
                    </DialogHeader>

                    <form @submit.prevent="save" class="space-y-4">
                        <div>
                            <Label for="nome">Nome *</Label>
                            <Input
                                id="nome"
                                v-model="form.nome"
                                placeholder="Ex: Taxa Normal"
                                required
                            />
                            <p v-if="form.errors.nome" class="text-sm text-destructive mt-1">
                                {{ form.errors.nome }}
                            </p>
                        </div>

                        <div>
                            <Label for="taxa">Taxa (%) *</Label>
                            <Input
                                id="taxa"
                                v-model="form.taxa"
                                type="number"
                                step="0.01"
                                min="0"
                                max="100"
                                placeholder="Ex: 23.00"
                                required
                            />
                            <p v-if="form.errors.taxa" class="text-sm text-destructive mt-1">
                                {{ form.errors.taxa }}
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

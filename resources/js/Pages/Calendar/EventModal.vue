<script setup>
import { useForm } from '@inertiajs/vue3'
import {watch} from "vue";
import { Button } from '@/components/ui/button'
import { Input } from '@/components/ui/input'
import { Label } from '@/components/ui/label'
import { Textarea } from '@/components/ui/textarea'
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select'
import { Dialog, DialogContent, DialogHeader, DialogTitle } from '@/components/ui/dialog'
import { Alert, AlertDescription } from '@/components/ui/alert'

const props = defineProps({
    show: Boolean,
    event: Object,
    isOwner: Boolean,
    users: Array,
    entities: Array,
    types: Array,
    actions: Array,
})

const emit = defineEmits(['close', 'saved'])

const form = useForm({
    titulo: '',
    data_evento: '',
    hora: '09:00',
    duracao: 15,
    entity_id: null,
    calendar_type_id: null,
    calendar_action_id: null,
    descricao: '',
    conhecimento: '',
    partilha: [],
    estado: 'ativo'
})

watch(() => props.show, (newVal) => {
    if (newVal) {
        if (props.event?.id) {
            form.titulo = props.event.titulo
            form.data_evento = props.event.data
            form.hora = props.event.hora?.substring(0, 5) || '09:00'
            form.duracao = props.event.duracao
            form.entity_id = props.event.entity_id
            form.calendar_type_id = props.event.calendar_type_id
            form.calendar_action_id = props.event.calendar_action_id
            form.descricao = props.event.descricao || ''
            form.conhecimento = props.event.conhecimento || ''
            form.partilha = props.event.partilha || []
            form.estado = props.event.estado
        } else {
            // Novo evento
            form.reset()
            form.data_evento = props.event?.data_evento || ''
            form.hora = '09:00'
            form.duracao = 15
            form.estado = 'ativo'
        }
    }
})

const save = () => {
    if (!props.isOwner && props.event?.id) return

    if (props.event?.id) {
        // EDITAR evento existente
        form.patch(`/calendario/${props.event.id}`, {
            preserveScroll: true,
            onSuccess: () => {
                emit('saved')
                emit('close')
            },
        })
    } else {
        // CRIAR novo evento
        form.post('/calendario', {
            preserveScroll: true,
            onSuccess: () => {
                emit('saved')
                emit('close')
            },
        })
    }
}

const deleteEvent = () => {
    if (!props.isOwner || !props.event?.id || !confirm('Eliminar este evento?')) return

    form.delete(`/calendario/${props.event.id}`, {
        preserveScroll: true,
        onSuccess: () => {
            emit('saved')
            emit('close')
        },
    })
}
</script>

<template>
    <Dialog :open="show" @update:open="(val) => !val && $emit('close')">
        <DialogContent class="max-w-2xl max-h-[90vh] overflow-y-auto">
            <DialogHeader>
                <DialogTitle>
                    {{ event?.id ? 'Evento #' + event.id : 'Novo Evento' }}
                </DialogTitle>
            </DialogHeader>

            <Alert v-if="event?.id && !isOwner" variant="warning">
                <AlertDescription>
                    ⚠️ Apenas visualização
                </AlertDescription>
            </Alert>

            <form @submit.prevent="save" class="space-y-4">
                <div>
                    <Label for="titulo">Título *</Label>
                    <Input
                        id="titulo"
                        v-model="form.titulo"
                        required
                        :disabled="!isOwner"
                    />
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <Label for="data">Data *</Label>
                        <Input
                            id="data"
                            v-model="form.data_evento"
                            type="date"
                            required
                            :disabled="!isOwner"
                        />
                    </div>
                    <div>
                        <Label for="hora">Hora *</Label>
                        <Input
                            id="hora"
                            v-model="form.hora"
                            type="time"
                            required
                            :disabled="!isOwner"
                        />
                    </div>
                </div>

                <div>
                    <Label for="duracao">Duração (minutos) *</Label>
                    <Input
                        id="duracao"
                        v-model="form.duracao"
                        type="number"
                        min="15"
                        step="15"
                        required
                        :disabled="!isOwner"
                    />
                </div>

                <div>
                    <Label for="entity">Entidade</Label>
                    <Select v-model="form.entity_id" :disabled="!isOwner">
                        <SelectTrigger>
                            <SelectValue placeholder="Selecione uma entidade" />
                        </SelectTrigger>
                        <SelectContent>
                            <SelectItem :value="null">Nenhuma</SelectItem>
                            <SelectItem v-for="e in entities" :key="e.id" :value="e.id">
                                {{ e.nome }}
                            </SelectItem>
                        </SelectContent>
                    </Select>
                </div>

                <div>
                    <Label for="type">Tipo</Label>
                    <Select v-model="form.calendar_type_id" :disabled="!isOwner">
                        <SelectTrigger>
                            <SelectValue placeholder="Selecione um tipo" />
                        </SelectTrigger>
                        <SelectContent>
                            <SelectItem :value="null">Nenhum</SelectItem>
                            <SelectItem v-for="t in types" :key="t.id" :value="t.id">
                                {{ t.nome }}
                            </SelectItem>
                        </SelectContent>
                    </Select>
                </div>

                <div>
                    <Label for="action">Ação</Label>
                    <Select v-model="form.calendar_action_id" :disabled="!isOwner">
                        <SelectTrigger>
                            <SelectValue placeholder="Selecione uma ação" />
                        </SelectTrigger>
                        <SelectContent>
                            <SelectItem :value="null">Nenhuma</SelectItem>
                            <SelectItem v-for="a in actions" :key="a.id" :value="a.id">
                                {{ a.nome }}
                            </SelectItem>
                        </SelectContent>
                    </Select>
                </div>

                <div>
                    <Label for="descricao">Descrição</Label>
                    <Textarea
                        id="descricao"
                        v-model="form.descricao"
                        rows="3"
                        :disabled="!isOwner"
                    />
                </div>

                <div>
                    <Label for="conhecimento">Conhecimento</Label>
                    <Textarea
                        id="conhecimento"
                        v-model="form.conhecimento"
                        rows="2"
                        :disabled="!isOwner"
                    />
                </div>

                <div v-if="isOwner">
                    <Label for="partilha">Partilhar com</Label>
                    <select
                        id="partilha"
                        v-model="form.partilha"
                        multiple
                        class="flex min-h-[100px] w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2"
                    >
                        <option v-for="u in users" :key="u.id" :value="u.id">{{ u.name }}</option>
                    </select>
                    <p class="text-xs text-muted-foreground mt-1">Ctrl+clique para múltiplos</p>
                </div>

                <!-- Botões -->
                <div class="flex justify-between pt-4">
                    <Button
                        v-if="event?.id && isOwner"
                        type="button"
                        @click="deleteEvent"
                        variant="destructive"
                        :disabled="form.processing"
                    >
                        Eliminar
                    </Button>
                    <div v-else></div>

                    <div class="flex gap-2">
                        <Button
                            type="button"
                            @click="$emit('close')"
                            variant="outline"
                            :disabled="form.processing"
                        >
                            {{ isOwner ? 'Cancelar' : 'Fechar' }}
                        </Button>
                        <Button
                            v-if="isOwner"
                            type="submit"
                            :disabled="form.processing"
                        >
                            {{ form.processing ? 'A guardar...' : 'Guardar' }}
                        </Button>
                    </div>
                </div>
            </form>
        </DialogContent>
    </Dialog>
</template>

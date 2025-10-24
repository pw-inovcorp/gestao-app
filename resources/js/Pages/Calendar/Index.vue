<script setup>
import { ref } from 'vue'
import AppLayout from '@/layouts/AppLayout.vue'
import EventModal from './EventModal.vue'
import FullCalendar from '@fullcalendar/vue3'
import dayGridPlugin from '@fullcalendar/daygrid'
import timeGridPlugin from '@fullcalendar/timegrid'
import interactionPlugin from '@fullcalendar/interaction'
import { Button } from '@/components/ui/button'
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select'
import { Alert, AlertDescription } from '@/components/ui/alert'


const props = defineProps({
    users: Array,
    entities: Array,
    types: Array,
    actions: Array,
})

const calendar = ref(null)
const showModal = ref(false)
const editingEvent = ref(null)
const loading = ref(false)
const isOwner = ref(true)
const filterUser = ref(null)
const filterEntity = ref(null)

const calendarOptions = {
    plugins: [dayGridPlugin, timeGridPlugin, interactionPlugin],
    initialView: 'dayGridMonth',
    locale: 'pt',
    headerToolbar: {
        left: 'prev,next today',
        center: 'title',
        right: 'dayGridMonth,timeGridWeek,timeGridDay'
    },
    buttonText: {
        today: 'Hoje',
        month: 'Mês',
        week: 'Semana',
        day: 'Dia'
    },
    editable: false,
    selectable: true,
    events: loadEvents,
    select: handleDateClick,
    eventClick: handleEventClick,
}

async function loadEvents(info, successCallback) {
    let url = `/calendario/eventos?start=${info.startStr}&end=${info.endStr}`

    if (filterUser.value) url += `&user_id=${filterUser.value}`
    if (filterEntity.value) url += `&entity_id=${filterEntity.value}`

    const res = await fetch(url)
    const events = await res.json()
    successCallback(events)
}

function handleDateClick(info) {
    editingEvent.value = { data_evento: info.startStr }
    isOwner.value = true
    showModal.value = true
}

async function handleEventClick(info) {
    info.jsEvent.preventDefault()
    loading.value = true

    try {
        const res = await fetch(`/calendario/${info.event.id}`)
        if (!res.ok) {
            alert('Sem permissão para ver este evento.')
            return
        }

        const event = await res.json()
        editingEvent.value = event
        isOwner.value = event.isOwner
        showModal.value = true
    } catch (e) {
        alert('Erro ao carregar evento')
    } finally {
        loading.value = false
    }
}

function applyFilters() {
    calendar.value.getApi().refetchEvents()
}

function closeModal() {
    showModal.value = false
    editingEvent.value = null
    isOwner.value = true
}

function handleSaved() {
    calendar.value.getApi().refetchEvents()
}
</script>

<template>
    <AppLayout>
        <div class="p-6">
            <h1 class="text-2xl font-bold mb-4">Calendário</h1>

            <div class="bg-white p-4 rounded-lg border mb-4">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-sm font-medium mb-2">Utilizador</label>
                        <Select v-model="filterUser" @update:model-value="applyFilters">
                            <SelectTrigger>
                                <SelectValue placeholder="Meus eventos" />
                            </SelectTrigger>
                            <SelectContent>
                                <SelectItem :value="null">Meus eventos</SelectItem>
                                <SelectItem v-for="u in users" :key="u.id" :value="u.id">
                                    {{ u.name }}
                                </SelectItem>
                            </SelectContent>
                        </Select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium mb-2">Entidade</label>
                        <Select v-model="filterEntity" @update:model-value="applyFilters">
                            <SelectTrigger>
                                <SelectValue placeholder="Todas as entidades" />
                            </SelectTrigger>
                            <SelectContent>
                                <SelectItem :value="null">Todas as entidades</SelectItem>
                                <SelectItem v-for="e in entities" :key="e.id" :value="e.id">
                                    {{ e.nome }}
                                </SelectItem>
                            </SelectContent>
                        </Select>
                    </div>

                    <div class="flex items-end">
                        <Button
                            v-if="filterUser || filterEntity"
                            @click="filterUser = null; filterEntity = null; applyFilters()"
                            variant="outline"
                            class="w-full"
                        >
                            Limpar Filtros
                        </Button>
                    </div>
                </div>
            </div>

            <Alert class="mb-4">
                <AlertDescription>
                    <span v-if="!filterUser">
                        A ver: <strong>seus eventos</strong> + eventos <strong>partilhados com você</strong>
                    </span>
                    <span v-else>
                        A ver: <strong>todos os eventos</strong> do utilizador filtrado
                    </span>
                </AlertDescription>
            </Alert>

            <div class="bg-white p-4 rounded-lg border">
                <FullCalendar ref="calendar" :options="calendarOptions" />
            </div>

            <EventModal
                :show="showModal"
                :event="editingEvent"
                :isOwner="isOwner"
                :users="users"
                :entities="entities"
                :types="types"
                :actions="actions"
                @close="closeModal"
                @saved="handleSaved"
            />

            <div v-if="loading" class="fixed inset-0 bg-black/50 flex items-center justify-center z-50">
                <div class="bg-white rounded-lg p-6">
                    <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-blue-600 mx-auto"></div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>

<style scoped>
@media (max-width: 768px) {
    :deep(.fc .fc-toolbar-chunk:first-child),
    :deep(.fc .fc-toolbar-chunk:last-child) {
        display: none;
    }

    :deep(.fc .fc-toolbar-chunk:nth-child(2)) {
        width: 100%;
        text-align: center;
    }
}
</style>

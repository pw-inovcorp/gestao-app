<script setup>
import { ref, onMounted } from 'vue'
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card'
import { Button } from '@/components/ui/button'
import { Calendar, ChevronRight, Clock } from 'lucide-vue-next'
import { Link } from '@inertiajs/vue3'
import {
    Dialog,
    DialogContent,
    DialogHeader,
    DialogTitle,
} from '@/components/ui/dialog'
import axios from 'axios'

const weekData = ref(null)
const loading = ref(true)
const showModal = ref(false)
const selectedEvent = ref(null)

function openEvent(event) {
    selectedEvent.value = event
    showModal.value = true
}

function closeModal() {
    showModal.value = false
    selectedEvent.value = null
}

async function loadWeek() {
    loading.value = true

    try {
        const response = await axios.get('/calendario/eventos-semana')
        weekData.value = response.data
    } catch (error) {
        console.log('Erro:', error)
    }

    loading.value = false
}

onMounted(() => {
    loadWeek()
})
</script>

<template>
    <Card>
        <CardHeader class="flex-row items-center justify-between pb-4">
            <CardTitle class="text-lg flex items-center gap-2">
                <Calendar class="h-5 w-5" />
                Calendário (2 Semanas)
            </CardTitle>
            <Link href="/calendario">
                <Button variant="ghost" size="sm">
                    Ver Completo
                    <ChevronRight class="h-4 w-4 ml-1" />
                </Button>
            </Link>
        </CardHeader>

        <CardContent>
            <div v-if="loading" class="py-8 text-center text-sm text-muted-foreground">
                A carregar...
            </div>

            <div v-else-if="weekData" class="space-y-4">
                <div v-for="(week, index) in weekData.weeks" :key="index">
                    <div v-if="index > 0" class="text-xs font-medium text-muted-foreground uppercase mb-2">
                        Próxima Semana
                    </div>

                    <div class="grid grid-cols-7 gap-px bg-border rounded-lg overflow-hidden">
                        <div v-for="day in week.days" :key="day.date" class="bg-card">
                            <div class="p-2 text-center border-b" :class="day.isToday ? 'bg-primary/10' : 'bg-muted/50'">
                                <div class="text-xs font-medium text-muted-foreground uppercase">
                                    {{ day.dayName }}
                                </div>
                                <div class="text-lg font-semibold mt-1" :class="day.isToday ? 'text-primary' : ''">
                                    {{ day.dayNumber }}
                                </div>
                            </div>

                            <div class="p-2 min-h-[120px] space-y-1">
                                <div
                                    v-for="event in day.events"
                                    :key="event.id"
                                    @click="openEvent(event)"
                                    class="text-xs p-1.5 rounded cursor-pointer hover:opacity-80"
                                    :style="'background-color: ' + event.cor + '; color: white;'"
                                >
                                    <div class="font-medium truncate">{{ event.hora }}</div>
                                    <div class="truncate">{{ event.titulo }}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </CardContent>
    </Card>

    <Dialog :open="showModal" @update:open="closeModal">
        <DialogContent v-if="selectedEvent" class="w-[calc(100vw-2rem)] sm:max-w-lg">
            <DialogHeader>
                <DialogTitle>{{ selectedEvent.titulo }}</DialogTitle>
            </DialogHeader>

            <div class="space-y-4 mt-4">
                <div class="flex items-start gap-3">
                    <Calendar class="h-5 w-5 text-muted-foreground mt-0.5" />
                    <div>
                        <p class="text-sm font-medium">Data</p>
                        <p class="text-sm text-muted-foreground">{{ selectedEvent.dataFormatada }}</p>
                    </div>
                </div>

                <div class="flex items-start gap-3">
                    <Clock class="h-5 w-5 text-muted-foreground mt-0.5" />
                    <div>
                        <p class="text-sm font-medium">Hora</p>
                        <p class="text-sm text-muted-foreground">{{ selectedEvent.hora }} ({{ selectedEvent.duracao }} min)</p>
                    </div>
                </div>

                <div v-if="selectedEvent.tipo" class="flex items-start gap-3">
                    <div class="w-5 h-5 rounded mt-0.5" :style="`background-color: ${selectedEvent.cor}`"></div>
                    <div>
                        <p class="text-sm font-medium">Tipo</p>
                        <p class="text-sm text-muted-foreground">{{ selectedEvent.tipo }}</p>
                    </div>
                </div>

                <div v-if="selectedEvent.entidade">
                    <p class="text-sm font-medium">Entidade</p>
                    <p class="text-sm text-muted-foreground">{{ selectedEvent.entidade }}</p>
                </div>

                <div v-if="selectedEvent.accao">
                    <p class="text-sm font-medium">Ação</p>
                    <p class="text-sm text-muted-foreground">{{ selectedEvent.accao }}</p>
                </div>

                <div v-if="selectedEvent.descricao" class="pt-2 border-t">
                    <p class="text-sm font-medium mb-2">Descrição</p>
                    <p class="text-sm text-muted-foreground">{{ selectedEvent.descricao }}</p>
                </div>

                <div v-if="selectedEvent.conhecimento" class="pt-2 border-t">
                    <p class="text-sm font-medium mb-2">Conhecimento</p>
                    <p class="text-sm text-muted-foreground">{{ selectedEvent.conhecimento }}</p>
                </div>
            </div>
        </DialogContent>
    </Dialog>
</template>

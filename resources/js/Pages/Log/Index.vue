<script setup>
import AppLayout from '@/layouts/AppLayout.vue'
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card'
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from '@/components/ui/table'
import Pagination from '@/components/Pagination.vue'

defineProps({
logs: Object
})
</script>

<template>
    <AppLayout>
        <div class="max-w-7xl mx-auto space-y-6">
            <div>
                <h1 class="text-3xl font-bold">Logs de Atividade</h1>
                <p class="text-slate-600 mt-1">Histórico de todas as ações no sistema</p>
            </div>


            <Card>
                <CardHeader>
                    <CardTitle>Lista de Logs</CardTitle>
                    <CardDescription>Total: {{ logs.total }} registos</CardDescription>
                </CardHeader>
                <CardContent>
                    <div class="overflow-x-auto">
                        <Table>
                            <TableHeader>
                                <TableRow>
                                    <TableHead>Data</TableHead>
                                    <TableHead>Hora</TableHead>
                                    <TableHead>Utilizador</TableHead>
                                    <TableHead>Menu</TableHead>
                                    <TableHead>Ação</TableHead>
                                    <TableHead>Dispositivo</TableHead>
                                    <TableHead>IP</TableHead>
                                </TableRow>
                            </TableHeader>
                            <TableBody>
                                <TableRow v-for="log in logs.data" :key="log.id">
                                    <TableCell>{{ log.data }}</TableCell>
                                    <TableCell>{{ log.hora }}</TableCell>
                                    <TableCell>{{ log.utilizador }}</TableCell>
                                    <TableCell>{{ log.menu }}</TableCell>
                                    <TableCell>{{ log.accao }}</TableCell>
                                    <TableCell>{{ log.dispositivo }}</TableCell>
                                    <TableCell>{{ log.ip }}</TableCell>
                                </TableRow>
                            </TableBody>
                        </Table>
                    </div>

                    <div class="mt-4">
                        <Pagination :pagination="logs" />
                    </div>
                </CardContent>
            </Card>
        </div>
    </AppLayout>
</template>

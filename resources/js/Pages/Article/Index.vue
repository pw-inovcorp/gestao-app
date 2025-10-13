<script setup>
import AppLayout from '@/layouts/AppLayout.vue'
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from '@/components/ui/table'
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card'
import { Badge } from '@/components/ui/badge'
import Pagination from '@/components/Pagination.vue'
import {Button} from "@/components/ui/button/index.js";
import {Link, router} from "@inertiajs/vue3";
import {ref} from "vue";

const props = defineProps({
    articles: Object
})

const deletingArticleId = ref(null)

const formatPrice = (price) => {
    return new Intl.NumberFormat('pt-PT', {
        style: 'currency',
        currency: 'EUR'
    }).format(price)
}

const deleteArticle = (articleId) => {
    if (confirm('Tem a certeza que deseja eliminar este artigo?')) {
        deletingArticleId.value = articleId
        router.delete(`/artigos/${articleId}`, {
            onFinish: () => {
                deletingArticleId.value = null
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
                    <h1 class="text-2xl sm:text-3xl font-bold">Artigos</h1>
                    <p class="text-slate-600 mt-1">Gerir artigos do sistema</p>
                </div>

                <Link href="/artigos/criar">
                    <Button class="w-full sm:w-auto">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                        </svg>
                        Novo Artigo
                    </Button>
                </Link>
            </div>

            <Card>
                <CardHeader>
                    <CardTitle>Lista de Artigos</CardTitle>
                    <CardDescription>Total: {{ articles.total }} artigos</CardDescription>
                </CardHeader>
                <CardContent class="space-y-4">

                    <div class="hidden md:block overflow-x-auto">
                        <Table>
                            <TableHeader>
                                <TableRow>
                                    <TableHead>Referência</TableHead>
                                    <TableHead>Foto</TableHead>
                                    <TableHead>Nome</TableHead>
                                    <TableHead>Descrição</TableHead>
                                    <TableHead>Preço</TableHead>
                                    <TableHead>IVA</TableHead>
                                    <TableHead>Estado</TableHead>
                                    <TableHead class="text-right">Ações</TableHead>
                                </TableRow>
                            </TableHeader>
                            <TableBody>
                                <TableRow v-for="article in articles.data" :key="article.id">
                                    <TableCell class="font-medium">{{ article.referencia }}</TableCell>
                                    <TableCell>
                                        <div v-if="article.foto" class="w-12 h-12 rounded-md overflow-hidden bg-slate-100">
                                            <img :src="`/storage/${article.foto}`" :alt="article.nome" class="w-full h-full object-cover">
                                        </div>
                                        <div v-else class="w-12 h-12 rounded-md bg-slate-100 flex items-center justify-center">
                                            <svg class="w-6 h-6 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m2.25 15.75 5.159-5.159a2.25 2.25 0 0 1 3.182 0l5.159 5.159m-1.5-1.5 1.409-1.409a2.25 2.25 0 0 1 3.182 0l2.909 2.909m-18 3.75h16.5a1.5 1.5 0 0 0 1.5-1.5V6a1.5 1.5 0 0 0-1.5-1.5H3.75A1.5 1.5 0 0 0 2.25 6v12a1.5 1.5 0 0 0 1.5 1.5Zm10.5-11.25h.008v.008h-.008V8.25Zm.375 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Z" />
                                            </svg>
                                        </div>
                                    </TableCell>
                                    <TableCell class="font-medium">{{ article.nome }}</TableCell>
                                    <TableCell>
                                        <span class="max-w-[200px] truncate block" :title="article.descricao">
                                            {{ article.descricao || '-' }}
                                        </span>
                                    </TableCell>
                                    <TableCell class="font-medium">{{ formatPrice(article.preco) }}</TableCell>
                                    <TableCell>
                                        <span v-if="article.iva_rate">{{ article.iva_rate.taxa }}%</span>
                                        <span v-else>-</span>
                                    </TableCell>
                                    <TableCell>
                                        <Badge :variant="article.estado === 'ativo' ? 'default' : 'destructive'">
                                            {{ article.estado }}
                                        </Badge>
                                    </TableCell>
                                    <TableCell>
                                        <div class="flex justify-end gap-2">

                                            <Link :href="`/artigos/${article.id}`">
                                                <Button variant="ghost" size="sm" title="Ver Detalhes">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                                    </svg>
                                                </Button>
                                            </Link>

                                            <Link :href="`/artigos/${article.id}/editar`">
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
                                                @click="deleteArticle(article.id)"
                                                :disabled="deletingArticleId === article.id"
                                                title="Eliminar"
                                            >
                                                <svg v-if="deletingArticleId !== article.id" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
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
                        <Card v-for="article in articles.data" :key="article.id" class="p-4">
                            <div class="flex gap-4 mb-3">
                                <div v-if="article.foto" class="w-16 h-16 rounded-md overflow-hidden bg-slate-100 shrink-0">
                                    <img :src="`/storage/${article.foto}`" :alt="article.nome" class="w-full h-full object-cover">
                                </div>
                                <div v-else class="w-16 h-16 rounded-md bg-slate-100 flex items-center justify-center shrink-0">
                                    <svg class="w-8 h-8 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m2.25 15.75 5.159-5.159a2.25 2.25 0 0 1 3.182 0l5.159 5.159m-1.5-1.5 1.409-1.409a2.25 2.25 0 0 1 3.182 0l2.909 2.909m-18 3.75h16.5a1.5 1.5 0 0 0 1.5-1.5V6a1.5 1.5 0 0 0-1.5-1.5H3.75A1.5 1.5 0 0 0 2.25 6v12a1.5 1.5 0 0 0 1.5 1.5Zm10.5-11.25h.008v.008h-.008V8.25Zm.375 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Z" />
                                    </svg>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="font-medium text-lg truncate">{{ article.nome }}</p>
                                    <p class="text-sm text-slate-500">{{ article.referencia }}</p>
                                    <Badge :variant="article.estado === 'ativo' ? 'default' : 'destructive'" class="mt-1">
                                        {{ article.estado }}
                                    </Badge>
                                </div>

                                <!-- Meter aqui as ações para Mobile -->
                            </div>

                            <div class="space-y-2 text-sm border-t pt-3">
                                <div v-if="article.descricao" class="flex justify-between">
                                    <span class="text-slate-500">Descrição:</span>
                                    <span class="truncate ml-2">{{ article.descricao }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-slate-500">Preço:</span>
                                    <span class="font-medium">{{ formatPrice(article.preco) }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-slate-500">IVA:</span>
                                    <span>{{ article.iva_rate ? article.iva_rate.taxa + '%' : '-' }}</span>
                                </div>
                            </div>
                        </Card>
                    </div>

                    <Pagination :pagination="articles" />
                </CardContent>
            </Card>
        </div>
    </AppLayout>
</template>

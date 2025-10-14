<script setup>
import { ref, computed, watch } from 'vue'
import { Input } from '@/components/ui/input'
import { Button } from '@/components/ui/button'
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card'
import { FormControl, FormField, FormItem, FormLabel, FormMessage, FormDescription } from '@/components/ui/form'
import { Select, SelectContent, SelectGroup, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select'
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from '@/components/ui/table'

const props = defineProps({
    clients: Array,
    articles: Array,
    suppliers: Array,
    existingItems: { type: Array, default: () => [] },
})

const items = ref([...props.existingItems])
const searchQuery = ref('')

const filteredArticles = computed(() => {
    if (!searchQuery.value) return []
    const q = searchQuery.value.toLowerCase()
    return props.articles.filter(a =>
        a.nome.toLowerCase().includes(q) || a.referencia.toLowerCase().includes(q)
    ).slice(0, 10)
})

const addItem = (article) => {
    const existing = items.value.find(i => i.article_id === article.id)
    if (existing) {
        existing.quantidade++
    } else {
        items.value.push({
            article_id: article.id,
            article: article,
            fornecedor_id: null,
            quantidade: 1,
            preco_unitario: null,
            preco_custo: parseFloat(article.preco),
            subtotal: 0
        })
    }
    searchQuery.value = ''
}

const removeItem = (index) => items.value.splice(index, 1)

const calculateSubtotal = (item) => {
    item.subtotal = item.quantidade * (item.preco_unitario || 0)
}

watch(items, () => items.value.forEach(calculateSubtotal), { deep: true })

const totalValue = computed(() =>
    items.value.reduce((sum, i) => sum + i.subtotal, 0)
)

const totalWithIva = computed(() =>
    items.value.reduce((sum, i) => {
        const iva = (i.article.iva_rate?.taxa || 0) / 100
        return sum + i.subtotal * (1 + iva)
    }, 0)
)

const formatPrice = (price) =>
    new Intl.NumberFormat('pt-PT', { style: 'currency', currency: 'EUR' }).format(price)

defineExpose({ items, totalValue, totalWithIva })
</script>

<template>
    <div class="space-y-6">
        <Card>
            <CardHeader>
                <CardTitle>Informações Básicas</CardTitle>
                <CardDescription>Dados principais da encomenda</CardDescription>
            </CardHeader>
            <CardContent class="space-y-6">
                <FormField v-slot="{ componentField }" name="cliente_id">
                    <FormItem>
                        <FormLabel>Cliente *</FormLabel>
                        <Select v-bind="componentField">
                            <FormControl>
                                <SelectTrigger>
                                    <SelectValue placeholder="Selecione o cliente" />
                                </SelectTrigger>
                            </FormControl>
                            <SelectContent>
                                <SelectGroup>
                                    <SelectItem v-for="client in clients" :key="client.id" :value="client.id.toString()">
                                        {{ client.nome }} ({{ client.nif }})
                                    </SelectItem>
                                </SelectGroup>
                            </SelectContent>
                        </Select>
                        <FormMessage />
                    </FormItem>
                </FormField>

                <FormField v-slot="{ componentField }" name="estado">
                    <FormItem>
                        <FormLabel>Estado *</FormLabel>
                        <Select v-bind="componentField">
                            <FormControl>
                                <SelectTrigger>
                                    <SelectValue placeholder="Selecione o estado" />
                                </SelectTrigger>
                            </FormControl>
                            <SelectContent>
                                <SelectGroup>
                                    <SelectItem value="rascunho">Rascunho</SelectItem>
                                    <SelectItem value="fechado">Fechado</SelectItem>
                                </SelectGroup>
                            </SelectContent>
                        </Select>
                        <FormDescription>Ao fechar, a data da encomenda será definida automaticamente</FormDescription>
                        <FormMessage />
                    </FormItem>
                </FormField>
            </CardContent>
        </Card>

        <Card>
            <CardHeader>
                <CardTitle>Artigos</CardTitle>
                <CardDescription>Pesquisar e adicionar artigos à encomenda</CardDescription>
            </CardHeader>
            <CardContent class="space-y-4">
                <div class="relative">
                    <Input v-model="searchQuery" type="text" placeholder="Pesquisar por referência ou nome do artigo..." class="w-full" />

                    <div v-if="filteredArticles.length > 0" class="absolute z-50 w-full mt-1 bg-white border rounded-md shadow-lg max-h-60 overflow-auto">
                        <button v-for="article in filteredArticles" :key="article.id" type="button" @click="addItem(article)"
                                class="w-full px-4 py-2 text-left hover:bg-slate-100 flex justify-between items-center">
                            <div>
                                <div class="font-medium">{{ article.nome }}</div>
                                <div class="text-sm text-slate-500">{{ article.referencia }}</div>
                            </div>
                            <div class="text-sm font-medium">{{ formatPrice(article.preco) }}</div>
                        </button>
                    </div>
                </div>

                <div v-if="items.length > 0" class="border rounded-md">
                    <Table>
                        <TableHeader>
                            <TableRow>
                                <TableHead>Artigo</TableHead>
                                <TableHead>Fornecedor</TableHead>
                                <TableHead class="w-24">Qtd</TableHead>
                                <TableHead>Preço Unit.</TableHead>
                                <TableHead>Preço Custo</TableHead>
                                <TableHead>Subtotal</TableHead>
                                <TableHead class="w-16"></TableHead>
                            </TableRow>
                        </TableHeader>
                        <TableBody>
                            <TableRow v-for="(item, index) in items" :key="index">
                                <TableCell>
                                    <div class="font-medium">{{ item.article.nome }}</div>
                                    <div class="text-sm text-slate-500">{{ item.article.referencia }}</div>
                                </TableCell>
                                <TableCell>
                                    <select v-model="item.fornecedor_id" class="w-full px-2 py-1 border rounded text-sm">
                                        <option :value="null">Sem fornecedor</option>
                                        <option v-for="supplier in suppliers" :key="supplier.id" :value="supplier.id">
                                            {{ supplier.nome }}
                                        </option>
                                    </select>
                                </TableCell>
                                <TableCell>
                                    <Input v-model.number="item.quantidade" type="number" min="1" class="w-20" />
                                </TableCell>
                                <TableCell>
                                    <Input v-model.number="item.preco_unitario" type="number" step="0.01" min="0" class="w-28" />
                                </TableCell>
                                <TableCell>
                                    <Input v-model.number="item.preco_custo" type="number" step="0.01" min="0" class="w-28" />
                                </TableCell>
                                <TableCell class="font-medium">{{ formatPrice(item.subtotal) }}</TableCell>
                                <TableCell>
                                    <Button type="button" variant="ghost" size="sm" @click="removeItem(index)" class="text-red-600 hover:text-red-700">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                        </svg>
                                    </Button>
                                </TableCell>
                            </TableRow>
                        </TableBody>
                    </Table>

                    <div class="p-4 bg-slate-50 border-t space-y-2">
                        <div class="flex justify-between text-sm">
                            <span class="font-medium">Total (sem IVA):</span>
                            <span class="font-bold">{{ formatPrice(totalValue) }}</span>
                        </div>
                        <div class="flex justify-between text-sm">
                            <span class="font-medium">Total (com IVA):</span>
                            <span class="font-bold text-lg">{{ formatPrice(totalWithIva) }}</span>
                        </div>
                    </div>
                </div>

                <div v-else class="text-center py-8 text-slate-500">
                    Nenhum artigo adicionado. Use a pesquisa acima para adicionar artigos.
                </div>
            </CardContent>
        </Card>
    </div>
</template>

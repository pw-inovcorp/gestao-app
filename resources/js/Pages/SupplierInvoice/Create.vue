<script setup>
import AppLayout from '@/layouts/AppLayout.vue'
import { useForm, Link } from '@inertiajs/vue3'
import { Button } from '@/components/ui/button'
import { Input } from '@/components/ui/input'
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card'
import { FormControl, FormField, FormItem, FormLabel, FormMessage, FormDescription } from '@/components/ui/form'
import { Select, SelectContent, SelectGroup, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select'
import { toTypedSchema } from '@vee-validate/zod'
import * as z from 'zod'
import { useForm as useVeeForm } from 'vee-validate'
import { ref, computed, watch } from 'vue'

const props = defineProps({
    suppliers: Array,
    supplierOrders: Array
})

const documentFile = ref(null)
const documentPreview = ref(null)

const getTodayDate = () => new Date().toISOString().split('T')[0]

const formSchema = toTypedSchema(z.object({
    fornecedor_id: z.string().min(1, 'Selecione um fornecedor'),
    supplier_order_id: z.string().optional(),
    data_fatura: z.string().min(1, 'A data da fatura é obrigatória'),
    data_vencimento: z.string().min(1, 'A data de vencimento é obrigatória'),
    valor_total: z.union([z.string().min(1, 'O valor total é obrigatório'), z.number()]).transform(val => String(val)),
})
    .refine((data) => {
        const dataFatura = new Date(data.data_fatura)
        const dataVencimento = new Date(data.data_vencimento)
        return dataVencimento >= dataFatura
    }, {
        message: 'A data de vencimento deve ser igual ou posterior à data da fatura',
        path: ['data_vencimento']
    })
)

const veeForm = useVeeForm({
    validationSchema: formSchema,
    initialValues: {
        fornecedor_id: '',
        supplier_order_id: '',
        data_fatura: getTodayDate(),
        data_vencimento: '',
        valor_total: '',
    }
})

const inertiaForm = useForm({
    fornecedor_id: '',
    supplier_order_id: null,
    data_fatura: getTodayDate(),
    data_vencimento: '',
    valor_total: '',
    documento: null,
})

const handleDocumentChange = (event) => {
    const file = event.target.files[0]
    if (file) {
        documentFile.value = file
        documentPreview.value = file.name
    }
}

const removeDocument = () => {
    documentFile.value = null
    documentPreview.value = null
    document.getElementById('documento').value = ''
}

const selectedSupplier = computed(() =>
    props.suppliers.find(s => s.id.toString() === veeForm.values.fornecedor_id)
)

const filteredSupplierOrders = computed(() =>
    props.supplierOrders.filter(o => o.fornecedor_id.toString() === veeForm.values.fornecedor_id)
)

const formatPrice = (price) =>
    new Intl.NumberFormat('pt-PT', { style: 'currency', currency: 'EUR' }).format(price)

watch(() => veeForm.values.fornecedor_id, () => veeForm.setFieldValue('supplier_order_id', ''))

const onSubmit = veeForm.handleSubmit((values) => {
    const formData = new FormData()
    formData.append('fornecedor_id', values.fornecedor_id)
    if (values.supplier_order_id) formData.append('supplier_order_id', values.supplier_order_id)
    formData.append('data_fatura', values.data_fatura)
    formData.append('data_vencimento', values.data_vencimento)
    formData.append('valor_total', values.valor_total)
    if (documentFile.value) formData.append('documento', documentFile.value)

    inertiaForm.transform(() => formData).post('/faturas-fornecedor', {
        forceFormData: true,
        onSuccess: () => {
            veeForm.resetForm()
            veeForm.setFieldValue('data_fatura', getTodayDate())
            removeDocument()
        }
    })
})
</script>

<template>
    <AppLayout>
        <div class="max-w-4xl mx-auto space-y-6">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold">Nova Fatura de Fornecedor</h1>
                    <p class="text-slate-600 mt-1">Registar nova fatura de fornecedor</p>
                </div>
                <Link href="/faturas-fornecedor">
                    <Button variant="outline">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                        </svg>
                        Voltar
                    </Button>
                </Link>
            </div>

            <form @submit.prevent="onSubmit" class="space-y-6">
                <Card>
                    <CardHeader>
                        <CardTitle>Informações do Fornecedor</CardTitle>
                        <CardDescription>Selecione o fornecedor e encomenda relacionada</CardDescription>
                    </CardHeader>
                    <CardContent class="space-y-6">
                        <FormField v-slot="{ componentField }" name="fornecedor_id">
                            <FormItem>
                                <FormLabel>Fornecedor *</FormLabel>
                                <Select v-bind="componentField">
                                    <FormControl>
                                        <SelectTrigger>
                                            <SelectValue placeholder="Selecione o fornecedor" />
                                        </SelectTrigger>
                                    </FormControl>
                                    <SelectContent>
                                        <SelectGroup>
                                            <SelectItem v-for="supplier in suppliers" :key="supplier.id" :value="supplier.id.toString()">
                                                {{ supplier.nome }} ({{ supplier.nif }})
                                            </SelectItem>
                                        </SelectGroup>
                                    </SelectContent>
                                </Select>
                                <FormMessage />
                            </FormItem>
                        </FormField>

                        <div v-if="selectedSupplier" class="p-4 bg-slate-50 rounded-md space-y-2">
                            <div class="flex justify-between text-sm">
                                <span class="text-slate-600">NIF:</span>
                                <span class="font-medium">{{ selectedSupplier.nif }}</span>
                            </div>
                            <div v-if="selectedSupplier.email" class="flex justify-between text-sm">
                                <span class="text-slate-600">Email:</span>
                                <span class="font-medium">{{ selectedSupplier.email }}</span>
                            </div>
                            <div v-if="selectedSupplier.telefone" class="flex justify-between text-sm">
                                <span class="text-slate-600">Telefone:</span>
                                <span class="font-medium">{{ selectedSupplier.telefone }}</span>
                            </div>
                        </div>

                        <FormField v-slot="{ componentField }" name="supplier_order_id">
                            <FormItem>
                                <FormLabel>Encomenda Fornecedor (opcional)</FormLabel>
                                <Select v-bind="componentField" :disabled="!veeForm.values.fornecedor_id || !filteredSupplierOrders.length">
                                    <FormControl>
                                        <SelectTrigger>
                                            <SelectValue :placeholder="!veeForm.values.fornecedor_id ? 'Selecione primeiro um fornecedor' : !filteredSupplierOrders.length ? 'Sem encomendas disponíveis' : 'Selecione uma encomenda (opcional)'" />
                                        </SelectTrigger>
                                    </FormControl>
                                    <SelectContent>
                                        <SelectGroup>
                                            <SelectItem v-for="order in filteredSupplierOrders" :key="order.id" :value="order.id.toString()">
                                                {{ order.numero }} - {{ formatPrice(order.valor_total) }}
                                            </SelectItem>
                                        </SelectGroup>
                                    </SelectContent>
                                </Select>
                                <FormDescription>Associe esta fatura a uma encomenda existente</FormDescription>
                                <FormMessage />
                            </FormItem>
                        </FormField>
                    </CardContent>
                </Card>

                <Card>
                    <CardHeader>
                        <CardTitle>Dados da Fatura</CardTitle>
                        <CardDescription>Datas e valor da fatura</CardDescription>
                    </CardHeader>
                    <CardContent class="space-y-6">
                        <div class="grid md:grid-cols-2 gap-4">
                            <FormField v-slot="{ componentField }" name="data_fatura">
                                <FormItem>
                                    <FormLabel>Data da Fatura *</FormLabel>
                                    <FormControl>
                                        <Input type="date" v-bind="componentField" />
                                    </FormControl>
                                    <FormDescription>Padrão: hoje</FormDescription>
                                    <FormMessage />
                                </FormItem>
                            </FormField>

                            <FormField v-slot="{ componentField }" name="data_vencimento">
                                <FormItem>
                                    <FormLabel>Data de Vencimento *</FormLabel>
                                    <FormControl>
                                        <Input type="date" v-bind="componentField" />
                                    </FormControl>
                                    <FormDescription>Deve ser igual ou posterior à data da fatura</FormDescription>
                                    <FormMessage />
                                </FormItem>
                            </FormField>
                        </div>

                        <FormField v-slot="{ componentField }" name="valor_total">
                            <FormItem>
                                <FormLabel>Valor Total *</FormLabel>
                                <FormControl>
                                    <Input type="text" inputmode="decimal" placeholder="0.00" v-bind="componentField" />
                                </FormControl>
                                <FormDescription>Valor total da fatura em euros</FormDescription>
                                <FormMessage />
                            </FormItem>
                        </FormField>
                    </CardContent>
                </Card>

                <Card>
                    <CardHeader>
                        <CardTitle>Documento</CardTitle>
                        <CardDescription>Anexar cópia da fatura (PDF, JPG, PNG - máx. 5MB)</CardDescription>
                    </CardHeader>
                    <CardContent class="space-y-4">
                        <div v-if="documentPreview" class="flex items-center gap-3 p-4 bg-slate-50 rounded-md">
                            <svg class="w-8 h-8 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                            <div class="flex-1 min-w-0">
                                <p class="font-medium truncate">{{ documentPreview }}</p>
                                <p class="text-sm text-slate-500">Documento selecionado</p>
                            </div>
                            <Button type="button" variant="ghost" size="sm" class="text-red-600 hover:text-red-700" @click="removeDocument">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </Button>
                        </div>

                        <div v-else class="border-2 border-dashed border-slate-300 rounded-lg p-6">
                            <div class="flex flex-col items-center justify-center space-y-3">
                                <svg class="w-12 h-12 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                </svg>
                                <div class="text-center">
                                    <label for="documento" class="cursor-pointer">
                                        <span class="text-blue-600 hover:text-blue-700 font-medium">Carregar documento</span>
                                        <span class="text-slate-500"> ou arraste aqui</span>
                                    </label>
                                    <p class="text-xs text-slate-500 mt-1">PDF, JPG, PNG até 5MB</p>
                                </div>
                            </div>
                        </div>

                        <input id="documento" type="file" accept=".pdf,.jpg,.jpeg,.png" class="hidden" @change="handleDocumentChange" />
                    </CardContent>
                </Card>

                <div class="flex gap-4">
                    <Button type="submit" :disabled="inertiaForm.processing" class="flex-1">
                        {{ inertiaForm.processing ? 'A guardar...' : 'Criar Fatura' }}
                    </Button>

                    <Link href="/faturas-fornecedor" class="flex-1">
                        <Button type="button" variant="outline" class="w-full">Cancelar</Button>
                    </Link>
                </div>
            </form>
        </div>
    </AppLayout>
</template>

<script setup>
import { ref, computed } from 'vue'
import { Input } from '@/components/ui/input'
import { Textarea } from '@/components/ui/textarea'
import { Button } from '@/components/ui/button'
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card'
import { FormControl, FormField, FormItem, FormLabel, FormMessage, FormDescription } from '@/components/ui/form'
import { Select, SelectContent, SelectGroup, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select'
import { useFormValues } from 'vee-validate'

const props = defineProps({
    ivaRates: Array,
    existingImage: String
})

const imagePreview = ref(props.existingImage || null)
const imageFile = ref(null)

const formValues = useFormValues()

const handleImageChange = (event) => {
    const file = event.target.files[0]
    if (file) {
        imageFile.value = file
        const reader = new FileReader()
        reader.onload = (e) => {
            imagePreview.value = e.target.result
        }
        reader.readAsDataURL(file)
    }
}

const removeImage = () => {
    imagePreview.value = null
    imageFile.value = null
    const input = document.getElementById('foto')
    if (input) input.value = ''
}

const precoComIva = computed(() => {
    const preco = parseFloat(formValues.value.preco) || 0
    const ivaRate = props.ivaRates?.find(r => r.id.toString() === formValues.value.iva_rate_id)
    if (!ivaRate) return preco.toFixed(2)
    const precoTotal = preco + (preco * ivaRate.taxa / 100)
    return precoTotal.toFixed(2)
})

defineExpose({
    imageFile,
    removeImage
})
</script>

<template>
    <div class="space-y-6">
        <Card>
            <CardHeader>
                <CardTitle>Informações Básicas</CardTitle>
                <CardDescription>Dados principais do artigo</CardDescription>
            </CardHeader>
            <CardContent class="space-y-6">
                <FormField v-slot="{ componentField }" name="nome">
                    <FormItem>
                        <FormLabel>Nome *</FormLabel>
                        <FormControl>
                            <Input type="text" placeholder="Nome do artigo" v-bind="componentField" />
                        </FormControl>
                        <FormMessage />
                    </FormItem>
                </FormField>

                <FormField v-slot="{ componentField }" name="descricao">
                    <FormItem>
                        <FormLabel>Descrição</FormLabel>
                        <FormControl>
                            <Textarea
                                placeholder="Descrição do artigo..."
                                class="min-h-[100px]"
                                v-bind="componentField"
                            />
                        </FormControl>
                        <FormMessage />
                    </FormItem>
                </FormField>

                <div class="grid md:grid-cols-2 gap-4">
                    <FormField v-slot="{ componentField }" name="preco">
                        <FormItem>
                            <FormLabel>Preço (sem IVA) *</FormLabel>
                            <FormControl>
                                <Input
                                    type="text"
                                    inputmode="decimal"
                                    placeholder="0.00"
                                    v-bind="componentField"
                                />
                            </FormControl>
                            <FormDescription>Preço sem IVA incluído</FormDescription>
                            <FormMessage />
                        </FormItem>
                    </FormField>

                    <FormField v-slot="{ componentField }" name="iva_rate_id">
                        <FormItem>
                            <FormLabel>Taxa de IVA *</FormLabel>
                            <Select v-bind="componentField">
                                <FormControl>
                                    <SelectTrigger>
                                        <SelectValue placeholder="Selecione a taxa de IVA" />
                                    </SelectTrigger>
                                </FormControl>
                                <SelectContent>
                                    <SelectGroup>
                                        <SelectItem
                                            v-for="rate in ivaRates"
                                            :key="rate.id"
                                            :value="rate.id.toString()"
                                        >
                                            {{ rate.nome }} ({{ rate.taxa }}%)
                                        </SelectItem>
                                    </SelectGroup>
                                </SelectContent>
                            </Select>
                            <FormDescription>
                                Preço com IVA: <strong>{{ precoComIva }}€</strong>
                            </FormDescription>
                            <FormMessage />
                        </FormItem>
                    </FormField>
                </div>
            </CardContent>
        </Card>

        <Card>
            <CardHeader>
                <CardTitle>Foto do Artigo</CardTitle>
                <CardDescription>Imagem do artigo (máximo 2MB)</CardDescription>
            </CardHeader>
            <CardContent class="space-y-4">
                <div v-if="imagePreview" class="space-y-4">
                    <div class="relative w-full max-w-xs">
                        <img
                            :src="imagePreview.startsWith('data:') ? imagePreview : `/storage/${imagePreview}`"
                            alt="Preview"
                            class="w-full h-48 object-cover rounded-lg border"
                        />
                        <Button
                            type="button"
                            variant="destructive"
                            size="sm"
                            class="absolute top-2 right-2"
                            @click="removeImage"
                        >
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </Button>
                    </div>
                </div>

                <div v-else class="border-2 border-dashed border-slate-300 rounded-lg p-6">
                    <div class="flex flex-col items-center justify-center space-y-3">
                        <svg class="w-12 h-12 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m2.25 15.75 5.159-5.159a2.25 2.25 0 0 1 3.182 0l5.159 5.159m-1.5-1.5 1.409-1.409a2.25 2.25 0 0 1 3.182 0l2.909 2.909m-18 3.75h16.5a1.5 1.5 0 0 0 1.5-1.5V6a1.5 1.5 0 0 0-1.5-1.5H3.75A1.5 1.5 0 0 0 2.25 6v12a1.5 1.5 0 0 0 1.5 1.5Zm10.5-11.25h.008v.008h-.008V8.25Zm.375 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Z" />
                        </svg>
                        <div class="text-center">
                            <label for="foto" class="cursor-pointer">
                                <span class="text-blue-600 hover:text-blue-700 font-medium">
                                    Carregar imagem
                                </span>
                                <span class="text-slate-500"> ou arraste aqui</span>
                            </label>
                            <p class="text-xs text-slate-500 mt-1">PNG, JPG, GIF, WEBP até 2MB</p>
                        </div>
                    </div>
                </div>

                <input
                    id="foto"
                    type="file"
                    accept="image/*"
                    class="hidden"
                    @change="handleImageChange"
                />
            </CardContent>
        </Card>

        <Card>
            <CardHeader>
                <CardTitle>Observações e Estado</CardTitle>
                <CardDescription>Informações adicionais</CardDescription>
            </CardHeader>
            <CardContent class="space-y-6">
                <FormField v-slot="{ componentField }" name="observacoes">
                    <FormItem>
                        <FormLabel>Observações</FormLabel>
                        <FormControl>
                            <Textarea
                                placeholder="Notas adicionais sobre o artigo..."
                                class="min-h-[100px]"
                                v-bind="componentField"
                            />
                        </FormControl>
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
                                    <SelectItem value="ativo">Ativo</SelectItem>
                                    <SelectItem value="inativo">Inativo</SelectItem>
                                </SelectGroup>
                            </SelectContent>
                        </Select>
                        <FormMessage />
                    </FormItem>
                </FormField>
            </CardContent>
        </Card>
    </div>
</template>

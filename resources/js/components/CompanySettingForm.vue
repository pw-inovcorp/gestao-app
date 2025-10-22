<script setup>
import { ref } from 'vue'
import { Input } from '@/components/ui/input'
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card'
import { Button } from '@/components/ui/button'
import { FormControl, FormField, FormItem, FormLabel, FormMessage } from '@/components/ui/form'

const props = defineProps({
    existingLogo: String
})

const imagePreview = ref(props.existingLogo || null)
const imageFile = ref(null)
const imageRemoved = ref(false)

const handleImageChange = (event) => {
    const file = event.target.files[0]
    if (file) {
        imageFile.value = file
        imageRemoved.value = false
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
    imageRemoved.value = true
    const input = document.getElementById('logo')
    if (input) input.value = ''
}

defineExpose({
    imageFile,
    imageRemoved,
    removeImage
})
</script>

<template>
    <div class="space-y-6">
        <Card>
            <CardHeader>
                <CardTitle>Dados da Empresa</CardTitle>
                <CardDescription>Informações que aparecem nos documentos e na aplicação</CardDescription>
            </CardHeader>
            <CardContent class="space-y-6">
                <div class="space-y-2">
                    <label class="text-sm font-medium">Logotipo</label>

                    <div v-if="imagePreview" class="space-y-4">
                        <div class="relative w-full max-w-xs">
                            <img
                                :src="imagePreview.startsWith('data:') ? imagePreview : `/storage/${imagePreview}`"
                                alt="Logo da empresa"
                                class="w-full h-32 object-contain rounded-lg border bg-white p-2"
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

                    <div v-else class="border-2 border-dashed border-slate-300 rounded-lg p-6 max-w-xs">
                        <div class="flex flex-col items-center justify-center space-y-2">
                            <svg class="w-10 h-10 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m2.25 15.75 5.159-5.159a2.25 2.25 0 0 1 3.182 0l5.159 5.159m-1.5-1.5 1.409-1.409a2.25 2.25 0 0 1 3.182 0l2.909 2.909m-18 3.75h16.5a1.5 1.5 0 0 0 1.5-1.5V6a1.5 1.5 0 0 0-1.5-1.5H3.75A1.5 1.5 0 0 0 2.25 6v12a1.5 1.5 0 0 0 1.5 1.5Zm10.5-11.25h.008v.008h-.008V8.25Zm.375 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Z" />
                            </svg>
                            <div class="text-center">
                                <label for="logo" class="cursor-pointer">
                                    <span class="text-blue-600 hover:text-blue-700 font-medium text-sm">
                                        Carregar logotipo
                                    </span>
                                </label>
                                <p class="text-xs text-slate-500 mt-1">PNG, JPG, SVG (max 2MB)</p>
                            </div>
                        </div>
                    </div>

                    <input
                        id="logo"
                        type="file"
                        accept="image/*"
                        class="hidden"
                        @change="handleImageChange"
                    />
                </div>

                <div class="grid md:grid-cols-2 gap-6">
                    <FormField v-slot="{ componentField }" name="nome">
                        <FormItem>
                            <FormLabel>Nome da Empresa *</FormLabel>
                            <FormControl>
                                <Input type="text" placeholder="Nome da empresa" v-bind="componentField" />
                            </FormControl>
                            <FormMessage />
                        </FormItem>
                    </FormField>

                    <FormField v-slot="{ componentField }" name="numero_contribuinte">
                        <FormItem>
                            <FormLabel>Número de Contribuinte *</FormLabel>
                            <FormControl>
                                <Input type="text" placeholder="123456789" maxlength="9" v-bind="componentField" />
                            </FormControl>
                            <FormMessage />
                        </FormItem>
                    </FormField>

                    <FormField v-slot="{ componentField }" name="morada">
                        <FormItem class="md:col-span-2">
                            <FormLabel>Morada</FormLabel>
                            <FormControl>
                                <Input type="text" placeholder="Rua, número, andar" v-bind="componentField" />
                            </FormControl>
                            <FormMessage />
                        </FormItem>
                    </FormField>

                    <FormField v-slot="{ componentField }" name="codigo_postal">
                        <FormItem>
                            <FormLabel>Código Postal</FormLabel>
                            <FormControl>
                                <Input type="text" placeholder="1234-567" v-bind="componentField" />
                            </FormControl>
                            <FormMessage />
                        </FormItem>
                    </FormField>

                    <FormField v-slot="{ componentField }" name="localidade">
                        <FormItem>
                            <FormLabel>Localidade</FormLabel>
                            <FormControl>
                                <Input type="text" placeholder="Lisboa" v-bind="componentField" />
                            </FormControl>
                            <FormMessage />
                        </FormItem>
                    </FormField>
                </div>
            </CardContent>
        </Card>
    </div>
</template>

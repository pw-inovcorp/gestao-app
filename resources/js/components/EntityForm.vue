<script setup>
    import { Button } from '@/components/ui/button'
    import { Input } from '@/components/ui/input'
    import { Textarea } from '@/components/ui/textarea'
    import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card'
    import { Checkbox } from '@/components/ui/checkbox'
    import { FormControl, FormField, FormItem, FormMessage } from '@/components/ui/form'
    import { Select, SelectContent, SelectGroup, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select'
    import { ref, computed, watch } from 'vue'
    import { useFormValues } from 'vee-validate'
    import axios from 'axios'

    const props = defineProps({
        countries: Array
    })

    const checkingVies = ref(false)
    const viesResult = ref(null)
    const selectedCountryId = ref(null)

    const formValues = useFormValues()

    watch(() => formValues.value.country_id, (newValue) => {
        selectedCountryId.value = newValue
        viesResult.value = null
    })

    const selectedCountry = computed(() => {
        if (!selectedCountryId.value || !props.countries) return null
        return props.countries.find(c => c.id.toString() === selectedCountryId.value)
    })

    const checkVies = async (nif) => {
        if (!nif || nif.length !== 9) {
            viesResult.value = { error: true, message: 'NIF deve ter 9 dígitos' }
            return
        }

        if (!selectedCountry.value) {
            viesResult.value = { error: true, message: 'Selecione o país primeiro' }
            return
        }

        checkingVies.value = true
        viesResult.value = null

        try {
            const { data } = await axios.post('/entidades/check-vies', {
                nif: nif,
                country_code: selectedCountry.value.codigo
            })

            viesResult.value = data.valid
                ? { valid: true, data: data.data }
                : { valid: false, message: data.message }
        } catch (error) {
            viesResult.value = {
                error: true,
                message: error.response?.data?.message || 'Erro ao validar NIF'
            }
        } finally {
            checkingVies.value = false
        }
    }
</script>

<template>
    <div class="space-y-6">
        <Card>
            <CardHeader>
                <CardTitle>Informações Básicas</CardTitle>
                <CardDescription>Dados principais da entidade</CardDescription>
            </CardHeader>
            <CardContent class="space-y-6">
                <div class="space-y-3">
                    <label class="text-sm font-medium">Tipo de Entidade *</label>
                    <div class="space-y-3">
                        <FormField v-slot="{ value, handleChange }" name="tipo_cliente">
                            <FormItem class="flex items-start gap-3 space-y-0">
                                <FormControl>
                                    <Checkbox :model-value="value" @update:model-value="handleChange" />
                                </FormControl>
                                <label class="text-sm font-medium cursor-pointer">Cliente</label>
                            </FormItem>
                        </FormField>

                        <FormField v-slot="{ value, handleChange }" name="tipo_fornecedor">
                            <FormItem class="flex items-start gap-3 space-y-0">
                                <FormControl>
                                    <Checkbox :model-value="value" @update:model-value="handleChange" />
                                </FormControl>
                                <label class="text-sm font-medium cursor-pointer">Fornecedor</label>
                            </FormItem>
                        </FormField>
                    </div>
                    <FormField v-slot="{}" name="tipo_cliente">
                        <FormItem>
                            <FormMessage />
                        </FormItem>
                    </FormField>
                </div>

                <FormField v-slot="{ componentField }" name="nome">
                    <FormItem>
                        <label class="text-sm font-medium">Nome da Empresa *</label>
                        <FormControl>
                            <Input type="text" placeholder="Nome da empresa" v-bind="componentField" />
                        </FormControl>
                        <FormMessage />
                    </FormItem>
                </FormField>

                <div class="grid md:grid-cols-2 gap-4">
                    <FormField v-slot="{ componentField }" name="nif">
                        <FormItem>
                            <label class="text-sm font-medium">NIF *</label>
                            <div class="flex gap-2">
                                <FormControl class="flex-1">
                                    <Input
                                        type="text"
                                        placeholder="123456789"
                                        maxlength="9"
                                        v-bind="componentField"
                                        @blur="checkVies(componentField.modelValue)"
                                    />
                                </FormControl>
                                <Button
                                    type="button"
                                    variant="outline"
                                    size="icon"
                                    @click="checkVies(componentField.modelValue)"
                                    :disabled="checkingVies"
                                >
                                    <svg v-if="!checkingVies" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                    </svg>
                                    <svg v-else class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" />
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z" />
                                    </svg>
                                </Button>
                            </div>
                            <p class="text-sm text-muted-foreground">Validar com VIES</p>
                            <FormMessage />
                        </FormItem>
                    </FormField>

                    <FormField v-slot="{ componentField }" name="country_id">
                        <FormItem>
                            <label class="text-sm font-medium">País *</label>
                            <Select v-bind="componentField">
                                <FormControl>
                                    <SelectTrigger>
                                        <SelectValue placeholder="Selecione o país" />
                                    </SelectTrigger>
                                </FormControl>
                                <SelectContent>
                                    <SelectGroup>
                                        <SelectItem
                                            v-for="country in countries"
                                            :key="country.id"
                                            :value="country.id.toString()"
                                        >
                                            {{ country.nome }}
                                        </SelectItem>
                                    </SelectGroup>
                                </SelectContent>
                            </Select>
                            <FormMessage />
                        </FormItem>
                    </FormField>
                </div>

                <div v-if="viesResult" class="rounded-md p-3 text-sm" :class="{
                    'bg-green-50 text-green-800 border border-green-200': viesResult.valid,
                    'bg-red-50 text-red-800 border border-red-200': viesResult.error || !viesResult.valid
                }">
                    <div v-if="viesResult.valid" class="space-y-1">
                        <div class="font-medium">✓ NIF Válido</div>
                        <div v-if="viesResult.data?.name">{{ viesResult.data.name }}</div>
                    </div>
                    <div v-else class="font-medium">✗ {{ viesResult.message }}</div>
                </div>
            </CardContent>
        </Card>

        <Card>
            <CardHeader>
                <CardTitle>Morada</CardTitle>
                <CardDescription>Endereço da entidade</CardDescription>
            </CardHeader>
            <CardContent class="space-y-6">
                <FormField v-slot="{ componentField }" name="morada">
                    <FormItem>
                        <label class="text-sm font-medium">Morada</label>
                        <FormControl>
                            <Input type="text" placeholder="Rua, número, andar" v-bind="componentField" />
                        </FormControl>
                        <FormMessage />
                    </FormItem>
                </FormField>

                <div class="grid md:grid-cols-2 gap-4">
                    <FormField v-slot="{ componentField }" name="codigo_postal">
                        <FormItem>
                            <label class="text-sm font-medium">Código Postal</label>
                            <FormControl>
                                <Input type="text" placeholder="1234-567" v-bind="componentField" />
                            </FormControl>
                            <FormMessage />
                        </FormItem>
                    </FormField>

                    <FormField v-slot="{ componentField }" name="localidade">
                        <FormItem>
                            <label class="text-sm font-medium">Localidade</label>
                            <FormControl>
                                <Input type="text" placeholder="Lisboa" v-bind="componentField" />
                            </FormControl>
                            <FormMessage />
                        </FormItem>
                    </FormField>
                </div>
            </CardContent>
        </Card>

        <Card>
            <CardHeader>
                <CardTitle>Contactos</CardTitle>
                <CardDescription>Informações de contacto</CardDescription>
            </CardHeader>
            <CardContent class="space-y-6">
                <FormField v-slot="{ componentField }" name="email">
                    <FormItem>
                        <label class="text-sm font-medium">Email</label>
                        <FormControl>
                            <Input type="email" placeholder="email@exemplo.com" v-bind="componentField" />
                        </FormControl>
                        <FormMessage />
                    </FormItem>
                </FormField>

                <div class="grid md:grid-cols-2 gap-4">
                    <FormField v-slot="{ componentField }" name="telefone">
                        <FormItem>
                            <label class="text-sm font-medium">Telefone</label>
                            <FormControl>
                                <Input type="text" placeholder="212345678" v-bind="componentField" />
                            </FormControl>
                            <FormMessage />
                        </FormItem>
                    </FormField>

                    <FormField v-slot="{ componentField }" name="telemovel">
                        <FormItem>
                            <label class="text-sm font-medium">Telemóvel</label>
                            <FormControl>
                                <Input type="text" placeholder="912345678" v-bind="componentField" />
                            </FormControl>
                            <FormMessage />
                        </FormItem>
                    </FormField>
                </div>

                <FormField v-slot="{ componentField }" name="website">
                    <FormItem>
                        <label class="text-sm font-medium">Website</label>
                        <FormControl>
                            <Input type="url" placeholder="https://exemplo.com" v-bind="componentField" />
                        </FormControl>
                        <FormMessage />
                    </FormItem>
                </FormField>
            </CardContent>
        </Card>

        <Card>
            <CardHeader>
                <CardTitle>Notas e Observações</CardTitle>
                <CardDescription>Informações adicionais</CardDescription>
            </CardHeader>
            <CardContent>
                <FormField v-slot="{ componentField }" name="observacoes">
                    <FormItem>
                        <label class="text-sm font-medium">Observações</label>
                        <FormControl>
                            <Textarea
                                placeholder="Notas adicionais sobre a entidade..."
                                class="min-h-[120px]"
                                v-bind="componentField"
                            />
                        </FormControl>
                        <FormMessage />
                    </FormItem>
                </FormField>
            </CardContent>
        </Card>

        <Card>
            <CardHeader>
                <CardTitle>Consentimento e Estado</CardTitle>
                <CardDescription>RGPD e estado da entidade</CardDescription>
            </CardHeader>
            <CardContent class="space-y-6">
                <FormField v-slot="{ value, handleChange }" name="consentimento_rgpd">
                    <FormItem class="flex items-start gap-3 space-y-0">
                        <FormControl>
                            <Checkbox :model-value="value" @update:model-value="handleChange" />
                        </FormControl>
                        <div class="space-y-1">
                            <label class="text-sm font-medium cursor-pointer">Consentimento RGPD</label>
                            <p class="text-sm text-muted-foreground">
                                A entidade deu consentimento para tratamento de dados pessoais
                            </p>
                        </div>
                    </FormItem>
                </FormField>

                <FormField v-slot="{ componentField }" name="estado">
                    <FormItem>
                        <label class="text-sm font-medium">Estado *</label>
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

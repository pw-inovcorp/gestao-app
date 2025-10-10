<script setup>
import { Input } from '@/components/ui/input'
import { Textarea } from '@/components/ui/textarea'
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card'
import { Checkbox } from '@/components/ui/checkbox'
import { FormControl, FormField, FormItem, FormLabel, FormMessage } from '@/components/ui/form'
import { Select, SelectContent, SelectGroup, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select'

const props = defineProps({
    entities: Array,
    contactFunctions: Array
})
</script>

<template>
    <div class="space-y-6">
        <Card>
            <CardHeader>
                <CardTitle>Informações Básicas</CardTitle>
                <CardDescription>Dados principais do contacto</CardDescription>
            </CardHeader>
            <CardContent class="space-y-6">
                <FormField v-slot="{ componentField }" name="entity_id">
                    <FormItem>
                        <FormLabel>Entidade *</FormLabel>
                        <Select v-bind="componentField">
                            <FormControl>
                                <SelectTrigger>
                                    <SelectValue placeholder="Selecione a entidade" />
                                </SelectTrigger>
                            </FormControl>
                            <SelectContent>
                                <SelectGroup>
                                    <SelectItem
                                        v-for="entity in entities"
                                        :key="entity.id"
                                        :value="entity.id.toString()"
                                    >
                                        {{ entity.nome }} ({{ entity.nif }})
                                    </SelectItem>
                                </SelectGroup>
                            </SelectContent>
                        </Select>
                        <FormMessage />
                    </FormItem>
                </FormField>

                <div class="grid md:grid-cols-2 gap-4">
                    <FormField v-slot="{ componentField }" name="nome">
                        <FormItem>
                            <FormLabel>Nome *</FormLabel>
                            <FormControl>
                                <Input type="text" placeholder="João" v-bind="componentField" />
                            </FormControl>
                            <FormMessage />
                        </FormItem>
                    </FormField>

                    <FormField v-slot="{ componentField }" name="apelido">
                        <FormItem>
                            <FormLabel>Apelido *</FormLabel>
                            <FormControl>
                                <Input type="text" placeholder="Silva" v-bind="componentField" />
                            </FormControl>
                            <FormMessage />
                        </FormItem>
                    </FormField>
                </div>

                <FormField v-slot="{ componentField }" name="contact_function_id">
                    <FormItem>
                        <FormLabel>Função</FormLabel>
                        <Select v-bind="componentField">
                            <FormControl>
                                <SelectTrigger>
                                    <SelectValue placeholder="Selecione a função" />
                                </SelectTrigger>
                            </FormControl>
                            <SelectContent>
                                <SelectGroup>
                                    <SelectItem
                                        v-for="func in contactFunctions"
                                        :key="func.id"
                                        :value="func.id.toString()"
                                    >
                                        {{ func.nome }}
                                    </SelectItem>
                                </SelectGroup>
                            </SelectContent>
                        </Select>
                        <FormMessage />
                    </FormItem>
                </FormField>
            </CardContent>
        </Card>

        <Card>
            <CardHeader>
                <CardTitle>Contactos</CardTitle>
                <CardDescription>Informações de contacto</CardDescription>
            </CardHeader>
            <CardContent class="space-y-6">
                <div class="grid md:grid-cols-2 gap-4">
                    <FormField v-slot="{ componentField }" name="telefone">
                        <FormItem>
                            <FormLabel>Telefone</FormLabel>
                            <FormControl>
                                <Input type="text" placeholder="212345678" v-bind="componentField" />
                            </FormControl>
                            <FormMessage />
                        </FormItem>
                    </FormField>

                    <FormField v-slot="{ componentField }" name="telemovel">
                        <FormItem>
                            <FormLabel>Telemóvel</FormLabel>
                            <FormControl>
                                <Input type="text" placeholder="912345678" v-bind="componentField" />
                            </FormControl>
                            <FormMessage />
                        </FormItem>
                    </FormField>
                </div>

                <FormField v-slot="{ componentField }" name="email">
                    <FormItem>
                        <FormLabel>Email</FormLabel>
                        <FormControl>
                            <Input type="email" placeholder="joao.silva@exemplo.com" v-bind="componentField" />
                        </FormControl>
                        <FormMessage />
                    </FormItem>
                </FormField>
            </CardContent>
        </Card>

        <Card>
            <CardHeader>
                <CardTitle>Observações e Configurações</CardTitle>
                <CardDescription>Informações adicionais</CardDescription>
            </CardHeader>
            <CardContent class="space-y-6">
                <FormField v-slot="{ componentField }" name="observacoes">
                    <FormItem>
                        <FormLabel>Observações</FormLabel>
                        <FormControl>
                            <Textarea
                                placeholder="Notas adicionais sobre o contacto..."
                                class="min-h-[100px]"
                                v-bind="componentField"
                            />
                        </FormControl>
                        <FormMessage />
                    </FormItem>
                </FormField>

                <FormField v-slot="{ value, handleChange }" name="consentimento_rgpd">
                    <FormItem class="flex items-start gap-3 space-y-0">
                        <FormControl>
                            <Checkbox :model-value="value" @update:model-value="handleChange" />
                        </FormControl>
                        <div class="space-y-1">
                            <FormLabel class="cursor-pointer">Consentimento RGPD</FormLabel>
                            <p class="text-sm text-muted-foreground">
                                O contacto deu consentimento para tratamento de dados pessoais
                            </p>
                        </div>
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

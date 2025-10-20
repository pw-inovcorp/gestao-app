<script setup>
import { computed } from 'vue'
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card'
import { FormControl, FormField, FormItem, FormLabel, FormMessage } from '@/components/ui/form'
import { Input } from '@/components/ui/input'

const props = defineProps({
    permissions: Object
})

// Traduzir nomes de módulos
const moduleNames = {
    'users': 'Utilizadores',
    'entities': 'Entidades',
    'contacts': 'Contactos',
    'articles': 'Artigos',
    'proposals': 'Propostas',
    'orders': 'Encomendas',
    'supplier-orders': 'Encomendas Fornecedor',
    'supplier-invoices': 'Faturas Fornecedor',
    'settings': 'Configurações',
    'roles': 'Permissões'
}

// Traduzir ações
const actionNames = {
    'view': 'Ver',
    'create': 'Criar',
    'edit': 'Editar',
    'delete': 'Eliminar',
    'vies': 'Validar VIES',
    'convert': 'Converter',
    'pdf': 'Gerar PDF',
    'payment': 'Gerir Pagamentos'
}

const getModuleName = (module) => moduleNames[module] || module
const getActionName = (action) => actionNames[action] || action


const sortedModules = computed(() => {
    return Object.keys(props.permissions).sort((a, b) => {
        const nameA = getModuleName(a)
        const nameB = getModuleName(b)
        return nameA.localeCompare(nameB)
    })
})
</script>

<template>
    <div class="space-y-6">
        <Card>
            <CardHeader>
                <CardTitle>Informações do Grupo</CardTitle>
                <CardDescription>Nome do grupo de permissões</CardDescription>
            </CardHeader>
            <CardContent>
                <FormField v-slot="{ componentField }" name="name">
                    <FormItem>
                        <FormLabel>Nome do Grupo *</FormLabel>
                        <FormControl>
                            <Input type="text" placeholder="Ex: Gestor de Armazém" v-bind="componentField" />
                        </FormControl>
                        <FormMessage />
                    </FormItem>
                </FormField>
            </CardContent>
        </Card>

        <Card>
            <CardHeader>
                <CardTitle>Permissões</CardTitle>
                <CardDescription>Selecione as permissões para este grupo</CardDescription>
            </CardHeader>
            <CardContent class="space-y-6">
                <div v-for="module in sortedModules" :key="module" class="space-y-3">
                    <h3 class="font-semibold text-sm text-slate-700 border-b pb-2">
                        {{ getModuleName(module) }}
                    </h3>

                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3 pl-2">
                        <FormField
                            v-for="permission in permissions[module]"
                            :key="permission.id"
                            v-slot="{ value, handleChange }"
                            type="checkbox"
                            name="permissions"
                            :value="permission.id"
                            :unchecked-value="false"
                        >
                            <FormItem>
                                <label class="flex items-center gap-2 cursor-pointer hover:text-slate-900">
                                    <input
                                        type="checkbox"
                                        :checked="value?.includes(permission.id)"
                                        @change="handleChange"
                                        class="rounded border-gray-300 text-primary"
                                    />
                                    <span class="text-sm font-normal">
                                        {{ getActionName(permission.name.split('.')[1]) }}
                                    </span>
                                </label>
                            </FormItem>
                        </FormField>
                    </div>
                </div>

                <FormField v-slot="{}" name="permissions">
                    <FormItem>
                        <FormMessage />
                    </FormItem>
                </FormField>
            </CardContent>
        </Card>
    </div>
</template>

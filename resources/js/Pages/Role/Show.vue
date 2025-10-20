<script setup>
import AppLayout from '@/layouts/AppLayout.vue'
import { Button } from '@/components/ui/button'
import { Badge } from '@/components/ui/badge'
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card'
import { Link } from '@inertiajs/vue3'

const props = defineProps({
    role: Object
})


const groupedPermissions = props.role.permissions.reduce((acc, permission) => {
    const [module] = permission.name.split('.')
    if (!acc[module]) {
        acc[module] = []
    }
    acc[module].push(permission)
    return acc
}, {})

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

const translatePermission = (permissionName) => {
    const [module, action] = permissionName.split('.')
    const moduleName = moduleNames[module] || module
    const actionName = actionNames[action] || action
    return `${moduleName} - ${actionName}`
}
</script>

<template>
    <AppLayout>
        <div class="max-w-4xl mx-auto space-y-6">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold">{{ role.name }}</h1>
                    <p class="text-slate-600 mt-1">Detalhes do grupo de permissões</p>
                </div>
                <div class="flex gap-2">
                    <Link href="/permissoes">
                        <Button variant="outline">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                            </svg>
                            Voltar
                        </Button>
                    </Link>
                    <Link :href="`/permissoes/${role.id}/editar`">
                        <Button>
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L6.832 19.82a4.5 4.5 0 0 1-1.897 1.13l-2.685.8.8-2.685a4.5 4.5 0 0 1 1.13-1.897L16.863 4.487Zm0 0L19.5 7.125" />
                            </svg>
                            Editar
                        </Button>
                    </Link>
                </div>
            </div>

            <div class="grid gap-6 md:grid-cols-2">
                <Card>
                    <CardHeader>
                        <CardTitle>Informações</CardTitle>
                    </CardHeader>
                    <CardContent class="space-y-3">
                        <div class="flex justify-between">
                            <span class="label">Nome do Grupo:</span>
                            <span class="font-medium">{{ role.name }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="label">Total de Permissões:</span>
                            <Badge variant="secondary">{{ role.permissions.length }}</Badge>
                        </div>
                        <div class="flex justify-between">
                            <span class="label">Utilizadores Associados:</span>
                            <Badge :variant="role.users.length > 0 ? 'default' : 'outline'">
                                {{ role.users.length }}
                            </Badge>
                        </div>
                    </CardContent>
                </Card>

                <Card v-if="role.users.length > 0">
                    <CardHeader>
                        <CardTitle>Utilizadores</CardTitle>
                    </CardHeader>
                    <CardContent>
                        <div class="space-y-2">
                            <div v-for="user in role.users" :key="user.id" class="flex items-center gap-2">
                                <div class="h-2 w-2 rounded-full bg-green-500"></div>
                                <span class="text-sm">{{ user.name }}</span>
                            </div>
                        </div>
                    </CardContent>
                </Card>
            </div>

            <Card>
                <CardHeader>
                    <CardTitle>Permissões ({{ role.permissions.length }})</CardTitle>
                </CardHeader>
                <CardContent>
                    <div v-if="role.permissions.length > 0" class="space-y-4">
                        <div v-for="(permissions, module) in groupedPermissions" :key="module">
                            <h3 class="font-semibold text-sm mb-2 text-slate-700">
                                {{ moduleNames[module] || module }}
                            </h3>
                            <div class="flex gap-2">
                                <Badge v-for="permission in permissions" :key="permission.id" variant="outline">
                                    {{ actionNames[permission.name.split('.')[1]] || permission.name.split('.')[1] }}
                                </Badge>
                            </div>
                        </div>
                    </div>
                    <div v-else class="text-center py-8 text-slate-500">
                        Nenhuma permissão atribuída
                    </div>
                </CardContent>
            </Card>
        </div>
    </AppLayout>
</template>

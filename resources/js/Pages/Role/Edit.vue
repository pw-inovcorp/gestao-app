<script setup>
import AppLayout from '@/layouts/AppLayout.vue'
import RolePermissionForm from '@/components/RolePermissionForm.vue'
import { useForm, Link } from '@inertiajs/vue3'
import { Button } from '@/components/ui/button'
import { toTypedSchema } from '@vee-validate/zod'
import * as z from 'zod'
import { useForm as useVeeForm } from 'vee-validate'

const props = defineProps({
    role: Object,
    permissions: Object
})

const formSchema = toTypedSchema(z.object({
    name: z.string().min(2, 'Nome deve ter no mínimo 2 caracteres').max(255),
    permissions: z.array(z.number()).min(1, 'Deve selecionar pelo menos uma permissão')
}))

const veeForm = useVeeForm({
    validationSchema: formSchema,
    initialValues: {
        name: props.role.name,
        permissions: props.role.permissions.map(p => p.id)
    }
})

const inertiaForm = useForm({
    name: props.role.name,
    permissions: props.role.permissions.map(p => p.id)
})

const onSubmit = veeForm.handleSubmit((values) => {
    inertiaForm.transform(() => values)
        .patch(`/permissoes/${props.role.id}`)
})
</script>

<template>
    <AppLayout>
        <div class="max-w-4xl mx-auto space-y-6">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold">Editar Grupo de Permissões</h1>
                    <p class="text-slate-600 mt-1">{{ role.name }}</p>
                </div>
                <Link href="/permissoes">
                    <Button variant="outline">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                        </svg>
                        Voltar
                    </Button>
                </Link>
            </div>

            <form @submit.prevent="onSubmit" class="space-y-6">
                <RolePermissionForm
                    :permissions="permissions"
                    :selected-permissions="role.permissions.map(p => p.id)"
                />

                <div class="flex gap-4">
                    <Button type="submit" :disabled="inertiaForm.processing" class="flex-1">
                        <span v-if="inertiaForm.processing">A guardar...</span>
                        <span v-else>Atualizar Grupo</span>
                    </Button>

                    <Link href="/permissoes" class="flex-1">
                        <Button type="button" variant="outline" class="w-full">
                            Cancelar
                        </Button>
                    </Link>
                </div>
            </form>
        </div>
    </AppLayout>
</template>

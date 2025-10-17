<script setup>
import { ref } from 'vue'
import { router } from '@inertiajs/vue3'
import { Dialog, DialogContent, DialogDescription, DialogFooter, DialogHeader, DialogTitle } from '@/components/ui/dialog'
import { Button } from '@/components/ui/button'
import { Input } from '@/components/ui/input'
import { Label } from '@/components/ui/label'

const props = defineProps({
    open: Boolean,
    invoice: Object
})

const emit = defineEmits(['close'])

const proofFile = ref(null)
const sending = ref(false)

const handleFileChange = (event) => {
    const file = event.target.files[0]
    if (file) {
        proofFile.value = file
    }
}

const sendProof = () => {
    if (!proofFile.value) {
        alert('Selecione o comprovativo')
        return
    }

    sending.value = true
    const formData = new FormData()
    formData.append('comprovativo_pagamento', proofFile.value)

    router.post(`/faturas-fornecedor/${props.invoice.id}/comprovativo`, formData, {
        forceFormData: true,
        onSuccess: () => {
            proofFile.value = null
            emit('close')
        },
        onFinish: () => {
            sending.value = false
        }
    })
}

const skip = () => {
    proofFile.value = null
    emit('close')
}
</script>

<template>
    <Dialog :open="open" @update:open="(val) => !val && emit('close')">
        <DialogContent class="sm:max-w-md">
            <DialogHeader>
                <DialogTitle>Enviar Comprovativo ao Fornecedor?</DialogTitle>
                <DialogDescription>
                    Fatura {{ invoice?.numero }} - {{ invoice?.supplier?.nome }}
                </DialogDescription>
            </DialogHeader>

            <div class="space-y-4 py-4">
                <div>
                    <Label for="comprovativo">Comprovativo de Pagamento</Label>
                    <Input
                        id="comprovativo"
                        type="file"
                        accept=".pdf,.jpg,.jpeg,.png"
                        @change="handleFileChange"
                        class="mt-2"
                    />
                </div>
            </div>

            <DialogFooter class="gap-2">
                <Button
                    variant="outline"
                    @click="skip"
                    :disabled="sending"
                >
                    Não Enviar
                </Button>
                <Button
                    @click="sendProof"
                    :disabled="sending || !proofFile"
                >
                    {{ sending ? 'A enviar...' : 'Enviar' }}
                </Button>
            </DialogFooter>
        </DialogContent>
    </Dialog>
</template>

<script setup>
import { ref, watch } from 'vue'
import { router } from '@inertiajs/vue3'
import { Input } from '@/components/ui/input'
import { useDebounceFn } from '@vueuse/core'
import { vOnClickOutside } from '@vueuse/components'

const query = ref('')
const results = ref([])
const open = ref(false)

const search = useDebounceFn(async () => {
    if (query.value.length < 2) {
        results.value = []
        open.value = false
        return
    }

    try {
        const res = await fetch(`/api/search?q=${encodeURIComponent(query.value)}`)
        results.value = await res.json()
        open.value = true
    } catch (err) {
        console.error('Erro na pesquisa:', err)
    }
}, 300)

watch(query, search)

const clear = () => {
    query.value = ''
    results.value = []
    open.value = false
}

const goTo = (url) => {
    router.visit(url)
    clear()
}
</script>

<template>
    <div class="relative flex-1 max-w-md">
        <div class="relative">
            <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
            </svg>

            <Input v-model="query" placeholder="Pesquisar entidades, contactos, artigos, encomendas..." class="pl-10 pr-10" @focus="open = results.length > 0" />

            <button v-if="query" @click="clear" class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>

        <div v-if="open" v-on-click-outside="() => open = false" class="absolute top-full mt-2 w-full bg-white rounded-lg shadow-lg border max-h-96 overflow-y-auto z-50">
            <div v-if="results.length === 0 && query.length >= 2" class="p-4 text-center text-sm text-slate-500">Nenhum resultado encontrado</div>

            <div v-else class="py-2">
                <div v-if="results.entities?.length">
                    <div class="px-3 py-2 text-xs font-semibold text-slate-500 uppercase">Entidades</div>
                    <button v-for="entity in results.entities" :key="entity.id" @click="goTo(`/entidades/${entity.id}`)" class="w-full px-3 py-2 text-left hover:bg-slate-50 flex items-center justify-between group">
                        <div>
                            <div class="font-medium text-sm">{{ entity.nome }}</div>
                            <div class="text-xs text-slate-500">NIF: {{ entity.nif }}</div>
                        </div>
                        <svg class="w-4 h-4 text-slate-400 opacity-0 group-hover:opacity-100" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                        </svg>
                    </button>
                </div>

                <div v-if="results.contacts?.length">
                    <div class="px-3 py-2 text-xs font-semibold text-slate-500 uppercase border-t">Contactos</div>
                    <button v-for="contact in results.contacts" :key="contact.id" @click="goTo(`/contactos/${contact.id}`)" class="w-full px-3 py-2 text-left hover:bg-slate-50 flex items-center justify-between group">
                        <div>
                            <div class="font-medium text-sm">{{ contact.nome }} {{ contact.apelido }}</div>
                            <div class="text-xs text-slate-500">{{ contact.email || contact.telemovel }}</div>
                        </div>
                        <svg class="w-4 h-4 text-slate-400 opacity-0 group-hover:opacity-100" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                        </svg>
                    </button>
                </div>

                <div v-if="results.articles?.length">
                    <div class="px-3 py-2 text-xs font-semibold text-slate-500 uppercase border-t">Artigos</div>
                    <button v-for="article in results.articles" :key="article.id" @click="goTo(`/artigos/${article.id}`)" class="w-full px-3 py-2 text-left hover:bg-slate-50 flex items-center justify-between group">
                        <div>
                            <div class="font-medium text-sm">{{ article.nome }}</div>
                            <div class="text-xs text-slate-500">Ref: {{ article.referencia }}</div>
                        </div>
                        <svg class="w-4 h-4 text-slate-400 opacity-0 group-hover:opacity-100" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                        </svg>
                    </button>
                </div>

                <div v-if="results.proposals?.length">
                    <div class="px-3 py-2 text-xs font-semibold text-slate-500 uppercase border-t">Propostas</div>
                    <button v-for="proposal in results.proposals" :key="proposal.id" @click="goTo(`/propostas/${proposal.id}`)" class="w-full px-3 py-2 text-left hover:bg-slate-50 flex items-center justify-between group">
                        <div>
                            <div class="font-medium text-sm">{{ proposal.numero }}</div>
                            <div class="text-xs text-slate-500">{{ proposal.client_name }}</div>
                        </div>
                        <svg class="w-4 h-4 text-slate-400 opacity-0 group-hover:opacity-100" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                        </svg>
                    </button>
                </div>

                <div v-if="results.orders?.length">
                    <div class="px-3 py-2 text-xs font-semibold text-slate-500 uppercase border-t">Encomendas</div>
                    <button v-for="order in results.orders" :key="order.id" @click="goTo(`/encomendas/${order.id}`)" class="w-full px-3 py-2 text-left hover:bg-slate-50 flex items-center justify-between group">
                        <div>
                            <div class="font-medium text-sm">{{ order.numero }}</div>
                            <div class="text-xs text-slate-500">{{ order.client_name }}</div>
                        </div>
                        <svg class="w-4 h-4 text-slate-400 opacity-0 group-hover:opacity-100" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                        </svg>
                    </button>
                </div>

                <div v-if="results.supplierOrders?.length">
                    <div class="px-3 py-2 text-xs font-semibold text-slate-500 uppercase border-t">Encomendas Fornecedor</div>
                    <button v-for="so in results.supplierOrders" :key="so.id" @click="goTo(`/encomendas-fornecedor/${so.id}`)" class="w-full px-3 py-2 text-left hover:bg-slate-50 flex items-center justify-between group">
                        <div>
                            <div class="font-medium text-sm">{{ so.numero }}</div>
                            <div class="text-xs text-slate-500">{{ so.supplier_name }}</div>
                        </div>
                        <svg class="w-4 h-4 text-slate-400 opacity-0 group-hover:opacity-100" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                        </svg>
                    </button>
                </div>

                <div v-if="results.supplierInvoices?.length">
                    <div class="px-3 py-2 text-xs font-semibold text-slate-500 uppercase border-t">Faturas Fornecedor</div>
                    <button v-for="si in results.supplierInvoices" :key="si.id" @click="goTo(`/faturas-fornecedor/${si.id}`)" class="w-full px-3 py-2 text-left hover:bg-slate-50 flex items-center justify-between group">
                        <div>
                            <div class="font-medium text-sm">{{ si.numero }}</div>
                            <div class="text-xs text-slate-500">{{ si.supplier_name }}</div>
                        </div>
                        <svg class="w-4 h-4 text-slate-400 opacity-0 group-hover:opacity-100" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                        </svg>
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>

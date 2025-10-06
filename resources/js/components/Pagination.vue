<script setup>
import { Link } from '@inertiajs/vue3'
import { Button } from '@/components/ui/button'

defineProps({
    pagination: Object
})
</script>

<template>
    <div class="flex justify-between items-center">
        <p class="text-sm text-slate-600">
            {{ pagination.from }} - {{ pagination.to }} de {{ pagination.total }}
        </p>

        <div class="flex gap-1">
            <Link v-if="pagination.prev_page_url" :href="pagination.first_page_url" preserve-scroll>
                <Button variant="outline" size="sm">Primeira</Button>
            </Link>

            <Link v-if="pagination.prev_page_url" :href="pagination.prev_page_url" preserve-scroll>
                <Button variant="outline" size="sm">Anterior</Button>
            </Link>

            <Button variant="outline" size="sm" disabled>
                {{ pagination.current_page }} / {{ pagination.last_page }}
            </Button>

            <Link v-if="pagination.next_page_url" :href="pagination.next_page_url" preserve-scroll>
                <Button variant="outline" size="sm">Próxima</Button>
            </Link>

            <Link v-if="pagination.next_page_url" :href="pagination.last_page_url" preserve-scroll>
                <Button variant="outline" size="sm">Última</Button>
            </Link>
        </div>
    </div>
</template>

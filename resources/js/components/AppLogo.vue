<script setup>
import { computed } from 'vue'
import { usePage } from '@inertiajs/vue3'

const props = defineProps({
    variant: {
        type: String,
        default: 'sidebar',
    }
})

const page = usePage()

const companyLogo = computed(() => page.props.company?.logo || null)
const companyName = computed(() => page.props.company?.name || 'Gestão-App')

const sidebarClasses = computed(() =>
    'text-xl font-bold text-white hover:text-blue-400 flex items-center gap-2'
)

const authClasses = computed(() =>
    'flex justify-center mb-2'
)
</script>

<template>
    <div v-if="variant === 'sidebar'" :class="sidebarClasses">
        <img
            v-if="companyLogo"
            :src="companyLogo"
            alt="Logo"
            class="h-8 w-auto object-contain"
        />
        <span v-else>{{ companyName }}</span>
    </div>


    <div v-else-if="variant === 'auth'" :class="authClasses">
        <div v-if="companyLogo" class="w-20 h-20 flex items-center justify-center">
            <img
                :src="companyLogo"
                alt="Logo"
                class="max-w-full max-h-full object-contain"
            />
        </div>
        <div v-else class="w-16 h-16 bg-gray-900 rounded-lg flex items-center justify-center">
            <span class="text-3xl text-white font-bold">{{ companyName[0] }}</span>
        </div>
    </div>
</template>

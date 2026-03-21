<template>
    <div class="relative w-[500px]">
        <div
            class="flex items-center rounded-xl h-[60px] bg-[#d9d9d9] border-2 border-[#a6a6a6]"
            :class="{ 'rounded-b-none': suggestions.length > 0 }"
        >
            <SearchIcon class="absolute left-4" />
            <input
                ref="inputEl"
                v-model="query"
                type="text"
                :placeholder="placeholder"
                class="w-full h-full pl-12 pr-4 text-lg rounded-xl focus:outline-none bg-transparent text-gray-800"
                @keydown="handleKeydown"
            />
        </div>
        <SearchSuggestions
            ref="suggestionsRef"
            :suggestions="suggestions"
            @select="handleSelect"
        />
    </div>
</template>

<script setup lang="ts">
import type { Site } from '~/types/site'
import type SearchSuggestionsComponent from './SearchSuggestions.vue'
import SearchIcon from './icons/SearchIcon.vue'

defineProps({
    placeholder: {
        type: String,
        default: 'Search...',
    },
})

const query = ref('')
const router = useRouter()
const { canType, getSuggestions } = useSearch()

const inputEl = ref<HTMLInputElement | null>(null)
const suggestionsRef = ref<InstanceType<typeof SearchSuggestionsComponent> | null>(null)

const suggestions = computed(() => getSuggestions(query.value))

function handleKeydown(e: KeyboardEvent) {
    if (e.key === 'Enter') {
        e.preventDefault()
        const active = suggestionsRef.value?.getActiveSite(suggestions.value)
        if (active) {
            navigateToResults(active.title)
        } else if (query.value.trim()) {
            navigateToResults(query.value.trim())
        }
        return
    }

    if (e.key === 'ArrowDown') {
        e.preventDefault()
        suggestionsRef.value?.moveDown(suggestions.value.length)
        return
    }

    if (e.key === 'ArrowUp') {
        e.preventDefault()
        suggestionsRef.value?.moveUp(suggestions.value.length)
        return
    }

    // Allow control keys (backspace, delete, arrows, etc.)
    if (e.key.length > 1) return

    // Block the character if it doesn't match any site
    if (!canType(query.value, e.key)) {
        e.preventDefault()
    }
}

function handleSelect(site: Site) {
    navigateToResults(site.title)
}

function navigateToResults(q: string) {
    router.push({ path: '/results', query: { q } })
}

watch(query, () => {
    suggestionsRef.value?.reset()
})
</script>

<style scoped>
input {
    line-height: 60px;
}
</style>

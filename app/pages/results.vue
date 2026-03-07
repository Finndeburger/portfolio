<template>
    <div class="px-10 py-8 max-w-2xl">
        <div class="mb-8">
            <SearchInput placeholder="Search..." />
        </div>

        <p v-if="primary.length === 0 && !sponsored" class="text-[#70757a]">
            No results found for "{{ query }}".
        </p>

        <ResultCard v-if="sponsored" :site="sponsored" :sponsored="true" />
        <ResultCard v-for="site in primary" :key="site.slug" :site="site" />
        <ResultCard v-for="site in others" :key="site.slug" :site="site" />
    </div>
</template>

<script setup lang="ts">
const route = useRoute()
const { getOrderedResults } = useSearch()

const query = (route.query.q as string) || ''
const { sponsored, primary, others } = getOrderedResults(query)
</script>

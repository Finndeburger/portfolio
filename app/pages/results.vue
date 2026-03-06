<template>
    <div class="root-div">
        <div class="p-10">
            <h1 class="text-2xl mb-4">Results for "{{ route.query.q }}"</h1>

            <div class="text-black" v-if="results.length === 0">
                No results found.
            </div>

            <div
                v-for="site in results"
                :key="site.url"
                class="mb-6 border border-black p-4 rounded-lg">
                <h2 class="text-xl font-bold">
                    <NuxtLink :to="site.url">{{ site.title }}</NuxtLink>
                </h2>
                <p class="text-gray-600">{{ site.description }}</p>
            </div>
        </div>
    </div>
</template>

<script setup lang="ts">
import { useRoute } from "vue-router";
import { useSearch } from "../composables/useSearch";

const route = useRoute();
const { search } = useSearch();
const results = search((route.query.q as string) || "");
</script>

<style scoped>
.root-div {
    background-color: #e4e4e4;
    min-height: 100vh;
}
</style>

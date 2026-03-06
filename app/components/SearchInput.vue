<template>
    <div
        class="relative flex items-center rounded-xl"
        style="
            width: 500px;
            height: 60px;
            background-color: #d9d9d9;
            border: 2px solid #a6a6a6;
        ">
        <SearchIcon class="absolute left-4" />
        <input
            v-model="query"
            type="text"
            @keyup="handleEnter"
            :placeholder="placeholder"
            class="w-full h-full pl-12 pr-4 text-lg rounded-xl focus:outline-none bg-transparent text-gray-800" />
    </div>
</template>

<script setup lang="ts">
import { ref } from "vue";
import SearchIcon from "./icons/SearchIcon.vue";

// Props for flexibility
defineProps({
    placeholder: {
        type: String,
        default: "Search...",
    },
});

// Search
const query = ref("");
const router = useRouter();

function handleEnter(e: KeyboardEvent) {
    if (e.key === 'Enter' && query.value.trim() !== "") {
    router.push({ path: "/results", query: { q: query.value.trim() } });
  }
}
</script>

<style scoped>
/* Optional: make the input text stay centered vertically on Safari */
input {
    line-height: 60px;
}
</style>

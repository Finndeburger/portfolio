<template>
    <div class="root-div">
        <HomeNav />

        <div class="w-screen flex items-center justify-center flex-col mt-50">
            <img src="../assets/logo.png" alt="Logo" class="w-xl mb-30" />
            <SearchInput v-model="searchQuery" />
        </div>
    </div>
</template>

<script setup lang="ts">
import HomeNav from "../components/HomeNav.vue";
import SearchInput from "../components/SearchInput.vue";

const searchQuery = ref("");
watch(searchQuery, (val) => {
    console.log("User typed:", val);
});
</script>

<style scoped>
.root-div {
    background-color: #e4e4e4;
    min-height: 100vh;
}
</style>

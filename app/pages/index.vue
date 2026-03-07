<template>
    <div class="flex flex-col min-h-screen overflow-hidden">
        <!-- Nav -->
        <nav class="flex justify-end items-center gap-8 px-10 py-6">
            <a href="#" class="text-xs font-semibold uppercase tracking-wide text-gray-400 hover:text-black transition-colors">Gmail</a>
            <a href="#" class="text-xs font-semibold uppercase tracking-wide text-gray-400 hover:text-black transition-colors">Images</a>
            <svg class="w-6 h-6 fill-current text-black cursor-pointer" viewBox="0 0 24 24">
                <path d="M4 8h4V4H4v4zm6 12h4v-4h-4v4zm-6 0h4v-4H4v4zm0-6h4v-4H4v4zm6 0h4v-4h-4v4zm6-10v4h4V4h-4zm-6 4h4V4h-4v4zm6 6h4v-4h-4v4zm0 6h4v-4h-4v4z" />
            </svg>
            <PfpIcon class="w-8 h-8" />
        </nav>

        <!-- Main -->
        <main class="flex-1 flex flex-col items-center justify-center max-w-3xl mx-auto w-full -translate-y-[5vh]">
            <!-- Logo -->
            <div class="text-center mb-10">
                <img src="../assets/logo.png" alt="LookAtMe" class="w-xl mx-auto" />
                <div class="flex gap-4 justify-center mt-4">
                    <span class="text-xs font-bold uppercase tracking-wide text-[#FF6600] relative after:content-[''] after:absolute after:-bottom-1.5 after:left-1/2 after:-translate-x-1/2 after:w-1 after:h-1 after:bg-[#FF6600] after:rounded-full">Web</span>
                    <span class="text-xs font-bold uppercase tracking-wide text-gray-400 cursor-pointer hover:text-black transition-colors">Images</span>
                    <span class="text-xs font-bold uppercase tracking-wide text-gray-400 cursor-pointer hover:text-black transition-colors">News</span>
                    <span class="text-xs font-bold uppercase tracking-wide text-gray-400 cursor-pointer hover:text-black transition-colors">Maps</span>
                </div>
            </div>

            <!-- Search -->
            <SearchInput placeholder="Search or type URL" />

            <!-- Quick Actions -->
            <div class="flex gap-6 mt-12">
                <div class="flex flex-col items-center gap-3" v-for="action in actions" :key="action.label">
                    <button class="w-16 h-16 bg-white rounded-full flex items-center justify-center shadow-md hover:scale-105 active:scale-95 transition-transform cursor-pointer">
                        <svg class="w-6 h-6 text-[#212121]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" v-html="action.icon"></svg>
                    </button>
                    <span class="text-[11px] font-semibold uppercase tracking-wide text-[#212121]">{{ action.label }}</span>
                </div>
            </div>
        </main>

        <!-- Background Ticks -->
        <div class="absolute bottom-0 left-0 w-full h-30 flex justify-center items-end gap-5 opacity-10 pointer-events-none" style="mask-image: linear-gradient(to top, black, transparent); -webkit-mask-image: linear-gradient(to top, black, transparent);">
            <div v-for="(t, i) in ticks" :key="i" class="w-0.5 rounded-sm bg-black" :class="t === 'l' ? 'h-10' : t === 'm' ? 'h-6' : 'h-3'" />
        </div>
    </div>
</template>

<script setup lang="ts">
import PfpIcon from '~/components/icons/PfpIcon.vue'

const actions = [
    { label: 'Home', icon: '<path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/>' },
    { label: 'Saved', icon: '<polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/>' },
    { label: 'History', icon: '<circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/>' },
    { label: 'Add', icon: '<rect x="3" y="3" width="18" height="18" rx="2" ry="2"/><line x1="12" y1="8" x2="12" y2="16"/><line x1="8" y1="12" x2="16" y2="12"/>' },
]

const ticks = ['s','m','s','s','m','l','m','s','s','m','s','s','m','l','m','s','s','m','s','s','m','l','m','s','s','m','s']
</script>
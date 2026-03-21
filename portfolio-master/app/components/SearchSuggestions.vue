<template>
  <div
    v-if="suggestions.length > 0"
    class="absolute top-full left-0 w-full bg-white rounded-b-xl border border-t-0 border-[#a6a6a6] shadow-lg z-50 overflow-hidden"
  >
    <button
      v-for="(site, i) in suggestions"
      :key="site.slug"
      class="flex items-center gap-3 w-full px-4 py-2.5 text-left hover:bg-[#f0f0f0] transition-colors"
      :class="{ 'bg-[#f0f0f0]': i === activeIndex }"
      @click="$emit('select', site)"
      @mouseenter="activeIndex = i"
    >
      <Icon name="lucide:search" class="size-4 text-[#a6a6a6] shrink-0" />
      <span class="text-gray-800 truncate">{{ site.title.toLowerCase() }}</span>
    </button>
  </div>
</template>

<script setup lang="ts">
import type { Site } from '~/types/site'

defineProps<{
  suggestions: Site[]
}>()

defineEmits<{
  select: [site: Site]
}>()

const activeIndex = ref(-1)

function moveDown(max: number) {
  activeIndex.value = (activeIndex.value + 1) % max
}

function moveUp(max: number) {
  activeIndex.value = activeIndex.value <= 0 ? max - 1 : activeIndex.value - 1
}

function getActiveSite(suggestions: Site[]): Site | null {
  if (activeIndex.value >= 0 && activeIndex.value < suggestions.length) {
    return suggestions[activeIndex.value] ?? null
  }
  return null
}

function reset() {
  activeIndex.value = -1
}

defineExpose({ moveDown, moveUp, getActiveSite, reset })
</script>

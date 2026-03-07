<template>
  <div class="mb-6">
    <div v-if="sponsored" class="text-xs text-[#70757a] mb-1">Sponsored</div>
    <p class="text-sm text-[#202124] truncate">{{ site.dummyUrl }}</p>
    <NuxtLink :to="`/sites/${site.slug}`" class="text-xl text-[#1a0dab] hover:underline font-medium">
      {{ site.title }}
    </NuxtLink>
    <p class="text-sm text-[#4d5156] mt-1">{{ site.description }}</p>
  </div>
</template>

<script setup lang="ts">
import type { Site } from '~/types/site'

defineProps<{
  site: Site
  sponsored?: boolean
}>()
</script>

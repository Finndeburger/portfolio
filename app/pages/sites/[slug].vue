<script setup lang="ts">
definePageMeta({ layout: 'browser' })

const route = useRoute()
const slug = route.params.slug as string
const { getSiteBySlug } = useSites()

const site = getSiteBySlug(slug)

if (!site) {
  throw createError({ statusCode: 404, statusMessage: 'Site not found' })
}
</script>

<template>
  <div class="p-10">
    <h1 class="text-3xl font-bold mb-4">{{ site.title }}</h1>
    <p>{{ site.description }}</p>
  </div>
</template>

<template>
  <div class="flex flex-col h-screen bg-[#dee1e6]">
    <BrowserBar :url="currentSite?.dummyUrl ?? route.path" />
    <div class="flex-1 overflow-y-auto h-0">
      <slot />
    </div>
  </div>
</template>

<script setup lang="ts">
const route = useRoute()
const { getSiteBySlug } = useSites()

const currentSite = computed(() => {
  const slug = (route.params.slug as string | undefined)
    ?? route.path.match(/^\/sites\/(.+)/)?.[1]
  return slug ? getSiteBySlug(slug) : undefined
})
</script>

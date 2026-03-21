<template>
  <div class="flex items-center h-10 bg-[#dee1e6] border-b border-[#b0b0b0] px-3 gap-3 shrink-0">
    <!-- Window dots -->
    <div class="flex gap-1.5">
      <span class="w-3 h-3 rounded-full bg-[#ff5f56]" ></span>
      <span class="w-3 h-3 rounded-full bg-[#ffbd2e]" ></span>
      <span class="w-3 h-3 rounded-full bg-[#27c93f]" ></span>
    </div>

    <!-- Back button -->
    <button
      class="flex items-center text-[#5f6368] hover:text-black transition-colors cursor-pointer"
      @click="goBack"
      aria-label="Go back"
    >
      <Icon name="lucide:arrow-left" class="size-5" />
    </button>

    <!-- URL bar -->
    <div class="flex-1 flex items-center h-7 bg-white rounded-full px-4 text-sm text-[#5f6368] select-all overflow-hidden">
      <Icon name="lucide:lock" class="size-3.5 mr-2 text-[#5f6368] shrink-0" />
      <span class="truncate">{{ url }}</span>
    </div>
  </div>
</template>

<script setup lang="ts">
defineProps<{
  url: string
}>()

const router = useRouter()

function goBack() {
  if (window.history.length > 1) {
    router.back()
  } else {
    router.push('/')
  }
}
</script>

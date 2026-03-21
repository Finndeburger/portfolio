import { ref } from 'vue'
import type { Site } from '~/types/site'

export function useSites() {
  const sites = ref<Site[]>([])

  const modules = import.meta.glob('../data/sites/*.ts', { eager: true }) as Record<string, { default: Site }>

  for (const path in modules) {
    const mod = modules[path]
    if (mod?.default !== undefined) {
      sites.value.push(mod.default)
    }
  }

  function getSiteBySlug(slug: string): Site | undefined {
    return sites.value.find((s) => s.slug === slug)
  }

  return { sites, getSiteBySlug }
}

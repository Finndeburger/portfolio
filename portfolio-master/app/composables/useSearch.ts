import type { Site } from '~/types/site'
import { useSites } from './useSites'

export function useSearch() {
  const { sites } = useSites()

  function matchesSite(site: Site, query: string): boolean {
    const q = query.toLowerCase()
    return (
      site.title.toLowerCase().includes(q) ||
      site.tags.some((tag) => tag.toLowerCase().includes(q))
    )
  }

  /** Check if appending `nextChar` to `currentQuery` still matches at least one site */
  function canType(currentQuery: string, nextChar: string): boolean {
    const candidate = (currentQuery + nextChar).toLowerCase()
    if (candidate.length === 0) return true
    return sites.value.some((site) => matchesSite(site, candidate))
  }

  /** Return all sites matching the query substring */
  function getSuggestions(query: string): Site[] {
    if (!query) return []
    return sites.value.filter((site) => matchesSite(site, query))
  }

  /** Ordered results for the results page */
  function getOrderedResults(query: string): {
    sponsored: Site | null
    primary: Site[]
    others: Site[]
  } {
    const primary = getSuggestions(query)
    const primarySlugs = new Set(primary.map((s) => s.slug))

    // Pick a random sponsored site that isn't already a primary result
    const sponsoredCandidates = sites.value.filter(
      (s) => s.sponsored && !primarySlugs.has(s.slug)
    )
    const sponsored =
      sponsoredCandidates.length > 0
        ? sponsoredCandidates[Math.floor(Math.random() * sponsoredCandidates.length)] ?? null
        : null

    // Everything else, shuffled
    const usedSlugs = new Set([...primarySlugs, ...(sponsored ? [sponsored.slug] : [])])
    const others = sites.value
      .filter((s) => !usedSlugs.has(s.slug))
      .sort(() => Math.random() - 0.5)

    return { sponsored, primary, others }
  }

  return { canType, getSuggestions, getOrderedResults }
}

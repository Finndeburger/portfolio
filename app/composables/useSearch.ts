import { useSites } from "./useSites";

export function useSearch() {
  const { sites } = useSites();

  const search = (query: string) => {
    const q = query.toLowerCase();
    return sites.value.filter(
      (site) =>
        site.title.toLowerCase().includes(q) ||
        site.tags.some((tag: string) => tag.toLowerCase().includes(q))
    );
  };

  return { search };
}

import { ref } from 'vue';

export function useSites() {
  const sites = ref<any[]>([]);

  const modules = import.meta.glob("../data/sites/*.ts", { eager: true }) as Record<string, { default: any }>;

  for (const path in modules) {
    const mod = modules[path];
    if (mod?.default !== undefined) {
      sites.value.push(mod.default);
    }
  }

  return { sites };
}

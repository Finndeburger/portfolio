// https://nuxt.com/docs/api/configuration/nuxt-config
export default defineNuxtConfig({
  compatibilityDate: '2025-07-15',
  devtools: {
    enabled: true,
 
    timeline: {
      enabled: true
    }
  },
  modules: ['@nuxt/ui', '@nuxt/icon', '@nuxt/image'],
  css: ['~/assets/css/main.css']
})
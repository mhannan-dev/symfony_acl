export default defineNuxtConfig({
  compatibilityDate: '2025-07-15',
  devtools: { enabled: true },

  modules: ['@nuxtjs/tailwindcss'],

  nitro: {
    devProxy: {
      '/api': {
        target: 'http://127.0.0.1:8000/api',
        changeOrigin: true,
        prependPath: false,
      },
    },
  },

  css: ['~/assets/css/main.css'],

  app: {
    head: {
      title: 'Symfony ACL',
    },
  },
})

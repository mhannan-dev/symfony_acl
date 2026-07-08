export default defineNuxtConfig({
  compatibilityDate: '2025-07-15',
  devtools: { enabled: true },

  extends: [
    './layers/shared',
    './layers/features/auth',
  ],

  modules: [
    '@nuxtjs/tailwindcss',
    '@nuxt/icon'
  ],

  nitro: {
    devProxy: {
      '/api/v1': {
        target: 'http://127.0.0.1:8000/api/v1',
        changeOrigin: true,
      },
    },
  },

  imports: {
    dirs: [
      '~~/layers/shared/composables/**',
      '~~/layers/features/**/composables/**',
    ],
  },

  components: [
    { path: '~/components', pathPrefix: false },
    { path: '~~/layers/shared/components', pathPrefix: false },
    { path: '~~/layers/features/auth/components', pathPrefix: false },
  ],

  css: ['~/assets/css/main.css'],

  app: {
    head: {
      title: 'RBAC',
    },
  },
})

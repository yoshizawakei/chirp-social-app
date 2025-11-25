export default defineNuxtConfig({
  modules: [
    '@pinia/nuxt',
  ],

  plugins: [
    '~/plugins/firebase.client.js',
  ],

  runtimeConfig: {
    public: {
      apiBase: process.env.API_URL || "http://localhost/api",
      API_URL: process.env.NUXT_PUBLIC_API_URL,
      FIREBASE_API_KEY: process.env.NUXT_PUBLIC_FIREBASE_API_KEY,
      FIREBASE_AUTH_DOMAIN: process.env.NUXT_PUBLIC_FIREBASE_AUTH_DOMAIN,
      FIREBASE_PROJECT_ID: process.env.NUXT_PUBLIC_FIREBASE_PROJECT_ID,
      FIREBASE_APP_ID: process.env.NUXT_PUBLIC_FIREBASE_APP_ID,
    }
  },

  ssr: true,

  devServer: {
    host: '0.0.0.0',
    port: 3000,
  },

  css: [
    '~/assets/css/main.css',
  ],
})

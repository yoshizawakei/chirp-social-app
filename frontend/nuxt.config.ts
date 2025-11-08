// 💡 修正 1: defineNuxtConfig は 'nuxt/config' からインポートします
import { defineNuxtConfig } from 'nuxt/config'

export default defineNuxtConfig({
  modules: [
    '@pinia/nuxt',
  ],

  plugins: [
    '~/plugins/firebase.js',
    '~/plugins/vuex.js',
  ],

  runtimeConfig: {
    public: {
      firebase: {
        apiKey: process.env.FIREBASE_API_KEY,
        authDomain: process.env.FIREBASE_AUTH_DOMAIN,
        projectId: process.env.FIREBASE_PROJECT_ID,
        appId: process.env.FIREBASE_APP_ID,
      },
    },
  },

  ssr: true,

  devServer: {
    host: '0.0.0.0',
    port: 3000,
  }
})
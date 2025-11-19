
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
    '~/assets/css/main.css', // 💡 グローバルCSSのパスを設定
  ],

})
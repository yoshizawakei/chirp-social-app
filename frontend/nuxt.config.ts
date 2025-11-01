// https://nuxt.com/docs/api/configuration/nuxt-config
export default defineNuxtConfig({
  compatibilityDate: '2025-07-15',
  devtools: { enabled: true },

  runtimeConfig: {
      // private: {}, // サーバーサイド専用の変数があればここに

      // public セクション: クライアントサイド（ブラウザ）に公開される変数
      public: {
        FIREBASE_API_KEY: process.env.NUXT_ENV_FIREBASE_API_KEY,
        FIREBASE_AUTH_DOMAIN: process.env.NUXT_ENV_FIREBASE_AUTH_DOMAIN,
        FIREBASE_PROJECT_ID: process.env.NUXT_ENV_FIREBASE_PROJECT_ID,
        FIREBASE_STORAGE_BUCKET: process.env.NUXT_ENV_FIREBASE_STORAGE_BUCKET,
        FIREBASE_MESSAGING_SENDER_ID: process.env.NUXT_ENV_FIREBASE_MESSAGING_SENDER_ID,
        FIREBASE_APP_ID: process.env.NUXT_ENV_FIREBASE_APP_ID,
      }
  },

  plugins: [
    './plugins/firebase.js', // 作成するプラグインのパス
  ],

  modules: ['@nuxt/devtools'],

  rootDir: './',

  vite: {
    // Dockerでのファイル監視問題を回避するための設定
    server: {
      watch: {
        usePolling: true // ファイル変更を定期的に確認するように強制
      }
    }
  },

})

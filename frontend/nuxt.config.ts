export default {
  // 必須モジュールの設定
  modules: [
    // @nuxtjs/axios は削除しました。組み込みの $fetch を使用します。
  ],

  // 必須プラグインの設定
  plugins: [
    '~/plugins/firebase.js',
    // 💡 Vuexストアを有効にするために必要な設定 (Nuxt 2 の場合)
  ],

  // 💡 Firebaseの環境変数をクライアントサイドに公開
  publicRuntimeConfig: {
    firebase: {
      apiKey: process.env.FIREBASE_API_KEY, 
      authDomain: process.env.FIREBASE_AUTH_DOMAIN,
      projectId: process.env.FIREBASE_PROJECT_ID,
      appId: process.env.FIREBASE_APP_ID,
    },
  },
  
  // 💡 API通信のベースURLを環境変数として設定すると便利です
  // ただし、$fetchでは各呼び出しでbaseURLを指定する必要があります
}
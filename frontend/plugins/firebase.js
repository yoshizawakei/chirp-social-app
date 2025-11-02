// frontend/plugins/firebase.js
import firebase from 'firebase/app'
import 'firebase/auth'

// Nuxt.jsの設定を取得
const config = require('~/nuxt.config').default.publicRuntimeConfig.firebase;

// SSRを考慮し、ブラウザでのみ初期化
if (process.browser && config && !firebase.apps.length) {
  firebase.initializeApp(config)
}

// 認証サービスのエクスポート
export const auth = firebase.auth()

// Vueインスタンスに $auth を注入
export default (context, inject) => {
  // $authを通じて Firebase Auth 機能にアクセス
  inject('auth', auth)
}
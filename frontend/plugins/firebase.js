// frontend/plugins/firebase.js

import { initializeApp } from 'firebase/app';
import { getAuth } from 'firebase/auth';

// export default (context, inject) を修正
// $config の中にある public プロパティを使用します
export default ({ $config }, inject) => {

  // $config.public から設定値を取得します
    const config = $config.public;

    const firebaseConfig = {
    apiKey: config.FIREBASE_API_KEY,
    authDomain: config.FIREBASE_AUTH_DOMAIN,
    projectId: config.FIREBASE_PROJECT_ID,
    storageBucket: config.FIREBASE_STORAGE_BUCKET,
    messagingSenderId: config.FIREBASE_MESSAGING_SENDER_ID,
    appId: config.FIREBASE_APP_ID
    };

    // 1. Firebaseアプリを初期化
    const app = initializeApp(firebaseConfig);

    // 2. Firebase Authenticationサービスを取得
    const auth = getAuth(app);

    // 3. Nuxt.jsのコンテキストに注入
    inject('firebaseAuth', auth);
};
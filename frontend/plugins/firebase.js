// frontend/plugins/firebase.js

import { initializeApp } from 'firebase/app';
import { getAuth } from 'firebase/auth';
import { defineNuxtPlugin } from '#app';

export default defineNuxtPlugin((nuxtApp) => {

    const config = nuxtApp.$config.public;

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

    // 3. Nuxt.jsのグローバルなプロパティとして注入
    return {
    provide: {
        firebaseAuth: auth
    }
    };
});
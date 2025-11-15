// frontend/plugins/firebase.js
import { initializeApp } from 'firebase/app'
import { getAuth } from 'firebase/auth'
import { getFirestore } from 'firebase/firestore'

export default defineNuxtPlugin((nuxtApp) => {
    const config = nuxtApp.$config.public.firebase;

    let auth = null;
    let firestore = null;

    // クライアント側のみ Firebase 初期化
    if (process.client && config) {
        const firebaseApp = initializeApp(config);

        // 認証サービス
        auth = getAuth(firebaseApp);

        // Firestore（投稿機能に必須）
        firestore = getFirestore(firebaseApp);
    }

    return {
        provide: {
            auth,
            firestore,
        }
    }
});

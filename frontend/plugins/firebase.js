import { initializeApp } from 'firebase/app' // firebase.initializeApp のための関数をインポート
import { getAuth } from 'firebase/auth' // 💡 修正点: getAuth 関数をインポート

// defineNuxtPlugin と useRuntimeConfig を使用
export default defineNuxtPlugin((nuxtApp) => {
    // runtimeConfig から public.firebase の設定を取得
    const config = nuxtApp.$config.public.firebase; 
    let auth = null;

    // SSR を考慮し、クライアント側でのみ初期化 (process.client)
    if (process.client && config) {
        // アプリケーションが初期化されていない場合のみ実行
        const firebaseApp = initializeApp(config); 
        
        // 💡 修正点: getAuth(app) を使用して auth インスタンスを取得
        auth = getAuth(firebaseApp); 
    }

    // 💡 サーバーサイドで auth が null のままであってもエラーにしない
    //    クライアントサイドでのみ auth が利用可能になる
    return {
        provide: {
            // 💡 $auth が null でない場合にのみ提供されます
            auth: auth
        }
    }
})
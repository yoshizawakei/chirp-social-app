// 💡 plugins/vuex.js

import { createStore } from 'vuex'
import authModule from '~/store/auth'

export default defineNuxtPlugin((nuxtApp) => {
    // 1. ストアを構築し、注入する (これは同期的に完了)
    const store = createStore({
        modules: {
            auth: authModule,
        },
        strict: process.env.NODE_ENV !== 'production' 
    })

    nuxtApp.vueApp.use(store)
    nuxtApp.vueApp.config.globalProperties.$store = store
    
    // 2. 💡 ここが重要！アプリの起動後にフックでディスパッチを遅延させる
    nuxtApp.hook('app:mounted', () => {
        if (process.client) {
            // アプリがマウントされた後（全てのコンポーネントとモジュールがロードされた後）に実行
            if (store._actions['auth/onAuthStateChangedAction']) {
                store.dispatch('auth/onAuthStateChangedAction').catch(e => {
                    console.error("Firebase auth state check failed during final check:", e);
                });
                console.log("✅ Auth check successfully run via app:mounted hook.");
            } else {
                console.error("❌ Auth module missing even after app:mounted.");
            }
        }
    })
    
    console.log("✅ Vuex store successfully registered with Nuxt app.");

})
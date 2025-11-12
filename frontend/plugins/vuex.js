// plugins/vuex.js

import { createStore } from 'vuex'
import authModule from '~/store/auth'
import postsModule from '~/store/posts'


export default defineNuxtPlugin((nuxtApp) => {
    const store = createStore({
        modules: {
            auth: authModule,
            posts: postsModule,
        },
        strict: process.env.NODE_ENV !== 'production' 
    })

    nuxtApp.vueApp.use(store)
    nuxtApp.vueApp.config.globalProperties.$store = store
    
    nuxtApp.hook('app:mounted', () => {
        if (process.client) {
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
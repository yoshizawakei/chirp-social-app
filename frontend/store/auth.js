// 💡 frontend/store/auth.js
// Firebase関連のインポート (お使いのインポートを維持)
import { 
    getAuth, 
    createUserWithEmailAndPassword, 
    signInWithEmailAndPassword, 
    onAuthStateChanged, 
    signOut 
} from 'firebase/auth' 
import { initializeApp } from 'firebase/app' 
// import router from 'vue-router' // Nuxtでは use/navigateTo を使うため、これは不要

// 💡 Vuexモジュールをデフォルトエクスポートとして定義
export default {
    // 💡 名前空間を有効にする
    namespaced: true, 

    state: () => ({
        user: null,
        authChecked: false, // 認証チェックが完了したかを示すフラグ
    }),

    mutations: {
        setUser(state, user) {
            state.user = user
        },
        setAuthChecked(state, status) {
            state.authChecked = status
        }
    },

    actions: {
        // 認証状態の変更監視 (アプリ起動時に呼び出される)
        onAuthStateChangedAction({ commit }) {
            return new Promise((resolve) => {
                if (process.client) {
                    try {
                        const auth = getAuth()
                        
                        onAuthStateChanged(auth, user => { 
                            // ユーザーがログイン状態の場合
                            commit('setUser', user)
                            commit('setAuthChecked', true)
                            resolve(user)
                        })
                    } catch (e) {
                        console.error("Firebase Auth 初期化エラー:", e);
                        commit('setAuthChecked', true);
                        resolve(null);
                    }
                } else {
                    // サーバーサイドではすぐに解決
                    commit('setAuthChecked', true)
                    resolve(null)
                }
            })
        },
        
        // ユーザー登録
        async signUpAction({ dispatch }, { email, password }) {
            const auth = getAuth()
            await createUserWithEmailAndPassword(auth, email, password)
            // 登録後、認証状態の更新を待つ
            await dispatch('onAuthStateChangedAction') 
        },

        // ログイン
        async loginAction({ dispatch }, { email, password }) {
            const auth = getAuth()
            await signInWithEmailAndPassword(auth, email, password)
            await dispatch('onAuthStateChangedAction') 
        },

        // ログアウト
        async logoutAction({ commit }) {
            const auth = getAuth()
            await signOut(auth)
            // ログアウト後、ユーザー情報をクリア
            commit('setUser', null) 
            // 💡 ログアウト後のリダイレクトはコンポーネント側で行うのがベスト
        }
    },

    getters: {
        isLoggedIn: (state) => !!state.user
    }
}
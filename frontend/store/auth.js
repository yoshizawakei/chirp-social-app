// frontend/store/auth.js

import {
    createUserWithEmailAndPassword,
    signInWithEmailAndPassword,
    onAuthStateChanged,
    signOut
} from 'firebase/auth'


import { useNuxtApp } from '#app';

export default {
    namespaced: true,

    state: () => ({
        user: null,
        authChecked: false,
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
        onAuthStateChangedAction({ commit }) {
            return new Promise((resolve) => {
                if (process.client) {
                    try {
                        const { $auth } = useNuxtApp();

                        if (!$auth) {
                            throw new Error("Firebase Auth is not initialized.");
                        }

                        onAuthStateChanged($auth, user => {
                            commit('setUser', user)

                            console.log("AUTH_STATE_CHANGE: Firebase reports user:", user ? user.uid : 'null/undefined');

                            if (!this.state.auth.authChecked) {
                                commit('setAuthChecked', true)
                                resolve(user)
                            }
                        })
                    } catch (e) {
                        console.error("Firebase Auth 初期化エラー:", e);
                        commit('setAuthChecked', true);
                        resolve(null);
                    }
                } else {
                    commit('setAuthChecked', true)
                    resolve(null)
                }
            })
        },

        async signUpAction({ commit }, { email, password }) {
            const { $auth } = useNuxtApp();

            if (!$auth) throw new Error("認証サービスが利用できません。");

            const userCredential = await createUserWithEmailAndPassword($auth, email, password)
            commit('setUser', userCredential.user)
        },

        async loginAction({ commit }, { email, password }) {
            const { $auth } = useNuxtApp();

            if (!$auth) throw new Error("認証サービスが利用できません。");

            try {
                const userCredential = await signInWithEmailAndPassword($auth, email, password);
                commit('setUser', userCredential.user);

                // 💡 追記: ログイン成功時のユーザー情報をログに出力
                console.log("LOGIN_SUCCESS: User committed to store:", userCredential.user.uid);

            } catch (error) {
                // 💡 追記: ログイン失敗時のエラーをログに出力
                console.error("LOGIN_FAILED:", error.message);
                throw error;
            }
        },

        async logoutAction({ commit }) {
            const { $auth } = useNuxtApp();

            if (!$auth) return;

            await signOut($auth)
            commit('setUser', null)
        }
    },

    getters: {
        user: (state) => state.user,
        isLoggedIn: (state) => !!state.user,

        isAuthChecked: (state) => state.authChecked
    }
}
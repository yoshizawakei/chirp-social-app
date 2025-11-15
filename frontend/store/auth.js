// frontend/store/auth.js

import {
    createUserWithEmailAndPassword,
    signInWithEmailAndPassword,
    onAuthStateChanged,
    signOut
} from "firebase/auth";

import { useNuxtApp } from "#app";

export default {
    namespaced: true,

    // -------------------------
    // State
    // -------------------------
    state: () => ({
        user: null,
        authChecked: false, // 初回チェックが完了したか
    }),

    // -------------------------
    // Mutations
    // -------------------------
    mutations: {
        setUser(state, user) {
            state.user = user;
        },
        setAuthChecked(state, value) {
            state.authChecked = value;
        },
    },

    // -------------------------
    // Actions
    // -------------------------
    actions: {
        /**
         * アプリ起動時に Firebase のログイン状態を監視
         * SSR では何もせず、CSR のみ実行
         */
        initAuth({ commit }) {
            return new Promise((resolve) => {
                if (!process.client) {
                    commit("setAuthChecked", true);
                    return resolve(null);
                }

                const { $auth } = useNuxtApp();
                if (!$auth) {
                    console.error("Firebase Auth is not initialized.");
                    commit("setAuthChecked", true);
                    return resolve(null);
                }

                try {
                    onAuthStateChanged($auth, (user) => {
                        commit("setUser", user);
                        commit("setAuthChecked", true);
                        resolve(user);
                    });
                } catch (err) {
                    console.error("onAuthStateChanged Error:", err);
                    commit("setAuthChecked", true);
                    resolve(null);
                }
            });
        },

        // -------------------------
        // ユーザー登録
        // -------------------------
        async signUpAction({ commit }, { email, password }) {
            const { $auth } = useNuxtApp();
            if (!$auth) throw new Error("認証サービスが利用できません。");

            const userCredential = await createUserWithEmailAndPassword(
                $auth,
                email,
                password
            );

            commit("setUser", userCredential.user);
        },

        // -------------------------
        // ログイン
        // -------------------------
        async loginAction({ commit }, { email, password }) {
            const { $auth } = useNuxtApp();
            if (!$auth) throw new Error("認証サービスが利用できません。");

            try {
                const userCredential = await signInWithEmailAndPassword(
                    $auth,
                    email,
                    password
                );
                commit("setUser", userCredential.user);
                console.log("LOGIN SUCCESS:", userCredential.user.uid);
            } catch (error) {
                console.error("LOGIN FAILED:", error);
                throw error;
            }
        },

        // -------------------------
        // ログアウト
        // -------------------------
        async logoutAction({ commit }) {
            const { $auth } = useNuxtApp();
            if (!$auth) return;

            await signOut($auth);
            commit("setUser", null);
        },
    },

    // -------------------------
    // Getters
    // -------------------------
    getters: {
        user: (state) => state.user,
        isLoggedIn: (state) => !!state.user,
        isAuthChecked: (state) => state.authChecked,
    },
};

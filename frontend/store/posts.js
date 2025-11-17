// frontend/store/posts.js
import { useNuxtApp } from '#app';
import { $fetch } from 'ofetch';

const API_BASE_URL = 'http://localhost/api'; // .env の API_URL と揃えてもOK

export default {
    namespaced: true,

    state: () => ({
        posts: [],
        userPosts: [],
        postDetail: null,
        comments: [],
    }),

    mutations: {
        setPosts(state, posts) { state.posts = posts; },
        setUserPosts(state, posts) { state.userPosts = posts; },
        setPostDetail(state, post) { state.postDetail = post; },
        setComments(state, comments) { state.comments = comments; },
    },

    getters: {
        allPosts: (state) => state.posts,
        userPosts: (state) => state.userPosts,
        postDetail: (state) => state.postDetail,
        comments: (state) => state.comments,
    },

    actions: {
        // =====================
        // 投稿一覧（ホーム）
        // =====================
        async fetchPostsAction({ commit }) {
            try {
                const posts = await $fetch(`${API_BASE_URL}/posts`);
                commit('setPosts', posts);
            } catch (e) {
                console.error('fetchPostsAction error:', e);
            }
        },

        // =====================
        // 新規投稿（サイドバー）
        // payload: { message, userId, userEmail }
        // =====================
        async addPostAction(_, payload) {
            const { message, userId, userEmail } = payload || {};

            const text = (message || '').trim();
            if (!text) {
                throw new Error('投稿内容を入力してください。');
            }
            if (text.length > 120) {
                throw new Error('投稿は120文字以内で入力してください。');
            }
            if (!userId || !userEmail) {
                throw new Error('ユーザー情報が不足しています。');
            }

            const username = userEmail.split('@')[0];

            await $fetch(`${API_BASE_URL}/posts`, {
                method: 'POST',
                body: {
                    userId,
                    username,
                    message: text,
                },
            });
        },

        // =====================
        // 投稿削除
        // =====================
        async deletePostAction({ rootGetters }, postId) {
            const user = rootGetters['auth/user'];
            if (!user) throw new Error('ログインが必要です。');

            await $fetch(`${API_BASE_URL}/posts/${postId}`, {
                method: 'DELETE',
                body: {
                    userId: user.uid,
                },
            });
        },

        // =====================
        // いいねトグル
        // =====================
        async likePostAction({ rootGetters }, postId) {
            const user = rootGetters['auth/user'];
            if (!user) throw new Error('ログインが必要です。');

            await $fetch(`${API_BASE_URL}/posts/${postId}/like`, {
                method: 'POST',
                body: {
                    userId: user.uid,
                },
            });
        },

        // =====================
        // 投稿詳細
        // =====================
        async fetchPostDetailAction({ commit }, postId) {
            try {
                const post = await $fetch(`${API_BASE_URL}/posts/${postId}`);
                commit('setPostDetail', post);
            } catch (e) {
                console.error('fetchPostDetailAction error:', e);
                commit('setPostDetail', null);
            }
        },

        // =====================
        // コメント一覧
        // =====================
        async fetchCommentsAction({ commit }, postId) {
            try {
                const comments = await $fetch(`${API_BASE_URL}/posts/${postId}/comments`);
                commit('setComments', comments);
            } catch (e) {
                console.error('fetchCommentsAction error:', e);
                commit('setComments', []);
            }
        },

        // =====================
        // コメント投稿
        // =====================
        async addCommentAction({ rootGetters }, { postId, text }) {
            const user = rootGetters['auth/user'];
            if (!user) throw new Error('ログインが必要です。');

            const msg = (text || '').trim();
            if (!msg) throw new Error('コメントを入力してください。');
            if (msg.length > 120) throw new Error('コメントは120文字以内で入力してください。');

            const username = user.email.split('@')[0];

            await $fetch(`${API_BASE_URL}/posts/${postId}/comments`, {
                method: 'POST',
                body: {
                    userId: user.uid,
                    username,
                    text: msg,
                },
            });
        },
    },
};

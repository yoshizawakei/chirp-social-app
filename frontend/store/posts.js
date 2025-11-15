// frontend/store/posts.js
import { useNuxtApp } from '#app';
import {
    collection,
    onSnapshot,
    query,
    orderBy,
    doc,
    deleteDoc,
    getDoc,
    writeBatch,
    addDoc,
    serverTimestamp,
    where,
    arrayUnion,
    arrayRemove
} from 'firebase/firestore';

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
        // ホーム投稿一覧
        fetchPostsAction({ commit }) {
            const { $firestore } = useNuxtApp();
            if (!$firestore) return () => {};

            const q = query(
                collection($firestore, 'posts'),
                orderBy('createdAt', 'desc')
            );

            const unsubscribe = onSnapshot(q, (snapshot) => {
                const posts = snapshot.docs.map(docSnap => {
                    const data = docSnap.data();
                    const likes = data.likes || [];

                    let createdAt = data.createdAt ?? null;
                    if (!createdAt) {
                        createdAt = new Date(); // 🔥 null のままにしない
                    }

                    return {
                        id: docSnap.id,
                        ...data,
                        likes,
                        likeCount: likes.length,
                        createdAt,
                    };
                });

                commit("setPosts", posts);
            });

            return unsubscribe;
        },

        // 新規投稿
        async addPostAction({ rootGetters }, payload) {
            const { $firestore } = useNuxtApp();
            const user = rootGetters["auth/user"];
            if (!user) throw new Error("ログインが必要です。");

            const rawMessage = payload?.message;
            const message = typeof rawMessage === "string"
                ? rawMessage.trim()
                : String(rawMessage || "").trim();

            if (!message) {
                throw new Error("投稿内容を入力してください。");
            }

            const userId = payload.userId || user.uid;
            const userEmail = payload.userEmail || user.email;

            const newPost = {
                userId,
                username: userEmail.split("@")[0],
                email: userEmail,
                message,
                createdAt: serverTimestamp(),
                likes: [],
            };

            await addDoc(collection($firestore, "posts"), newPost);
        },

        // 投稿削除
        async deletePostAction({ rootGetters }, postId) {
            const { $firestore } = useNuxtApp();
            const user = rootGetters["auth/user"];
            if (!user) throw new Error("ログインが必要です。");

            const ref = doc($firestore, "posts", postId);
            const snap = await getDoc(ref);
            if (!snap.exists()) throw new Error("投稿が存在しません。");

            if (snap.data().userId !== user.uid) {
                throw new Error("削除権限がありません。");
            }

            await deleteDoc(ref);
        },

        // いいね
        async likePostAction({ rootGetters }, postId) {
            const { $firestore } = useNuxtApp();
            const user = rootGetters["auth/user"];
            if (!user) throw new Error("ログインが必要です。");

            const ref = doc($firestore, "posts", postId);
            const snap = await getDoc(ref);
            if (!snap.exists()) throw new Error("投稿が見つかりません。");

            const likes = snap.data().likes || [];
            const batch = writeBatch($firestore);

            if (likes.includes(user.uid)) {
                batch.update(ref, { likes: arrayRemove(user.uid) });
            } else {
                batch.update(ref, { likes: arrayUnion(user.uid) });
            }

            await batch.commit();
        },

        // プロフィール内の投稿一覧
        fetchUserPostsAction({ commit, rootGetters }) {
            const { $firestore } = useNuxtApp();
            const user = rootGetters["auth/user"];
            if (!user) return () => {};

            const q = query(
                collection($firestore, "posts"),
                where("userId", "==", user.uid),
                orderBy("createdAt", "desc")
            );

            const unsubscribe = onSnapshot(q, (snapshot) => {
                const posts = snapshot.docs.map(docSnap => {
                    const data = docSnap.data();
                    const likes = data.likes || [];

                    let createdAt = data.createdAt ?? null;
                    if (!createdAt) createdAt = new Date();

                    return {
                        id: docSnap.id,
                        ...data,
                        likes,
                        likeCount: likes.length,
                        createdAt,
                    };
                });

                commit("setUserPosts", posts);
            });

            return unsubscribe;
        },

        // 投稿詳細
        fetchPostDetailAction({ commit }, postId) {
            const { $firestore } = useNuxtApp();
            if (!postId) return () => {};

            const ref = doc($firestore, "posts", postId);

            const unsubscribe = onSnapshot(ref, (snap) => {
                if (!snap.exists()) {
                    commit("setPostDetail", null);
                    return;
                }

                const data = snap.data();
                const likes = data.likes || [];

                let createdAt = data.createdAt ?? null;
                if (!createdAt) createdAt = new Date();

                commit("setPostDetail", {
                    id: snap.id,
                    ...data,
                    likes,
                    likeCount: likes.length,
                    createdAt,
                });
            });

            return unsubscribe;
        },

        // コメント一覧
        fetchCommentsAction({ commit }, postId) {
            const { $firestore } = useNuxtApp();
            if (!postId) return () => {};

            const q = query(
                collection($firestore, "comments"),
                where("postId", "==", postId),
                orderBy("createdAt", "asc")
            );

            const unsubscribe = onSnapshot(q, (snapshot) => {
                const comments = snapshot.docs.map(doc => ({
                    id: doc.id,
                    ...doc.data(),
                }));
                commit("setComments", comments);
            });

            return unsubscribe;
        },

        // コメント投稿
        async addCommentAction({ rootGetters }, { postId, text }) {
            const { $firestore } = useNuxtApp();
            const user = rootGetters["auth/user"];
            if (!user) throw new Error("ログインが必要です。");

            const msg = typeof text === "string" ? text.trim() : "";
            if (!msg) throw new Error("コメントを入力してください。");

            const newComment = {
                postId,
                userId: user.uid,
                username: user.email.split("@")[0],
                text: msg,
                createdAt: serverTimestamp()
            };

            await addDoc(collection($firestore, "comments"), newComment);
        }
    }
};

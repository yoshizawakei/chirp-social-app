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

    // ----------------------
    //  State
    // ----------------------
    state: () => ({
        posts: [],
        userPosts: [],
        postDetail: null,
        comments: [],
    }),

    // ----------------------
    //  Mutations
    // ----------------------
    mutations: {
        setPosts(state, posts) { state.posts = posts; },
        setUserPosts(state, posts) { state.userPosts = posts; },
        setPostDetail(state, post) { state.postDetail = post; },
        setComments(state, comments) { state.comments = comments; },
    },

    // ----------------------
    //  Getters
    // ----------------------
    getters: {
        allPosts: (state) => state.posts,
        userPosts: (state) => state.userPosts,
        postDetail: (state) => state.postDetail,
        comments: (state) => state.comments,
    },

    // ----------------------
    //  Actions
    // ----------------------
    actions: {

        // =====================
        // ホーム投稿一覧
        // =====================
        fetchPostsAction({ commit }) {
            const { $firestore } = useNuxtApp();
            if (!$firestore) return () => {};

            const q = query(
                collection($firestore, 'posts'),
                orderBy('createdAt', 'desc')
            );

            const unsubscribe = onSnapshot(q, (snapshot) => {
                const posts = snapshot.docs.map(doc => {
                    const data = doc.data();
                    const likes = data.likes || [];
                    return {
                        id: doc.id,
                        ...data,
                        likes,
                        likeCount: likes.length,
                    };
                });

                commit("setPosts", posts);
            });

            return unsubscribe;
        },

        // =====================
        // 新規投稿
        // =====================
        async addPostAction({ rootGetters }, message) {
            const { $firestore } = useNuxtApp();

            const user = rootGetters["auth/user"];
            if (!user) throw new Error("ログインが必要です。");

            if (!message || message.trim() === "") {
                throw new Error("投稿内容を入力してください。");
            }

            const newPost = {
                userId: user.uid,
                username: user.email.split("@")[0],
                email: user.email,
                message,
                createdAt: serverTimestamp(),
                likes: [],
            };

            await addDoc(collection($firestore, "posts"), newPost);
        },

        // =====================
        // 投稿削除
        // =====================
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

        // =====================
        // いいね機能
        // =====================
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

        // =====================
        // プロフィール内の投稿一覧
        // =====================
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
                const posts = snapshot.docs.map(doc => {
                    const data = doc.data();
                    const likes = data.likes || [];
                    return {
                        id: doc.id,
                        ...data,
                        likes,
                        likeCount: likes.length,
                    };
                });

                commit("setUserPosts", posts);
            });

            return unsubscribe;
        },

        // =====================
        // 投稿詳細
        // =====================
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

                commit("setPostDetail", {
                    id: snap.id,
                    ...data,
                    likes,
                    likeCount: likes.length,
                });
            });

            return unsubscribe;
        },

        // =====================
        // コメント一覧
        // =====================
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

        // =====================
        // コメント投稿
        // =====================
        async addCommentAction({ rootGetters }, { postId, text }) {
            const { $firestore } = useNuxtApp();

            const user = rootGetters["auth/user"];
            if (!user) throw new Error("ログインが必要です。");

            if (!text || text.trim() === "") {
                throw new Error("コメントを入力してください。");
            }

            const newComment = {
                postId,
                userId: user.uid,
                username: user.email.split("@")[0],
                text,
                createdAt: serverTimestamp()
            };

            await addDoc(collection($firestore, "comments"), newComment);
        }
    }
};

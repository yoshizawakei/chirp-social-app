// store/posts.js (新規投稿、詳細、コメント機能に対応)

import { useNuxtApp } from '#app';
import {
    collection,
    onSnapshot,
    query,
    orderBy,
    doc,
    deleteDoc,
    updateDoc,
    arrayUnion,
    arrayRemove,
    getDoc,
    writeBatch,
    addDoc,
    serverTimestamp,
    where
} from 'firebase/firestore';

export default {
    namespaced: true,

    state: () => ({
        posts: [], // タイムラインの投稿リスト
        postDetail: null, // 現在表示中の詳細投稿
        comments: [], // 現在表示中のコメントリスト
        userPosts: [], // プロフィール画面用のユーザーの投稿リスト
    }),

    mutations: {
        setPosts(state, posts) {
            state.posts = posts;
        },
        setPostDetail(state, post) {
            state.postDetail = post;
        },
        setComments(state, comments) {
            state.comments = comments;
        },
        setUserPosts(state, posts) {
            state.userPosts = posts;
        }
    },

    getters: {
        allPosts: (state) => state.posts,
        
        // 投稿詳細画面で使う、特定の投稿を取得するGetter
        getPostById: (state) => (id) => {
            if (state.postDetail && state.postDetail.id === id) {
                return state.postDetail;
            }
            // タイムラインのキャッシュからも探す
            return state.posts.find(post => post.id === id);
        },

        // 特定の投稿のコメントリストを取得するGetter
        getCommentsByPostId: (state) => (postId) => {
            return state.comments;
        },

        // プロフィール画面用のユーザーの投稿リスト
        userPosts: (state) => state.userPosts,
    },

    actions: {
        // --- 投稿一覧 (ホーム画面用) ---
        fetchPostsAction({ commit }) {
            const { $firestore } = useNuxtApp();

            if (!$firestore) return () => {};

            const postsCollection = collection($firestore, 'posts');
            const q = query(postsCollection, orderBy('createdAt', 'desc'));

            const unsubscribe = onSnapshot(q, (snapshot) => {
                const posts = snapshot.docs.map(doc => {
                    const data = doc.data();
                    const likes = data.likes || [];

                    return {
                        id: doc.id,
                        ...data,
                        likes: likes,
                        likeCount: likes.length,
                    };
                });
                commit('setPosts', posts);
            }, (error) => {
                console.error("Error fetching posts:", error);
            });
            return unsubscribe;
        },

        // --- プロフィール投稿一覧 (プロフィール画面用 - 次のステップで利用) ---
        fetchUserPostsAction({ commit, rootGetters }) {
            const { $firestore } = useNuxtApp();
            const currentUserId = rootGetters['auth/user']?.uid;

            if (!$firestore || !currentUserId) return () => {};

            const postsCollection = collection($firestore, 'posts');
            // ログインユーザーの投稿のみをフィルタリング
            const q = query(
                postsCollection,
                where('userId', '==', currentUserId),
                orderBy('createdAt', 'desc')
            );

            const unsubscribe = onSnapshot(q, (snapshot) => {
                const posts = snapshot.docs.map(doc => {
                    const data = doc.data();
                    const likes = data.likes || [];
                    return {
                        id: doc.id,
                        ...data,
                        likes: likes,
                        likeCount: likes.length,
                    };
                });
                commit('setUserPosts', posts);
            }, (error) => {
                console.error("Error fetching user posts:", error);
            });
            return unsubscribe;
        },


        // --- 新規投稿 (サイドバーフォーム用) ---
        async addPostAction(_, payload) { 
            // 💡 修正点: payload全体を受け取り、そこから message と user を取り出す
            const { message, user } = payload; 

            const { $firestore } = useNuxtApp();
            
            // この時点で user が undefined なら、前回のエラーログが出ます。
            if (!$firestore || !user) {
                console.error("POST_FAILED: User object was not found in action payload.");
                throw new Error("認証されていません。"); 
            }

            const newPost = {
                userId: user.uid,
                username: user.email.split('@')[0],
                email: user.email,
                message: message,
                createdAt: serverTimestamp(),
                likes: [],
            };

            await addDoc(collection($firestore, 'posts'), newPost);
        },


        // --- 投稿詳細 (詳細画面用) ---
        fetchPostDetailAction({ commit }, postId) {
            const { $firestore } = useNuxtApp();

            if (!$firestore || !postId) return () => {};

            const postRef = doc($firestore, 'posts', postId);

            const unsubscribe = onSnapshot(postRef, (docSnap) => {
                if (docSnap.exists()) {
                    const data = docSnap.data();
                    const likes = data.likes || [];
                    commit('setPostDetail', {
                        id: docSnap.id,
                        ...data,
                        likes: likes,
                        likeCount: likes.length,
                    });
                } else {
                    commit('setPostDetail', null);
                }
            }, (error) => {
                console.error("Error fetching post detail:", error);
            });
            return unsubscribe;
        },

        // --- コメント取得 (詳細画面用) ---
        fetchCommentsAction({ commit }, postId) {
            const { $firestore } = useNuxtApp();

            if (!$firestore || !postId) return () => {};

            const commentsCollection = collection($firestore, 'comments');
            const q = query(
                commentsCollection,
                where('postId', '==', postId),
                orderBy('createdAt', 'asc')
            );

            const unsubscribe = onSnapshot(q, (snapshot) => {
                const comments = snapshot.docs.map(doc => ({
                    id: doc.id,
                    ...doc.data()
                }));
                commit('setComments', comments);
            }, (error) => {
                console.error("Error fetching comments:", error);
            });
            return unsubscribe;
        },

        // --- コメント投稿 (詳細画面用) ---
        async addCommentAction({ rootGetters }, { postId, text }) {
            const { $firestore } = useNuxtApp();
            const user = rootGetters['auth/user'];

            if (!$firestore || !user) throw new Error("認証されていません。");

            const newComment = {
                postId: postId,
                userId: user.uid,
                username: user.email.split('@')[0],
                text: text,
                createdAt: serverTimestamp(),
            };

            await addDoc(collection($firestore, 'comments'), newComment);
        },

        // --- 削除 ---
        async deletePostAction({ rootGetters }, postId) {
            const { $firestore } = useNuxtApp();
            const currentUserId = rootGetters['auth/user']?.uid;

            if (!currentUserId) throw new Error("認証されていません。");

            const postRef = doc($firestore, 'posts', postId);
            const postSnap = await getDoc(postRef);

            if (!postSnap.exists() || postSnap.data().userId !== currentUserId) {
                throw new Error("投稿が見つからないか、他のユーザーの投稿です。");
            }

            await deleteDoc(postRef);
        },

        // --- いいね ---
        async likePostAction({ rootGetters }, postId) {
            const { $firestore } = useNuxtApp();
            const currentUserId = rootGetters['auth/user']?.uid;

            if (!currentUserId) throw new Error("ログインして「いいね」してください。");

            const postRef = doc($firestore, 'posts', postId);
            const postSnap = await getDoc(postRef);

            if (!postSnap.exists()) throw new Error("投稿が見つかりません。");

            const postData = postSnap.data();
            const likes = postData.likes || [];
            const isLiked = likes.includes(currentUserId);

            const batch = writeBatch($firestore);

            if (isLiked) {
                batch.update(postRef, { likes: arrayRemove(currentUserId) });
            } else {
                batch.update(postRef, { likes: arrayUnion(currentUserId) });
            }

            await batch.commit();
        },
    }
};
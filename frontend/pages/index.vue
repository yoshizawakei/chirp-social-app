<!-- frontend/pages/index.vue -->
<template>
    <div class="page-content">
        <h2 class="page-title">ホーム</h2>

        <div v-if="posts.length > 0" class="posts-container">
            <div
                v-for="post in posts"
                :key="post.id"
                class="post-item"
            >
                <div class="post-header">
                    <span class="post-username">@{{ post.username || '名無し' }}</span>
                    <span class="timestamp">{{ formatTime(post.createdAt) }}</span>
                </div>
                <p class="post-message">{{ post.message }}</p>

                <div class="post-actions">
                    <button class="action-btn" @click="goToDetail(post.id)">
                        <img :src="detailIcon" alt="コメント" class="action-icon icon-detail-img" />
                    </button>

                    <button
                        class="action-btn"
                        @click="likePost(post.id)"
                        :class="{ 'liked': post.likes.includes(currentUserId) }"
                    >
                        <img
                            :src="heartIcon"
                            alt="いいね"
                            class="action-icon icon-heart-img"
                        />
                        <span class="like-count">{{ post.likeCount || 0 }}</span>
                    </button>

                    <button
                        v-if="isPostOwner(post.userId)"
                        class="action-btn delete-btn"
                        @click="deletePost(post.id)"
                    >
                        <img
                            :src="crossIcon"
                            alt="削除"
                            class="action-icon icon-cross-img"
                        />
                    </button>
                </div>
            </div>
        </div>
        <div v-else class="empty-message">
            投稿はまだありません。サイドバーから最初の投稿をシェアしましょう！
        </div>
    </div>
</template>

<script setup>
import { computed, onMounted, onUnmounted } from 'vue';
import { useNuxtApp, navigateTo } from '#app';

definePageMeta({ middleware: 'auth' });

import heartIcon from '~/assets/images/heart.png';
import crossIcon from '~/assets/images/cross.png';
import detailIcon from '~/assets/images/detail.png';

const nuxtApp = useNuxtApp();
const store = nuxtApp.vueApp.config.globalProperties.$store;

const posts = computed(() => store.getters['posts/allPosts'] || []);
const currentUserId = computed(() => store.getters['auth/user']?.uid);

let unsubscribeListener = null;

onMounted(async () => {
    try {
        unsubscribeListener = await store.dispatch('posts/fetchPostsAction');
    } catch (e) {
        console.error('Failed to set up post listener:', e);
    }
});

onUnmounted(() => {
    if (unsubscribeListener) unsubscribeListener();
});

const formatTime = (ts) => {
    if (!ts) return '日時不明';

    try {
        let date;

        if (ts.toDate) {
        date = ts.toDate();          // Firestore Timestamp
        } else if (ts instanceof Date) {
        date = ts;                   // JS Date
        } else if (typeof ts === 'number') {
        date = new Date(ts);         // UNIX time
        } else {
        return '日時不明';
        }

        return date.toLocaleString('ja-JP', {
        year: 'numeric',
        month: '2-digit',
        day: '2-digit',
        hour: '2-digit',
        minute: '2-digit',
        });
    } catch (e) {
        return '日時不明';
    }
};

const isPostOwner = (postUserId) => postUserId === currentUserId.value;

const likePost = async (postId) => {
    await store.dispatch('posts/likePostAction', postId);
};

const deletePost = async (postId) => {
    if (confirm('本当にこの投稿を削除しますか？')) {
        await store.dispatch('posts/deletePostAction', postId);
    }
};

const goToDetail = (postId) => {
    navigateTo(`/post/${postId}`);
};
</script>


<style scoped>
.page-content {
    min-height: 100vh;
}
.page-title {
    font-size: 20px;
    font-weight: bold;
    color: white;
    padding: 15px 20px;
    border-bottom: 1px solid #38444d;
    text-align: left;
    position: sticky;
    top: 0;
    background-color: #15202b;
    z-index: 5;
}
.post-item {
    padding: 15px 20px;
    border-bottom: 1px solid #38444d;
    text-align: left;
    transition: background-color 0.1s;
}
.post-item:hover {
    background-color: #1a2a3a;
}
.post-header {
    display: flex;
    align-items: center;
    margin-bottom: 5px;
    font-size: 15px;
}
.post-username {
    font-weight: bold;
    color: white;
    margin-right: 10px;
}
.timestamp {
    color: #8899a6;
    font-size: 13px;
}
.post-message {
    color: white;
    font-size: 16px;
    margin-bottom: 10px;
    word-wrap: break-word;
}
.post-actions {
    display: flex;
    justify-content: space-between;
    align-items: center;
    max-width: 400px;
    margin-top: 10px;
}
.action-btn {
    display: flex;
    align-items: center;
    background: none;
    border: none;
    color: #8899a6;
    cursor: pointer;
    padding: 5px 0;
    transition: color 0.2s;
}
.action-btn:hover {
    color: #1da1f2;
}
.action-btn.liked {
    color: #e0245e; /* いいね済み */
}
.action-btn.liked:hover {
    color: #e0245e;
}
.action-icon {
    width: 20px;
    height: 20px;
    margin-right: 5px;
    filter: invert(50%) sepia(10%) saturate(100%) hue-rotate(180deg) brightness(100%) contrast(80%);
}
.action-btn.liked .icon-heart-img {
    filter: none;
}
.like-count {
    font-size: 13px;
    margin-left: 2px;
}
.delete-btn {
    color: #e74c3c;
}
.delete-btn:hover {
    color: #c0392b;
}
.empty-message {
    padding: 50px 20px;
    color: #8899a6;
    text-align: center;
    font-size: 16px;
}
</style>

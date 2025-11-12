<template>
    <!-- <NuxtLayout name="default"> -->
        <div class="page-content">
            <h2 class="page-title">ホーム</h2>

            <div v-if="posts.length > 0">
                <div
                    v-for="post in posts"
                    :key="post.id"
                    class="post-item"
                >
                    <div class="post-header">
                        <span class="post-username">@{{ post.username || post.email || '名無し' }}</span>
                        <span class="post-actions">
                            <span class="like-count">{{ post.likeCount || 0 }}</span>
                            <img 
                                :src="heartIcon" 
                                alt="いいね" 
                                class="action-icon icon-heart-img" 
                                @click="likePost(post.id)" 
                            />

                            <img 
                                :src="detailIcon" 
                                alt="詳細" 
                                class="action-icon icon-detail-img" 
                                @click="goToDetail(post.id)"
                            />
                            <img 
                                v-if="isPostOwner(post.userId)" 
                                :src="crossIcon" 
                                alt="削除" 
                                class="action-icon icon-cross-img" 
                                @click="deletePost(post.id)" 
                            />
                        </span>
                    </div>
                    <p class="post-message">{{ post.message }}</p>
                    <div class="post-footer">
                        <span class="timestamp">
                            {{ formatTime(post.createdAt) }}
                        </span>
                        <NuxtLink :to="`/post/${post.id}`" class="comment-link">コメントを見る</NuxtLink>
                    </div>
                </div>
            </div>
            <div v-else class="empty-message">
                投稿はまだありません。サイドバーから最初の投稿をシェアしましょう！
            </div>
        </div>
    <!-- </NuxtLayout> -->
</template>

<script setup>
import { computed, onMounted, onUnmounted } from 'vue';
import { useNuxtApp, navigateTo } from '#app'; // navigateTo をインポート

definePageMeta({
    middleware: 'auth'
})

// 💡 画像ファイルのインポート
import heartIcon from '~/assets/images/heart.png';
import crossIcon from '~/assets/images/cross.png';
import detailIcon from '~/assets/images/detail.png';

const nuxtApp = useNuxtApp();
const store = nuxtApp.vueApp.config.globalProperties.$store;

// 投稿一覧を取得 (TypeError 対策として || [] を適用済み)
const posts = computed(() => {
    return store.getters['posts/allPosts'] || [];
});

let unsubscribeListener = null;

// リアルタイムリスナーセットアップ
onMounted(() => {
    // onMounted は非同期関数を直接返せないため、戻り値を受け取る
    unsubscribeListener = store.dispatch('posts/fetchPostsAction');
});

// コンポーネント破棄時にリスナーを解除
onUnmounted(() => {
    if (unsubscribeListener) {
        unsubscribeListener();
    }
});

// タイムスタンプの表示を整形
const formatTime = (timestamp) => {
    if (!timestamp) return 'ロード中...';
    // Firebase Timestamp オブジェクトかどうかをチェック
    if (timestamp.toDate) {
        return timestamp.toDate().toLocaleString('ja-JP', {
            year: 'numeric', month: '2-digit', day: '2-digit',
            hour: '2-digit', minute: '2-digit'
        });
    }
    return '日付不明';
};

// ログインユーザーのIDを取得 (認証ストアから取得を想定)
const currentUserId = computed(() => store.getters['auth/user']?.uid);

/**
 * 投稿者が現在のログインユーザーであるかチェック
 * @param {string} postUserId 投稿を作成したユーザーID
 * @returns {boolean}
 */
const isPostOwner = (postUserId) => {
    return postUserId === currentUserId.value;
};

/**
 * 投稿にいいねを付けるアクションをディスパッチ
 * @param {string} postId 投稿ID
 */
const likePost = async (postId) => {
    try {
        await store.dispatch('posts/likePostAction', postId);
    } catch (e) {
        alert('いいねに失敗しました。');
        console.error('Like Post Error:', e);
    }
};

/**
 * 投稿を削除するアクションをディスパッチ
 * @param {string} postId 投稿ID
 */
const deletePost = async (postId) => {
    if (confirm('本当にこの投稿を削除しますか？')) {
        try {
            await store.dispatch('posts/deletePostAction', postId);
        } catch (e) {
            alert('投稿の削除に失敗しました。');
            console.error('Delete Post Error:', e);
        }
    }
};

/**
 * 投稿詳細画面へ遷移
 * @param {string} postId 投稿ID
 */
const goToDetail = (postId) => {
    navigateTo(`/post/${postId}`);
};

</script>

<style scoped>
.page-content {
    padding: 20px 0;
}

.page-title {
    font-size: 28px;
    color: white;
    margin-bottom: 30px;
    border-bottom: 1px solid #33334d;
    padding-bottom: 15px;
    text-align: left;
}

/* 投稿アイテムのスタイル */
.post-item {
    background-color: #24243e;
    border: 1px solid #33334d;
    padding: 20px;
    margin-bottom: 20px;
    border-radius: 6px;
    text-align: left;
}

.post-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 10px;
}

.post-username {
    font-weight: bold;
    color: white;
    font-size: 16px;
}

/* 💡 アクションアイコンのスタイル */
.post-actions .action-icon {
    width: 18px;
    height: 18px;
    margin-left: 15px;
    cursor: pointer;
    vertical-align: middle;
}

.post-message {
    font-size: 14px;
    color: #e4e4e4;
    margin-top: 5px;
}

.post-footer {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-top: 10px;
}

.timestamp {
    font-size: 12px;
    color: #aaa;
}

.comment-link {
    color: #6a40e7;
    font-size: 13px;
    display: inline-block;
}

.empty-message {
    padding: 40px;
    background-color: #24243e;
    border: 1px dashed #33334d;
    border-radius: 6px;
    color: #aaa;
    margin-top: 30px;
    text-align: center;
}
</style>
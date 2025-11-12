<template>
    <div class="page-content">
        <h2 class="page-title">コメント</h2>

        <div class="post-item original-post">
            <div class="post-header">
                <span class="post-username">test</span>
                <span class="post-actions">
                    <img :src="heartIcon" alt="いいね" class="action-icon icon-heart-img" />
                    <img :src="crossIcon" alt="削除" class="action-icon icon-cross-img" />
                    <img :src="detailIcon" alt="シェア" class="action-icon icon-detail-img" />
                </span>
            </div>
            <p class="post-message">test message</p>
        </div>

        <div class="comment-list">
            <div class="comment-item">
                <div class="comment-header">
                    <span class="comment-username">test</span>
                </div>
                <p class="comment-message">test comment</p>
            </div>
        </div>

        <div class="comment-input-area">
            <h3 class="comment-input-title">コメント</h3>
            <textarea
                v-model="newComment"
                placeholder="コメントを入力..."
                class="comment-input"
            ></textarea>
            <button class="comment-button" @click="postComment">コメント</button>
        </div>
    </div>
</template>

<script setup>
import { ref } from 'vue';

definePageMeta({
    middleware: 'auth'
});

// 💡 画像ファイルをすべてインポート
import heartIcon from '~/assets/images/heart.png';
import crossIcon from '~/assets/images/cross.png';
import detailIcon from '~/assets/images/detail.png';

const newComment = ref('');
const route = useRoute();
const postId = route.params.id;

const postComment = () => {
    if (newComment.value.trim() === '') {
        alert('コメントを入力してください。');
        return;
    }
    console.log(`投稿 ID: ${postId} にコメント: ${newComment.value}`);
    alert(`コメントを投稿しました: ${newComment.value}`);
    newComment.value = '';
}
</script>

<style scoped>
.page-content {
    padding: 20px 0;
    max-width: 800px;
}

.page-title {
    font-size: 28px;
    color: white;
    margin-bottom: 30px;
    border-bottom: 1px solid #33334d;
    padding-bottom: 15px;
    text-align: left;
}

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

/* コメントアイテムのスタイル */
.comment-list {
    margin-bottom: 30px;
}
.comment-item {
    background-color: #1a1a2e; 
    border-top: 1px solid #33334d;
    padding: 15px 0;
    text-align: left;
}
.comment-header {
    margin-bottom: 5px;
}
.comment-username {
    font-weight: bold;
    color: white;
    font-size: 14px;
}
.comment-message {
    font-size: 13px;
    color: #e4e4e4;
}

/* コメント入力エリアのスタイル */
.comment-input-area {
    margin-top: 20px;
    padding: 20px 0;
    border-top: 1px solid #33334d;
    position: relative;
    text-align: left;
}
.comment-input-title {
    font-size: 18px;
    color: white;
    margin-bottom: 15px;
    font-weight: 600;
}
.comment-input {
    width: 100%;
    min-height: 80px;
    background-color: #1a1a2e;
    border: 1px solid #33334d;
    color: #e4e4e4;
    padding: 10px;
    border-radius: 4px;
    resize: vertical;
    box-sizing: border-box;
    margin-bottom: 10px;
}
.comment-button {
    position: absolute;
    bottom: 20px;
    right: 0;
    padding: 8px 20px;
}
</style>
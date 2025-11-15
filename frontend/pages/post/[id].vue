<!-- frontend/pages/post/[id].vue -->
<template>
    <div class="page-content">
        <h2 class="page-title">投稿詳細</h2>

        <div v-if="postDetail" class="post-detail-container">
        <div class="post-item original-post">
            <div class="post-header">
            <span class="post-username">@{{ postDetail.username || "名無し" }}</span>
            <span class="timestamp">{{ formatTime(postDetail.createdAt) }}</span>
            </div>
            <p class="post-message">{{ postDetail.message }}</p>
        </div>

        <div class="comment-input-area">
            <textarea
            v-model="newComment"
            placeholder="コメントを入力... (120文字以内)"
            class="comment-input"
            :disabled="isPostingComment"
            maxlength="120"
            ></textarea>
            <button
            class="comment-button"
            @click="postComment"
            :disabled="!newComment.trim() || isPostingComment || newComment.length > 120"
            >
            {{ isPostingComment ? "投稿中..." : "コメント" }}
            </button>
        </div>

        <div class="comment-list">
            <div v-if="comments.length === 0" class="no-comments">
            まだコメントがありません。
            </div>

            <div
            v-for="comment in comments"
            :key="comment.id"
            class="comment-item"
            >
            <div class="comment-header">
                <span class="comment-username">@{{ comment.username }}</span>
                <span class="timestamp">{{ formatTime(comment.createdAt) }}</span>
            </div>
            <p class="comment-message">{{ comment.text }}</p>
            </div>
        </div>
        </div>

        <div v-else class="loading-message">
        {{ postDetail === null ? "投稿が見つかりません。" : "投稿を読み込み中..." }}
        </div>
    </div>
    </template>

    <script setup>
    import { ref, computed, onMounted, onUnmounted } from "vue";
    import { useRoute, useNuxtApp } from "#app";

    definePageMeta({
    middleware: "auth",
});

const nuxtApp = useNuxtApp();
const store = nuxtApp.vueApp.config.globalProperties.$store;
const route = useRoute();

const postId = route.params.id;
const newComment = ref("");
const isPostingComment = ref(false);

const postDetail = computed(() => store.getters["posts/postDetail"]);
const comments = computed(() => store.getters["posts/comments"] || []);

let unsubscribePostDetail = null;
let unsubscribeComments = null;

onMounted(() => {
    if (postId) {
        unsubscribePostDetail = store.dispatch(
        "posts/fetchPostDetailAction",
        postId
        );
        unsubscribeComments = store.dispatch("posts/fetchCommentsAction", postId);
    }
});

onUnmounted(() => {
    if (unsubscribePostDetail) unsubscribePostDetail();
    if (unsubscribeComments) unsubscribeComments();
    store.commit("posts/setPostDetail", null);
    store.commit("posts/setComments", []);
    });

const formatTime = (ts) => {
    if (!ts) return "日時不明";

    try {
        let date;

        if (ts.toDate) {
        date = ts.toDate();
        } else if (ts instanceof Date) {
        date = ts;
        } else if (typeof ts === "number") {
        date = new Date(ts);
        } else {
        return "日時不明";
        }

        return date.toLocaleString("ja-JP", {
        year: "numeric",
        month: "2-digit",
        day: "2-digit",
        hour: "2-digit",
        minute: "2-digit",
        });
    } catch {
        return "日時不明";
    }
};

const postComment = async () => {
    const text = newComment.value.trim();

    if (!text || text.length > 120) {
        alert("コメントを入力するか、文字数を調整してください (120文字以内)。");
        return;
    }

    isPostingComment.value = true;

    try {
        await store.dispatch("posts/addCommentAction", {
        postId,
        text,
        });
        newComment.value = "";
    } catch (e) {
        console.error("Comment Post Error:", e);
        alert("コメントの投稿に失敗しました。再度ログインしてください。");
    } finally {
        isPostingComment.value = false;
    }
};
</script>


<style scoped>
.page-content {
    padding: 0;
    min-height: 100vh;
}
.page-title {
    font-size: 20px;
    font-weight: bold;
    color: white;
    padding: 15px 20px;
    border-bottom: 1px solid #38444d;
    text-align: left;
    background-color: #15202b;
}
.post-item {
    padding: 15px 20px;
    border-bottom: 1px solid #38444d;
    text-align: left;
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
.comment-input-area {
    padding: 15px 20px;
    border-bottom: 1px solid #38444d;
}
.comment-input {
    width: 100%;
    min-height: 50px;
    background-color: #15202b;
    border: none;
    color: white;
    padding: 10px 0;
    resize: none;
    box-sizing: border-box;
    font-size: 16px;
    border-bottom: 1px solid #38444d;
}
.comment-button {
    float: right;
    margin-top: 10px;
    padding: 8px 15px;
    background-color: #1da1f2;
    color: white;
    border: none;
    border-radius: 9999px;
    cursor: pointer;
    font-weight: bold;
    font-size: 15px;
}
.comment-button:disabled {
    background-color: #444;
    opacity: 0.5;
}
.comment-list {
    clear: both;
    padding-top: 10px;
}
.comment-item {
    padding: 15px 20px;
    border-bottom: 1px solid #38444d;
    text-align: left;
}
.comment-item:hover {
    background-color: #1a2a3a;
}
.no-comments,
.loading-message {
    padding: 30px 20px;
    color: #8899a6;
    text-align: center;
}
</style>

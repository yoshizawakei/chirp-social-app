<!-- frontend/pages/post/[id].vue -->
<template>
  <div class="page-content">
    <h2 class="page-title">
      投稿詳細
      <span v-if="comments.length">（コメント {{ comments.length }}件）</span>
    </h2>

    <div v-if="postDetail" class="post-detail-container">
      <!-- 元の投稿 -->
      <div class="post-item original-post">
        <div class="post-header">
          <span class="post-username">@{{ postDetail.username || "名無し" }}</span>
          <span class="timestamp">{{ formatTime(postDetail.createdAt) }}</span>
        </div>
        <p class="post-message">{{ postDetail.message }}</p>
      </div>

      <!-- コメント入力 -->
      <div class="comment-input-area">
        <textarea
          v-model="newComment"
          placeholder="コメントを入力...（120文字以内）"
          class="comment-input"
          :disabled="isPostingComment"
          maxlength="120"
        ></textarea>
        <div class="comment-footer">
          <span class="comment-char-count">{{ newComment.length }} / 120</span>
          <button
            class="comment-button"
            @click="postComment"
            :disabled="!newComment.trim() || isPostingComment || newComment.length > 120"
          >
            {{ isPostingComment ? "投稿中..." : "コメント" }}
          </button>
        </div>
      </div>

      <!-- コメント一覧 -->
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
console.log("🔥 [id].vue is loaded correctly");

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

onMounted(async () => {
  if (postId) {
    await store.dispatch("posts/fetchPostDetailAction", postId);
    await store.dispatch("posts/fetchCommentsAction", postId);
  }
});

onUnmounted(() => {
  store.commit("posts/setPostDetail", null);
  store.commit("posts/setComments", []);
});

const formatTime = (ts) => {
  if (!ts) return "日時不明";

  try {
    let date;

    if (ts.toDate) {
      date = ts.toDate(); // Firestore Timestamp（互換用）
    } else if (ts instanceof Date) {
      date = ts;
    } else if (typeof ts === "number") {
      date = new Date(ts);
    } else if (typeof ts === "string") {
      date = new Date(ts); // Laravel の createdAt
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

    // ✅ コメント投稿後に入力欄をクリア
    newComment.value = "";

    // ✅ 最新のコメント一覧を再取得して反映
    await store.dispatch("posts/fetchCommentsAction", postId);
    // ✅ 投稿詳細側のコメント数も最新化したいなら一覧再取得も可
    await store.dispatch("posts/fetchPostDetailAction", postId);
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
.page-title span {
  font-size: 14px;
  color: #8899a6;
  margin-left: 10px;
}

/* 元投稿 */
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

/* コメント入力 */
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
.comment-footer {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-top: 8px;
}
.comment-char-count {
  font-size: 12px;
  color: #8899a6;
}
.comment-button {
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

/* コメントリスト */
.comment-list {
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
.comment-header {
  display: flex;
  align-items: center;
  margin-bottom: 5px;
}
.comment-username {
  font-weight: bold;
  color: white;
  margin-right: 10px;
}
.comment-message {
  font-size: 15px;
  color: white;
}
.no-comments,
.loading-message {
  padding: 30px 20px;
  color: #8899a6;
  text-align: center;
}
</style>

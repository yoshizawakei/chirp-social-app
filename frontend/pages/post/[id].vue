<template>
  <div class="max-w-xl mx-auto py-6">

    <!-- 戻るボタン -->
    <NuxtLink to="/" class="text-blue-500">&lt; 戻る</NuxtLink>

    <!-- 投稿本体 -->
    <div class="border-b mt-4 pb-4">
      <div class="flex justify-between mb-1">
        <span class="font-bold">{{ post?.username }}</span>
        <span class="text-sm text-gray-500">
          {{ formatDate(post?.createdAt) }}
        </span>
      </div>

      <p class="whitespace-pre-wrap mb-2">{{ post?.message }}</p>

      <div class="text-sm text-gray-600 flex gap-4">
        ❤️ {{ post?.likeCount }}
        💬 {{ comments.length }}
      </div>
    </div>

    <!-- コメント一覧 -->
    <h2 class="text-xl font-bold mt-6 mb-4">コメント</h2>

    <div v-if="loadingComments" class="text-center py-4">
      読み込み中...
    </div>

    <div v-else>
      <div
        v-for="c in comments"
        :key="c.id"
        class="border-b py-3"
      >
        <div class="flex justify-between mb-1">
          <span class="font-bold">{{ c.username }}</span>
          <span class="text-sm text-gray-500">
            {{ formatDate(c.createdAt) }}
          </span>
        </div>
        <p class="whitespace-pre-wrap">{{ c.text }}</p>
      </div>
    </div>

    <!-- コメント投稿フォーム -->
    <form @submit.prevent="submitComment" class="mt-6 flex gap-2">
      <input
        v-model="commentText"
        type="text"
        placeholder="コメントを書く..."
        class="flex-1 border px-3 py-2 rounded"
      />
      <button class="bg-blue-500 text-white px-4 py-2 rounded">
        投稿
      </button>
    </form>

  </div>
</template>

<script setup>
import { useAuth } from "~/composables/useAuth";

const route = useRoute();
const config = useRuntimeConfig().public;

const post = ref(null);
const comments = ref([]);
const loadingComments = ref(true);
const commentText = ref("");

const { user, init } = useAuth();

// -------------------------------------------
// 初期化
// -------------------------------------------
onMounted(async () => {
  await init();
  await fetchPost();
  await fetchComments();
});

// -------------------------------------------
// 投稿詳細
// -------------------------------------------
const fetchPost = async () => {
  post.value = await $fetch(`${config.API_URL}/posts/${route.params.id}`);
};

// -------------------------------------------
// コメント一覧
// -------------------------------------------
const fetchComments = async () => {
  loadingComments.value = true;

  comments.value = await $fetch(
    `${config.API_URL}/posts/${route.params.id}/comments`
  );

  loadingComments.value = false;
};

// -------------------------------------------
// コメント投稿
// -------------------------------------------
const submitComment = async () => {
  if (!commentText.value.trim()) return;

  const token = await user.value.getIdToken();

  await $fetch(`${config.API_URL}/posts/${route.params.id}/comments`, {
    method: "POST",
    body: {
      text: commentText.value,
    },
    headers: {
      Authorization: `Bearer ${token}`,
    },
  });

  commentText.value = "";
  await fetchComments();
};

// -------------------------------------------
// 日付整形
// -------------------------------------------
const formatDate = (dateStr) => {
  if (!dateStr) return "";
  const d = new Date(dateStr);
  return `${d.getFullYear()}/${d.getMonth() + 1}/${d.getDate()}`;
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

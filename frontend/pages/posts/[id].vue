<template>
  <div class="detail-container text-white">

    <!-- タイトル -->
    <h1 class="page-title">コメント</h1>

    <!-- 投稿ブロック -->
    <div class="post-block">

      <div class="post-header">
        <!-- 投稿者名 -->
        <div class="post-username">{{ post?.username }}</div>

        <!-- アイコン群 -->
        <div class="icon-group">

          <!-- いいね -->
          <button @click="toggleLike" class="icon-btn">
            <img
              :src="post?.liked ? '/assets/images/heart_red.png' : '/assets/images/heart.png'"
              class="icon-img"
            />
            <span>{{ post?.likeCount }}</span>
          </button>

          <!-- 削除 -->
          <button
            v-if="post?.userId === currentUserUid"
            @click="deletePost"
            class="icon-btn"
          >
            <img src="/assets/images/cross.png" class="icon-img" />
          </button>

        </div>
      </div>

      <!-- 投稿本文 -->
      <div class="post-message">{{ post?.message }}</div>

    </div>

    <!-- コメントタイトル -->
    <div class="comment-title">コメント</div>

    <!-- コメント一覧 -->
    <div v-for="c in comments" :key="c.id" class="comment-item">
      <div class="comment-user">{{ c.username }}</div>
      <div class="comment-text">{{ c.text }}</div>
    </div>

    <!-- コメント投稿フォーム -->
    <form @submit.prevent="submitComment" class="comment-form">

      <input
        v-model="commentText"
        type="text"
        placeholder="コメントを書く..."
        class="comment-input"
      />

      <button class="comment-btn">コメント</button>
    </form>

  </div>
</template>

<script setup>
import { useAuth } from "~/composables/useAuth";

const route = useRoute();
const config = useRuntimeConfig().public;

const { user, init } = useAuth();

const post = ref(null);
const comments = ref([]);
const commentText = ref("");

onMounted(async () => {
  await init();
  await fetchPost();
  await fetchComments();
});

const currentUserUid = computed(() => user.value?.uid ?? null);

const fetchPost = async () => {
  const data = await $fetch(`${config.API_URL}/posts/${route.params.id}`);

  post.value = {
    ...data,
    liked: data.likes.includes(currentUserUid.value),
  };
};

const fetchComments = async () => {
  comments.value = await $fetch(
    `${config.API_URL}/posts/${route.params.id}/comments`
  );
};

const toggleLike = async () => {
  const token = await user.value.getIdToken();

  const res = await $fetch(`${config.API_URL}/posts/${route.params.id}/like`, {
    method: "POST",
    headers: { Authorization: `Bearer ${token}` },
    body: { userId: user.value.uid },
  });

  post.value.liked = res.liked;
  post.value.likeCount = res.likeCount;
};

const submitComment = async () => {
  if (!commentText.value.trim()) return;

  const token = await user.value.getIdToken();

  await $fetch(`${config.API_URL}/posts/${route.params.id}/comments`, {
    method: "POST",
    headers: { Authorization: `Bearer ${token}` },
    body: {
      userId: user.value.uid,
      username: user.value.displayName || "名無し",
      text: commentText.value,
    },
  });

  commentText.value = "";
  await fetchComments();
};

const deletePost = async () => {
  const token = await user.value.getIdToken();

  await $fetch(`${config.API_URL}/posts/${route.params.id}`, {
    method: "DELETE",
    headers: { Authorization: `Bearer ${token}` },
    body: { userId: user.value.uid },
  });

  navigateTo("/");
};
</script>

<style scoped>
.detail-container {
  width: 100%;
  padding: 20px 30px;
  background: #0f1923;
}

/* ページタイトル */
.page-title {
  font-size: 22px;
  font-weight: bold;
  margin-bottom: 10px;
}

/* 投稿ブロック */
.post-block {
  border-bottom: 1px solid #555;
  padding-bottom: 10px;
  margin-bottom: 20px;
}

.post-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.post-username {
  font-size: 18px;
  font-weight: bold;
}

.icon-group {
  display: flex;
  align-items: center;
  gap: 10px;
}

.icon-btn {
  background: none;
  border: none;
  color: white;
  cursor: pointer;
  display: flex;
  align-items: center;
  gap: 4px;
}

.icon-img {
  width: 22px;
  height: 22px;
}

.post-message {
  margin-top: 5px;
  font-size: 16px;
}

/* コメントタイトル */
.comment-title {
  border-top: 1px solid #555;
  border-bottom: 1px solid #555;
  padding: 6px;
  text-align: center;
  font-size: 16px;
  margin-bottom: 10px;
}

/* コメント一覧 */
.comment-item {
  padding: 10px 0;
  border-bottom: 1px solid #555;
}

.comment-user {
  font-weight: bold;
  margin-bottom: 3px;
}

.comment-text {
  color: #ddd;
}

/* コメント入力フォーム */
.comment-form {
  margin-top: 20px;
  display: flex;
  gap: 10px;
  align-items: center;
}

.comment-input {
  flex: 1;
  padding: 10px 14px;
  border-radius: 8px;
  border: 1px solid #777;
  background: white;
  color: black;
}

.comment-btn {
  background: #6d28d9;
  padding: 10px 16px;
  border-radius: 20px;
  border: none;
  color: white;
  cursor: pointer;
}
.comment-btn:hover {
  opacity: 0.9;
}
</style>


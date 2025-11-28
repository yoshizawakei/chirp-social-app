<template>
  <div class="detail-container text-white">

    <h1 class="page-title">コメント</h1>

    <!-- 投稿ブロック -->
    <div class="post-block">
      <div class="post-header">
        <div class="post-username">{{ post?.username }}</div>

        <div class="icon-group">
          <!-- いいね -->
          <button @click="toggleLike" class="icon-btn">
            <img src="/assets/images/heart.png" class="icon-img" />
            <span>{{ post?.likeCount }}</span>
          </button>

          <!-- 投稿削除 -->
          <button
            v-if="post?.userId === currentUserUid"
            @click="openDeletePostModal"
            class="icon-btn"
          >
            <img src="/assets/images/cross.png" class="icon-img" />
          </button>
        </div>
      </div>

      <div class="post-message">{{ post?.message }}</div>
    </div>

    <!-- コメントタイトル -->
    <div class="comment-title">コメント</div>

    <!-- コメント一覧 -->
    <div
      v-for="c in comments"
      :key="c.id"
      class="comment-item"
    >
      <div class="comment-header">
        <div class="comment-user">{{ c.username }}</div>

        <!-- 自分のコメントだけ編集/削除表示 -->
        <div v-if="c.userId === currentUserUid" class="comment-icons">

          <!-- 編集 -->
          <button
            class="icon-btn"
            @click="openEditModal(c)"
          >
            <img src="/assets/images/detail.png" class="icon-img" />
          </button>

          <!-- 削除 -->
          <button
            class="icon-btn"
            @click="openDeleteCommentModal(c)"
          >
            <img src="/assets/images/cross.png" class="icon-img" />
          </button>

        </div>
      </div>

      <div class="comment-text">{{ c.text }}</div>
    </div>

    <!-- コメント投稿 -->
    <form @submit.prevent="submitComment" class="comment-form">
      <input
        v-model="commentText"
        type="text"
        placeholder="コメントを書く..."
        class="comment-input"
      />
      <button class="comment-btn">コメント</button>
    </form>

    <!-- 編集モーダル -->
    <ConfirmModal
      :visible="showEditModal"
      message="コメントを編集しますか？"
      inputLabel="新しい内容"
      v-model="editText"
      @confirm="confirmEdit"
      @cancel="cancelEdit"
      type="edit"
    />

    <!-- コメント削除モーダル -->
    <ConfirmModal
      :visible="showDeleteCommentModal"
      message="このコメントを削除しますか？"
      @confirm="confirmDeleteComment"
      @cancel="showDeleteCommentModal = false"
    />

    <!-- 投稿削除モーダル -->
    <ConfirmModal
      :visible="showDeletePostModal"
      message="この投稿を削除しますか？"
      @confirm="confirmDeletePost"
      @cancel="showDeletePostModal = false"
    />

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

const currentUserUid = computed(() => user.value?.uid ?? null);

// 編集関連
const showEditModal = ref(false);
const editTarget = ref(null);
const editText = ref("");

// コメント削除関連
const showDeleteCommentModal = ref(false);
const deleteTargetComment = ref(null);

// 投稿削除関連
const showDeletePostModal = ref(false);

onMounted(async () => {
  await init();
  await fetchPost();
  await fetchComments();
});

// 投稿詳細取得
const fetchPost = async () => {
  const data = await $fetch(`${config.API_URL}/posts/${route.params.id}`);
  post.value = {
    ...data,
    liked: data.likes.includes(currentUserUid.value),
  };
};

// コメント一覧取得
const fetchComments = async () => {
  comments.value = await $fetch(
    `${config.API_URL}/posts/${route.params.id}/comments`
  );
};

// いいね
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

// コメント投稿
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

// ▼コメント編集関連
const openEditModal = (comment) => {
  editTarget.value = comment;
  editText.value = comment.text;
  showEditModal.value = true;
};

const confirmEdit = async () => {
  if (!editText.value.trim()) return;

  const token = await user.value.getIdToken();

  await $fetch(
    `${config.API_URL}/posts/${route.params.id}/comments/${editTarget.value.id}`,
    {
      method: "PUT",
      headers: { Authorization: `Bearer ${token}` },
      body: {
        userId: user.value.uid,
        text: editText.value,
      },
    }
  );

  showEditModal.value = false;
  await fetchComments();
};

const cancelEdit = () => {
  showEditModal.value = false;
};

// ▼コメント削除関連
const openDeleteCommentModal = (comment) => {
  deleteTargetComment.value = comment;
  showDeleteCommentModal.value = true;
};

const confirmDeleteComment = async () => {
  const token = await user.value.getIdToken();

  await $fetch(
    `${config.API_URL}/posts/${route.params.id}/comments/${deleteTargetComment.value.id}`,
    {
      method: "DELETE",
      headers: { Authorization: `Bearer ${token}` },
      body: { userId: user.value.uid },
    }
  );

  showDeleteCommentModal.value = false;
  await fetchComments();
};

// ▼投稿削除関連
const openDeletePostModal = () => {
  showDeletePostModal.value = true;
};

const confirmDeletePost = async () => {
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
.page-title {
  font-size: 22px;
  font-weight: bold;
  margin-bottom: 10px;
}

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

.comment-title {
  border-top: 1px solid #555;
  border-bottom: 1px solid #555;
  padding: 6px;
  font-size: 16px;
  margin-bottom: 10px;
}

.comment-item {
  padding: 10px 0;
  border-bottom: 1px solid #555;
}

.comment-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.comment-icons {
  display: flex;
  gap: 10px;
}

.comment-user {
  font-weight: bold;
  margin-bottom: 3px;
}

.comment-text {
  color: #ddd;
}

.comment-form {
  margin-top: 20px;
  display: flex;
  gap: 10px;
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

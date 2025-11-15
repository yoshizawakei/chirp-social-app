<!-- frontend/components/TweetItem.vue -->
<template>
  <div class="tweet-item">
    <div class="tweet-header">
      <span class="user-name">{{ tweet.user_name }}</span>
      <span class="tweet-date">{{ formatDate(tweet.created_at) }}</span>
    </div>

    <p class="tweet-content" @click="goToDetail">
      {{ tweet.content }}
    </p>

    <div class="tweet-actions">
      <button 
        @click="toggleLike" 
        :class="['like-button', { 'liked': isLiked }]"
        :disabled="!loggedInUserId"
      >
        <span class="icon">♥</span>
        {{ tweet.likes_count || 0 }}
      </button>

      <button @click="goToDetail" class="comment-button">
        <span class="icon">💬</span>
        {{ tweet.comments_count || 0 }}
      </button>

      <button v-if="isMyTweet" @click="deleteTweet" class="delete-button">
        <span class="icon">🗑️</span>
        削除
      </button>
    </div>
  </div>
</template>

<script setup>
import { useNuxtApp } from "#app";

const API_BASE_URL = 'http://localhost:8000/api';

const props = defineProps({
  tweet: { type: Object, required: true },
  isDetail: { type: Boolean, default: false }
});

const emit = defineEmits(['tweet-deleted', 'like-toggled']);

const nuxtApp = useNuxtApp();

const loggedInUserId = computed(() =>
  nuxtApp.$store.getters['auth/userId']
);

const isMyTweet = computed(() =>
  props.tweet.user_id === loggedInUserId.value
);

const isLiked = computed(() =>
  props.tweet.likes?.some(like => like.user_id === loggedInUserId.value)
);

const goToDetail = () => {
  if (!props.isDetail) {
    navigateTo(`/tweets/${props.tweet.id}`);
  }
};

const formatDate = (dateString) => {
  return new Date(dateString).toLocaleDateString();
};

// 投稿の削除
const deleteTweet = async () => {
  if (!isMyTweet.value) return;
  if (!confirm("この投稿を削除しますか？")) return;

  try {
    await nuxtApp.$fetch(`/tweets/${props.tweet.id}`, {
      method: "DELETE",
      baseURL: API_BASE_URL,
    });

    emit("tweet-deleted");
  } catch (e) {
    console.error("投稿削除エラー:", e);
    alert("投稿の削除に失敗しました。");
  }
};

// いいね追加・削除
const toggleLike = async () => {
  if (!loggedInUserId.value) {
    alert("いいねするにはログインが必要です。");
    return;
  }

  try {
    const method = isLiked.value ? "DELETE" : "POST";

    await nuxtApp.$fetch(`/tweets/${props.tweet.id}/like`, {
      method,
      baseURL: API_BASE_URL,
      body: { user_id: loggedInUserId.value },
    });

    emit("like-toggled");
  } catch (e) {
    console.error("いいね処理エラー:", e);
    alert("いいね処理に失敗しました。");
  }
};
</script>

<style scoped>
/* スタイル省略 */
</style>

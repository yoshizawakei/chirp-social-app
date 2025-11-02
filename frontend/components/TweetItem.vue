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

<script>
const API_BASE_URL = 'http://localhost:8000/api';

export default {
  props: {
    tweet: { type: Object, required: true, },
    isDetail: { type: Boolean, default: false, }
  },
  computed: {
    loggedInUserId() { return this.$store.getters['auth/userId']; }, 
    isMyTweet() { return this.tweet.user_id === this.loggedInUserId },
    isLiked() { return this.tweet.likes && this.tweet.likes.some(like => like.user_id === this.loggedInUserId); }
  },
  methods: {
    goToDetail() { if (!this.isDetail) { this.$router.push(`/tweets/${this.tweet.id}`) } },
    formatDate(dateString) { return new Date(dateString).toLocaleDateString() },

    // 投稿の削除 (DELETE)
    async deleteTweet() {
      if (!this.isMyTweet || !confirm('この投稿を削除しますか？')) { return; }

      try {
        await $fetch(`/tweets/${this.tweet.id}`, {
            method: 'DELETE',
            baseURL: API_BASE_URL,
        });

        this.$emit('tweet-deleted');
      } catch (e) {
        console.error('投稿削除エラー:', e);
        alert('投稿の削除に失敗しました。');
      }
    },

    // いいねの追加・削除 (POST/DELETE)
    async toggleLike() {
        if (!this.loggedInUserId) { alert('いいねするにはログインが必要です。'); return; }
        
        try {
            const method = this.isLiked ? 'DELETE' : 'POST';
            const endpoint = `/tweets/${this.tweet.id}/like`;
            
            await $fetch(endpoint, {
                method: method,
                body: { user_id: this.loggedInUserId }, 
                baseURL: API_BASE_URL,
            });
            
            this.$emit('like-toggled');
        } catch (e) {
             console.error('いいね処理エラー:', e);
             alert('いいね処理に失敗しました。');
        }
    },
  },
}
</script>

<style scoped>
/* スタイルは前回の提案を参照 */
</style>
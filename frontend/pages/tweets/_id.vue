<template>
  <div class="comment-page">
    <h1 class="page-title">コメント</h1>
    
    <div v-if="tweet" class="main-tweet">
      <TweetItem :tweet="tweet" :is-detail="true" @like-toggled="fetchTweetDetail" />
      <div class="divider"></div>
    </div>
    <p v-else>投稿が見つかりません。</p>

    <div v-if="$store.getters['auth/isLoggedIn']" class="comment-form-area">
      <textarea v-model="newCommentContent" placeholder="コメント" rows="3" class="comment-textarea"></textarea>
      <button @click="postComment" class="comment-button" :disabled="!newCommentContent.trim() || newCommentContent.length > 120">
        コメント
      </button>
    </div>
    <p v-else class="login-prompt">コメントするにはログインしてください。</p>

    <div class="comment-list-header">コメント</div>
    <div v-if="comments.length" class="comments-list">
      <CommentItem v-for="comment in comments" :key="comment.id" :comment="comment" />
    </div>
    <p v-else class="no-comments">まだコメントはありません。</p>
  </div>
</template>

<script>
import TweetItem from '@/components/TweetItem.vue'
import CommentItem from '@/components/CommentItem.vue' // 💡 CommentItemは別途実装が必要です

const API_BASE_URL = 'http://localhost:8000/api';

export default {
  components: { TweetItem, CommentItem },
  data() {
    return {
      tweet: null,
      comments: [],
      newCommentContent: '',
    }
  },
  computed: {
    loggedInUser() {
        return {
            id: this.$store.getters['auth/userId'],
            name: this.$store.getters['auth/userName'],
        };
    }
  },
  mounted() {
    this.fetchTweetDetail()
  },
  methods: {
    // 投稿詳細とコメント一覧の取得 (GET)
    async fetchTweetDetail() {
      const tweetId = this.$route.params.id
      if (!tweetId) return

      try {
        const response = await $fetch(`/tweets/${tweetId}/comments`, {
            baseURL: API_BASE_URL,
        })
        this.tweet = response.tweet 
        this.comments = response.comments
      } catch (e) {
        console.error('投稿詳細の取得に失敗しました:', e)
        this.tweet = null 
      }
    },

    // コメントの追加 (POST)
    async postComment() {
      const tweetId = this.$route.params.id

      if (!this.loggedInUser.id) {
          alert('コメントするにはログインが必要です。')
          this.$router.push('/login');
          return
      }
      if (!this.newCommentContent.trim() || this.newCommentContent.length > 120) {
        alert('コメントは1文字以上120文字以内で入力してください。')
        return
      }
      
      try {
        const payload = {
          content: this.newCommentContent,
          user_id: this.loggedInUser.id,
          user_name: this.loggedInUser.name,
        }
        
        await $fetch(`/tweets/${tweetId}/comments`, {
            method: 'POST', 
            body: payload,
            baseURL: API_BASE_URL,
        });
        
        this.newCommentContent = '' 
        await this.fetchTweetDetail()
      } catch (e) {
        alert('コメントの追加に失敗しました。')
        console.error('コメント処理エラー:', e)
      }
    },
  },
}
</script>

<style scoped>
/* スタイルは前回の提案を参照 */
</style>
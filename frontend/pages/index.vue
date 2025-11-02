<template>
  <div class="home-page">
    <div v-if="!isAuthenticated" class="welcome-message">
        <h1>Welcome to SHARE!</h1>
        <p>何気ないことをつぶやけるSNSアプリです。</p>
        <NuxtLink to="/register" class="start-button">さっそく新規登録する</NuxtLink>
    </div>

    <div v-else class="authenticated-content">
        <h1>ホーム</h1>
        
        <div class="post-form-area">
          <textarea 
            v-model="newTweetContent" 
            placeholder="今、何してる？"
            rows="4"
            class="tweet-textarea"
          ></textarea>
          <button @click="postTweet" class="share-button" :disabled="!newTweetContent.trim() || newTweetContent.length > 120">
            シェアする
          </button>
        </div>

        <hr class="divider">

        <div v-if="tweets.length" class="tweets-list">
          <TweetItem 
            v-for="tweet in tweets" 
            :key="tweet.id" 
            :tweet="tweet"
            @tweet-deleted="fetchTweets" 
            @like-toggled="fetchTweets"
          />
        </div>
        <p v-else class="no-tweets">投稿はまだありません。</p>
    </div>
  </div>
</template>

<script>
import TweetItem from '@/components/TweetItem.vue' 

const API_BASE_URL = 'http://localhost:8000/api';

export default {
  components: { TweetItem },
  data() {
    return {
      tweets: [],
      newTweetContent: '',
    }
  },
  computed: {
    isAuthenticated() {
        return this.$store.getters['auth/isLoggedIn'];
    },
    loggedInUser() {
        return {
            id: this.$store.getters['auth/userId'],
            name: this.$store.getters['auth/userName'],
        };
    }
  },
  mounted() {
    if (this.isAuthenticated) {
        this.fetchTweets()
    }
  },
  methods: {
    // 投稿一覧の取得 (GET)
    async fetchTweets() {
      try {
        const response = await $fetch('/tweets', {
            baseURL: API_BASE_URL,
        });
        this.tweets = response.tweets;
      } catch (e) {
        console.error('投稿の取得に失敗しました:', e);
      }
    },

    // 新しい投稿の追加 (POST)
    async postTweet() {
      if (!this.isAuthenticated || !this.loggedInUser.id) {
          alert('投稿するにはログインが必要です。')
          this.$router.push('/login');
          return
      }
      if (!this.newTweetContent.trim() || this.newTweetContent.length > 120) {
        alert('投稿内容は1文字以上120文字以内で入力してください。')
        return
      }

      try {
        const payload = {
          content: this.newTweetContent,
          user_id: this.loggedInUser.id,
          user_name: this.loggedInUser.name,
        }
        
        await $fetch('/tweets', { 
            method: 'POST', 
            body: payload,
            baseURL: API_BASE_URL,
        });
        
        this.newTweetContent = ''
        await this.fetchTweets()
      } catch (e) {
        alert('投稿に失敗しました。')
        console.error('投稿処理エラー:', e)
      }
    },
  },
}
</script>

<style scoped>
/* スタイルは前回の提案を参照 */
</style>
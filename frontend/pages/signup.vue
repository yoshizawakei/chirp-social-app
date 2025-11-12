<template>
  <NuxtLayout name="auth">
    <div class="form-container">
      <div class="auth-box">
        <h2>新規登録</h2>
        <form @submit.prevent="registerUser">
          <input
            v-model="name"
            type="text"
            placeholder="ユーザーネーム"
            required
            class="input-field"
          />
          <input
            v-model="email"
            type="email"
            placeholder="メールアドレス"
            required
            class="input-field"
          />
          <input
            v-model="password"
            type="password"
            placeholder="パスワード"
            required
            class="input-field"
          />
          <p v-if="error" class="error-message">{{ error }}</p>
          <button type="submit" class="auth-button">新規登録</button>
        </form>
        <NuxtLink to="/login" class="link-text">ログインはこちら</NuxtLink>
      </div>
    </div>
  </NuxtLayout>
</template>

<script setup>
import { ref } from 'vue'
import { useRouter, useNuxtApp, navigateTo } from '#app' 

definePageMeta({
  layout: 'auth', // 💡 作成済みの auth.vue レイアウトを適用
})

const nuxtApp = useNuxtApp()
const router = useRouter()

const name = ref('')
const email = ref('')
const password = ref('')
// 💡 修正: 変数名を error に統一
const error = ref(null)

const registerUser = async () => {
  const store = nuxtApp.vueApp.config.globalProperties.$store
  
  if (!store) {
    error.value = 'アプリケーションの初期化に失敗しました。'
    console.error('Vuex store is not initialized.')
    return
  }

  error.value = null

  try {
    // 💡 修正: Vuex アクション名を 'signUpAction' に修正
    await store.dispatch('auth/signUpAction', { 
        email: email.value, 
        password: password.value,
        name: name.value
    })
    
    // 登録成功後、ログイン画面へリダイレクト
    await navigateTo('/login') 

  } catch (e) {
    error.value = 'ユーザー登録に失敗しました: ' + e.message
    console.error('ユーザー登録エラー:', e)
  }
}
</script>

<style scoped>
/* スタイルは全て含める必要があります */
.form-container {
  display: flex;
  justify-content: center;
  align-items: center;
  min-height: 100vh;
  padding-top: 80px;
}
.auth-box {
  background: white;
  padding: 40px;
  border-radius: 8px;
  width: 100%;
  max-width: 380px;
  text-align: center;
  color: #333;
  box-shadow: 0 0 20px rgba(0, 0, 0, 0.1);
}
h2 {
  font-size: 24px;
  margin-bottom: 30px;
  font-weight: 600;
  color: #333;
}
.input-field {
  width: 100%;
  padding: 12px 15px;
  margin-bottom: 20px;
  border: 1px solid #ccc;
  border-radius: 4px;
  box-sizing: border-box;
  font-size: 16px;
}
.auth-button {
  width: 100%;
  padding: 12px;
  background-color: #6a40e7;
  color: white;
  border: none;
  border-radius: 4px;
  font-size: 16px;
  cursor: pointer;
  transition: background-color 0.2s;
}
.auth-button:hover {
  background-color: #5b34d9;
}
.error-message {
    color: #e74c3c;
    margin-top: -10px;
    margin-bottom: 20px;
    font-size: 14px;
    text-align: left;
    padding-left: 5px;
}
.link-text {
    color: #6a40e7;
    text-decoration: none;
    font-size: 14px;
    margin-top: 10px;
    display: inline-block;
}
</style>
<!-- frontend/components/TheHeader.vue -->
<template>
  <header class="header">
    <nav>
      <NuxtLink to="/">ホーム </NuxtLink>
      
      <template v-if="isLoggedIn">
        <span>ようこそ、{{ userName || 'ユーザー' }}さん</span>
        <button @click="handleLogout">ログアウト</button>
      </template>
      <template v-else>
        <NuxtLink to="/login">ログイン</NuxtLink>
        <NuxtLink to="/signup">ユーザー登録</NuxtLink>
      </template>
    </nav>
  </header>
</template>

<script setup>
import { computed } from 'vue'
import { useRouter } from '#app'
import { useNuxtApp } from '#app'

const nuxtApp = useNuxtApp()
const router = useRouter()

// 💡 ストアと$authの手動取得
const store = nuxtApp.vueApp.config.globalProperties.$store
const auth = nuxtApp.$auth // (ログアウト処理自体はストアで行うため、ここでは不要だが残しておく)

// ゲッターを computed でラップしてリアクティブにする
const isLoggedIn = computed(() => {
    return store ? store.getters['auth/isLoggedIn'] : false
})

const userName = computed(() => {
    return store && store.state.auth.user ? store.state.auth.user.displayName : ''
})

const handleLogout = async () => {
  if (!store) {
    console.error('ログアウト失敗: ストアが見つかりません。')
    return
  }
  
  try {
    // 💡 修正: $auth を渡さずにディスパッチ
    await store.dispatch('auth/logout') 
    
    // ログアウト成功後、ログイン画面へリダイレクト
    router.push('/login')
  } catch (error) {
    console.error('ログアウトエラー:', error)
  }
}
</script>

<style scoped>
.header { padding: 10px; background-color: #f0f0f0; display: flex; justify-content: space-between; align-items: center; }
.header nav a, .header nav button, .header nav span { margin-left: 15px; }
.header nav button { cursor: pointer; background: none; border: 1px solid #ccc; padding: 5px 10px; border-radius: 4px; }
</style>
<template>
  <div>
    <div v-if="!$store.getters['auth/authChecked']" class="loading-screen">読み込み中...</div>
    <NuxtPage v-else />
  </div>
</template>

<script>
export default {
    mounted() {
        // 💡 アプリケーション起動時に、Firebase認証状態の監視を開始
        this.$store.dispatch('auth/onAuthStateChangedAction'); 
    }
}
</script>

<style>
/* グローバルスタイル */
body {
    margin: 0;
    padding: 0;
    background-color: #1e1e2d;
    color: white;
    font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif;
}
.loading-screen {
    position: fixed;
    top: 0;
    left: 0;
    width: 100vw;
    height: 100vh;
    background-color: #1e1e2d;
    color: white;
    display: flex;
    justify-content: center;
    align-items: center;
    font-size: 20px;
    z-index: 9999;
}
</style>
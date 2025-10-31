<script setup lang="ts">
import { ref } from 'vue';

// Nuxtの推奨フック: useFetch
const { data, pending, error } = await useFetch('/api/test', {
  // SSRを無効にし、クライアント側だけで実行する場合はコメントアウトを外す
  // server: false, 
});

const message = ref('APIを呼び出し中...');

if (data.value && data.value.message) {
  message.value = data.value.message;
} else if (error.value) {
  console.error('API Error:', error.value);
  message.value = 'エラーが発生しました';
}
</script>

<template>
  <main>
    <h1>Nuxt.js + Laravel API 接続確認</h1>
    <p v-if="pending">ロード中...</p>
    <p v-else>{{ message }}</p>
  </main>
</template>
<template>
<div class="login-container">
<div class="login-form">
    <h2>ログイン</h2>
    <form @submit.prevent="handleLogin">
    <input v-model="email" type="email" placeholder="メールアドレス" required />
    <input v-model="password" type="password" placeholder="パスワード" required />
    <button type="submit">ログイン</button>
    </form>
    <p>アカウントをお持ちでない方は <NuxtLink to="/signup">新規登録</NuxtLink> へ</p>
</div>
</div>
</template>

<script setup>
import { ref } from 'vue'
const email = ref('')
const password = ref('')

const handleLogin = () => {
  // ログイン処理をここに実装します
    console.log('Login attempt with:', email.value, password.value)
    alert('ログインボタンが押されました。次はAPI連携を実装します！')
}
</script>

<style scoped>
/* スタイルは簡略化しています */
.login-container {
    display: flex;
    justify-content: center;
    align-items: center;
    height: 100vh;
    background-color: #1e1e2d;
}
.login-form {
    background: white;
    padding: 30px;
    border-radius: 8px;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    text-align: center;
}
input {
    display: block;
    width: 100%;
    margin-bottom: 15px;
    padding: 10px;
    border: 1px solid #ccc;
    border-radius: 4px;
}
button {
    padding: 10px 20px;
    background-color: #5b50f7;
    color: white;
    border: none;
    border-radius: 4px;
    cursor: pointer;
}
</style>
<template>
<NuxtLayout name="auth">
<div class="form-container">
    <div class="auth-box">
    <h2>ログイン</h2>
    <form @submit.prevent="handleLogin">
        <input v-model="email" type="email" placeholder="メールアドレス" required class="input-field" />
        <input v-model="password" type="password" placeholder="パスワード" required class="input-field" />
        <p v-if="error" class="error-message">{{ error }}</p>
        <button type="submit" class="auth-button">ログイン</button>
    </form>
    <NuxtLink to="/register" class="link-text">新規登録はこちら</NuxtLink>
    </div>
</div>
</NuxtLayout>
</template>

<script>
export default {
  data() {
    return {
      email: '',
      password: '',
      error: null,
    }
  },
  methods: {
    async handleLogin() {
      this.error = null;

      if (!this.email || !this.password) {
        this.error = 'メールアドレスとパスワードを入力してください。';
        return;
      }

      try {
        // 1. Firebaseでログイン
        await this.$auth.signInWithEmailAndPassword(this.email, this.password)

        alert('ログイン成功しました！');
        this.$router.push('/'); 

      } catch (err) {
        if (err.code === 'auth/user-not-found' || err.code === 'auth/wrong-password') {
            this.error = 'メールアドレスまたはパスワードが正しくありません。';
        } else {
            this.error = 'ログインに失敗しました。: ' + err.message;
        }
      }
    }
  }
}
</script>

<style scoped>
/* 共通スタイルはlogin.vueのものを参照 */
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
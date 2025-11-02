<template>
  <NuxtLayout name="auth">
    <div class="form-container">
      <div class="auth-box">
        <h2>新規登録</h2>
        <form @submit.prevent="handleSignup">
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
          <button type="submit" class="auth-button">新規登録</button>
        </form>
      </div>
    </div>
  </NuxtLayout>
</template>

<script setup>
import { ref } from 'vue'
import { useRouter } from '@nuxtjs/composition-api'
import { useNuxtApp } from '#app'

const name = ref('')
const email = ref('')
const password = ref('')
const router = useRouter()
const error = ref(null)

const { $auth } = useNuxtApp(); 

const handleSignup = async () => {
    error.value = null;

    // 📝 要件バリデーション
    if (name.value.length === 0 || name.value.length > 20) {
        error.value = 'ユーザーネームは1〜20文字で入力してください。';
        return;
    }
    if (password.value.length < 6) {
        error.value = 'パスワードは6文字以上で入力してください。';
        return;
    }
    
    try {
        // 1. Firebaseでユーザー登録
        const userCredential = await $auth.createUserWithEmailAndPassword(email.value, password.value)
        
        // 2. ユーザーネームを設定 (要件のユーザーネーム)
        await userCredential.user.updateProfile({
          displayName: name.value
        })

        // 3. 認証成功: ホーム画面へリダイレクト
        alert('新規登録とログインに成功しました！');
        router.push('/');

    } catch (err) {
        console.error('新規登録エラー:', err);

        // Firebaseのエラーコードに基づくメッセージ表示
        if (err.code === 'auth/email-already-in-use') {
              error.value = 'このメールアドレスは既に使用されています。';
        } else if (err.code === 'auth/weak-password') {
              error.value = 'パスワードが弱すぎます。（6文字以上が必要です）';
        } else {
            error.value = '登録に失敗しました。: ' + err.message;
        }
    }
}
</script>

<style>
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
</style>
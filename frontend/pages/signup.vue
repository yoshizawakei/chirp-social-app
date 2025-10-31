<template>
  <div class="page-container">
    <header class="header">
      <div class="logo">SHARE</div>
      <nav class="nav">
        <nuxt-link to="/signup" class="nav-item active">新規登録</nuxt-link>
        <nuxt-link to="/login" class="nav-item">ログイン</nuxt-link>
      </nav>
    </header>

    <div class="form-wrapper">
      <div class="signup-box">
        <h2 class="title">新規登録</h2>
        
        <form @submit.prevent="signUp" class="signup-form">
          
          <div class="form-group">
            <input type="text" id="username" v-model="username" required maxlength="20" placeholder="ユーザーネーム">
            <p v-if="errors.username" class="error-message">{{ errors.username }}</p>
          </div>

          <div class="form-group">
            <input type="email" id="email" v-model="email" required placeholder="メールアドレス">
            <p v-if="errors.email" class="error-message">{{ errors.email }}</p>
          </div>

          <div class="form-group">
            <input type="password" id="password" v-model="password" required minlength="6" placeholder="パスワード">
            <p v-if="errors.password" class="error-message">{{ errors.password }}</p>
          </div>
          
          <p v-if="firebaseError" class="error-message firebase-error">{{ firebaseError }}</p>

          <button type="submit" class="submit-button">新規登録</button>
        </form>
      </div>
    </div>
  </div>
</template>

<script>
// (前回の新規登録ロジックは変更なし)
import { createUserWithEmailAndPassword, updateProfile } from 'firebase/auth';

export default {
  // layout: 'empty' // レイアウトを使用しない場合はコメントを外す
  data() {
    return {
      username: '',
      email: '',
      password: '',
      errors: {},
      firebaseError: null,
    };
  },
  methods: {
    // ------------------------------------------------------------------
    // ユーザー認証ロジック (変更なし)
    // ------------------------------------------------------------------
    async signUp() {
      this.firebaseError = null;
      this.errors = {}; 

      if (!this.validateForm()) {
        return; 
      }

      try {
        const auth = this.$firebaseAuth;
        const userCredential = await createUserWithEmailAndPassword(auth, this.email, this.password);
        
        await updateProfile(userCredential.user, {
          displayName: this.username,
        });

        this.$router.push('/timeline');

      } catch (error) {
        console.error('Firebase 登録エラー:', error);
        switch (error.code) {
          case 'auth/email-already-in-use':
            this.firebaseError = 'このメールアドレスは既に使用されています。';
            break;
          case 'auth/invalid-email':
            this.firebaseError = 'メールアドレスの形式が正しくありません。';
            break;
          case 'auth/weak-password':
            this.firebaseError = 'パスワードが弱すぎます。6文字以上で設定してください。';
            break;
          default:
            this.firebaseError = '登録中に予期せぬエラーが発生しました。';
        }
      }
    },

    // ------------------------------------------------------------------
    // クライアントサイド・バリデーション (変更なし)
    // ------------------------------------------------------------------
    validateForm() {
      let isValid = true;

      // ユーザーネームのバリデーション
      if (!this.username) {
        this.errors.username = 'ユーザーネームは必須です。';
        isValid = false;
      } else if (this.username.length > 20) {
        this.errors.username = 'ユーザーネームは20文字以内で入力してください。';
        isValid = false;
      }

      // メールアドレスの簡易バリデーション
      if (!this.email) {
        this.errors.email = 'メールアドレスは必須です。';
        isValid = false;
      } else if (!/^\S+@\S+\.\S+$/.test(this.email)) {
        this.errors.email = 'メールアドレスの形式が正しくありません。';
        isValid = false;
      }

      // パスワードのバリデーション
      if (!this.password) {
        this.errors.password = 'パスワードは必須です。';
        isValid = false;
      } else if (this.password.length < 6) {
        this.errors.password = 'パスワードは6文字以上で入力してください。';
        isValid = false;
      }

      return isValid;
    }
  }
}
</script>

<style scoped>
/* ページ全体のスタイル */
.page-container {
  min-height: 100vh;
  background-color: #1a1a2e; /* 背景色 (濃い紫/ダークテーマ) */
  color: #e0e0e0;
}

/* ヘッダーのスタイル */
.header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 20px 40px;
  /* border-bottom: 1px solid #333; */
}

.logo {
  font-size: 24px;
  font-weight: bold;
  letter-spacing: 2px;
}

.nav a {
  color: #e0e0e0;
  text-decoration: none;
  margin-left: 20px;
  font-size: 14px;
  padding-bottom: 3px;
}

.nav .active {
  font-weight: bold;
  /* 下線のような視覚的な強調 */
  border-bottom: 2px solid #fff; 
}

/* フォームの配置 */
.form-wrapper {
  display: flex;
  justify-content: center;
  align-items: center;
  padding-top: 50px; /* ヘッダーの下に少しスペースを開ける */
}

/* 中央のフォームボックス */
.signup-box {
  background-color: #ffffff; /* 白いボックス */
  padding: 30px 40px;
  border-radius: 8px;
  width: 100%;
  max-width: 400px;
  box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
  color: #333; /* ボックス内の文字色を黒に */
}

.title {
  text-align: center;
  font-size: 20px;
  margin-bottom: 30px;
  color: #333;
}

/* フォームの入力フィールド */
.form-group {
  margin-bottom: 20px;
}

input {
  width: 100%;
  padding: 12px;
  border: 1px solid #ccc;
  border-radius: 4px;
  box-sizing: border-box;
  font-size: 16px;
}

input::placeholder {
  color: #aaa;
}

/* ボタン */
.submit-button {
  width: 100%;
  padding: 12px;
  background-color: #6a0dad; /* 画像に見られる紫 */
  color: white;
  border: none;
  border-radius: 4px;
  cursor: pointer;
  font-size: 16px;
  font-weight: bold;
  transition: background-color 0.3s;
}

.submit-button:hover {
  background-color: #580099;
}

/* エラーメッセージ */
.error-message {
  color: #ff4d4d; /* 明るい赤 */
  font-size: 0.85em;
  margin-top: 5px;
}

.firebase-error {
  text-align: center;
  margin-bottom: 15px;
}
</style>
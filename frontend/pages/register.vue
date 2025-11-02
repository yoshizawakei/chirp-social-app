<template>
  <NuxtLayout name="auth">
    <div class="form-container">
      <div class="auth-box">
        <h2>新規登録</h2>
        <form @submit.prevent="handleSignup">
          <input v-model="name" type="text" placeholder="ユーザーネーム" required class="input-field" />
          <input v-model="email" type="email" placeholder="メールアドレス" required class="input-field" />
          <input v-model="password" type="password" placeholder="パスワード" required class="input-field" />
          <p v-if="error" class="error-message">{{ error }}</p>
          <button type="submit" class="auth-button">新規登録</button>
        </form>
      </div>
    </div>
  </NuxtLayout>
</template>

<script>
export default {
  data() {
    return {
      name: '',
      email: '',
      password: '',
      error: null,
    }
  },
  methods: {
    async handleSignup() {
      this.error = null;

      // 📝 要件バリデーション
      if (this.name.length === 0 || this.name.length > 20) {
        this.error = 'ユーザーネームは1〜20文字で入力してください。';
        return;
      }
      if (this.password.length < 6) {
        this.error = 'パスワードは6文字以上で入力してください。';
        return;
      }
      
      try {
        // 1. Firebaseでユーザー登録
        const userCredential = await this.$auth.createUserWithEmailAndPassword(this.email, this.password)
        
        // 2. ユーザーネームを設定 (displayName)
        await userCredential.user.updateProfile({
          displayName: this.name
        })

        alert('新規登録とログインに成功しました！');
        this.$router.push('/');

      } catch (err) {
        if (err.code === 'auth/email-already-in-use') {
             this.error = 'このメールアドレスは既に使用されています。';
        } else if (err.code === 'auth/weak-password') {
             this.error = 'パスワードが弱すぎます。（6文字以上が必要です）';
        } else {
            this.error = '登録に失敗しました。: ' + err.message;
        }
      }
    }
  }
}
</script>

<style scoped>
/* 💡 スタイルはlogin.vueのものをそのまま利用 */
/* ... form-container, auth-box, h2, input-field, auth-button ... */
</style>
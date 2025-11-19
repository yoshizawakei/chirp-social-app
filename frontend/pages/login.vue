<template>
    <div class="login-page">
        <h2>ログイン</h2>

        <form @submit.prevent="submit">
        <input v-model="email" type="email" placeholder="メールアドレス" required />
        <input v-model="password" type="password" placeholder="パスワード" required />
        <button type="submit">ログイン</button>
        </form>

        <p>アカウントがない？ <NuxtLink to="/register">新規登録</NuxtLink></p>
    </div>
</template>

<script setup>
const email = ref("");
const password = ref("");
const router = useRouter();
const { login } = useAuth();

const submit = async () => {
    await login(email.value, password.value);
    router.push("/");
};
</script>


<style scoped>
.form-container {
    display: flex;
    justify-content: center;
    align-items: center;
    min-height: 100vh;
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
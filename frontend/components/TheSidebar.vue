<template>
<nav class="sidebar">
    <NuxtLink to="/" class="sidebar-item" exact-active-class="active">
    <span class="icon">🏠</span>
    ホーム
    </NuxtLink>

    <div v-if="$store.getters['auth/isLoggedIn']" @click="logout" class="sidebar-item logout">
    <span class="icon">🚪</span>
    ログアウト
    </div>
</nav>
</template>

<script>
export default {
methods: {
async logout() {
    if (!confirm('ログアウトしますか？')) return;
    try {
    // ストアのログアウトアクションを呼び出す
    await this.$store.dispatch('auth/logout')
    this.$router.push('/login')
    } catch (e) {
    alert('ログアウトに失敗しました。')
    }
},
},
}
</script>
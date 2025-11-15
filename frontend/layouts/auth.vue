<!-- frontend/layouts/auth.vue -->
<template>
    <div class="auth-layout">
    <header class="header">
        <NuxtLink to="/">
        <span class="logo-text">SHARE</span>
        </NuxtLink>
        <nav class="auth-nav">
        <NuxtLink to="/signup" :class="{ 'active': $route.path === '/signup' }">新規登録</NuxtLink>
        <NuxtLink to="/login" :class="{ 'active': $route.path === '/login' }">ログイン</NuxtLink>
        </nav>
    </header>
    <main class="auth-content">
        <slot />
    </main>
    </div>
</template>

<style scoped>
.auth-layout {
    min-height: 100vh;
    background-color: #1e1e2d;
    color: white;
    font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif;
}
.header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 20px 50px;
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    z-index: 10;
}
.logo-text {
    font-size: 24px;
    font-weight: bold;
    color: white;
}
.auth-nav a {
    color: white;
    text-decoration: none;
    font-size: 14px;
    margin-left: 20px;
    opacity: 0.7;
    transition: opacity 0.2s;
}
.auth-nav a:hover,
.auth-nav a.active {
    opacity: 1;
    font-weight: bold;
}
.auth-content {
    display: flex;
    justify-content: center;
    align-items: center;
    height: 100vh;
}
</style>
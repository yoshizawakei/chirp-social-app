<template>
  <div>
    <NuxtLayout>
      <NuxtPage />
    </NuxtLayout>
  </div>
</template>

<script setup>
import { useNuxtApp } from '#app' 
import { onMounted } from 'vue'

const nuxtApp = useNuxtApp()

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
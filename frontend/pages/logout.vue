<template>
  <div></div>
</template>

<script setup>
const { logout } = useAuth()

await logout()
navigateTo('/login')
</script>

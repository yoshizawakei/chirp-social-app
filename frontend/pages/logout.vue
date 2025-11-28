<script setup>
import { useAuth } from "~/composables/useAuth";

const { logout } = useAuth();

onMounted(async () => {
  await logout();
  navigateTo("/login");
});
</script>

<template>
  <div></div>
</template>

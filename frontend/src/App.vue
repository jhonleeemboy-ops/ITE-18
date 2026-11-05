<script setup>
// The outer shell of the app. RouterView shows the page that matches the address.
import { RouterView } from "vue-router";
import { toast } from "./composables/toast.js";
</script>

<template>
  <RouterView />

  <!-- Small pop-up message at the bottom of the screen -->
  <div class="toast" :class="{ 'is-visible': toast.visible }" role="status">{{ toast.message }}</div>
</template>

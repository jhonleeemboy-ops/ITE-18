<script setup>
// The "Disaster Ready" logo and name, used at the top of every page.
import { RouterLink } from "vue-router";
import AppIcon from "./AppIcon.vue";

defineProps({
  // Second part of the small line under the name
  place: { type: String, default: "Butuan City" },
  to: { type: String, default: "/" },
});
</script>

<template>
  <RouterLink class="brand" :to="to">
    <span class="brand-logo"><AppIcon name="shield" /></span>
    <span class="brand-text">
      <span class="brand-name">Disaster Ready</span>
      <span class="brand-place">Barangay Ampayon<span class="brand-city"> · {{ place }}</span></span>
    </span>
  </RouterLink>
</template>

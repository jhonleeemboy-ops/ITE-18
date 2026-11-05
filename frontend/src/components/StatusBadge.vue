<script setup>
// A small colored label for a status, e.g. <StatusBadge value="evacuated" />
import { computed } from "vue";

const props = defineProps({ value: { type: String, required: true } });

// Which color each status uses
const colors = {
  safe: "green",
  open: "green",
  resolved: "green",
  sent: "green",
  evacuated: "teal",
  info: "teal",
  active: "amber",
  yellow: "amber",
  pending: "amber",
  "in progress": "amber",
  medium: "amber",
  "near capacity": "amber",
  "needs help": "red",
  high: "red",
  full: "red",
  failed: "red",
  standby: "gray",
  low: "gray",
  archived: "gray",
};

const color = computed(() => colors[props.value] || "gray");
</script>

<template>
  <span class="badge" :class="'badge-' + color">{{ value }}</span>
</template>

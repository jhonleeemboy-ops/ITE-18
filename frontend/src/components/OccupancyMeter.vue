<script setup>
// A bar showing how full an evacuation center is
import { computed } from "vue";
import { percent } from "../utils/format.js";

const props = defineProps({
  occupants: { type: Number, required: true },
  capacity: { type: Number, required: true },
  label: { type: String, default: "Occupancy" },
});

const filled = computed(() => percent(props.occupants, props.capacity));

// Amber when 75% full, red when 95% full
const level = computed(() => {
  if (filled.value >= 95) return "is-danger";
  if (filled.value >= 75) return "is-warning";
  return "";
});
</script>

<template>
  <div class="meter-wrap">
    <div class="meter-text">
      <span>{{ occupants }} / {{ capacity }} people</span>
      <span>{{ filled }}%</span>
    </div>
    <div
      class="meter"
      :class="level"
      role="progressbar"
      :aria-label="label"
      :aria-valuenow="filled"
      aria-valuemin="0"
      aria-valuemax="100"
    >
      <span :style="{ width: filled + '%' }"></span>
    </div>
  </div>
</template>

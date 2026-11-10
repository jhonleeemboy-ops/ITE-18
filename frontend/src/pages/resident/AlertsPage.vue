<script setup>
// Resident portal: Alerts and announcements (ADVISORIES in the database)
// Connected to the Laravel API: GET /advisories (newest first).
import { onMounted, ref } from "vue";
import StatusBadge from "../../components/StatusBadge.vue";
import api from "../../api";
import { formatDateTime } from "../../utils/format.js";

const advisories = ref([]);
const loading = ref(true);
const error = ref("");

onMounted(async () => {
  try {
    const { data } = await api.get("/advisories");
    advisories.value = data;
  } catch (e) {
    error.value = "Could not load alerts. Please try again.";
  } finally {
    loading.value = false;
  }
});
</script>

<template>
  <div class="page">
    <div class="page-head">
      <div>
        <p class="eyebrow">Official updates</p>
        <h1>Alerts and announcements</h1>
        <p>Published by the Barangay Ampayon disaster response team.</p>
      </div>
    </div>

    <p v-if="loading" role="status">Loading alerts…</p>
    <p v-else-if="error" class="field-error" role="alert">{{ error }}</p>
    <p v-else-if="!advisories.length" class="list-meta">There are no announcements yet.</p>

    <ul v-else class="alert-list">
      <li v-for="advisory in advisories" :key="advisory.id" class="card">
        <p class="list-meta">
          <StatusBadge :value="advisory.severity" /> · {{ formatDateTime(advisory.issued_at) }}
        </p>
        <h2>{{ advisory.title }}</h2>
        <p>{{ advisory.message }}</p>
        <p class="list-meta">Status: <StatusBadge :value="advisory.status" /></p>
      </li>
    </ul>
  </div>
</template>
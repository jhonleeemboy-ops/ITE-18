<script setup>
// Resident portal: Alerts and announcements (ADVISORIES in the ERD)
import StatusBadge from "../../components/StatusBadge.vue";
import { advisories } from "../../data/sample-data.js";
import { formatDateTime } from "../../utils/format.js";
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

    <ul class="alert-list">
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

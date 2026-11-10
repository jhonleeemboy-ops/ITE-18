<script setup>
// Staff: Dashboard (overview of everything)
// Connected to the Laravel API: /dashboard, /advisories, /evacuation-centers, /incidents (staff only).
import { computed, onMounted, ref } from "vue";
import { RouterLink } from "vue-router";
import OccupancyMeter from "../../components/OccupancyMeter.vue";
import StatusBadge from "../../components/StatusBadge.vue";
import api from "../../api";
import { incidentTypes } from "../../data/sample-data.js"; // only the type labels
import { formatDateTime, formatNumber } from "../../utils/format.js";
import { mapCenter, mapIncident } from "../../utils/mappers.js";

const stats = ref(null);
const advisories = ref([]);
const centers = ref([]);
const incidents = ref([]);
const loading = ref(true);
const error = ref("");

// A missing key in the API totals means 0
const evacuatedHouseholds = computed(() => stats.value?.households_by_safety?.evacuated ?? 0);
const openRequests = computed(
  () => (stats.value?.incidents_by_status?.pending ?? 0) + (stats.value?.incidents_by_status?.in_progress ?? 0)
);
const openCenters = computed(() => centers.value.filter((center) => center.status !== "standby").length);
const activeAdvisory = computed(() => advisories.value.find((advisory) => advisory.status === "active"));
const recentIncidents = computed(() => incidents.value.slice(0, 10)); // newest first from the API

onMounted(async () => {
  try {
    const [dash, advisoryList, centerList, incidentList] = await Promise.all([
      api.get("/dashboard"),
      api.get("/advisories"),
      api.get("/evacuation-centers"),
      api.get("/incidents"),
    ]);
    stats.value = dash.data;
    advisories.value = advisoryList.data;
    centers.value = centerList.data.map(mapCenter);
    incidents.value = incidentList.data.map(mapIncident);
  } catch (e) {
    error.value = "Could not load the dashboard. Please try again.";
  } finally {
    loading.value = false;
  }
});
</script>

<template>
  <div class="page">
    <div class="page-head">
      <div>
        <h2 class="page-subtitle">Good day! Here's what's happening in Barangay Ampayon.</h2>
      </div>
      <div class="button-row no-margin">
        <RouterLink to="/staff/households" class="btn btn-outline">Household profiles</RouterLink>
        <RouterLink to="/staff/sms-alerts" class="btn btn-primary">Send SMS alert</RouterLink>
      </div>
    </div>

    <p v-if="loading" role="status">Loading dashboard…</p>
    <p v-else-if="error" class="field-error" role="alert">{{ error }}</p>

    <template v-else>
      <div v-if="activeAdvisory" class="alert alert-warning section-gap-bottom">
        <span class="alert-icon" aria-hidden="true">!</span>
        <div class="alert-body">
          <p class="alert-title">{{ activeAdvisory.title }}</p>
          <p class="alert-text">{{ activeAdvisory.message }}</p>
          <p class="alert-text">Issued {{ formatDateTime(activeAdvisory.issued_at) }}</p>
        </div>
      </div>

      <div class="grid grid-4">
        <div class="card stat">
          <p class="stat-value">{{ stats.households_total }}</p>
          <p class="stat-label">Registered households</p>
          <p class="stat-note">{{ evacuatedHouseholds }} evacuated</p>
        </div>
        <div class="card stat">
          <p class="stat-value">{{ formatNumber(stats.people_total) }}</p>
          <p class="stat-label">Total residents</p>
          <p class="stat-note">From household profiles</p>
        </div>
        <div class="card stat">
          <p class="stat-value">{{ openCenters }} / {{ centers.length }}</p>
          <p class="stat-label">Evacuation centers in use</p>
          <p class="stat-note">The rest on standby</p>
        </div>
        <div class="card stat">
          <p class="stat-value">{{ openRequests }}</p>
          <p class="stat-label">Open assistance requests</p>
          <p class="stat-note">Pending or in progress</p>
        </div>
      </div>

      <div class="grid grid-2 section-gap">
        <section class="card">
          <div class="card-head">
            <h2>Evacuation center capacity</h2>
            <RouterLink to="/staff/evacuation-centers" class="link-button">View all</RouterLink>
          </div>
          <ul class="list">
            <li v-for="center in centers" :key="center.id">
              <div class="center-row">
                <p class="list-title">{{ center.name }}</p>
                <StatusBadge :value="center.status" />
              </div>
              <OccupancyMeter :occupants="center.occupants" :capacity="center.capacity" :label="center.name + ' occupancy'" />
            </li>
          </ul>
        </section>

        <section class="card">
          <div class="card-head">
            <h2>Recent assistance requests</h2>
          </div>
          <div class="table-wrap">
            <table class="table">
              <thead>
                <tr>
                  <th>Resident</th>
                  <th>Type</th>
                  <th>Priority</th>
                  <th>Status</th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="incident in recentIncidents" :key="incident.id">
                  <td>
                    <strong>{{ incident.reporter_name }}</strong>
                    <span class="cell-note">{{ incident.purok }}</span>
                  </td>
                  <td>{{ incidentTypes[incident.type] }}</td>
                  <td><StatusBadge :value="incident.priority" /></td>
                  <td><StatusBadge :value="incident.status" /></td>
                </tr>
                <tr v-if="recentIncidents.length === 0">
                  <td colspan="4" class="empty-cell">No assistance requests yet.</td>
                </tr>
              </tbody>
            </table>
          </div>
        </section>
      </div>
    </template>
  </div>
</template>
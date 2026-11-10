<script setup>
// Resident portal: Home
// Connected to the Laravel API: /me, /evacuation-centers, /advisories, /emergency-contacts, /incidents.
import { computed, onMounted, ref } from "vue";
import { RouterLink } from "vue-router";
import AppIcon from "../../components/AppIcon.vue";
import OccupancyMeter from "../../components/OccupancyMeter.vue";
import StatusBadge from "../../components/StatusBadge.vue";
import api from "../../api";
import { formatDateTime } from "../../utils/format.js";
import {
  assignedCenterFor,
  mapCenter,
  mapHousehold,
  mapIncident,
} from "../../utils/mappers.js";

const user = ref(null);
const household = ref(null);
const centers = ref([]);
const advisories = ref([]);
const emergencyContacts = ref([]);
const myRequests = ref([]);
const loading = ref(true);
const error = ref("");

const firstName = computed(() => (user.value?.name ?? "").split(" ")[0]);
const center = computed(() =>
  household.value ? assignedCenterFor(centers.value, household.value.purok) : null
);
const activeAdvisory = computed(() => advisories.value.find((a) => a.status === "active"));

onMounted(async () => {
  try {
    const [me, centerList, advisoryList, contactList, incidentList] = await Promise.all([
      api.get("/me"),
      api.get("/evacuation-centers"),
      api.get("/advisories"),
      api.get("/emergency-contacts"),
      api.get("/incidents"), // residents only receive their own requests
    ]);

    user.value = me.data;
    household.value = me.data.household ? mapHousehold(me.data.household) : null;
    centers.value = centerList.data.map(mapCenter);
    advisories.value = advisoryList.data;
    emergencyContacts.value = contactList.data;
    myRequests.value = incidentList.data.map(mapIncident); // newest first
  } catch (e) {
    error.value = "Could not load your home page. Please try again.";
  } finally {
    loading.value = false;
  }
});
</script>

<template>
  <div class="page">
    <div class="page-head">
      <div>
        <p class="eyebrow">Citizen portal</p>
        <h1>Hello, {{ firstName }}</h1>
        <p>Official updates and services for your household.</p>
      </div>
    </div>

    <p v-if="loading" role="status">Loading…</p>
    <p v-else-if="error" class="field-error" role="alert">{{ error }}</p>

    <template v-else>
      <!-- Latest active advisory -->
      <section v-if="activeAdvisory" class="hero-alert" aria-labelledby="advisory-title">
        <span class="alert-icon" aria-hidden="true">!</span>
        <div>
          <p class="hero-alert-label">Active advisory · {{ formatDateTime(activeAdvisory.issued_at) }}</p>
          <h2 id="advisory-title">{{ activeAdvisory.title }}</h2>
          <p>{{ activeAdvisory.message }}</p>
          <div class="button-row">
            <RouterLink to="/resident/alerts" class="btn btn-primary">View all alerts</RouterLink>
            <RouterLink to="/resident/evacuation" class="btn btn-outline">Find my evacuation center</RouterLink>
          </div>
        </div>
      </section>

      <!-- Three summary cards -->
      <div class="grid grid-3">
        <RouterLink to="/resident/household" class="card card-link">
          <p class="card-kicker">Household profile</p>
          <template v-if="household">
            <p class="stat-value">{{ household.members }} members</p>
            <p class="stat-note">{{ household.purok }} · <StatusBadge :value="household.safety" /></p>
          </template>
          <p v-else class="stat-note">No household found.</p>
        </RouterLink>

        <RouterLink to="/resident/evacuation" class="card card-link">
          <p class="card-kicker">Your evacuation center</p>
          <template v-if="center">
            <p class="card-title">{{ center.name }}</p>
            <OccupancyMeter :occupants="center.occupants" :capacity="center.capacity" :label="center.name + ' occupancy'" />
          </template>
          <p v-else class="stat-note">Not assigned yet.</p>
        </RouterLink>

        <RouterLink to="/resident/assistance" class="card card-link">
          <p class="card-kicker">Assistance requests</p>
          <p class="stat-value">{{ myRequests.length }}</p>
          <p class="stat-note">
            Latest:
            <StatusBadge v-if="myRequests.length" :value="myRequests[0].status" />
            <span v-else>none yet</span>
          </p>
        </RouterLink>
      </div>

      <div class="grid grid-2 section-gap">
        <!-- Announcements -->
        <section class="card">
          <div class="card-head">
            <h2>Barangay announcements</h2>
            <RouterLink to="/resident/alerts" class="link-button">View all</RouterLink>
          </div>
          <ul class="list">
            <li v-for="advisory in advisories.slice(0, 3)" :key="advisory.id">
              <p class="list-title">{{ advisory.title }} <StatusBadge :value="advisory.status" /></p>
              <p class="list-text">{{ advisory.message }}</p>
              <p class="list-meta">{{ formatDateTime(advisory.issued_at) }}</p>
            </li>
          </ul>
          <p v-if="!advisories.length" class="list-meta">No announcements yet.</p>
        </section>

        <!-- Emergency contacts -->
        <section class="card">
          <div class="card-head">
            <h2>Emergency contacts</h2>
          </div>
          <ul class="list">
            <li v-for="contact in emergencyContacts" :key="contact.id" class="contact-row">
              <span class="contact-icon"><AppIcon name="phone" /></span>
              <div class="contact-text">
                <p class="list-title">{{ contact.name }}</p>
                <p class="list-meta">{{ contact.category }}</p>
              </div>
              <a v-if="contact.phone !== 'To be verified'" class="contact-phone" :href="'tel:' + contact.phone">{{ contact.phone }}</a>
              <span v-else class="contact-phone is-pending">To be verified</span>
            </li>
          </ul>
          <p class="note">For life-threatening emergencies, call <strong>911</strong> right away.</p>
        </section>
      </div>
    </template>
  </div>
</template>
<script setup>
// Resident portal: Home
import { RouterLink } from "vue-router";
import AppIcon from "../../components/AppIcon.vue";
import OccupancyMeter from "../../components/OccupancyMeter.vue";
import StatusBadge from "../../components/StatusBadge.vue";
import {
  advisories,
  assignedCenterFor,
  currentResident,
  emergencyContacts,
  findHousehold,
  incidents,
} from "../../data/sample-data.js";
import { formatDateTime } from "../../utils/format.js";
import { getUser } from "../../services/session.js";

const household = findHousehold(currentResident.household_id);
const center = assignedCenterFor(household.purok);
const activeAdvisory = advisories.find((advisory) => advisory.status === "active");
const myRequests = incidents.filter((incident) => incident.reporter_id === currentResident.id);
// Greet the signed-in user (from the API) if there is one; otherwise the sample resident
const firstName = (getUser()?.name || currentResident.name).split(" ")[0];
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
        <p class="stat-value">{{ household.members }} members</p>
        <p class="stat-note">{{ household.purok }} · <StatusBadge :value="household.safety" /></p>
      </RouterLink>

      <RouterLink to="/resident/evacuation" class="card card-link">
        <p class="card-kicker">Your evacuation center</p>
        <p class="card-title">{{ center.name }}</p>
        <OccupancyMeter :occupants="center.occupants" :capacity="center.capacity" :label="center.name + ' occupancy'" />
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
  </div>
</template>

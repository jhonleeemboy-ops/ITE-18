<script setup>
// Staff: Dashboard (overview of everything)
import { RouterLink } from "vue-router";
import OccupancyMeter from "../../components/OccupancyMeter.vue";
import StatusBadge from "../../components/StatusBadge.vue";
import { advisories, evacuationCenters, households, incidents, incidentTypes } from "../../data/sample-data.js";
import { formatDateTime, formatNumber } from "../../utils/format.js";

// Numbers for the four summary cards (counted from the sample data)
const totalResidents = households.reduce((sum, household) => sum + household.members, 0);
const evacuatedHouseholds = households.filter((household) => household.safety === "evacuated").length;
const openCenters = evacuationCenters.filter((center) => center.status !== "standby").length;
const pendingRequests = incidents.filter((incident) => incident.status !== "resolved").length;
const activeAdvisory = advisories.find((advisory) => advisory.status === "active");
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
        <p class="stat-value">{{ households.length }}</p>
        <p class="stat-label">Registered households</p>
        <p class="stat-note">{{ evacuatedHouseholds }} evacuated</p>
      </div>
      <div class="card stat">
        <p class="stat-value">{{ formatNumber(totalResidents) }}</p>
        <p class="stat-label">Total residents</p>
        <p class="stat-note">From household profiles</p>
      </div>
      <div class="card stat">
        <p class="stat-value">{{ openCenters }} / {{ evacuationCenters.length }}</p>
        <p class="stat-label">Evacuation centers in use</p>
        <p class="stat-note">The rest on standby</p>
      </div>
      <div class="card stat">
        <p class="stat-value">{{ pendingRequests }}</p>
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
          <li v-for="center in evacuationCenters" :key="center.id">
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
              <tr v-for="incident in incidents" :key="incident.id">
                <td>
                  <strong>{{ incident.reporter_name }}</strong>
                  <span class="cell-note">{{ incident.purok }}</span>
                </td>
                <td>{{ incidentTypes[incident.type] }}</td>
                <td><StatusBadge :value="incident.priority" /></td>
                <td><StatusBadge :value="incident.status" /></td>
              </tr>
            </tbody>
          </table>
        </div>
      </section>
    </div>
  </div>
</template>

<script setup>
// Staff: Evacuation centers (EVACUATION_CENTERS table in the ERD)
import OccupancyMeter from "../../components/OccupancyMeter.vue";
import StatusBadge from "../../components/StatusBadge.vue";
import { evacuationCenters } from "../../data/sample-data.js";
import { showToast } from "../../composables/toast.js";
</script>

<template>
  <div class="page">
    <div class="page-head">
      <div>
        <h2 class="page-subtitle">Capacity and status of each center.</h2>
      </div>
      <button type="button" class="btn btn-primary" @click="showToast('Adding centers needs the backend. Nothing was saved.')">
        Add center
      </button>
    </div>

    <div class="grid grid-2">
      <section v-for="center in evacuationCenters" :key="center.id" class="card">
        <div class="center-row">
          <h2 class="card-title">{{ center.name }}</h2>
          <StatusBadge :value="center.status" />
        </div>
        <p class="list-meta">Serves {{ center.serves.join(", ") }} (sample rule)</p>
        <OccupancyMeter :occupants="center.occupants" :capacity="center.capacity" :label="center.name + ' occupancy'" />
        <div class="button-row">
          <button type="button" class="btn btn-outline" @click="showToast('Checking in evacuees needs the backend (suggested evacuations table).')">
            Check in evacuees
          </button>
        </div>
      </section>
    </div>
  </div>
</template>

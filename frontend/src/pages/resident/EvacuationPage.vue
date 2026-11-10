<script setup>
// Resident portal: Evacuation
// Connected to the Laravel API: GET /me and GET /evacuation-centers.
import { computed, onMounted, ref } from "vue";
import OccupancyMeter from "../../components/OccupancyMeter.vue";
import StatusBadge from "../../components/StatusBadge.vue";
import api from "../../api";
import { assignedCenterFor, mapCenter, mapHousehold } from "../../utils/mappers.js";

const household = ref(null);
const centers = ref([]);
const loading = ref(true);
const error = ref("");

// The center whose Purok list includes the resident's Purok (comes from the database)
const center = computed(() =>
  household.value ? assignedCenterFor(centers.value, household.value.purok) : null
);

onMounted(async () => {
  try {
    const [me, list] = await Promise.all([api.get("/me"), api.get("/evacuation-centers")]);
    household.value = me.data.household ? mapHousehold(me.data.household) : null;
    centers.value = list.data.map(mapCenter);
  } catch (e) {
    error.value = "Could not load evacuation information. Please try again.";
  } finally {
    loading.value = false;
  }
});
</script>

<template>
  <div class="page">
    <div class="page-head">
      <div>
        <p class="eyebrow">Your safety plan</p>
        <h1>Evacuation information</h1>
        <p v-if="household">Your assigned center is based on your registered {{ household.purok }} address.</p>
      </div>
    </div>

    <p v-if="loading" role="status">Loading evacuation information…</p>
    <p v-else-if="error" class="field-error" role="alert">{{ error }}</p>

    <template v-else>
      <section class="card assigned-center">
        <p class="card-kicker">Assigned evacuation center</p>
        <template v-if="center">
          <div class="assigned-head">
            <h2>{{ center.name }}</h2>
            <StatusBadge :value="center.status" />
          </div>
          <OccupancyMeter :occupants="center.occupants" :capacity="center.capacity" :label="center.name + ' occupancy'" />
        </template>
        <p v-else>No evacuation center is assigned to your Purok yet. Please contact the barangay.</p>
      </section>

      <section class="card section-gap">
        <div class="card-head">
          <h2>All evacuation centers</h2>
          <p>{{ centers.length }} centers</p>
        </div>
        <ul class="list">
          <li v-for="item in centers" :key="item.id">
            <div class="center-row">
              <p class="list-title">{{ item.name }}</p>
              <StatusBadge :value="item.status" />
            </div>
            <OccupancyMeter :occupants="item.occupants" :capacity="item.capacity" :label="item.name + ' occupancy'" />
          </li>
        </ul>
      </section>
    </template>
  </div>
</template>
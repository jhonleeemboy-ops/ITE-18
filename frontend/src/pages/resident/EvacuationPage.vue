<script setup>
// Resident portal: Evacuation
import OccupancyMeter from "../../components/OccupancyMeter.vue";
import StatusBadge from "../../components/StatusBadge.vue";
import { assignedCenterFor, currentResident, evacuationCenters, findHousehold } from "../../data/sample-data.js";

const household = findHousehold(currentResident.household_id);
const center = assignedCenterFor(household.purok);
</script>

<template>
  <div class="page">
    <div class="page-head">
      <div>
        <p class="eyebrow">Your safety plan</p>
        <h1>Evacuation information</h1>
        <p>Your assigned center is based on your registered {{ household.purok }} address (sample rule).</p>
      </div>
    </div>

    <section class="card assigned-center">
      <p class="card-kicker">Assigned evacuation center</p>
      <div class="assigned-head">
        <h2>{{ center.name }}</h2>
        <StatusBadge :value="center.status" />
      </div>
      <OccupancyMeter :occupants="center.occupants" :capacity="center.capacity" :label="center.name + ' occupancy'" />
    </section>

    <section class="card section-gap">
      <div class="card-head">
        <h2>All evacuation centers</h2>
        <p>{{ evacuationCenters.length }} centers</p>
      </div>
      <ul class="list">
        <li v-for="item in evacuationCenters" :key="item.id">
          <div class="center-row">
            <p class="list-title">{{ item.name }}</p>
            <StatusBadge :value="item.status" />
          </div>
          <OccupancyMeter :occupants="item.occupants" :capacity="item.capacity" :label="item.name + ' occupancy'" />
        </li>
      </ul>
    </section>
  </div>
</template>

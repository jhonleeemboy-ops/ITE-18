<script setup>
// Staff: Household profiles (HOUSEHOLDS table in the ERD)
import { computed, ref } from "vue";
import StatusBadge from "../../components/StatusBadge.vue";
import { households, puroks } from "../../data/sample-data.js";
import { showToast } from "../../composables/toast.js";

const search = ref("");
const purokFilter = ref("");

// The table only shows households that match the search box and the Purok filter
const shownHouseholds = computed(() =>
  households.filter((household) => {
    const matchesName = household.head_name.toLowerCase().includes(search.value.trim().toLowerCase());
    const matchesPurok = purokFilter.value === "" || household.purok === purokFilter.value;
    return matchesName && matchesPurok;
  })
);
</script>

<template>
  <div class="page">
    <div class="page-head">
      <div>
        <h2 class="page-subtitle">Household registry and safety status.</h2>
      </div>
      <button type="button" class="btn btn-primary" @click="showToast('Adding households needs the backend. Nothing was saved.')">
        Add household
      </button>
    </div>

    <section class="card">
      <div class="filters">
        <div class="field">
          <label for="household-search">Search by head of household</label>
          <input id="household-search" v-model="search" type="search" placeholder="Type a name…">
        </div>
        <div class="field">
          <label for="household-purok">Purok</label>
          <select id="household-purok" v-model="purokFilter">
            <option value="">All Puroks</option>
            <option v-for="purok in puroks" :key="purok" :value="purok">{{ purok }}</option>
          </select>
        </div>
      </div>

      <p class="list-meta" role="status">{{ shownHouseholds.length }} of {{ households.length }} households shown</p>

      <div class="table-wrap">
        <table class="table">
          <thead>
            <tr>
              <th>Head of household</th>
              <th>Purok</th>
              <th>Members</th>
              <th>Safety</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="household in shownHouseholds" :key="household.id">
              <td><strong>{{ household.head_name }}</strong></td>
              <td>{{ household.purok }}</td>
              <td>{{ household.members }}</td>
              <td><StatusBadge :value="household.safety" /></td>
            </tr>
            <tr v-if="shownHouseholds.length === 0">
              <td colspan="4" class="empty-cell">No households match your search.</td>
            </tr>
          </tbody>
        </table>
      </div>
    </section>
  </div>
</template>

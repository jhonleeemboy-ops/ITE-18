<script setup>
// Resident portal: My Household
import StatusBadge from "../../components/StatusBadge.vue";
import { currentResident, findHousehold } from "../../data/sample-data.js";
import { showToast } from "../../composables/toast.js";

const household = findHousehold(currentResident.household_id);
</script>

<template>
  <div class="page">
    <div class="page-head">
      <div>
        <p class="eyebrow">Household ID · AMP-{{ String(household.id).padStart(4, "0") }} (sample)</p>
        <h1>My household profile</h1>
        <p>Keep this information updated so responders can give the right help.</p>
      </div>
      <button type="button" class="btn btn-primary" @click="showToast('Saving changes needs the backend. Nothing was changed.')">
        Update profile
      </button>
    </div>

    <section class="card">
      <dl class="details">
        <div>
          <dt>Head of household</dt>
          <dd>{{ household.head_name }}</dd>
        </div>
        <div>
          <dt>Mobile number</dt>
          <dd>{{ currentResident.mobile_number }}</dd>
        </div>
        <div>
          <dt>Home address</dt>
          <dd>{{ household.purok }}, Barangay Ampayon</dd>
        </div>
        <div>
          <dt>Household members</dt>
          <dd>{{ household.members }}</dd>
        </div>
        <div>
          <dt>Safety status</dt>
          <dd><StatusBadge :value="household.safety" /></dd>
        </div>
      </dl>
    </section>

    <section class="card section-gap">
      <div class="card-head">
        <h2>Family members</h2>
      </div>
      <p class="muted">
        The member list (names, ages, seniors, PWD, infants) will appear here once the database has a
        household members table. It was suggested in the ERD review.
      </p>
    </section>
  </div>
</template>

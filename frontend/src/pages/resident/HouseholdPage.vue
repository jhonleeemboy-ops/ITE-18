<script setup>
// Resident portal: My Household
// Connected to the Laravel API: GET /me and PATCH /households/{id}/safety.
import { onMounted, ref } from "vue";
import StatusBadge from "../../components/StatusBadge.vue";
import api from "../../api";
import { showToast } from "../../composables/toast.js";
import { formatMobile, mapHousehold, toApiValue } from "../../utils/mappers.js";

const user = ref(null);
const household = ref(null);
const loading = ref(true);
const error = ref("");
const saving = ref(false);

const safetyOptions = ["safe", "evacuated", "needs help"];

onMounted(async () => {
  try {
    const { data } = await api.get("/me");
    user.value = data;
    household.value = data.household ? mapHousehold(data.household) : null;
  } catch (e) {
    error.value = "Could not load your household. Please try again.";
  } finally {
    loading.value = false;
  }
});

// The resident can update only their own household's safety status
async function updateSafety(event) {
  const value = event.target.value;
  saving.value = true;
  try {
    const { data } = await api.patch(`/households/${household.value.id}/safety`, {
      safety: toApiValue(value),
    });
    household.value = mapHousehold(data);
    showToast("Safety status updated.");
  } catch (e) {
    showToast("Could not update your safety status. Please try again.");
    event.target.value = household.value.safety; // put the old value back
  } finally {
    saving.value = false;
  }
}
</script>

<template>
  <div class="page">
    <p v-if="loading" role="status">Loading your household…</p>
    <p v-else-if="error" class="field-error" role="alert">{{ error }}</p>
    <p v-else-if="!household">No household is linked to your account. Please contact the barangay.</p>

    <template v-else>
      <div class="page-head">
        <div>
          <p class="eyebrow">Household ID · AMP-{{ String(household.id).padStart(4, "0") }}</p>
          <h1>My household profile</h1>
          <p>Keep this information updated so responders can give the right help.</p>
        </div>
        <button type="button" class="btn btn-primary" @click="showToast('Editing the profile is not available yet. Nothing was changed.')">
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
            <dd>{{ formatMobile(user.mobile_number) }}</dd>
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
            <dd>
              <StatusBadge :value="household.safety" />
              <select
                :value="household.safety" :disabled="saving" aria-label="Change safety status"
                @change="updateSafety"
              >
                <option v-for="option in safetyOptions" :key="option" :value="option">{{ option }}</option>
              </select>
            </dd>
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
    </template>
  </div>
</template>
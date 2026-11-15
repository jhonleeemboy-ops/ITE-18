<script setup>
// Staff: Household profiles (HOUSEHOLDS table in the database)
// Connected to the Laravel API: GET /households and POST /households (staff only).
import { computed, onMounted, reactive, ref } from "vue";
import StatusBadge from "../../components/StatusBadge.vue";
import api from "../../api";
import { showToast } from "../../composables/toast.js";
import { mapHousehold, purokNumber, toApiValue } from "../../utils/mappers.js";
import { puroks } from "../../utils/options.js";
import { focusFirstError } from "../../utils/validation.js";

const households = ref([]);
const loading = ref(true);
const error = ref("");

const search = ref("");
const purokFilter = ref("");

// ----- Add household form (for households listed by barangay personnel) -----
const showForm = ref(false);
const saving = ref(false);
const form = reactive({ headName: "", purok: "", members: "", safety: "safe" });
const errors = reactive({});
const safetyOptions = ["safe", "evacuated", "needs help"];

function clearError(fieldId) {
  errors[fieldId] = "";
}

async function addHousehold() {
  errors["household-head"] = form.headName.trim() === "" ? "Enter the name of the head of household." : "";
  errors["household-new-purok"] = form.purok === "" ? "Choose the Purok." : "";
  errors["household-members"] = Number(form.members) >= 1 ? "" : "Enter the number of household members.";

  if (focusFirstError(errors, ["household-head", "household-new-purok", "household-members"])) return;

  saving.value = true;
  try {
    const { data } = await api.post("/households", {
      head_name: form.headName.trim(),
      purok: purokNumber(form.purok), // "Purok 3" -> 3
      members: Number(form.members),
      safety: toApiValue(form.safety), // "needs help" -> "needs_help"
    });
    households.value.push(mapHousehold(data));
    form.headName = "";
    form.purok = "";
    form.members = "";
    form.safety = "safe";
    showForm.value = false;
    showToast("Household added.");
  } catch (e) {
    const apiErrors = e.response?.data?.errors;
    if (apiErrors?.head_name) errors["household-head"] = apiErrors.head_name[0];
    else if (apiErrors?.purok) errors["household-new-purok"] = apiErrors.purok[0];
    else if (apiErrors?.members) errors["household-members"] = apiErrors.members[0];
    else showToast("The household was NOT saved. Please try again.");
  } finally {
    saving.value = false;
  }
}

onMounted(async () => {
  try {
    const { data } = await api.get("/households");
    households.value = data.map(mapHousehold);
  } catch (e) {
    error.value = "Could not load households. Please try again.";
  } finally {
    loading.value = false;
  }
});

// The table only shows households that match the search box and the Purok filter
const shownHouseholds = computed(() =>
  households.value.filter((household) => {
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
      <button type="button" class="btn btn-primary" :aria-expanded="showForm" @click="showForm = !showForm">
        {{ showForm ? "Close" : "Add household" }}
      </button>
    </div>

    <section v-if="showForm" class="card section-gap-bottom">
      <div class="card-head">
        <h2>New household</h2>
        <p>For households listed by barangay personnel</p>
      </div>

      <form novalidate @submit.prevent="addHousehold">
        <div class="filters">
          <div class="field">
            <label for="household-head">Head of household</label>
            <input
              id="household-head" v-model="form.headName" type="text" aria-describedby="household-head-error"
              :aria-invalid="errors['household-head'] ? 'true' : null" @input="clearError('household-head')"
            >
            <p v-if="errors['household-head']" id="household-head-error" class="field-error">{{ errors["household-head"] }}</p>
          </div>
          <div class="field">
            <label for="household-new-purok">Purok</label>
            <select
              id="household-new-purok" v-model="form.purok" aria-describedby="household-new-purok-error"
              :aria-invalid="errors['household-new-purok'] ? 'true' : null" @change="clearError('household-new-purok')"
            >
              <option value="">Select the Purok</option>
              <option v-for="purok in puroks" :key="purok" :value="purok">{{ purok }}</option>
            </select>
            <p v-if="errors['household-new-purok']" id="household-new-purok-error" class="field-error">{{ errors["household-new-purok"] }}</p>
          </div>
        </div>
        <div class="filters">
          <div class="field">
            <label for="household-members">Number of members</label>
            <input
              id="household-members" v-model="form.members" type="number" min="1" inputmode="numeric"
              aria-describedby="household-members-error"
              :aria-invalid="errors['household-members'] ? 'true' : null" @input="clearError('household-members')"
            >
            <p v-if="errors['household-members']" id="household-members-error" class="field-error">{{ errors["household-members"] }}</p>
          </div>
          <div class="field">
            <label for="household-safety">Safety status</label>
            <select id="household-safety" v-model="form.safety">
              <option v-for="option in safetyOptions" :key="option" :value="option">{{ option }}</option>
            </select>
          </div>
        </div>

        <button type="submit" class="btn btn-primary" :disabled="saving">Save household</button>
      </form>
    </section>

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

      <p v-if="loading" role="status">Loading households…</p>
      <p v-else-if="error" class="field-error" role="alert">{{ error }}</p>

      <template v-else>
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
      </template>
    </section>
  </div>
</template>
<script setup>
// Staff: Evacuation centers (EVACUATION_CENTERS table in the database)
// Connected to the Laravel API:
//   GET   /evacuation-centers                 list of centers
//   POST  /evacuation-centers                 add a center
//   PATCH /evacuation-centers/{id}/occupants  people arriving or leaving
import { onMounted, reactive, ref } from "vue";
import OccupancyMeter from "../../components/OccupancyMeter.vue";
import StatusBadge from "../../components/StatusBadge.vue";
import api from "../../api";
import { showToast } from "../../composables/toast.js";
import { mapCenter, purokNumber } from "../../utils/mappers.js";
import { puroks } from "../../utils/options.js";
import { focusFirstError } from "../../utils/validation.js";

const centers = ref([]);
const loading = ref(true);
const loadError = ref("");

// ----- Add center form -----
const showForm = ref(false);
const saving = ref(false);
const form = reactive({ name: "", capacity: "", puroks: [] });
const errors = reactive({});

// ----- People arriving / leaving: one number box per center -----
const people = reactive({}); // { centerId: number typed }
const countErrors = reactive({}); // { centerId: error message }
const busyCenter = ref(null); // id of the center being saved

onMounted(async () => {
  try {
    const { data } = await api.get("/evacuation-centers");
    centers.value = data.map(mapCenter);
  } catch (e) {
    loadError.value = "Could not load the evacuation centers. Please try again.";
  } finally {
    loading.value = false;
  }
});

function clearError(fieldId) {
  errors[fieldId] = "";
}

async function addCenter() {
  errors["center-name"] = form.name.trim() === "" ? "Enter the name of the center." : "";
  errors["center-capacity"] = Number(form.capacity) >= 1 ? "" : "Enter how many people the center can hold.";

  if (focusFirstError(errors, ["center-name", "center-capacity"])) return;

  saving.value = true;
  try {
    const { data } = await api.post("/evacuation-centers", {
      name: form.name.trim(),
      capacity: Number(form.capacity),
      puroks: form.puroks.map(purokNumber), // "Purok 3" -> 3
    });
    centers.value.push(mapCenter(data));
    form.name = "";
    form.capacity = "";
    form.puroks = [];
    showForm.value = false;
    showToast("Evacuation center added.");
  } catch (e) {
    const apiErrors = e.response?.data?.errors;
    if (apiErrors?.name) errors["center-name"] = apiErrors.name[0];
    else if (apiErrors?.capacity) errors["center-capacity"] = apiErrors.capacity[0];
    else showToast("The center was NOT saved. Please try again.");
  } finally {
    saving.value = false;
  }
}

// action is "check_in" (people arriving) or "check_out" (people leaving)
async function updateOccupants(center, action) {
  const count = Number(people[center.id]);

  if (!Number.isInteger(count) || count < 1) {
    countErrors[center.id] = "Enter the number of people (1 or more).";
    return;
  }

  countErrors[center.id] = "";
  busyCenter.value = center.id;
  try {
    const { data } = await api.patch(`/evacuation-centers/${center.id}/occupants`, { action, people: count });
    // replace the old center in the list with the updated one
    const index = centers.value.findIndex((item) => item.id === center.id);
    centers.value[index] = mapCenter(data);
    people[center.id] = "";
    showToast(action === "check_in" ? `${count} checked in.` : `${count} checked out.`);
  } catch (e) {
    countErrors[center.id] = e.response?.data?.errors?.people?.[0] ?? "Not saved. Please try again.";
  } finally {
    busyCenter.value = null;
  }
}
</script>

<template>
  <div class="page">
    <div class="page-head">
      <div>
        <h2 class="page-subtitle">Capacity and status of each center.</h2>
      </div>
      <button type="button" class="btn btn-primary" :aria-expanded="showForm" @click="showForm = !showForm">
        {{ showForm ? "Close" : "Add center" }}
      </button>
    </div>

    <section v-if="showForm" class="card section-gap-bottom">
      <div class="card-head">
        <h2>New evacuation center</h2>
      </div>

      <form novalidate @submit.prevent="addCenter">
        <div class="filters">
          <div class="field">
            <label for="center-name">Name of the center</label>
            <input
              id="center-name" v-model="form.name" type="text" aria-describedby="center-name-error"
              :aria-invalid="errors['center-name'] ? 'true' : null" @input="clearError('center-name')"
            >
            <p v-if="errors['center-name']" id="center-name-error" class="field-error">{{ errors["center-name"] }}</p>
          </div>
          <div class="field">
            <label for="center-capacity">Capacity (people)</label>
            <input
              id="center-capacity" v-model="form.capacity" type="number" min="1" inputmode="numeric"
              aria-describedby="center-capacity-error"
              :aria-invalid="errors['center-capacity'] ? 'true' : null" @input="clearError('center-capacity')"
            >
            <p v-if="errors['center-capacity']" id="center-capacity-error" class="field-error">{{ errors["center-capacity"] }}</p>
          </div>
        </div>

        <fieldset class="check-group">
          <legend>Puroks served (optional)</legend>
          <label v-for="purok in puroks" :key="purok" class="check-item">
            <input v-model="form.puroks" type="checkbox" :value="purok"> {{ purok }}
          </label>
        </fieldset>

        <button type="submit" class="btn btn-primary" :disabled="saving">Save center</button>
      </form>
    </section>

    <p v-if="loading" role="status">Loading evacuation centers…</p>
    <p v-else-if="loadError" class="field-error" role="alert">{{ loadError }}</p>
    <p v-else-if="centers.length === 0" class="list-meta">No evacuation centers yet. Use “Add center” to create one.</p>

    <div v-else class="grid grid-2">
      <section v-for="center in centers" :key="center.id" class="card">
        <div class="center-row">
          <h2 class="card-title">{{ center.name }}</h2>
          <StatusBadge :value="center.status" />
        </div>
        <p class="list-meta">
          {{ center.serves.length ? "Serves " + center.serves.join(", ") : "No Puroks assigned yet" }}
        </p>
        <OccupancyMeter :occupants="center.occupants" :capacity="center.capacity" :label="center.name + ' occupancy'" />

        <div class="field count-field">
          <label :for="'people-' + center.id">People arriving or leaving</label>
          <input
            :id="'people-' + center.id" v-model="people[center.id]" type="number" min="1" inputmode="numeric"
            placeholder="Number of people" :aria-describedby="'people-error-' + center.id"
            :aria-invalid="countErrors[center.id] ? 'true' : null" @input="countErrors[center.id] = ''"
          >
          <p v-if="countErrors[center.id]" :id="'people-error-' + center.id" class="field-error">{{ countErrors[center.id] }}</p>
        </div>
        <div class="button-row no-margin">
          <button type="button" class="btn btn-primary" :disabled="busyCenter === center.id" @click="updateOccupants(center, 'check_in')">
            Check in
          </button>
          <button type="button" class="btn btn-outline" :disabled="busyCenter === center.id" @click="updateOccupants(center, 'check_out')">
            Check out
          </button>
        </div>
      </section>
    </div>
  </div>
</template>

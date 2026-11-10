<script setup>
// Resident portal: Assistance requests (saved as INCIDENTS in the database)
// Connected to the Laravel API: GET /incidents and POST /incidents.
import { onMounted, reactive, ref } from "vue";
import StatusBadge from "../../components/StatusBadge.vue";
import api from "../../api";
import { incidentTypes } from "../../data/sample-data.js"; // only the type labels
import { formatDateTime } from "../../utils/format.js";
import { mapIncident } from "../../utils/mappers.js";
import { focusFirstError } from "../../utils/validation.js";

const myRequests = ref([]);
const loading = ref(true);
const loadError = ref("");
const submitting = ref(false);

const form = reactive({ type: "", description: "" });
const errors = reactive({});
const status = ref("");

function clearError(fieldId) {
  errors[fieldId] = "";
  status.value = "";
}

onMounted(async () => {
  try {
    // residents only receive their own requests, newest first
    const { data } = await api.get("/incidents");
    myRequests.value = data.map(mapIncident);
  } catch (e) {
    loadError.value = "Could not load your requests. Please try again.";
  } finally {
    loading.value = false;
  }
});

async function submit() {
  errors["request-type"] = form.type === "" ? "Choose the type of help you need." : "";
  errors["request-description"] = form.description.trim().length < 10 ? "Tell us a bit more (at least 10 characters)." : "";

  if (focusFirstError(errors, ["request-type", "request-description"])) return;

  submitting.value = true;
  status.value = "";
  try {
    const { data } = await api.post("/incidents", {
      type: form.type,
      description: form.description.trim(),
    });
    myRequests.value.unshift(mapIncident(data));
    form.type = "";
    form.description = "";
    status.value = "Your request was sent to the barangay.";
  } catch (e) {
    const apiErrors = e.response?.data?.errors;
    if (apiErrors?.type) errors["request-type"] = apiErrors.type[0];
    else if (apiErrors?.description) errors["request-description"] = apiErrors.description[0];
    else if (!e.response) status.value = "Cannot reach the server. Your request was NOT sent. Please try again.";
    else status.value = "Your request was NOT sent. Please try again.";
  } finally {
    submitting.value = false;
  }
}
</script>

<template>
  <div class="page">
    <div class="page-head">
      <div>
        <p class="eyebrow">Barangay support</p>
        <h1>Request assistance</h1>
        <p>Requests go to the Barangay Ampayon disaster response team.</p>
      </div>
    </div>

    <div class="grid grid-2">
      <section class="card">
        <div class="card-head">
          <h2>New request</h2>
        </div>

        <form novalidate @submit.prevent="submit">
          <div class="field">
            <label for="request-type">Type of assistance</label>
            <select
              id="request-type" v-model="form.type" aria-describedby="request-type-error"
              :aria-invalid="errors['request-type'] ? 'true' : null" @change="clearError('request-type')"
            >
              <option value="">Choose one</option>
              <option v-for="(label, key) in incidentTypes" :key="key" :value="key">{{ label }}</option>
            </select>
            <p v-if="errors['request-type']" id="request-type-error" class="field-error">{{ errors["request-type"] }}</p>
          </div>

          <div class="field">
            <label for="request-description">Tell us what you need</label>
            <textarea
              id="request-description" v-model="form.description" rows="4"
              placeholder="Example: We need drinking water for 6 people."
              aria-describedby="request-description-error"
              :aria-invalid="errors['request-description'] ? 'true' : null" @input="clearError('request-description')"
            ></textarea>
            <p v-if="errors['request-description']" id="request-description-error" class="field-error">{{ errors["request-description"] }}</p>
          </div>

          <button type="submit" class="btn btn-primary" :disabled="submitting">Submit request</button>
          <p v-if="status" class="form-status" role="status">{{ status }}</p>
        </form>

        <p class="note">For life-threatening emergencies, call <strong>911</strong> right away.</p>
      </section>

      <section class="card">
        <div class="card-head">
          <h2>Your requests</h2>
          <p>{{ myRequests.length }} total</p>
        </div>

        <p v-if="loading" role="status">Loading your requests…</p>
        <p v-else-if="loadError" class="field-error" role="alert">{{ loadError }}</p>
        <p v-else-if="!myRequests.length" class="list-meta">You have not sent any requests yet.</p>

        <ul v-else class="list">
          <li v-for="request in myRequests" :key="request.id">
            <p class="list-title">{{ incidentTypes[request.type] }} <StatusBadge :value="request.status" /></p>
            <p class="list-text">{{ request.description }}</p>
            <p class="list-meta">{{ formatDateTime(request.created_at) }}</p>
          </li>
        </ul>
      </section>
    </div>
  </div>
</template>
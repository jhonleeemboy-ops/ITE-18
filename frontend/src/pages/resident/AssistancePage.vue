<script setup>
// Resident portal: Assistance requests (saved as INCIDENTS in the ERD)
import { reactive, ref } from "vue";
import StatusBadge from "../../components/StatusBadge.vue";
import { currentResident, incidents, incidentTypes } from "../../data/sample-data.js";
import { formatDateTime } from "../../utils/format.js";
import { focusFirstError } from "../../utils/validation.js";

// This resident's requests (a copy, so new demo requests only live on this page)
const myRequests = ref(incidents.filter((incident) => incident.reporter_id === currentResident.id));

const form = reactive({ type: "", description: "" });
const errors = reactive({});
const status = ref("");

function clearError(fieldId) {
  errors[fieldId] = "";
  status.value = "";
}

function submit() {
  errors["request-type"] = form.type === "" ? "Choose the type of help you need." : "";
  errors["request-description"] = form.description.trim().length < 10 ? "Tell us a bit more (at least 10 characters)." : "";

  if (focusFirstError(errors, ["request-type", "request-description"])) return;

  // DEMO: add it to the list on this page only
  myRequests.value.unshift({
    id: Date.now(),
    type: form.type,
    status: "pending",
    description: form.description.trim(),
    created_at: new Date().toISOString(),
  });
  form.type = "";
  form.description = "";
  status.value = "Demo: your request was added below, but it was NOT sent to the barangay. Sending needs the backend.";
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

          <button type="submit" class="btn btn-primary">Submit request</button>
          <p v-if="status" class="form-status" role="status">{{ status }}</p>
        </form>

        <p class="note">For life-threatening emergencies, call <strong>911</strong> right away.</p>
      </section>

      <section class="card">
        <div class="card-head">
          <h2>Your requests</h2>
          <p>{{ myRequests.length }} total</p>
        </div>
        <ul class="list">
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

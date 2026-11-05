<script setup>
// Staff: SMS alerts
// DEMO: no SMS is sent. The SMS provider isn't chosen or connected yet.
import { computed, reactive, ref } from "vue";
import { puroks, smsLogs } from "../../data/sample-data.js";
import { formatDateTime } from "../../utils/format.js";
import { focusFirstError } from "../../utils/validation.js";

const form = reactive({ title: "", message: "", sendTo: "all" });
const errors = reactive({});
const status = ref("");

// One SMS fits 160 characters; longer messages count as more than one SMS
const smsCount = computed(() => Math.max(1, Math.ceil(form.message.length / 160)));

function clearError(fieldId) {
  errors[fieldId] = "";
  status.value = "";
}

function submit() {
  errors["sms-title"] = form.title.trim() === "" ? "Enter a title for this alert." : "";
  errors["sms-message"] = form.message.trim().length < 10 ? "Write the alert message (at least 10 characters)." : "";

  if (focusFirstError(errors, ["sms-title", "sms-message"])) return;

  // Honest demo message: nothing was sent
  status.value = "Demo: no SMS was sent. The SMS provider isn't connected yet.";
}
</script>

<template>
  <div class="page">
    <div class="page-head">
      <div>
        <h2 class="page-subtitle">Send verified emergency messages to registered residents.</h2>
      </div>
    </div>

    <div class="grid grid-2">
      <section class="card">
        <div class="card-head">
          <h2>Compose alert</h2>
        </div>

        <form novalidate @submit.prevent="submit">
          <div class="field">
            <label for="sms-title">Alert title</label>
            <input
              id="sms-title" v-model="form.title" type="text" placeholder="Example: Orange Rainfall Advisory"
              aria-describedby="sms-title-error" :aria-invalid="errors['sms-title'] ? 'true' : null"
              @input="clearError('sms-title')"
            >
            <p v-if="errors['sms-title']" id="sms-title-error" class="field-error">{{ errors["sms-title"] }}</p>
          </div>

          <div class="field">
            <label for="sms-message">Message</label>
            <textarea
              id="sms-message" v-model="form.message" rows="4"
              placeholder="Keep it short and clear. Say what to do and where to go."
              aria-describedby="sms-message-count sms-message-error"
              :aria-invalid="errors['sms-message'] ? 'true' : null" @input="clearError('sms-message')"
            ></textarea>
            <p id="sms-message-count" class="field-hint">
              {{ form.message.length }} characters · {{ smsCount }} SMS per person
            </p>
            <p v-if="errors['sms-message']" id="sms-message-error" class="field-error">{{ errors["sms-message"] }}</p>
          </div>

          <div class="field">
            <label for="sms-send-to">Send to</label>
            <select id="sms-send-to" v-model="form.sendTo">
              <option value="all">All registered residents</option>
              <option v-for="purok in puroks" :key="purok" :value="purok">Residents of {{ purok }}</option>
            </select>
          </div>

          <button type="submit" class="btn btn-primary">Send SMS alert</button>
          <p v-if="status" class="form-status" role="status">{{ status }}</p>
        </form>
      </section>

      <section class="card">
        <div class="card-head">
          <h2>Recent alerts sent</h2>
          <p>Sample log</p>
        </div>
        <div class="table-wrap">
          <table class="table">
            <thead>
              <tr>
                <th>Alert</th>
                <th>Sent</th>
                <th>Failed</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="log in smsLogs" :key="log.id">
                <td>
                  <strong>{{ log.title }}</strong>
                  <span class="cell-note">{{ formatDateTime(log.sent_at) }} · {{ log.recipients }} recipients</span>
                </td>
                <td>{{ log.sent }}</td>
                <td>{{ log.failed }}</td>
              </tr>
            </tbody>
          </table>
        </div>
        <p class="note">Failed messages will be retried or flagged once the SMS provider is connected.</p>
      </section>
    </div>
  </div>
</template>

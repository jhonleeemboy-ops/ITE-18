<script setup>
// Staff: SMS alerts (ADVISORIES and SMS_LOGS tables)
// Connected to the Laravel API:
//   GET    /sms-alerts       the log of alerts
//   POST   /sms-alerts       saves the alert as an advisory, then hands it to the SMS gateway
//   DELETE /sms-alerts/{id}  deletes an alert (its advisory and its log row)
//
// The backend sends through TextBee (an Android phone with a SIM) when TEXTBEE_API_KEY
// is set in the backend .env. Without the key, alerts are only saved and queued.
// IMPORTANT: this page must never say an SMS was sent when it was only queued or failed.
// "Sent" here means the gateway accepted the message; it is not proof of delivery.
import { computed, onMounted, reactive, ref } from "vue";
import api from "../../api";
import { formatDateTime } from "../../utils/format.js";
import { mapSmsLog, purokNumber } from "../../utils/mappers.js";
import { puroks } from "../../utils/options.js";
import { focusFirstError } from "../../utils/validation.js";

const severityLabels = {
  info: "Information",
  yellow: "Yellow (be alert)",
  orange: "Orange (prepare)",
  red: "Red (act now)",
};

const logs = ref([]);
const loading = ref(true);
const loadError = ref("");

const form = reactive({ title: "", message: "", severity: "info", sendTo: "all" });
const errors = reactive({});
const status = ref("");
const statusIsError = ref(false);
const sending = ref(false);
const deletingId = ref(null); // id of the alert being deleted

// One SMS fits 160 characters; longer messages count as more than one SMS
const smsCount = computed(() => Math.max(1, Math.ceil(form.message.length / 160)));

// Keep created_at too, so a queued alert (no sent_at yet) can still show its time
function toLog(row) {
  return { ...mapSmsLog(row), created_at: row.created_at };
}

onMounted(async () => {
  try {
    const { data } = await api.get("/sms-alerts");
    logs.value = data.map(toLog);
  } catch (e) {
    loadError.value = "Could not load the alert log. Please try again.";
  } finally {
    loading.value = false;
  }
});

// Status of one log row: what really happened to the SMS
function logStatus(log) {
  if (log.sent > 0 && log.failed === 0) return { label: "Sent to gateway", color: "badge-green" };
  if (log.sent > 0) return { label: "Partly sent", color: "badge-amber" };
  if (log.failed > 0) return { label: "Failed", color: "badge-red" };
  return { label: "Queued", color: "badge-gray" };
}

async function deleteAlert(log) {
  const ok = window.confirm(
    `Delete "${log.title}"?\n\nIt will be removed from this log and from the residents' portal. ` +
      "Text messages already sent cannot be recalled."
  );
  if (!ok) return;

  deletingId.value = log.id;
  try {
    await api.delete(`/sms-alerts/${log.id}`);
    logs.value = logs.value.filter((item) => item.id !== log.id);
    status.value = `"${log.title}" was deleted.`;
    statusIsError.value = false;
  } catch (e) {
    status.value = "The alert was NOT deleted. Please try again.";
    statusIsError.value = true;
  } finally {
    deletingId.value = null;
  }
}

function clearError(fieldId) {
  errors[fieldId] = "";
  status.value = "";
}

async function submit() {
  errors["sms-title"] = form.title.trim() === "" ? "Enter a title for this alert." : "";
  errors["sms-message"] = form.message.trim().length < 10 ? "Write the alert message (at least 10 characters)." : "";

  if (focusFirstError(errors, ["sms-title", "sms-message"])) return;

  sending.value = true;
  status.value = "";
  statusIsError.value = false;
  try {
    const { data } = await api.post("/sms-alerts", {
      title: form.title.trim(),
      message: form.message.trim(),
      severity: form.severity,
      send_to: form.sendTo === "all" ? "all" : purokNumber(form.sendTo), // "Purok 3" -> 3
    });

    // show the new alert at the top of the log
    logs.value.unshift(toLog({ ...data.sms_log, advisory: data.advisory }));

    // Say exactly what happened to the SMS part
    const log = data.sms_log;
    const people = `${log.recipients} ${log.recipients === 1 ? "recipient" : "recipients"}`;
    const saved = "Alert saved and posted as an advisory. ";

    if (log.recipients === 0) {
      status.value = saved + "No SMS was sent: no registered resident matches this choice.";
    } else if (!data.sms.configured) {
      status.value = saved + `Queued for ${people}. No SMS was sent because the SMS gateway is not set up.`;
    } else if (log.failed === 0) {
      status.value = saved + `SMS handed to the gateway for ${people}.`;
    } else {
      statusIsError.value = true;
      status.value = saved + `SMS handed to the gateway for ${log.sent} of ${people}; ${log.failed} failed. ${data.sms.error ?? ""}`;
    }

    form.title = "";
    form.message = "";
    form.severity = "info";
    form.sendTo = "all";
  } catch (e) {
    statusIsError.value = true;
    const apiErrors = e.response?.data?.errors;
    if (apiErrors?.title) errors["sms-title"] = apiErrors.title[0];
    else if (apiErrors?.message) errors["sms-message"] = apiErrors.message[0];
    else if (!e.response) status.value = "Cannot reach the server. The alert was NOT saved. Please try again.";
    else status.value = "The alert was NOT saved. Please try again.";
  } finally {
    sending.value = false;
  }
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
              id="sms-message" v-model="form.message" rows="4" maxlength="1000"
              placeholder="Keep it short and clear. Say what to do and where to go."
              aria-describedby="sms-message-count sms-message-error"
              :aria-invalid="errors['sms-message'] ? 'true' : null" @input="clearError('sms-message')"
            ></textarea>
            <p id="sms-message-count" class="field-hint">
              {{ form.message.length }} characters · {{ smsCount }} SMS per person
            </p>
            <p v-if="errors['sms-message']" id="sms-message-error" class="field-error">{{ errors["sms-message"] }}</p>
          </div>

          <div class="filters">
            <div class="field">
              <label for="sms-send-to">Send to</label>
              <select id="sms-send-to" v-model="form.sendTo">
                <option value="all">All registered residents</option>
                <option v-for="purok in puroks" :key="purok" :value="purok">Residents of {{ purok }}</option>
              </select>
            </div>
            <div class="field">
              <label for="sms-severity">Level</label>
              <select id="sms-severity" v-model="form.severity">
                <option v-for="(label, key) in severityLabels" :key="key" :value="key">{{ label }}</option>
              </select>
            </div>
          </div>

          <button type="submit" class="btn btn-primary" :disabled="sending">{{ sending ? "Sending…" : "Send alert" }}</button>
          <p v-if="status" class="form-status" :class="{ 'is-error': statusIsError }" role="status">{{ status }}</p>
        </form>

        <p class="note">
          Every alert is saved and shown to residents in the portal as an advisory. SMS goes out through the
          barangay's gateway phone, so that phone must be on and online. Not every resident has a phone:
          keep using megaphones, the rescue vehicle and Purok officers.
        </p>
      </section>

      <section class="card">
        <div class="card-head">
          <h2>Alert log</h2>
          <p>{{ logs.length }} alerts</p>
        </div>

        <p v-if="loading" role="status">Loading the alert log…</p>
        <p v-else-if="loadError" class="field-error" role="alert">{{ loadError }}</p>

        <div v-else class="table-wrap">
          <table class="table">
            <thead>
              <tr>
                <th>Alert</th>
                <th>Status</th>
                <th>Sent</th>
                <th>Failed</th>
                <th aria-label="Actions"></th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="log in logs" :key="log.id">
                <td>
                  <strong>{{ log.title }}</strong>
                  <span class="cell-note">{{ formatDateTime(log.sent_at ?? log.created_at) }} · {{ log.recipients }} {{ log.recipients === 1 ? "recipient" : "recipients" }}</span>
                </td>
                <td><span class="badge" :class="logStatus(log).color">{{ logStatus(log).label }}</span></td>
                <td>{{ log.sent }}</td>
                <td>{{ log.failed }}</td>
                <td>
                  <button
                    type="button" class="btn btn-small" :disabled="deletingId === log.id"
                    :aria-label="'Delete ' + log.title" @click="deleteAlert(log)"
                  >
                    {{ deletingId === log.id ? "Deleting…" : "Delete" }}
                  </button>
                </td>
              </tr>
              <tr v-if="logs.length === 0">
                <td colspan="5" class="empty-cell">No alerts yet.</td>
              </tr>
            </tbody>
          </table>
        </div>
      </section>
    </div>
  </div>
</template>
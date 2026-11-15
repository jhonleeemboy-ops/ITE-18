<script setup>
// Staff: Relief distribution (RELIEF_ITEMS and RELIEF_DISTRIBUTIONS tables)
// Connected to the Laravel API:
//   GET  /relief-items            stock of each item
//   GET  /relief-distributions    latest 100 releases
//   POST /relief-distributions    record a release (the API also reduces the stock)
//   GET  /households              list for the "Household" dropdown
import { onMounted, reactive, ref } from "vue";
import api from "../../api";
import { showToast } from "../../composables/toast.js";
import { formatDateTime, formatNumber } from "../../utils/format.js";
import { mapDistribution, mapHousehold } from "../../utils/mappers.js";
import { focusFirstError } from "../../utils/validation.js";

const items = ref([]);
const distributions = ref([]);
const households = ref([]);
const loading = ref(true);
const loadError = ref("");

// ----- New distribution form -----
const showForm = ref(false);
const saving = ref(false);
const form = reactive({ householdId: "", itemId: "", quantity: "" });
const errors = reactive({});

onMounted(async () => {
  try {
    const [itemList, distributionList, householdList] = await Promise.all([
      api.get("/relief-items"),
      api.get("/relief-distributions"),
      api.get("/households"),
    ]);
    items.value = itemList.data;
    distributions.value = distributionList.data.map(mapDistribution);
    households.value = householdList.data.map(mapHousehold);
  } catch (e) {
    loadError.value = "Could not load the relief records. Please try again.";
  } finally {
    loading.value = false;
  }
});

function clearError(fieldId) {
  errors[fieldId] = "";
}

async function recordDistribution() {
  errors["relief-household"] = form.householdId === "" ? "Choose the household." : "";
  errors["relief-item"] = form.itemId === "" ? "Choose the relief item." : "";
  errors["relief-quantity"] = Number(form.quantity) >= 1 ? "" : "Enter a quantity of 1 or more.";

  if (focusFirstError(errors, ["relief-household", "relief-item", "relief-quantity"])) return;

  saving.value = true;
  try {
    const { data } = await api.post("/relief-distributions", {
      household_id: form.householdId,
      relief_item_id: form.itemId,
      quantity: Number(form.quantity),
    });
    distributions.value.unshift(mapDistribution(data)); // newest first

    // the API reduced the stock, so show the new number on this page too
    const item = items.value.find((entry) => entry.id === data.relief_item_id);
    if (item) item.quantity_in_stock = data.relief_item.quantity_in_stock;

    form.householdId = "";
    form.itemId = "";
    form.quantity = "";
    showForm.value = false;
    showToast("Distribution recorded.");
  } catch (e) {
    const apiErrors = e.response?.data?.errors;
    if (apiErrors?.household_id) errors["relief-household"] = apiErrors.household_id[0];
    else if (apiErrors?.relief_item_id) errors["relief-item"] = apiErrors.relief_item_id[0];
    else if (apiErrors?.quantity) errors["relief-quantity"] = apiErrors.quantity[0]; // e.g. not enough stock
    else showToast("The distribution was NOT saved. Please try again.");
  } finally {
    saving.value = false;
  }
}
</script>

<template>
  <div class="page">
    <div class="page-head">
      <div>
        <h2 class="page-subtitle">Relief stock and releases to households.</h2>
      </div>
      <button type="button" class="btn btn-primary" :aria-expanded="showForm" :disabled="loading || loadError !== ''" @click="showForm = !showForm">
        {{ showForm ? "Close" : "New distribution" }}
      </button>
    </div>

    <p v-if="loading" role="status">Loading relief records…</p>
    <p v-else-if="loadError" class="field-error" role="alert">{{ loadError }}</p>

    <template v-else>
      <section v-if="showForm" class="card section-gap-bottom">
        <div class="card-head">
          <h2>New distribution</h2>
        </div>

        <form novalidate @submit.prevent="recordDistribution">
          <div class="field">
            <label for="relief-household">Household</label>
            <select
              id="relief-household" v-model="form.householdId" aria-describedby="relief-household-error"
              :aria-invalid="errors['relief-household'] ? 'true' : null" @change="clearError('relief-household')"
            >
              <option value="">Choose a household</option>
              <option v-for="household in households" :key="household.id" :value="household.id">
                {{ household.head_name }} ({{ household.purok }})
              </option>
            </select>
            <p v-if="errors['relief-household']" id="relief-household-error" class="field-error">{{ errors["relief-household"] }}</p>
          </div>

          <div class="filters">
            <div class="field">
              <label for="relief-item">Relief item</label>
              <select
                id="relief-item" v-model="form.itemId" aria-describedby="relief-item-error"
                :aria-invalid="errors['relief-item'] ? 'true' : null" @change="clearError('relief-item')"
              >
                <option value="">Choose an item</option>
                <option v-for="item in items" :key="item.id" :value="item.id">
                  {{ item.name }} ({{ formatNumber(item.quantity_in_stock) }} {{ item.unit }} in stock)
                </option>
              </select>
              <p v-if="errors['relief-item']" id="relief-item-error" class="field-error">{{ errors["relief-item"] }}</p>
            </div>
            <div class="field">
              <label for="relief-quantity">Quantity</label>
              <input
                id="relief-quantity" v-model="form.quantity" type="number" min="1" inputmode="numeric"
                aria-describedby="relief-quantity-error"
                :aria-invalid="errors['relief-quantity'] ? 'true' : null" @input="clearError('relief-quantity')"
              >
              <p v-if="errors['relief-quantity']" id="relief-quantity-error" class="field-error">{{ errors["relief-quantity"] }}</p>
            </div>
          </div>

          <button type="submit" class="btn btn-primary" :disabled="saving">Record distribution</button>
        </form>
      </section>

      <div class="grid grid-4">
        <div v-for="item in items" :key="item.id" class="card stat">
          <p class="stat-value">{{ formatNumber(item.quantity_in_stock) }}</p>
          <p class="stat-label">{{ item.name }}</p>
          <p class="stat-note">In stock ({{ item.unit }})</p>
        </div>
      </div>
      <p v-if="items.length === 0" class="list-meta">No relief items in the database yet.</p>

      <section class="card section-gap">
        <div class="card-head">
          <h2>Recent distributions</h2>
          <p>{{ distributions.length }} records</p>
        </div>
        <div class="table-wrap">
          <table class="table">
            <thead>
              <tr>
                <th>Household</th>
                <th>Item</th>
                <th>Quantity</th>
                <th>Date</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="record in distributions" :key="record.id">
                <td>
                  <strong>{{ record.household?.head_name }}</strong>
                  <span class="cell-note">{{ record.household?.purok }}</span>
                </td>
                <td>{{ record.item?.name }}</td>
                <td>{{ record.quantity }} {{ record.item?.unit }}</td>
                <td>{{ formatDateTime(record.distributed_at) }}</td>
              </tr>
              <tr v-if="distributions.length === 0">
                <td colspan="4" class="empty-cell">No distributions recorded yet.</td>
              </tr>
            </tbody>
          </table>
        </div>
      </section>
    </template>
  </div>
</template>

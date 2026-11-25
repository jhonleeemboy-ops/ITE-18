<script setup>
// Staff: Relief distribution (RELIEF_ITEMS and RELIEF_DISTRIBUTIONS tables)
// Connected to the Laravel API:
//   GET   /relief-items              stock of each item
//   POST  /relief-items              add a new item with the quantity received
//   PATCH /relief-items/{id}/stock   add received goods to an item's stock
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

// which form is open: "" (none), "stock" or "distribution"
const openForm = ref("");

function toggleForm(name) {
  openForm.value = openForm.value === name ? "" : name;
}

// ----- New distribution form -----
const saving = ref(false);
const form = reactive({ householdId: "", itemId: "", quantity: "" });
const errors = reactive({});

// ----- Add stock form (goods received) -----
// itemId is an item's id, or "new" for an item that is not in the list yet
const savingStock = ref(false);
const stockForm = reactive({ itemId: "", name: "", unit: "", quantity: "" });

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
    openForm.value = "";
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

async function addStock() {
  const isNew = stockForm.itemId === "new";
  errors["stock-item"] = stockForm.itemId === "" ? "Choose the relief item." : "";
  errors["stock-name"] = isNew && stockForm.name.trim() === "" ? "Enter the name of the item." : "";
  errors["stock-unit"] = isNew && stockForm.unit.trim() === "" ? "Enter the unit, for example kg or packs." : "";
  errors["stock-quantity"] = Number.isInteger(Number(stockForm.quantity)) && Number(stockForm.quantity) >= 1
    ? "" : "Enter a quantity of 1 or more.";

  if (focusFirstError(errors, ["stock-item", "stock-name", "stock-unit", "stock-quantity"])) return;

  savingStock.value = true;
  try {
    const quantity = Number(stockForm.quantity);
    if (isNew) {
      const { data } = await api.post("/relief-items", {
        name: stockForm.name.trim(),
        unit: stockForm.unit.trim(),
        quantity,
      });
      items.value.push(data);
      items.value.sort((a, b) => a.name.localeCompare(b.name)); // same order as the API
      showToast(`${data.name} added.`);
    } else {
      const { data } = await api.patch(`/relief-items/${stockForm.itemId}/stock`, { quantity });
      const item = items.value.find((entry) => entry.id === data.id);
      if (item) item.quantity_in_stock = data.quantity_in_stock;
      showToast(`${formatNumber(quantity)} ${data.unit} of ${data.name} added to the stock.`);
    }

    stockForm.itemId = "";
    stockForm.name = "";
    stockForm.unit = "";
    stockForm.quantity = "";
    openForm.value = "";
  } catch (e) {
    const apiErrors = e.response?.data?.errors;
    if (apiErrors?.name) errors["stock-name"] = apiErrors.name[0]; // e.g. item already in the list
    else if (apiErrors?.unit) errors["stock-unit"] = apiErrors.unit[0];
    else if (apiErrors?.quantity) errors["stock-quantity"] = apiErrors.quantity[0];
    else showToast("The stock was NOT saved. Please try again.");
  } finally {
    savingStock.value = false;
  }
}
</script>

<template>
  <div class="page">
    <div class="page-head">
      <div>
        <h2 class="page-subtitle">Relief stock and releases to households.</h2>
      </div>
      <div class="button-row no-margin">
        <button
          type="button" class="btn btn-outline" :aria-expanded="openForm === 'stock'"
          :disabled="loading || loadError !== ''" @click="toggleForm('stock')"
        >
          {{ openForm === "stock" ? "Close" : "Add stock" }}
        </button>
        <button
          type="button" class="btn btn-primary" :aria-expanded="openForm === 'distribution'"
          :disabled="loading || loadError !== ''" @click="toggleForm('distribution')"
        >
          {{ openForm === "distribution" ? "Close" : "New distribution" }}
        </button>
      </div>
    </div>

    <p v-if="loading" role="status">Loading relief records…</p>
    <p v-else-if="loadError" class="field-error" role="alert">{{ loadError }}</p>

    <template v-else>
      <section v-if="openForm === 'stock'" class="card section-gap-bottom">
        <div class="card-head">
          <h2>Add stock</h2>
          <p>Relief goods received by the barangay.</p>
        </div>

        <form novalidate @submit.prevent="addStock">
          <div class="field">
            <label for="stock-item">Relief item</label>
            <select
              id="stock-item" v-model="stockForm.itemId" aria-describedby="stock-item-error"
              :aria-invalid="errors['stock-item'] ? 'true' : null" @change="clearError('stock-item')"
            >
              <option value="">Choose an item</option>
              <option v-for="item in items" :key="item.id" :value="item.id">
                {{ item.name }} ({{ formatNumber(item.quantity_in_stock) }} {{ item.unit }} in stock)
              </option>
              <option value="new">New item (not in the list)</option>
            </select>
            <p v-if="errors['stock-item']" id="stock-item-error" class="field-error">{{ errors["stock-item"] }}</p>
          </div>

          <div v-if="stockForm.itemId === 'new'" class="filters">
            <div class="field">
              <label for="stock-name">Name of the item</label>
              <input
                id="stock-name" v-model="stockForm.name" type="text" maxlength="100" placeholder="For example: Rice"
                aria-describedby="stock-name-error"
                :aria-invalid="errors['stock-name'] ? 'true' : null" @input="clearError('stock-name')"
              >
              <p v-if="errors['stock-name']" id="stock-name-error" class="field-error">{{ errors["stock-name"] }}</p>
            </div>
            <div class="field">
              <label for="stock-unit">Unit</label>
              <input
                id="stock-unit" v-model="stockForm.unit" type="text" maxlength="30" placeholder="For example: kg, packs, pieces"
                aria-describedby="stock-unit-error"
                :aria-invalid="errors['stock-unit'] ? 'true' : null" @input="clearError('stock-unit')"
              >
              <p v-if="errors['stock-unit']" id="stock-unit-error" class="field-error">{{ errors["stock-unit"] }}</p>
            </div>
          </div>

          <div class="field">
            <label for="stock-quantity">Quantity received</label>
            <input
              id="stock-quantity" v-model="stockForm.quantity" type="number" min="1" inputmode="numeric"
              aria-describedby="stock-quantity-error"
              :aria-invalid="errors['stock-quantity'] ? 'true' : null" @input="clearError('stock-quantity')"
            >
            <p v-if="errors['stock-quantity']" id="stock-quantity-error" class="field-error">{{ errors["stock-quantity"] }}</p>
          </div>

          <button type="submit" class="btn btn-primary" :disabled="savingStock">Save stock</button>
        </form>
      </section>

      <section v-if="openForm === 'distribution'" class="card section-gap-bottom">
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
      <p v-if="items.length === 0" class="list-meta">No relief items yet. Use “Add stock” to add one.</p>

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

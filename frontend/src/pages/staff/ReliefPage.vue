<script setup>
// Staff: Relief distribution
// Uses the PROPOSED relief_items and relief_distributions tables (not in ERD v1 yet).
import { findHousehold, findReliefItem, reliefDistributions, reliefItems } from "../../data/sample-data.js";
import { formatDateTime, formatNumber } from "../../utils/format.js";
import { showToast } from "../../composables/toast.js";
</script>

<template>
  <div class="page">
    <div class="page-head">
      <div>
        <h2 class="page-subtitle">Relief stock and family pack releases.</h2>
      </div>
      <button type="button" class="btn btn-primary" @click="showToast('Recording a distribution needs the backend. Nothing was saved.')">
        New distribution
      </button>
    </div>

    <div class="grid grid-4">
      <div v-for="item in reliefItems" :key="item.id" class="card stat">
        <p class="stat-value">{{ formatNumber(item.quantity_in_stock) }}</p>
        <p class="stat-label">{{ item.name }}</p>
        <p class="stat-note">In stock ({{ item.unit }}s)</p>
      </div>
    </div>

    <section class="card section-gap">
      <div class="card-head">
        <h2>Recent distributions</h2>
        <p>{{ reliefDistributions.length }} records</p>
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
            <tr v-for="record in reliefDistributions" :key="record.id">
              <td>
                <strong>{{ findHousehold(record.household_id).head_name }}</strong>
                <span class="cell-note">{{ findHousehold(record.household_id).purok }}</span>
              </td>
              <td>{{ findReliefItem(record.relief_item_id).name }}</td>
              <td>{{ record.quantity }}</td>
              <td>{{ formatDateTime(record.distributed_at) }}</td>
            </tr>
          </tbody>
        </table>
      </div>
    </section>
  </div>
</template>

<script setup>
// The frame around every staff page: side menu + top bar.
// The page itself appears where <RouterView /> is.
import { RouterLink, RouterView, useRoute } from "vue-router";
import AppIcon from "../components/AppIcon.vue";
import BrandMark from "../components/BrandMark.vue";
import SampleBanner from "../components/SampleBanner.vue";
import { currentStaff } from "../data/sample-data.js";

const route = useRoute();

const links = [
  { to: "/staff/dashboard", label: "Dashboard", icon: "grid" },
  { to: "/staff/households", label: "Household Profiles", icon: "users" },
  { to: "/staff/evacuation-centers", label: "Evacuation Centers", icon: "map" },
  { to: "/staff/relief", label: "Relief Distribution", icon: "package" },
  { to: "/staff/sms-alerts", label: "SMS Alerts", icon: "message" },
];

// Today's date, e.g. "Thursday, November 5, 2026"
const today = new Date().toLocaleDateString("en-US", {
  weekday: "long",
  month: "long",
  day: "numeric",
  year: "numeric",
});
</script>

<template>
  <div class="staff-shell">
    <aside class="staff-sidebar">
      <BrandMark place="Staff dashboard" to="/staff/dashboard" />

      <nav class="staff-nav" aria-label="Staff menu">
        <p class="staff-nav-label">Main menu</p>
        <RouterLink v-for="link in links" :key="link.to" :to="link.to">
          <AppIcon :name="link.icon" /> {{ link.label }}
        </RouterLink>
      </nav>

      <div class="staff-foot">
        <div class="staff-user">
          <span class="avatar" aria-hidden="true">SS</span>
          <span>
            <strong>{{ currentStaff.name }}</strong>
            <small>Barangay staff (sample)</small>
          </span>
        </div>
        <RouterLink to="/staff-login" class="btn btn-small"><AppIcon name="logout" /> Sign out</RouterLink>
      </div>
    </aside>

    <main class="staff-main">
      <div class="staff-topbar">
        <h1>{{ route.meta.title }}</h1>
        <p>{{ today }}</p>
      </div>
      <SampleBanner />
      <RouterView />
    </main>
  </div>
</template>

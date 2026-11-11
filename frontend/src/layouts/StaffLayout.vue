<script setup>
// The frame around every staff page: side menu + top bar.
// The page itself appears where <RouterView /> is.
import { RouterLink, RouterView, useRoute, useRouter } from "vue-router";
import AppIcon from "../components/AppIcon.vue";
import BrandMark from "../components/BrandMark.vue";
import SampleBanner from "../components/SampleBanner.vue";
import { currentUser } from "../utils/mappers.js";

const route = useRoute();
const router = useRouter();

// The real signed-in staff member saved at sign in
const user = currentUser();
const displayName = user?.name || "Staff";
const roleLabel = user?.role === "admin" ? "Administrator" : "Barangay staff";
const initials = displayName
  .split(" ")
  .map((word) => word[0])
  .slice(0, 2)
  .join("");

// Sign out: forget the login, then go back to the staff sign in
function signOut() {
  localStorage.removeItem("token");
  localStorage.removeItem("user");
  router.push("/staff-login");
}

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
          <span class="avatar" aria-hidden="true">{{ initials }}</span>
          <span>
            <strong>{{ displayName }}</strong>
            <small>{{ roleLabel }}</small>
          </span>
        </div>
        <button type="button" class="btn btn-small" @click="signOut"><AppIcon name="logout" /> Sign out</button>
      </div>
    </aside>

    <main class="staff-main">
      <div class="staff-topbar">
        <h1>{{ route.meta.title }}</h1>
        <p>{{ today }}</p>
      </div>
      <!-- Delete this line once EvacuationCenters, Relief and SmsAlerts are connected to the API -->
      <SampleBanner />
      <RouterView />
    </main>
  </div>
</template>
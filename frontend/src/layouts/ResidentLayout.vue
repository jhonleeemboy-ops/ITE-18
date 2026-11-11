<script setup>
// The frame around every resident portal page: header + top menu.
// The page itself appears where <RouterView /> is.
import { computed } from "vue";
import { RouterLink, RouterView, useRouter } from "vue-router";
import AppIcon from "../components/AppIcon.vue";
import BrandMark from "../components/BrandMark.vue";
import { currentUser } from "../utils/mappers.js";

const router = useRouter();

// The real signed-in user saved at sign in
const displayName = currentUser()?.name || "Resident";

// Sign out: forget the login, then go back to sign in
function signOut() {
  localStorage.removeItem("token");
  localStorage.removeItem("user");
  router.push("/");
}

const links = [
  { to: "/resident/home", label: "Home", icon: "home" },
  { to: "/resident/household", label: "My Household", icon: "users" },
  { to: "/resident/evacuation", label: "Evacuation", icon: "map" },
  { to: "/resident/assistance", label: "Assistance", icon: "help" },
  { to: "/resident/alerts", label: "Alerts", icon: "bell" },
];

// "Juan Dela Cruz" -> "JD"
const initials = computed(() =>
  displayName
    .split(" ")
    .map((word) => word[0])
    .slice(0, 2)
    .join("")
);
</script>

<template>
  <div class="portal">
    <header class="portal-header">
      <div class="portal-header-inner">
        <BrandMark place="Citizen portal" to="/resident/home" />
        <div class="portal-user">
          <span class="avatar" aria-hidden="true">{{ initials }}</span>
          <span class="portal-user-name">{{ displayName }}</span>
          <button type="button" class="btn btn-small" @click="signOut"><AppIcon name="logout" /> Sign out</button>
        </div>
      </div>

      <nav class="portal-nav" aria-label="Resident portal">
        <RouterLink v-for="link in links" :key="link.to" :to="link.to">
          <AppIcon :name="link.icon" /> {{ link.label }}
        </RouterLink>
      </nav>
    </header>

    <main class="portal-main">
      <RouterView />
    </main>
  </div>
</template>
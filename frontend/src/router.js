// The router decides which page to show for each address, for example:
//   #/                      -> resident sign in
//   #/resident/home         -> resident portal home
//   #/staff/dashboard       -> staff dashboard
// The "#" style (hash history) works on any host without extra server settings.
import { createRouter, createWebHashHistory } from "vue-router";

import LoginPage from "./pages/LoginPage.vue";
import StaffLoginPage from "./pages/StaffLoginPage.vue";

import ResidentLayout from "./layouts/ResidentLayout.vue";
import ResidentHome from "./pages/resident/HomePage.vue";
import ResidentHousehold from "./pages/resident/HouseholdPage.vue";
import ResidentEvacuation from "./pages/resident/EvacuationPage.vue";
import ResidentAssistance from "./pages/resident/AssistancePage.vue";
import ResidentAlerts from "./pages/resident/AlertsPage.vue";

import StaffLayout from "./layouts/StaffLayout.vue";
import StaffDashboard from "./pages/staff/DashboardPage.vue";
import StaffHouseholds from "./pages/staff/HouseholdsPage.vue";
import StaffEvacuationCenters from "./pages/staff/EvacuationCentersPage.vue";
import StaffRelief from "./pages/staff/ReliefPage.vue";
import StaffSmsAlerts from "./pages/staff/SmsAlertsPage.vue";

const routes = [
  // Public pages
  { path: "/", component: LoginPage, props: { mode: "signin" }, meta: { title: "Sign in" } },
  { path: "/register", component: LoginPage, props: { mode: "register" }, meta: { title: "Create account" } },
  { path: "/staff-login", component: StaffLoginPage, meta: { title: "Staff sign in" } },

  // Resident portal (top menu layout)
  {
    path: "/resident",
    component: ResidentLayout,
    children: [
      { path: "", redirect: "/resident/home" },
      { path: "home", component: ResidentHome, meta: { title: "Home" } },
      { path: "household", component: ResidentHousehold, meta: { title: "My Household" } },
      { path: "evacuation", component: ResidentEvacuation, meta: { title: "Evacuation" } },
      { path: "assistance", component: ResidentAssistance, meta: { title: "Assistance" } },
      { path: "alerts", component: ResidentAlerts, meta: { title: "Alerts" } },
    ],
  },

  // Staff dashboard (side menu layout)
  {
    path: "/staff",
    component: StaffLayout,
    children: [
      { path: "", redirect: "/staff/dashboard" },
      { path: "dashboard", component: StaffDashboard, meta: { title: "Dashboard" } },
      { path: "households", component: StaffHouseholds, meta: { title: "Household Profiles" } },
      { path: "evacuation-centers", component: StaffEvacuationCenters, meta: { title: "Evacuation Centers" } },
      { path: "relief", component: StaffRelief, meta: { title: "Relief Distribution" } },
      { path: "sms-alerts", component: StaffSmsAlerts, meta: { title: "SMS Alerts" } },
    ],
  },

  // Any unknown address goes back to sign in
  { path: "/:pathMatch(.*)*", redirect: "/" },
];

const router = createRouter({
  history: createWebHashHistory(),
  routes,
  // Start each new page at the top
  scrollBehavior() {
    return { top: 0 };
  },
});

// Show the page name in the browser tab
router.afterEach((to) => {
  document.title = (to.meta.title ? to.meta.title + " | " : "") + "Disaster Ready";
});

export default router;

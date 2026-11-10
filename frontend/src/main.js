// Starting point of the app: create Vue, add the page router, load the CSS files.
import { createApp } from "vue";
import App from "./App.vue";
import router from "./router.js";

// Plain external CSS files (the group agreed on this instead of Tailwind)
import "./css/base.css";
import "./css/login.css";
import "./css/app.css";
import "./css/portal.css";
import "./css/staff.css";

createApp(App).use(router).mount("#app");

// ---- TEMPORARY connection test: delete everything below once it works ----

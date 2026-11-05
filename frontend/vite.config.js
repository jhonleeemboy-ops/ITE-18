// Vite settings: the tool that runs the Vue app while we code ("npm run dev")
// and builds the final files for hosting ("npm run build").
import { defineConfig } from "vite";
import vue from "@vitejs/plugin-vue";

export default defineConfig({
  plugins: [vue()],
  // "./" makes the built files work from any folder or host
  base: "./",
  server: {
    port: 5173,
  },
});

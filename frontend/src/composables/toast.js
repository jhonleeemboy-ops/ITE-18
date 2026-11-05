// A tiny shared "toast" (pop-up message) that any page can use:
//   import { showToast } from "../composables/toast.js";
//   showToast("Saved!");
import { reactive } from "vue";

export const toast = reactive({ message: "", visible: false });

let hideTimer;

export function showToast(message) {
  toast.message = message;
  toast.visible = true;

  // Hide it again after 4 seconds
  clearTimeout(hideTimer);
  hideTimer = setTimeout(() => {
    toast.visible = false;
  }, 4000);
}

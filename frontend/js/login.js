/* =========================================================
   Disaster Ready: login page script (index.html)

   What this file does:
   1. Switches between the "Sign in" and "Create account" forms
   2. Checks the form fields before submitting (validation)
   3. Shows a short message for pages that are not built yet

   Nothing is sent to a server yet, because the backend is not ready.
   ========================================================= */

// ---------- Find the parts of the page we need ----------
const signinForm = document.getElementById("signin-form");
const registerForm = document.getElementById("register-form");
const toast = document.getElementById("toast");


/* ---------- 1. Switching between the two forms ---------- */

function showForm(formName, moveFocus) {
  const showRegister = formName === "register";

  signinForm.hidden = showRegister;
  registerForm.hidden = !showRegister;

  // Put "#register" or "#signin" in the address bar so this view can be linked
  history.replaceState(null, "", showRegister ? "#register" : "#signin");

  // Move keyboard focus to the new heading, so keyboard and
  // screen reader users know that the form changed
  if (moveFocus) {
    const visibleForm = showRegister ? registerForm : signinForm;
    visibleForm.querySelector("h2").focus();
  }
}

// Every button with data-show="register" or data-show="signin" switches forms
document.querySelectorAll("[data-show]").forEach(function (button) {
  button.addEventListener("click", function () {
    showForm(button.dataset.show, true);
  });
});

// If someone opens index.html#register, start on the registration form
if (location.hash === "#register") {
  showForm("register", false);
}

// Also switch if the "#..." part of the address changes while the page is open
window.addEventListener("hashchange", function () {
  showForm(location.hash === "#register" ? "register" : "signin", false);
});


/* ---------- 2. Checking the fields (validation) ---------- */

// Turns what the user typed into 10 digits.
// Example: "0917 123 4567" or "+63 917 123 4567" becomes "9171234567"
function cleanMobileNumber(text) {
  let digits = text.replace(/\D/g, ""); // remove spaces, dashes, "+", etc.

  if (digits.startsWith("63")) {
    digits = digits.slice(2); // typed with the country code: 63 917...
  } else if (digits.startsWith("0")) {
    digits = digits.slice(1); // typed the local way: 0917...
  }

  return digits;
}

// A Philippine mobile number has 10 digits after +63 and starts with 9
function isValidMobileNumber(digits) {
  return /^9\d{9}$/.test(digits);
}

// Shows a red message under a field
function showError(field, message) {
  document.getElementById(field.id + "-error").textContent = message;
  field.setAttribute("aria-invalid", "true");
}

// Removes the red message from a field
function clearError(field) {
  document.getElementById(field.id + "-error").textContent = "";
  field.removeAttribute("aria-invalid");
}

// Each check below returns true (OK) or false (has an error)

function checkRequired(field, message) {
  if (field.value.trim() === "") {
    showError(field, message);
    return false;
  }
  clearError(field);
  return true;
}

function checkMobile(field) {
  if (field.value.trim() === "") {
    showError(field, "Enter your mobile number.");
    return false;
  }
  if (!isValidMobileNumber(cleanMobileNumber(field.value))) {
    showError(field, "Enter a valid mobile number, like 917 123 4567.");
    return false;
  }
  clearError(field);
  return true;
}

function checkName(field) {
  if (field.value.trim().length < 2) {
    showError(field, "Enter your full name.");
    return false;
  }
  clearError(field);
  return true;
}

function checkNewPassword(field) {
  if (field.value === "") {
    showError(field, "Create a password.");
    return false;
  }
  if (field.value.length < 8) {
    showError(field, "Use at least 8 characters.");
    return false;
  }
  clearError(field);
  return true;
}

// Runs after a form's checks. If something is wrong, jump to the first
// field with an error. If everything is OK, show the status message.
function finishSubmit(form, allValid, message) {
  const status = form.querySelector(".form-status");

  if (!allValid) {
    status.textContent = "";
    form.querySelector('[aria-invalid="true"]').focus();
    return;
  }

  status.textContent = message;
}

// Sign in form
signinForm.addEventListener("submit", function (event) {
  event.preventDefault(); // stop the browser from reloading the page

  // Run every check first, so all errors show at the same time
  const mobileOK = checkMobile(document.getElementById("signin-mobile"));
  const passwordOK = checkRequired(document.getElementById("signin-password"), "Enter your password.");

  finishSubmit(
    signinForm,
    mobileOK && passwordOK,
    "Your details look correct, but sign-in is not connected to the server yet."
  );
});

// Create account form
registerForm.addEventListener("submit", function (event) {
  event.preventDefault();

  const nameOK = checkName(document.getElementById("register-name"));
  const mobileOK = checkMobile(document.getElementById("register-mobile"));
  const passwordOK = checkNewPassword(document.getElementById("register-password"));
  const purokOK = checkRequired(document.getElementById("register-purok"), "Select your Purok.");

  finishSubmit(
    registerForm,
    nameOK && mobileOK && passwordOK && purokOK,
    "Your details look correct, but registration is not connected to the server yet. Nothing was saved."
  );
});

// When the user fixes a field, remove its red message right away
document.querySelectorAll(".auth-form input, .auth-form select").forEach(function (field) {
  field.addEventListener("input", function () {
    if (field.getAttribute("aria-invalid") === "true") {
      clearError(field);
    }
    field.form.querySelector(".form-status").textContent = "";
  });
});


/* ---------- 3. Buttons for pages that are not built yet ---------- */

let toastTimer;

function showToast(message) {
  toast.textContent = message;
  toast.classList.add("is-visible");

  // Hide the message again after 4 seconds
  clearTimeout(toastTimer);
  toastTimer = setTimeout(function () {
    toast.classList.remove("is-visible");
  }, 4000);
}

document.querySelectorAll("[data-not-built]").forEach(function (button) {
  button.addEventListener("click", function () {
    showToast(button.dataset.notBuilt + " is not built yet.");
  });
});

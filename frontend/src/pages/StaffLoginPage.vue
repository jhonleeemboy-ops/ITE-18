<script setup>
// Staff sign in. Connected to the Laravel API: POST /login with email + password.
// Staff and admin accounts are created by the seeder (staff@brgy.test).
import { reactive, ref } from "vue";
import { RouterLink, useRouter } from "vue-router";
import AppIcon from "../components/AppIcon.vue";
import BrandMark from "../components/BrandMark.vue";
import { emailError, focusFirstError, requiredError } from "../utils/validation.js";
import api from "../api";

const router = useRouter();
const form = reactive({ email: "", password: "" });
const errors = reactive({});
const status = ref("");
const loading = ref(false);

function clearError(fieldId) {
  errors[fieldId] = "";
  status.value = "";
}

async function submit() {
  errors["staff-email"] = emailError(form.email);
  errors["staff-password"] = requiredError(form.password, "Enter your password.");

  if (focusFirstError(errors, ["staff-email", "staff-password"])) return;

  loading.value = true;
  status.value = "";
  try {
    const { data } = await api.post("/login", {
      email: form.email.trim(),
      password: form.password,
    });

    // residents must use the resident sign in
    if (!["staff", "admin"].includes(data.user.role)) {
      status.value = "This account is not a staff account. Please use the resident sign in.";
      return;
    }

    localStorage.setItem("token", data.token);
    localStorage.setItem("user", JSON.stringify(data.user));
    router.push("/staff/dashboard");
  } catch (e) {
    const apiErrors = e.response?.data?.errors;
    if (apiErrors?.password) errors["staff-password"] = apiErrors.password[0];
    else if (apiErrors?.email) errors["staff-email"] = apiErrors.email[0];
    else if (!e.response) status.value = "Cannot reach the server. Check your connection and try again.";
    else status.value = e.response.data?.message || "Something went wrong. Please try again.";
  } finally {
    loading.value = false;
  }
}
</script>

<template>
  <div class="login-page">
    <header class="site-header">
      <BrandMark />
      <RouterLink to="/" class="btn btn-small">
        <span class="label-long">Resident sign in</span>
        <span class="label-short">Residents</span>
      </RouterLink>
    </header>

    <main class="staff-login">
      <section class="auth-card">
        <form class="auth-form" novalidate @submit.prevent="submit">
          <p class="eyebrow">Barangay staff</p>
          <h1 class="auth-title">Staff sign in</h1>
          <p class="form-intro">For authorized Barangay Ampayon personnel only.</p>

          <div class="field">
            <label for="staff-email">Email</label>
            <input
              id="staff-email" v-model="form.email" type="email" autocomplete="username" placeholder="name@example.com"
              aria-describedby="staff-email-error" :aria-invalid="errors['staff-email'] ? 'true' : null"
              @input="clearError('staff-email')"
            >
            <p v-if="errors['staff-email']" id="staff-email-error" class="field-error">{{ errors["staff-email"] }}</p>
          </div>

          <div class="field">
            <label for="staff-password">Password</label>
            <input
              id="staff-password" v-model="form.password" type="password" autocomplete="current-password"
              placeholder="••••••••" aria-describedby="staff-password-error"
              :aria-invalid="errors['staff-password'] ? 'true' : null" @input="clearError('staff-password')"
            >
            <p v-if="errors['staff-password']" id="staff-password-error" class="field-error">{{ errors["staff-password"] }}</p>
          </div>

          <button type="submit" class="btn btn-primary btn-block" :disabled="loading">Sign in to dashboard <AppIcon name="arrow" /></button>
          <p v-if="status" class="form-status" role="status">{{ status }}</p>
        </form>
      </section>
    </main>
  </div>
</template>
<script setup>
// Staff sign in. DEMO: a valid email + password opens the sample staff dashboard.
// How staff will really sign in (email? username?) is still to be confirmed by the group.
import { reactive, ref } from "vue";
import { RouterLink, useRouter } from "vue-router";
import AppIcon from "../components/AppIcon.vue";
import BrandMark from "../components/BrandMark.vue";
import { emailError, focusFirstError, requiredError } from "../utils/validation.js";

const router = useRouter();
const form = reactive({ email: "", password: "" });
const errors = reactive({});
const status = ref("");

function clearError(fieldId) {
  errors[fieldId] = "";
  status.value = "";
}

function submit() {
  errors["staff-email"] = emailError(form.email);
  errors["staff-password"] = requiredError(form.password, "Enter your password.");

  if (focusFirstError(errors, ["staff-email", "staff-password"])) return;

  status.value = "Demo: staff sign-in isn't connected to the server yet. Opening the sample dashboard…";
  setTimeout(() => router.push("/staff/dashboard"), 1200);
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

          <button type="submit" class="btn btn-primary btn-block">Sign in to dashboard <AppIcon name="arrow" /></button>
          <p v-if="status" class="form-status" role="status">{{ status }}</p>
        </form>

        <p class="prototype-note">
          <AppIcon name="shield" />
          Demo: any valid email opens the sample dashboard. The staff sign-in method is still to be confirmed.
        </p>
      </section>
    </main>
  </div>
</template>

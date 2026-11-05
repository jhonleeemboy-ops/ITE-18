<script setup>
// Resident sign in + create account (one page, two forms).
// DEMO: nothing is sent to a server yet. A valid sign-in opens the sample portal.
import { computed, nextTick, reactive, ref, watch } from "vue";
import { RouterLink, useRouter } from "vue-router";
import AppIcon from "../components/AppIcon.vue";
import BrandMark from "../components/BrandMark.vue";
import { advisories, puroks } from "../data/sample-data.js";
import { showToast } from "../composables/toast.js";
import { focusFirstError, mobileError, newPasswordError, requiredError } from "../utils/validation.js";

const props = defineProps({ mode: { type: String, default: "signin" } });
const router = useRouter();

const isRegister = computed(() => props.mode === "register");
const latestAdvisory = advisories[0]; // SAMPLE advisory

// What the user typed in each form
const signin = reactive({ mobile: "", password: "" });
const register = reactive({ name: "", mobile: "", password: "", purok: "" });

// Error message for each field, by field id ("" or missing = no error)
const errors = reactive({});
const status = ref("");
const heading = ref(null);

function clearError(fieldId) {
  errors[fieldId] = "";
  status.value = "";
}

// When switching between the two forms, clear old messages and
// move focus to the new heading (helps keyboard and screen reader users)
watch(
  () => props.mode,
  async () => {
    Object.keys(errors).forEach((key) => (errors[key] = ""));
    status.value = "";
    await nextTick();
    heading.value?.focus();
  }
);

function submitSignin() {
  errors["signin-mobile"] = mobileError(signin.mobile);
  errors["signin-password"] = requiredError(signin.password, "Enter your password.");

  if (focusFirstError(errors, ["signin-mobile", "signin-password"])) return;

  status.value = "Demo: sign-in isn't connected to the server yet. Opening the sample resident portal…";
  setTimeout(() => router.push("/resident/home"), 1200);
}

function submitRegister() {
  errors["register-name"] = register.name.trim().length < 2 ? "Enter your full name." : "";
  errors["register-mobile"] = mobileError(register.mobile);
  errors["register-password"] = newPasswordError(register.password);
  errors["register-purok"] = requiredError(register.purok, "Select your Purok.");

  if (focusFirstError(errors, ["register-name", "register-mobile", "register-password", "register-purok"])) return;

  status.value = "Your details look correct, but registration isn't connected to the server yet. Nothing was saved.";
}
</script>

<template>
  <div class="login-page">
    <header class="site-header">
      <BrandMark />
      <RouterLink to="/staff-login" class="btn btn-small">
        <span class="label-long">Barangay staff login</span>
        <span class="label-short">Staff login</span>
      </RouterLink>
    </header>

    <main class="login-layout">
      <!-- Left side: welcome message and current advisory -->
      <section class="intro" aria-labelledby="page-title">
        <p class="chip chip-warning">{{ latestAdvisory.title }} active</p>
        <h1 id="page-title">One community.<br>Ready for anything.</h1>
        <p class="lead">Get official alerts, manage your household profile, find evacuation centers, and request emergency assistance.</p>

        <div class="alert alert-warning">
          <span class="alert-icon" aria-hidden="true">!</span>
          <div class="alert-body">
            <p class="alert-title">{{ latestAdvisory.title }}</p>
            <p class="alert-text">{{ latestAdvisory.message }}</p>
          </div>
          <span class="sample-tag">Sample</span>
        </div>

        <div class="quick-links">
          <button type="button" class="link-button" @click="showToast('The public evacuation centers page is not built yet.')">
            <AppIcon name="map" /> Evacuation centers
          </button>
          <button type="button" class="link-button" @click="showToast('The public emergency hotlines page is not built yet.')">
            <AppIcon name="shield" /> Emergency hotlines
          </button>
        </div>
      </section>

      <!-- Right side: the card with the two forms -->
      <section class="auth-card" aria-label="Resident access">
        <!-- Form 1: Sign in -->
        <form v-if="!isRegister" class="auth-form" novalidate @submit.prevent="submitSignin">
          <p class="eyebrow">Resident access</p>
          <h2 ref="heading" tabindex="-1">Welcome back</h2>
          <p class="form-intro">Sign in with your registered mobile number.</p>

          <div class="field">
            <label for="signin-mobile">Mobile number</label>
            <div class="input-group">
              <span class="input-prefix">+63</span>
              <input
                id="signin-mobile" v-model="signin.mobile" type="tel" inputmode="tel" autocomplete="tel-national"
                placeholder="917 123 4567" aria-describedby="signin-mobile-error"
                :aria-invalid="errors['signin-mobile'] ? 'true' : null" @input="clearError('signin-mobile')"
              >
            </div>
            <p v-if="errors['signin-mobile']" id="signin-mobile-error" class="field-error">{{ errors["signin-mobile"] }}</p>
          </div>

          <div class="field">
            <label for="signin-password">Password</label>
            <input
              id="signin-password" v-model="signin.password" type="password" autocomplete="current-password"
              placeholder="••••••••" aria-describedby="signin-password-error"
              :aria-invalid="errors['signin-password'] ? 'true' : null" @input="clearError('signin-password')"
            >
            <p v-if="errors['signin-password']" id="signin-password-error" class="field-error">{{ errors["signin-password"] }}</p>
          </div>

          <button type="submit" class="btn btn-primary btn-block">Sign in to citizen portal <AppIcon name="arrow" /></button>
          <p v-if="status" class="form-status" role="status">{{ status }}</p>

          <p class="switch-text">
            New to Disaster Ready?
            <RouterLink to="/register" class="link-button">Create an account</RouterLink>
          </p>
        </form>

        <!-- Form 2: Create an account -->
        <form v-else class="auth-form" novalidate @submit.prevent="submitRegister">
          <p class="eyebrow">Resident access</p>
          <h2 ref="heading" tabindex="-1">Create your account</h2>
          <p class="form-intro">Register your household with Barangay Ampayon.</p>

          <div class="field">
            <label for="register-name">Full name</label>
            <input
              id="register-name" v-model="register.name" type="text" autocomplete="name" placeholder="Juan Dela Cruz"
              aria-describedby="register-name-error" :aria-invalid="errors['register-name'] ? 'true' : null"
              @input="clearError('register-name')"
            >
            <p v-if="errors['register-name']" id="register-name-error" class="field-error">{{ errors["register-name"] }}</p>
          </div>

          <div class="field">
            <label for="register-mobile">Mobile number</label>
            <div class="input-group">
              <span class="input-prefix">+63</span>
              <input
                id="register-mobile" v-model="register.mobile" type="tel" inputmode="tel" autocomplete="tel-national"
                placeholder="917 123 4567" aria-describedby="register-mobile-error"
                :aria-invalid="errors['register-mobile'] ? 'true' : null" @input="clearError('register-mobile')"
              >
            </div>
            <p v-if="errors['register-mobile']" id="register-mobile-error" class="field-error">{{ errors["register-mobile"] }}</p>
          </div>

          <div class="field">
            <label for="register-password">Create password</label>
            <input
              id="register-password" v-model="register.password" type="password" autocomplete="new-password"
              placeholder="••••••••" aria-describedby="register-password-hint register-password-error"
              :aria-invalid="errors['register-password'] ? 'true' : null" @input="clearError('register-password')"
            >
            <p id="register-password-hint" class="field-hint">Use at least 8 characters.</p>
            <p v-if="errors['register-password']" id="register-password-error" class="field-error">{{ errors["register-password"] }}</p>
          </div>

          <div class="field">
            <label for="register-purok">Home address (Purok)</label>
            <select
              id="register-purok" v-model="register.purok" aria-describedby="register-purok-error"
              :aria-invalid="errors['register-purok'] ? 'true' : null" @change="clearError('register-purok')"
            >
              <option value="">Select your Purok</option>
              <option v-for="purok in puroks" :key="purok" :value="purok">{{ purok }}</option>
            </select>
            <p v-if="errors['register-purok']" id="register-purok-error" class="field-error">{{ errors["register-purok"] }}</p>
          </div>

          <button type="submit" class="btn btn-primary btn-block">Register household <AppIcon name="arrow" /></button>
          <p v-if="status" class="form-status" role="status">{{ status }}</p>

          <p class="switch-text">
            Already registered?
            <RouterLink to="/" class="link-button">Sign in</RouterLink>
          </p>
        </form>

        <p class="prototype-note">
          <AppIcon name="shield" />
          Demo: sign-in isn't connected to the server yet. Any valid mobile number opens the sample portal.
        </p>
      </section>
    </main>
  </div>
</template>

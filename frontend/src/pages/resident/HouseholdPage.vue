<script setup>
// Resident portal: My Household
// Connected to the Laravel API:
//   GET    /me                       the signed-in user and their household
//   PUT    /households/{id}          update the profile (head, Purok, members)
//   PATCH  /households/{id}/safety   update only the safety status
//   GET    /households/{id}/members  list of family members
//   POST   /households/{id}/members  add a family member
//   DELETE /household-members/{id}   remove a family member
import { onMounted, reactive, ref } from "vue";
import StatusBadge from "../../components/StatusBadge.vue";
import api from "../../api";
import { showToast } from "../../composables/toast.js";
import { formatMobile, mapHousehold, purokNumber, toApiValue } from "../../utils/mappers.js";
import { focusFirstError } from "../../utils/validation.js";

const user = ref(null);
const household = ref(null);
const loading = ref(true);
const error = ref("");
const saving = ref(false);

const safetyOptions = ["safe", "evacuated", "needs help"];
const puroks = ["Purok 1", "Purok 2", "Purok 3", "Purok 4", "Purok 5", "Purok 6", "Purok 7", "Purok 8"];

// ----- Update profile form -----
const editing = ref(false);
const savingProfile = ref(false);
const form = reactive({ headName: "", purok: "", members: "" });
const errors = reactive({});

// ----- Family members -----
const familyMembers = ref([]);
const addingMember = ref(false);
const removingId = ref(null); // id of the member being removed
const memberForm = reactive({ name: "", age: "", relationship: "", isPwd: false });
const relationships = ["Head", "Spouse", "Child", "Parent", "Sibling", "Grandparent", "Grandchild", "Other relative", "Not related"];

onMounted(async () => {
  try {
    const { data } = await api.get("/me");
    user.value = data;
    household.value = data.household ? mapHousehold(data.household) : null;

    if (household.value) {
      const list = await api.get(`/households/${household.value.id}/members`);
      familyMembers.value = list.data;
    }
  } catch (e) {
    error.value = "Could not load your household. Please try again.";
  } finally {
    loading.value = false;
  }
});

// Open the form with the current values already filled in
function startEditing() {
  form.headName = household.value.head_name;
  form.purok = household.value.purok;
  form.members = household.value.members;
  errors["profile-head"] = "";
  errors["profile-purok"] = "";
  errors["profile-members"] = "";
  editing.value = true;
}

function cancelEditing() {
  editing.value = false;
}

function clearError(fieldId) {
  errors[fieldId] = "";
}

async function saveProfile() {
  const members = Number(form.members);

  errors["profile-head"] = form.headName.trim() === "" ? "Enter the name of the head of household." : "";
  errors["profile-purok"] = form.purok === "" ? "Choose your Purok." : "";
  errors["profile-members"] =
    Number.isInteger(members) && members >= 1 && members <= 50 ? "" : "Enter the number of household members (1 to 50).";

  if (focusFirstError(errors, ["profile-head", "profile-purok", "profile-members"])) return;

  savingProfile.value = true;
  try {
    const { data } = await api.put(`/households/${household.value.id}`, {
      head_name: form.headName.trim(),
      purok: purokNumber(form.purok), // "Purok 3" -> 3
      members,
    });
    household.value = mapHousehold(data);
    editing.value = false;
    showToast("Household profile updated.");
  } catch (e) {
    const apiErrors = e.response?.data?.errors;
    if (apiErrors?.head_name) errors["profile-head"] = apiErrors.head_name[0];
    else if (apiErrors?.purok) errors["profile-purok"] = apiErrors.purok[0];
    else if (apiErrors?.members) errors["profile-members"] = apiErrors.members[0];
    else if (!e.response) showToast("Cannot reach the server. Your profile was NOT changed.");
    else showToast("Your profile was NOT changed. Please try again.");
  } finally {
    savingProfile.value = false;
  }
}

// Labels that help responders: senior (60 and above), infant (under 2), PWD
function memberTags(member) {
  const tags = [];
  if (member.age >= 60) tags.push("Senior");
  if (member.age < 2) tags.push("Infant");
  if (member.is_pwd) tags.push("PWD");
  return tags;
}

async function addMember() {
  const age = Number(memberForm.age);

  errors["member-name"] = memberForm.name.trim() === "" ? "Enter the name." : "";
  errors["member-age"] =
    memberForm.age !== "" && Number.isInteger(age) && age >= 0 && age <= 120 ? "" : "Enter the age (0 to 120).";

  if (focusFirstError(errors, ["member-name", "member-age"])) return;

  addingMember.value = true;
  try {
    const { data } = await api.post(`/households/${household.value.id}/members`, {
      name: memberForm.name.trim(),
      age,
      relationship: memberForm.relationship || null,
      is_pwd: memberForm.isPwd,
    });
    familyMembers.value.push(data);

    // the API raises the member count when the list becomes longer than it
    if (familyMembers.value.length > household.value.members) {
      household.value.members = familyMembers.value.length;
    }

    memberForm.name = "";
    memberForm.age = "";
    memberForm.relationship = "";
    memberForm.isPwd = false;
    showToast("Family member added.");
  } catch (e) {
    const apiErrors = e.response?.data?.errors;
    if (apiErrors?.name) errors["member-name"] = apiErrors.name[0];
    else if (apiErrors?.age) errors["member-age"] = apiErrors.age[0];
    else if (!e.response) showToast("Cannot reach the server. The family member was NOT saved.");
    else showToast("The family member was NOT saved. Please try again.");
  } finally {
    addingMember.value = false;
  }
}

async function removeMember(member) {
  if (!window.confirm(`Remove ${member.name} from your household list?`)) return;

  removingId.value = member.id;
  try {
    await api.delete(`/household-members/${member.id}`);
    familyMembers.value = familyMembers.value.filter((item) => item.id !== member.id);
    showToast("Family member removed.");
  } catch (e) {
    showToast("The family member was NOT removed. Please try again.");
  } finally {
    removingId.value = null;
  }
}

// The resident can update only their own household's safety status
async function updateSafety(event) {
  const value = event.target.value;
  saving.value = true;
  try {
    const { data } = await api.patch(`/households/${household.value.id}/safety`, {
      safety: toApiValue(value),
    });
    household.value = mapHousehold(data);
    showToast("Safety status updated.");
  } catch (e) {
    showToast("Could not update your safety status. Please try again.");
    event.target.value = household.value.safety; // put the old value back
  } finally {
    saving.value = false;
  }
}
</script>

<template>
  <div class="page">
    <p v-if="loading" role="status">Loading your household…</p>
    <p v-else-if="error" class="field-error" role="alert">{{ error }}</p>
    <p v-else-if="!household">No household is linked to your account. Please contact the barangay.</p>

    <template v-else>
      <div class="page-head">
        <div>
          <p class="eyebrow">Household ID · AMP-{{ String(household.id).padStart(4, "0") }}</p>
          <h1>My household profile</h1>
          <p>Keep this information updated so responders can give the right help.</p>
        </div>
        <button v-if="!editing" type="button" class="btn btn-primary" @click="startEditing">
          Update profile
        </button>
      </div>

      <!-- Edit form: shown after "Update profile" is clicked -->
      <section v-if="editing" class="card">
        <div class="card-head">
          <h2>Update profile</h2>
        </div>

        <form novalidate @submit.prevent="saveProfile">
          <div class="field">
            <label for="profile-head">Head of household</label>
            <input
              id="profile-head" v-model="form.headName" type="text" autocomplete="name"
              aria-describedby="profile-head-error"
              :aria-invalid="errors['profile-head'] ? 'true' : null" @input="clearError('profile-head')"
            >
            <p v-if="errors['profile-head']" id="profile-head-error" class="field-error">{{ errors["profile-head"] }}</p>
          </div>

          <div class="filters">
            <div class="field">
              <label for="profile-purok">Purok</label>
              <select
                id="profile-purok" v-model="form.purok" aria-describedby="profile-purok-error"
                :aria-invalid="errors['profile-purok'] ? 'true' : null" @change="clearError('profile-purok')"
              >
                <option v-for="purok in puroks" :key="purok" :value="purok">{{ purok }}</option>
              </select>
              <p v-if="errors['profile-purok']" id="profile-purok-error" class="field-error">{{ errors["profile-purok"] }}</p>
            </div>
            <div class="field">
              <label for="profile-members">Household members</label>
              <input
                id="profile-members" v-model="form.members" type="number" min="1" max="50" inputmode="numeric"
                aria-describedby="profile-members-error"
                :aria-invalid="errors['profile-members'] ? 'true' : null" @input="clearError('profile-members')"
              >
              <p v-if="errors['profile-members']" id="profile-members-error" class="field-error">{{ errors["profile-members"] }}</p>
            </div>
          </div>

          <div class="button-row no-margin">
            <button type="submit" class="btn btn-primary" :disabled="savingProfile">Save changes</button>
            <button type="button" class="btn btn-outline" :disabled="savingProfile" @click="cancelEditing">Cancel</button>
          </div>
        </form>

        <p class="note">Your mobile number is your sign-in. To change it, please contact the barangay.</p>
      </section>

      <!-- Normal view -->
      <section v-else class="card">
        <dl class="details">
          <div>
            <dt>Head of household</dt>
            <dd>{{ household.head_name }}</dd>
          </div>
          <div>
            <dt>Mobile number</dt>
            <dd>{{ formatMobile(user.mobile_number) }}</dd>
          </div>
          <div>
            <dt>Home address</dt>
            <dd>{{ household.purok }}, Barangay Ampayon</dd>
          </div>
          <div>
            <dt>Household members</dt>
            <dd>{{ household.members }}</dd>
          </div>
          <div>
            <dt>Safety status</dt>
            <dd>
              <StatusBadge :value="household.safety" />
              <select
                :value="household.safety" :disabled="saving" aria-label="Change safety status"
                @change="updateSafety"
              >
                <option v-for="option in safetyOptions" :key="option" :value="option">{{ option }}</option>
              </select>
            </dd>
          </div>
        </dl>
      </section>

      <section class="card section-gap">
        <div class="card-head">
          <h2>Family members</h2>
          <p>{{ familyMembers.length }} of {{ household.members }} listed</p>
        </div>
        <p class="muted">
          List everyone living in your home, including you. Ages and PWD status help responders
          know who needs help first. Only you and barangay staff can see this list.
        </p>

        <div v-if="familyMembers.length" class="table-wrap">
          <table class="table">
            <thead>
              <tr>
                <th>Name</th>
                <th>Age</th>
                <th>Relationship</th>
                <th>Needs attention</th>
                <th aria-label="Actions"></th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="member in familyMembers" :key="member.id">
                <td><strong>{{ member.name }}</strong></td>
                <td>{{ member.age }}</td>
                <td>{{ member.relationship || "—" }}</td>
                <td>
                  <span v-for="tag in memberTags(member)" :key="tag" class="badge badge-amber member-tag">{{ tag }}</span>
                  <span v-if="memberTags(member).length === 0">—</span>
                </td>
                <td>
                  <button
                    type="button" class="btn btn-small" :disabled="removingId === member.id"
                    :aria-label="'Remove ' + member.name" @click="removeMember(member)"
                  >
                    Remove
                  </button>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
        <p v-else class="list-meta">No family members listed yet.</p>

        <form class="member-form" novalidate @submit.prevent="addMember">
          <h3>Add a family member</h3>
          <div class="filters">
            <div class="field">
              <label for="member-name">Full name</label>
              <input
                id="member-name" v-model="memberForm.name" type="text" aria-describedby="member-name-error"
                :aria-invalid="errors['member-name'] ? 'true' : null" @input="clearError('member-name')"
              >
              <p v-if="errors['member-name']" id="member-name-error" class="field-error">{{ errors["member-name"] }}</p>
            </div>
            <div class="field">
              <label for="member-age">Age</label>
              <input
                id="member-age" v-model="memberForm.age" type="number" min="0" max="120" inputmode="numeric"
                aria-describedby="member-age-error"
                :aria-invalid="errors['member-age'] ? 'true' : null" @input="clearError('member-age')"
              >
              <p v-if="errors['member-age']" id="member-age-error" class="field-error">{{ errors["member-age"] }}</p>
            </div>
          </div>

          <div class="field">
            <label for="member-relationship">Relationship to the head of household (optional)</label>
            <select id="member-relationship" v-model="memberForm.relationship">
              <option value="">Choose one</option>
              <option v-for="option in relationships" :key="option" :value="option">{{ option }}</option>
            </select>
          </div>

          <label class="check-line">
            <input v-model="memberForm.isPwd" type="checkbox"> Person with disability (PWD)
          </label>

          <button type="submit" class="btn btn-primary" :disabled="addingMember">Add family member</button>
        </form>
      </section>
    </template>
  </div>
</template>

<style scoped>
/* Small styles used only by the family members part of this page */
.member-form {
  margin-top: 20px;
  padding-top: 18px;
  border-top: 1px solid #edf2f0;
}

.member-form h3 {
  margin: 0 0 12px;
  font-size: 16px;
}

.check-line {
  display: flex;
  align-items: center;
  gap: 8px;
  margin-bottom: 16px;
  font-size: 15px;
}

.check-line input {
  width: 18px;
  height: 18px;
}

.member-tag {
  margin-right: 6px;
}

.table-wrap {
  margin-top: 12px;
}
</style>
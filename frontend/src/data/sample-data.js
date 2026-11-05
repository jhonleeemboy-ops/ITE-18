/* =========================================================
   SAMPLE DATA: NOT REAL BARANGAY RECORDS
   Everything in this file is made up, so the pages have something
   to show while the Laravel backend is being built.

   Field names follow Jhon Lee's ERD (v1, Nov 5) where possible
   (head_name, purok, members, safety, capacity, occupants...),
   so replacing this file with real API calls later is easier.
   Tables marked "PROPOSED" are not in ERD v1 yet (see docs/erd-review.md).
   ========================================================= */

// Purok 1 to 8 comes from the Figma prototype. The real list still needs checking.
export const puroks = ["Purok 1", "Purok 2", "Purok 3", "Purok 4", "Purok 5", "Purok 6", "Purok 7", "Purok 8"];

// The signed-in people (demo only, no real login yet)
export const currentResident = { id: 1, household_id: 1, name: "Juan Dela Cruz", mobile_number: "0917 123 4567", role: "resident" };
export const currentStaff = { id: 90, name: "Sample Staff", email: "staff@example.com", role: "staff" };

// ERD table: HOUSEHOLDS
export const households = [
  { id: 1, head_name: "Juan Dela Cruz", purok: "Purok 3", members: 6, safety: "safe" },
  { id: 2, head_name: "Maria Santos", purok: "Purok 7", members: 4, safety: "evacuated" },
  { id: 3, head_name: "Pedro Ramirez", purok: "Purok 2", members: 5, safety: "safe" },
  { id: 4, head_name: "Ana Flores", purok: "Purok 5", members: 3, safety: "needs help" },
  { id: 5, head_name: "Jose Reyes", purok: "Purok 1", members: 7, safety: "evacuated" },
  { id: 6, head_name: "Liza Garcia", purok: "Purok 4", members: 2, safety: "safe" },
  { id: 7, head_name: "Ramon Bautista", purok: "Purok 6", members: 5, safety: "safe" },
  { id: 8, head_name: "Grace Mendoza", purok: "Purok 8", members: 4, safety: "safe" },
  { id: 9, head_name: "Carlo Villanueva", purok: "Purok 3", members: 6, safety: "evacuated" },
  { id: 10, head_name: "Rosa Aquino", purok: "Purok 2", members: 3, safety: "safe" },
];

// ERD table: EVACUATION_CENTERS
// "serves" is a sample-only rule for which Puroks go to which center.
export const evacuationCenters = [
  { id: 1, name: "Ampayon National High School", capacity: 500, occupants: 312, status: "open", serves: ["Purok 1", "Purok 2", "Purok 3"] },
  { id: 2, name: "Ampayon Covered Court", capacity: 200, occupants: 84, status: "open", serves: ["Purok 4", "Purok 5"] },
  { id: 3, name: "Ampayon Elementary School", capacity: 350, occupants: 0, status: "standby", serves: ["Purok 6"] },
  { id: 4, name: "St. Peter Parish Hall", capacity: 180, occupants: 171, status: "near capacity", serves: ["Purok 7", "Purok 8"] },
];

// ERD table: ADVISORIES (message and issued_at are suggested additions)
export const advisories = [
  {
    id: 1,
    title: "Yellow Rainfall Advisory",
    severity: "yellow",
    status: "active",
    message: "Moderate to heavy rainfall is expected within 24 hours. Residents near waterways should prepare to evacuate.",
    issued_at: "2026-11-05T08:40:00",
  },
  {
    id: 2,
    title: "Community Preparedness Drill",
    severity: "info",
    status: "active",
    message: "The barangay-wide earthquake and evacuation drill will be held on Saturday at 8:00 AM.",
    issued_at: "2026-11-04T15:15:00",
  },
  {
    id: 3,
    title: "Relief Pack Prepositioning",
    severity: "info",
    status: "archived",
    message: "Family food packs are ready at the covered court for emergency deployment.",
    issued_at: "2026-11-02T10:00:00",
  },
];

// ERD tables: INCIDENTS + INCIDENT_NOTES (these are the residents' assistance requests)
export const incidentTypes = {
  evacuation: "Evacuation assistance",
  relief: "Relief assistance",
  medical: "Medical assistance",
  rescue: "Rescue request",
  hazard: "Hazard report",
};

export const incidents = [
  {
    id: 1, reporter_id: 1, reporter_name: "Juan Dela Cruz", purok: "Purok 3",
    type: "relief", priority: "medium", status: "resolved",
    description: "Family food pack request for 6 members.",
    created_at: "2026-11-03T09:10:00",
    notes: [{ id: 1, text: "Food pack released at the covered court.", created_at: "2026-11-03T14:00:00" }],
  },
  {
    id: 2, reporter_id: 4, reporter_name: "Ana Flores", purok: "Purok 5",
    type: "medical", priority: "high", status: "in progress",
    description: "Senior family member needs maintenance medicine.",
    created_at: "2026-11-05T07:30:00",
    notes: [],
  },
  {
    id: 3, reporter_id: 9, reporter_name: "Carlo Villanueva", purok: "Purok 3",
    type: "rescue", priority: "high", status: "pending",
    description: "Floodwater rising near the house; two children at home.",
    created_at: "2026-11-05T08:55:00",
    notes: [],
  },
  {
    id: 4, reporter_id: 6, reporter_name: "Liza Garcia", purok: "Purok 4",
    type: "hazard", priority: "low", status: "pending",
    description: "Fallen tree branch blocking the road.",
    created_at: "2026-11-04T17:20:00",
    notes: [],
  },
];

// ERD table: EMERGENCY_CONTACTS
// Only 911 is a real number. The others must be checked with the barangay.
export const emergencyContacts = [
  { id: 1, name: "National Emergency Hotline", phone: "911", category: "National" },
  { id: 2, name: "Barangay Ampayon Rescue", phone: "To be verified", category: "Barangay" },
  { id: 3, name: "Butuan City DRRM Office", phone: "To be verified", category: "City" },
];

// PROPOSED tables (not in ERD v1): RELIEF_ITEMS and RELIEF_DISTRIBUTIONS
export const reliefItems = [
  { id: 1, name: "Family food pack", unit: "pack", quantity_in_stock: 1250 },
  { id: 2, name: "Bottled water (1 L)", unit: "bottle", quantity_in_stock: 8400 },
  { id: 3, name: "Hygiene kit", unit: "kit", quantity_in_stock: 320 },
  { id: 4, name: "Sleeping mat", unit: "piece", quantity_in_stock: 150 },
];

export const reliefDistributions = [
  { id: 1, household_id: 1, relief_item_id: 1, quantity: 1, distributed_at: "2026-11-03T14:00:00" },
  { id: 2, household_id: 2, relief_item_id: 1, quantity: 1, distributed_at: "2026-11-04T09:30:00" },
  { id: 3, household_id: 5, relief_item_id: 2, quantity: 12, distributed_at: "2026-11-04T10:15:00" },
  { id: 4, household_id: 9, relief_item_id: 3, quantity: 2, distributed_at: "2026-11-05T08:00:00" },
];

// PROPOSED table (not in ERD v1): SMS_LOGS
export const smsLogs = [
  { id: 1, advisory_id: 1, title: "Yellow Rainfall Advisory", recipients: 10, sent: 9, failed: 1, sent_at: "2026-11-05T08:42:00" },
  { id: 2, advisory_id: 2, title: "Community Preparedness Drill", recipients: 10, sent: 10, failed: 0, sent_at: "2026-11-04T15:20:00" },
];

// ---------- Small lookup helpers ----------

export function findHousehold(id) {
  return households.find((household) => household.id === id);
}

export function findReliefItem(id) {
  return reliefItems.find((item) => item.id === id);
}

// Sample rule: each Purok is assigned to one center
export function assignedCenterFor(purok) {
  return evacuationCenters.find((center) => center.serves.includes(purok));
}

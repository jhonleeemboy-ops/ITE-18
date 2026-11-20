// Fixed choices used by more than one page (not data from the database).

// Ampayon has 23 Puroks (barangay secretary). The API accepts the same range, 1 to 23.
// Makes the list "Purok 1", "Purok 2", ... "Purok 23".
export const puroks = Array.from({ length: 23 }, (_, i) => `Purok ${i + 1}`);

// The types of assistance request (INCIDENTS.type in the database) and their labels
export const incidentTypes = {
  evacuation: "Evacuation assistance",
  relief: "Relief assistance",
  medical: "Medical assistance",
  rescue: "Rescue request",
  hazard: "Hazard report",
};

// Advisory levels (ADVISORIES.severity in the database) and their labels
export const severityLabels = {
  info: "Information",
  yellow: "Yellow (be alert)",
  orange: "Orange (prepare)",
  red: "Red (act now)",
};

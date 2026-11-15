// Fixed choices used by more than one page (not data from the database).

// Purok 1 to 8, the same range the API accepts when a household registers.
export const puroks = ["Purok 1", "Purok 2", "Purok 3", "Purok 4", "Purok 5", "Purok 6", "Purok 7", "Purok 8"];

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

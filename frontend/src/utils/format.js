// Small helpers for showing numbers and dates nicely.

// "2026-11-05T08:40:00" -> "Nov 5, 2026, 8:40 AM"
export function formatDateTime(text) {
  return new Date(text).toLocaleString("en-US", {
    month: "short",
    day: "numeric",
    year: "numeric",
    hour: "numeric",
    minute: "2-digit",
  });
}

// 312 of 500 -> 62
export function percent(part, whole) {
  return whole ? Math.round((part / whole) * 100) : 0;
}

// 8400 -> "8,400"
export function formatNumber(value) {
  return value.toLocaleString("en-US");
}

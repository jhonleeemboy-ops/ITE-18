// Converts API data into the shape the pages already use (see data/sample-data.js).
const label = (value) => String(value ?? "").replace(/_/g, " ");

export const purokLabel = (n) => `Purok ${n}`;
export const purokNumber = (p) => Number(String(p).replace(/\D/g, ""));
export const toApiValue = (text) => String(text).trim().replace(/ /g, "_");

export function currentUser() {
  try {
    return JSON.parse(localStorage.getItem("user") || "null");
  } catch {
    return null;
  }
}

// "9171234567" -> "0917 123 4567"
export function formatMobile(n) {
  const d = String(n ?? "");
  return d.length === 10 ? `0${d.slice(0, 3)} ${d.slice(3, 6)} ${d.slice(6)}` : d;
}

export function mapHousehold(h) {
  return { id: h.id, head_name: h.head_name, purok: purokLabel(h.purok), members: h.members, safety: label(h.safety) };
}

export function mapCenter(c) {
  return {
    id: c.id, name: c.name, capacity: c.capacity, occupants: c.occupants,
    status: label(c.status),
    serves: (c.puroks ?? []).map((p) => purokLabel(p.purok)),
  };
}

export function mapIncident(i) {
  return {
    id: i.id, reporter_id: i.reporter_id,
    reporter_name: i.reporter?.name ?? "",
    purok: i.reporter?.household ? purokLabel(i.reporter.household.purok) : "",
    type: i.type, priority: i.priority, status: label(i.status),
    description: i.description ?? "", created_at: i.created_at,
    notes: i.notes ?? [],
  };
}

export function mapDistribution(d) {
  return {
    id: d.id, household_id: d.household_id, relief_item_id: d.relief_item_id,
    quantity: d.quantity, distributed_at: d.distributed_at,
    household: d.household ? mapHousehold(d.household) : null,
    item: d.relief_item ?? null,
  };
}

// sent_at === null means still queued (not confirmed by an SMS provider)
export function mapSmsLog(l) {
  return {
    id: l.id, advisory_id: l.advisory_id, title: l.advisory?.title ?? "",
    recipients: l.recipients, sent: l.sent, failed: l.failed, sent_at: l.sent_at,
  };
}

export function assignedCenterFor(centers, purok) {
  return centers.find((c) => c.serves.includes(purok));
}
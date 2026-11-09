# Frontend data fields (for matching the database and API)

Written 2026-11-06 for Jhon Lee, so the Laravel database and API match what the Vue frontend sends and shows. This is the start of the **API contract**: once Jhon Lee confirms the names, both sides use exactly these.

Field names follow ERD v1 where it has them. Fields marked **(new)** aren't in ERD v1 yet (see [erd-review.md](erd-review.md)).

## What the forms SEND to the backend

| Form (page) | Fields the user types | Notes / checks done in the browser |
|---|---|---|
| Resident **create account** (`#/register`) | `name`, `mobile_number`, `password`, `purok` | **Connected (Nov 9)** to `POST /api/register`. Sends `mobile_number` as 10 digits (e.g. `9171234567`) and `purok` as a **number 1–8**. Answer: 201 `{ user (with household), token }`; errors: 422 `{ message, errors: { field: [...] } }` |
| Resident **sign in** (`#/`) | `mobile_number`, `password` | Backend must check the password (hashed) and return the user + role |
| Staff **sign in** (`#/staff-login`) | `email`, `password` | The staff sign-in method is still to be confirmed |
| Resident **assistance request** (`#/resident/assistance`) | `type` (evacuation / relief / medical / rescue / hazard), `description` **(new)** | Saved as an INCIDENT; backend adds `reporter_id`, `status = pending`, `created_at` |
| Staff **SMS alert** (`#/staff/sms-alerts`) | `title`, `message` **(new)**, `send_to` (all or a Purok) | Saved as an ADVISORY + SMS log; must not claim "sent" unless the provider confirms it |

## What the pages SHOW (data the backend must return)

| Data | Fields used by the pages | Used on |
|---|---|---|
| Users | `id`, `household_id`, `name`, `mobile_number` **(new)**, `email`, `role` | Header (signed-in name), household page |
| Households | `id`, `head_name`, `purok`, `members`, `safety` (safe / evacuated / needs help) | Resident household page, staff household list (search by name, filter by Purok), dashboard totals |
| Evacuation centers | `id`, `name`, `capacity`, `occupants`, `status` (open / standby / near capacity) | Resident evacuation page, staff centers page, dashboard |
| Advisories | `id`, `title`, `severity` (yellow / orange / red / info), `status` (active / archived), `message` **(new)**, `issued_at` **(new)** | Resident home + alerts, sign-in page, staff dashboard |
| Incidents (assistance requests) | `id`, `reporter_id`, `type`, `priority` (high / medium / low), `status` (pending / in progress / resolved), `description` **(new)**, `created_at` **(new)** + reporter name and Purok | Resident assistance page, staff dashboard |
| Incident notes | `id`, `incident_id`, `text`, `created_at` | (Ready for a request detail view) |
| Emergency contacts | `id`, `name`, `phone`, `category` | Resident home |
| Relief items **(new table)** | `id`, `name`, `unit`, `quantity_in_stock` | Staff relief page |
| Relief distributions **(new table)** | `id`, `household_id`, `relief_item_id`, `quantity`, `distributed_at` | Staff relief page |
| SMS logs **(new table)** | `id`, `advisory_id`, `recipients`, `sent`, `failed`, `sent_at` | Staff SMS page |

## Sample-only rule to replace

- Which evacuation center a Purok goes to is a **sample rule** in the frontend (`serves` list). The real assignment should come from the database (e.g. a Purok column on centers, or an assignment table).

## Where this lives in the code

All of this is currently fake data in `frontend/src/data/sample-data.js`. When an API endpoint is ready, the matching import gets replaced by a call to that endpoint.

# ERD review (version 1, from Jhon Lee, 2026-11-05)

Image: [screenshots/erd-v1-2026-11-05.png](screenshots/erd-v1-2026-11-05.png)

These are **suggestions for Jhon Lee to decide on**. The final database design is his, as backend lead.

## What's in version 1

| Table | Fields | Links |
|---|---|---|
| HOUSEHOLDS | id, head_name, purok, members (int), safety | 1 household has 0..many users |
| USERS | id, household_id (FK), name, email, role | 1 user reports 0..many incidents |
| INCIDENTS | id, reporter_id (FK → users), type, priority, status | 1 incident has 0..many notes |
| INCIDENT_NOTES | id, incident_id (FK), text, created_at | |
| ADVISORIES | id, title, severity, status | none |
| EVACUATION_CENTERS | id, name, capacity (int), occupants (int), status | none |
| EMERGENCY_CONTACTS | id, name, phone, category | none |

Good: clear tables, foreign keys for users and incidents, incident notes as a separate table, roles on users (resident/staff).

## Important gaps (the title and the rubric depend on these)

| # | Gap | Why it matters | Suggestion |
|---|---|---|---|
| 1 | **No relief distribution tables** | "Relief Distribution" is in the title | `relief_items` (name, unit, quantity_in_stock) and `relief_distributions` (household_id FK, relief_item_id FK, quantity, distributed_at, distributed_by FK → users) |
| 2 | **USERS has no password and no mobile number** | Our login page uses mobile number + password; SMS needs phone numbers; the rubric checks hashed passwords | Add `mobile_number` (unique) and `password` (hashed by Laravel). Email can be optional, since many residents don't have one. |
| 3 | **No SMS log** | "SMS Alerts" is in the title; the rubric wants SMS failures handled | `sms_logs` (advisory_id FK, user_id FK or phone, status: queued/sent/failed, error_message, sent_at) |
| 4 | **Households and evacuation centers aren't linked** | Staff need to check evacuees in and out; occupancy should come from real records | `evacuations` (household_id FK, evacuation_center_id FK, people_count, checked_in_at, checked_out_at) |
| 5 | **ADVISORIES has no message text, author or date** | An alert needs a body to send by SMS | Add `message`, `created_by` (FK → users), `issued_at` |

## Smaller suggestions

- **IDs:** use whole numbers that count up automatically (Laravel's default `id()`) instead of `string`. Foreign keys then use `foreignId()`.
- **Dates:** `created_at` should be a date/time type, not `string`. Laravel adds `created_at` and `updated_at` to every table automatically.
- **Status and type fields:** write down the allowed values (e.g. incident status: pending / in progress / resolved). The rubric checks constraints and validation.
- **HOUSEHOLDS:** add `contact_number` and `address`. Explain what `safety` holds (e.g. safe / evacuated / missing).
- **Household members:** `members` is only a count. For profiling, consider `household_members` (household_id FK, name, birthdate, sex, vulnerability: senior / PWD / pregnant / infant), or at least a count of vulnerable members.
- **EVACUATION_CENTERS:** add `location` / `purok` and `facilities`.
- **INCIDENTS:** add `description`, `location` and `created_at`. These match the prototype's "assistance request" form.

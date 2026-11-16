# Handoff notes (read this first)

**For any AI assistant (Claude, Gemini, …) or teammate picking up the work.**
Read this file at the **start** of a session. At the **end** of a session, update "Current status", "Next tasks" and add a line to the "Session log", then commit and push.

The repo is public: **first names only** here, and **no secrets** (passwords, `.env`, API keys).

## The project

- **Disaster Ready**: household profiling, evacuation centers, relief distribution and SMS alerts for Barangay Ampayon, Butuan City.
- Group 4, ITE 18 (Applications Development and Emerging Technologies), Caraga State University.
- Team:
  - Jhann Clevill: design, frontend, coordination; Ch 3
  - Jhon Lee: backend + database, repo owner, leader; Ch 3
  - Marc Joven: Ch 1
  - John Humphrey: Ch 2
  - Ch 4–5: not assigned yet

## Deadlines

| When | What |
|---|---|
| **Nov 19, 2026** | Paper soft copy |
| **Nov 20, 2026, 11:59 PM** | Link of the working system (**must be online**) |
| Nov 23 – Dec 15 | Presentation + individual backend debugging exam (our slot likely Dec 7) |

Grading: paper 15%, system 30%, presentation 20%, **individual backend debugging 35%**. GitHub history counts per person. Details: [thesis-requirements.md](thesis-requirements.md).

## Tech stack

- **Frontend** (`frontend/`): Vue 3 + Vite + Vue Router (hash URLs `#/...`), plain external CSS, Axios.
- **Backend** (`backend/`): Laravel 13 + Sanctum (login tokens) + MySQL 8.4, run with Laragon (PHP 8.3, Composer).
- The system has a resident portal (sign in + register) and a staff dashboard (separate staff login).

## How to run (after the one-time setup in the [README](../README.md))

1. Laragon → **Start All**
2. `cd backend` → `php artisan migrate` (only if someone added a migration) → `php artisan serve --port=8000`
3. In a second terminal: `cd frontend` → `npm install` (only if package.json changed) → `npm run dev` → http://localhost:5173

**SMS (TextBee):** to really send texts, make a TextBee account, install the TextBee app on an Android phone with a SIM, generate an API key and put it in `backend/.env` as `TEXTBEE_API_KEY=` (optional: `TEXTBEE_DEVICE_ID`, `TEXTBEE_SIM_SLOT`). Never commit the key. With the key empty, alerts are still saved and shown in the portal, but no text is sent, and the page says so.

Demo accounts (password `password123`): staff `staff@brgy.test`, admin `admin@brgy.test`, resident mobile `09171234567`.

## Key files

| File | What it is |
|---|---|
| `backend/routes/api.php` | All API addresses |
| `frontend/src/api.js` | Axios client used by all pages (API base URL with a local fallback + login token on every request; a 401 sends you back to sign in) |
| `frontend/src/utils/mappers.js` | Turns API data into what the pages show; `currentUser()` for the layouts |
| `frontend/src/router.js` | Pages + guard: signed-out visitors and the wrong role go to the right sign in (token + user saved as `token`, `user` in the browser) |
| `frontend/src/pages/LoginPage.vue` | Resident sign in + register |
| `backend/app/Services/SmsSender.php` | Sends alerts through TextBee; turns gateway errors into messages staff understand |
| `frontend/src/utils/options.js` | Fixed choices shared by pages: Purok list, request types, advisory levels |
| `frontend/src/data/sample-data.js` | Old fake data. **No page imports it anymore** (can be deleted once the group agrees) |
| `frontend/src/services/` | Older API client + session helper; **not imported by any page anymore** |
| [frontend-data-fields.md](frontend-data-fields.md) | Every field the forms send and the pages show (API contract) |
| [checklist.md](checklist.md) | What's done / not done |
| [group-notes.md](group-notes.md) | Decisions from the group chat |
| [test-cases.md](test-cases.md) | Tests and results |
| [ai-use-log.md](ai-use-log.md) | Every AI tool used (the paper must declare them) |

## Current status (update at the end of each session)

*Last updated: 2026-11-16, desktop (Claude).*

- **Every page is connected to the API** (Jhon Lee, commit c8cac94, Nov 16). New: SMS through **TextBee**, household members (name, age, relationship, PWD), residents can edit their household, staff can add households, staff can add centers and check people in/out, staff can delete an alert. API tests N01–N16 pass; see [test-cases.md](test-cases.md).
- **Ampayon has 23 Puroks** (barangay secretary, via Marc). The system still only accepts **Purok 1–8** (5 backend checks, the seeder, `options.js`, `sample-data.js`). Must be fixed.
- The sign-in page now shows the **real** newest advisory (`GET /api/advisories/latest`), and every page except Home/Dashboard has a **Back to Home / Back to Dashboard** link (barangay feedback). Tests B01–B14.
- No staff page yet to update assistance requests (the API has `PATCH /incidents/{id}` and notes).
- No sign-out endpoint and login tokens never expire (`config/sanctum.php`: `'expiration' => null`).
- Barangay feedback on the prototype: wants a **back/home button**; asked what the **admin notification** is for; something about **inputting households** (details unclear).
- Hosting for the Nov 20 online link: **not decided**.
- Chapter 3 draft (system parts): [chapter3-draft.md](chapter3-draft.md).

## Next tasks (in order)

1. **Purok 1–8 → 1–23** in backend and frontend. Agree in the chat who does it.
2. Group: decide **hosting** (link due Nov 20), assign **Ch 4–5**.
3. Barangay feedback on household input: details still unclear. (Back/home: done. The admin notification was only in the Figma prototype.)
4. Staff page for assistance requests; sign out on the server.
5. Finish Chapter 3 (paper due Nov 19); check that its tables still match the code.
6. Ask Jhon Lee if the unused `frontend/src/services/` folder can be deleted; remove the duplicate `PUT /households/{household}` line in `routes/api.php`.

## Rules for AI assistants

- Read the existing code before changing it; build on it, don't rewrite it.
- Test before saying something works, and record the results in `test-cases.md`.
- Never commit secrets (`backend/.env` is gitignored; keep it that way).
- Label sample data as sample. Never pretend an SMS was sent.
- Don't invent facts about Barangay Ampayon (flooding, Puroks, hotlines).
- Add a row to `ai-use-log.md` for what you helped with.
- `git pull` before working; ask before pushing.

### When helping Jhann
- Keep replies short and simple; Jhann writes in Bisaya-English and is learning Vue, so explain code simply (Jhann has to defend it).
- Group chat messages: 1–5 lines, casual Bisaya-English.
- Commits should be from Jhann's GitHub account (`kuanjankalin-ITE18`) on every computer.

## Session log (add one line per session, newest first)

| Date | Computer / AI | What was done |
|---|---|---|
| 2026-11-16 | Desktop / Claude | Back to Home/Dashboard links on every page + real advisory on the sign-in page; 14 browser checks (B01–B14) |
| 2026-11-16 | Desktop / Claude | Pulled Jhon Lee's TextBee + staff pages update (c8cac94), ran the new migration, added empty TextBee keys to the local `.env`, 16 API tests + build; recorded the barangay's follow-up answers (23 Puroks) and feedback; updated the Ch 3 draft |
| 2026-11-13 | Desktop / Claude | Chapter 3 draft of the system parts (`docs/chapter3-draft.md`) |
| 2026-11-12 | Desktop / Claude | Pulled Jhon Lee's API-connected frontend (ed3f202) and tested it; added the missing API address fallback in `frontend/src/api.js` (pages failed without a `frontend/.env`); updated README + this file |
| 2026-11-11 | Desktop / Claude | Fresh setup: pulled 14 commits; installed Laragon 8.8 (PHP 8.3.36, MySQL 8.4.3), enabled PHP `zip`, added PHP/Composer/MySQL to PATH; backend + frontend set up and tested (both demo logins work) |
| 2026-11-09 | Desktop / Claude | Set up Jhon Lee's backend locally; connected Create account to the API (Axios, token saved, errors per field); 19 tests pass; created this handoff file |
| 2026-11-06 | Desktop / Claude | Frontend data fields list for Jhon Lee; reviewed the survey questionnaires |
| 2026-11-05 | Desktop / Claude | ERD review; moved the frontend to Vue (13-page draft with sample data) |
| 2026-10-15 | Desktop / Claude | First login page, Figma review, docs, GitHub setup |

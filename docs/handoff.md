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

Demo accounts (password `password123`): staff `staff@brgy.test`, admin `admin@brgy.test`, resident mobile `09171234567`.

## Key files

| File | What it is |
|---|---|
| `backend/routes/api.php` | All API addresses |
| `frontend/src/api.js` | Axios client used by all pages (API base URL with a local fallback + login token on every request; a 401 sends you back to sign in) |
| `frontend/src/utils/mappers.js` | Turns API data into what the pages show; `currentUser()` for the layouts |
| `frontend/src/router.js` | Pages + guard: signed-out visitors and the wrong role go to the right sign in (token + user saved as `token`, `user` in the browser) |
| `frontend/src/pages/LoginPage.vue` | Resident sign in + register |
| `frontend/src/data/sample-data.js` | Fake data still used by 3 staff pages (Evacuation Centers, Relief, SMS Alerts) and a few labels/Purok lists |
| `frontend/src/services/` | Older API client + session helper; **not imported by any page anymore** |
| [frontend-data-fields.md](frontend-data-fields.md) | Every field the forms send and the pages show (API contract) |
| [checklist.md](checklist.md) | What's done / not done |
| [group-notes.md](group-notes.md) | Decisions from the group chat |
| [test-cases.md](test-cases.md) | Tests and results |
| [ai-use-log.md](ai-use-log.md) | Every AI tool used (the paper must declare them) |

## Current status (update at the end of each session)

*Last updated: 2026-11-12, desktop (Claude).*

- **Sign in and Create account are connected** (Jhon Lee, Nov 11–12), for residents and staff, with a real Sign out and the user's real name in the menus.
- **Connected to the API:** resident Home, My Household, Evacuation, Assistance, Alerts; staff Dashboard and Household Profiles.
- **Still sample data:** staff Evacuation Centers, Relief Distribution, SMS Alerts. The backend endpoints for them already exist (`/evacuation-centers`, `/relief-items`, `/relief-distributions`, `GET`/`POST /sms-alerts`).
- Agreed in chat (Nov 12): Jhann adds the features on the frontend, Jhon Lee connects them to the backend.
- Data gathering (surveys) was on Nov 7: results not shared yet.
- Hosting for the Nov 20 online link: **not decided**.

## Next tasks (in order)

1. Connect the 3 staff pages still on sample data (Evacuation Centers, Relief, SMS Alerts). Agree in the chat who does which page first, so two people don't edit the same file.
2. Group: decide **hosting** (link due Nov 20), assign **Ch 4–5**, collect the **survey results**.
3. Chapter 3 draft (requirements tables, tech stack, API endpoints table, timeline). Paper due Nov 19.
4. Ask Jhon Lee if the unused `frontend/src/services/` folder can be deleted.

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
| 2026-11-12 | Desktop / Claude | Pulled Jhon Lee's API-connected frontend (ed3f202) and tested it; added the missing API address fallback in `frontend/src/api.js` (pages failed without a `frontend/.env`); updated README + this file |
| 2026-11-11 | Desktop / Claude | Fresh setup: pulled 14 commits; installed Laragon 8.8 (PHP 8.3.36, MySQL 8.4.3), enabled PHP `zip`, added PHP/Composer/MySQL to PATH; backend + frontend set up and tested (both demo logins work) |
| 2026-11-09 | Desktop / Claude | Set up Jhon Lee's backend locally; connected Create account to the API (Axios, token saved, errors per field); 19 tests pass; created this handoff file |
| 2026-11-06 | Desktop / Claude | Frontend data fields list for Jhon Lee; reviewed the survey questionnaires |
| 2026-11-05 | Desktop / Claude | ERD review; moved the frontend to Vue (13-page draft with sample data) |
| 2026-10-15 | Desktop / Claude | First login page, Figma review, docs, GitHub setup |

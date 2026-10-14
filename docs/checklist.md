# Project checklist

Last updated: 2026-10-15

A box is ticked only after the item has been checked and works. See [test-cases.md](test-cases.md) for test results.

## Phase 1: Project organization

- [x] Inspect the project folder and installed tools
- [x] Review every screen of the Figma prototype and save screenshots
- [x] Write README, status report, prototype notes and this checklist
- [ ] Fill in group member names and roles (table below)
- GitHub setup:
  - [x] Group repo exists (Jhon Lee: `jhonleeemboy-ops/ITE-18`)
  - [x] Jhann Clevill's GitHub account created (`kuanjankalin-ITE18`)
  - [x] Jhon Lee adds `kuanjankalin-ITE18` as collaborator, and the invite is accepted (push access confirmed 2026-10-15)
  - [x] Logged in to GitHub on this computer (`gh auth login`, checked with `gh auth status`)
  - [x] Commit name and email set for this project only (private GitHub noreply email)
  - [x] Project folder connected to the repo, first commit made locally, test push OK
  - [ ] Folder structure agreed with Jhon Lee (Jhann chose `frontend/`, `backend/`, `docs/`; Jhon Lee to confirm)
  - [x] First upload of the frontend and docs to the repo (2026-10-15)
  - [ ] Jhon Lee pulls the repo before pushing the backend
- [ ] Get the group's answers on the other open decisions

## Phase 2: Frontend foundation

- [x] Shared styles: colors, fonts, header, buttons, form fields (`frontend/css/base.css`)
- [x] Login page: sign in form with validation
- [x] Login page: create account form with validation
- [x] Login page works on desktop, tablet and phone
- [x] Labels, visible error messages, keyboard use
- [ ] Staff login page (waiting on decision 5: what staff sign in with)
- [ ] Shared navigation for the resident portal
- [ ] Shared sidebar for the staff portal

## Phase 3: Main pages (frontend only, sample data clearly labeled)

Resident portal:
- [ ] Home
- [ ] My Household (household profile)
- [ ] Evacuation (assigned center)
- [ ] Alerts and announcements
- [ ] Assistance requests (only if the group keeps it in scope)

Staff portal:
- [ ] Dashboard
- [ ] Household Profiles (list + add/edit household)
- [ ] Evacuation Centers (list, capacity, occupancy)
- [ ] Relief Distribution (record and review distributions)
- [ ] SMS Alerts (compose screen; real sending only in Phase 4)
- [ ] Reports & Analytics (only if the group keeps it in scope)

Public:
- [ ] Evacuation centers page (needs the verified list)
- [ ] Emergency hotlines page (needs verified numbers)

## Phase 4: Backend coordination

- [ ] Backend technology chosen (decision 1)
- [ ] ERD / database fields agreed with the backend member
- [ ] API contract written: endpoints, field names, responses
- [ ] Connect sign in and registration
- [ ] Connect household profiles
- [ ] Connect evacuation centers
- [ ] Connect relief distribution
- [ ] Connect alerts
- [ ] SMS provider approved (decision 6) and integrated
- [ ] Staff-only pages blocked for residents (checked on the server, not only in the browser)

## Phase 5: Testing and refinement

- [x] Login page tested (38 automated checks, see test-cases.md)
- [ ] Login page checked on a real Android phone
- [ ] Login page checked in Firefox and Edge
- [ ] Each new page tested before it is ticked above
- [ ] Access control tested once the backend exists

## Phase 6: Documentation

- [x] README with how to open the project
- [ ] Setup guide for the backend (after decision 1)
- [ ] Final list of finished and unfinished features
- [ ] Dependencies and configuration documented

---

## Group task list

Names are unknown for now. Replace "TBD" when roles are confirmed.

Members and agreements so far: see [group-notes.md](group-notes.md).

| Task | Area | Responsible | Status | Depends on | Next action |
|---|---|---|---|---|---|
| Login page (sign in + create account) | Frontend | Jhann Clevill | Done (frontend only) | None | Check it on a real phone |
| Confirm roles of members 3 and 4 | Coordination | Jhann Clevill | In progress | None | Ask in group chat |
| Backend language | Backend | Jhon Lee | Node.js agreed in chat | None | Confirm Express.js + which database |
| Frontend framework (plain JS vs Svelte/Vue) | Coordination | Group | Open | None | Confirm in group chat |
| GitHub repository | Coordination | Jhon Lee (owner) | Frontend + docs uploaded 2026-10-15 | None | Jhon Lee pulls, confirms layout, adds `backend/` |
| ERD / database fields | Backend | Jhon Lee | Promised this week (by ~Oct 18) | Database choice | Compare with [prototype-notes.md](prototype-notes.md) field list |
| Backend (sign in, register, data) | Backend | Jhon Lee | Planned for week of Oct 19 | ERD | |
| API contract for sign in and registration | Integration | Jhon Lee + Jhann Clevill | Not started | Backend language | Draft from the login form fields, Jhon Lee reviews |
| Verify Purok list, evacuation centers, hotlines | Research | TBD | Not started | None | Ask the barangay office |
| Staff sign-in method and roles | Coordination | Group | Not started | None | Group decision |
| Resident portal layout + Home page | Frontend | Jhann Clevill | Not started | None | Can start now with labeled sample data |
| SMS provider and budget | Backend | Group + adviser | Not started | Backend | Compare providers and costs later |

## Open decisions (need group agreement)

| Decision | Status |
|---|---|
| 1. Backend technology | Node.js agreed in chat; Express.js and the database still to confirm |
| 2. Git/GitHub | Done: group repo jhonleeemboy-ops/ITE-18, frontend + docs uploaded. Jhon Lee to confirm the folder layout. |
| 2b. Frontend framework: plain HTML/CSS/JS or Svelte/Vue | Open |
| 3. Scope: Assistance requests, Reports & Analytics | Open |
| 4. Real Purok list, evacuation centers, hotline numbers | Open |
| 5. Staff sign-in method and roles | Open |
| 6. SMS provider and budget | Open (later) |
| 7. Group member names and roles | Partly known: Jhon Lee = backend, Jhann Clevill = design/frontend; members 3 and 4 open |

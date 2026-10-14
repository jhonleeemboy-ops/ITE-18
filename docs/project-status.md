# Project status report

**Date:** 2026-10-15
**Prepared for:** Group 4

## 1. What was in the project folder

The folder `C:\claude clevill` was empty when we started. There was no code, Git repository, backend, database design (ERD) or documentation yet.

What it contains now is listed in the [README](../README.md#folder-structure).

## 2. Tools on this computer

| Tool | Status | Needed now? |
|---|---|---|
| Git 2.55 | Installed | Yes, once the group decides where the code lives (see decision 2) |
| Node.js 24 and npm 11 | Installed | No. The frontend is plain HTML, CSS and JavaScript. |
| Python 3.14 | Installed | Only to run a simple local test server (optional) |
| VS Code | Installed | Code editor |
| PHP, MySQL, XAMPP | **Not installed** | Depends on the backend decision (see decision 1) |

Nothing was installed or downloaded for this milestone.

## 3. What the Figma prototype contains

The prototype has more than the login page. It has **13 screens** in three areas:

| Area | Screens |
|---|---|
| Public | Sign in, Create account |
| Resident portal | Home, My Household, Evacuation, Assistance, Alerts |
| Staff portal | Dashboard, Household Profiles, Evacuation Centers, Relief Distribution, SMS Alerts, Reports & Analytics |

Important to know:

- It is a **clickable mockup**. No real login happens and nothing is saved.
- **All names, numbers and records in it are made-up sample data** (for example "2,418 households" and "Maria Alvarez, Barangay Administrator").
- Two screens go beyond our feature list (A to F): **Assistance requests** (residents asking for help) and **Reports & Analytics**. The group needs to decide if these are in scope.

Every screen is saved in `docs/screenshots/figma-prototype/`, and each one is described in [prototype-notes.md](prototype-notes.md).

## 4. What we can reuse

- **Visual design:** the exact colors, fonts (Manrope and DM Sans), spacing and icons were copied from the prototype into `frontend/css/base.css`.
- **Screen list and fields:** the fields shown in the prototype (household head, members, vulnerable members, emergency contact, Purok, and so on) are a good starting list for the database fields. They still need to be confirmed with the backend member.
- **The prototype's own code:** not reused. Figma Make generates React code, but we agreed on plain HTML, CSS and JavaScript so everyone in the group can read and defend it.

## 5. First milestone: login page (done)

Built `frontend/index.html` from the prototype's login screen:

- Sign in form and Create account form on one page, switching like the prototype does
- Checks every field before submitting: required fields, a valid Philippine mobile number (accepts `917 123 4567`, `0917…`, `+63 917…`), a password of at least 8 characters, and a chosen Purok
- Clear red error messages under each field
- Works on desktop, tablet and phones (tested down to 320px wide)
- Keyboard and screen reader basics: every field has a label, and focus moves to the first error

What it does **not** do yet, on purpose:

- It does not actually sign in or create an account (no backend yet). After a valid submit it shows: *"Your details look correct, but sign-in is not connected to the server yet."*
- "Barangay staff login", "Evacuation centers" and "Emergency hotlines" show a "not built yet" message.
- The rainfall advisory is sample text and is labeled **Sample** on the page.

Test results: [test-cases.md](test-cases.md).

### Differences from the prototype (on purpose)

| Change | Why |
|---|---|
| Small text made bigger (prototype labels are 8 to 9px; ours are 12 to 16px) | 8px text is too small to read, especially for older residents. Our design requirements say "readable text". |
| A few gray and amber text colors made slightly darker | The originals were slightly below the readability (contrast) standard. They look almost the same. |
| Purok dropdown starts on "Select your Purok" instead of "Purok 1" | Otherwise people who skip it are silently registered as Purok 1. |
| "Demo: enter any mobile number to continue" changed to an honest prototype note | Our page has nothing to continue to yet, so that sentence would be untrue. |
| "Sample" label on the advisory | The advisory is made-up text, not a real barangay alert. |

## 6. Decisions the group needs to make

**Update 2026-10-15 from the group chat:** backend will be Node.js (Jhon Lee), the GitHub repo exists but is empty, the ERD is expected this week, and Jhann Clevill handles design. The current status of each decision is in [checklist.md](checklist.md#open-decisions-need-group-agreement), and the details are in [group-notes.md](group-notes.md).

These are blocking the next phases. Bring them to the next group meeting.

1. **Backend technology.** Who is the backend member, and which language and database? PHP with MySQL (through XAMPP) is common for college projects. Node.js is already installed here. *Blocks:* real sign-in, registration and everything that saves data.
2. **Git and GitHub.** Does the group already have a GitHub repository? If yes, we should use that one instead of starting a separate one here. If not, should we create one? *Blocks:* safe sharing of code between members.
3. **Feature scope.** Are "Assistance requests" and "Reports & Analytics" (both in the prototype) part of the project, or not?
4. **Real data to verify with the barangay:**
   - How many Puroks Ampayon has (the prototype says Purok 1 to 8)
   - The real list of evacuation centers and their capacities
   - The hotline numbers in the prototype (Barangay Rescue 0917 700 1122, Butuan CDRRMO (085) 342 2121). These are unverified and must not be shown as real until checked. 911 is the national emergency number.
5. **Staff accounts.** What do staff sign in with (username, email or mobile number)? Is there just one "Barangay Administrator" role, or also others (for example encoder or BDRRMO staff)?
6. **SMS provider and budget.** Needed later (Phase 4). SMS costs money per message, so the group and adviser should approve a provider first.
7. **Group members and roles.** Names and responsibilities, to fill in the task list in [checklist.md](checklist.md).

## 7. Suggested next step

Without waiting on the decisions above, the next frontend task can be the **shared layout for the resident portal** (top navigation plus the Home page) using clearly labeled sample data. Once decision 1 is made, the backend member and the frontend member should agree on the **API contract**: the exact field names and responses for sign in and registration.

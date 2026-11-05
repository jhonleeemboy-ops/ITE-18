# Disaster Ready

**Disaster Ready: A Household Profiling, Evacuation Center, and Relief Distribution Management System with SMS Alerts for Barangay Ampayon, Butuan City**

Group 4 college mini-thesis (ITE 18).

## Current status (2026-11-05)

| Part | Status |
|---|---|
| Frontend (Vue) | **Draft built and tested.** Resident sign in + register, staff sign in, the resident portal (5 pages) and the staff dashboard (5 pages). It uses **sample data only** and isn't connected to a backend yet. |
| Backend (Laravel + MySQL) | Not added yet (Jhon Lee). ERD v1 received; see [docs/erd-review.md](docs/erd-review.md). |
| SMS | Not started. The provider still needs to be chosen. |

## How to run the frontend

You need **Node.js** (version 24 LTS) installed.

1. Open a terminal in the `frontend` folder (in VS Code: right-click the `frontend` folder → **Open in Integrated Terminal**).
2. The first time only, install the packages:
   ```
   npm install
   ```
3. Start the app:
   ```
   npm run dev
   ```
4. Open **http://localhost:5173** in your browser. The page refreshes by itself every time you save a file.

To stop it, click in the terminal and press **Ctrl + C**.

**Demo sign in:** any valid mobile number (e.g. `0917 123 4567`) with any password opens the sample resident portal. On the staff sign-in page, any valid email (e.g. `staff@example.com`) opens the sample dashboard. Nothing is checked against real accounts yet.

To make the final files for hosting: `npm run build` (they appear in `frontend/dist`).

## Folder structure

```
ITE-18
├── frontend                       Vue website (Jhann Clevill)
│   ├── index.html                 The one HTML page Vue starts from
│   ├── package.json               List of packages + the dev/build commands
│   ├── vite.config.js             Settings for Vite (runs and builds the app)
│   ├── public/logo.svg            Logo / browser tab icon
│   └── src
│       ├── main.js                Starts Vue, the router and the CSS
│       ├── App.vue                Outer shell of the app
│       ├── router.js              Which page shows for each address
│       ├── css/                   Plain CSS: base, login, app (shared), portal, staff
│       ├── data/sample-data.js    ALL the sample data (made up), using the ERD field names
│       ├── utils/                 Form checks (validation.js) and date/number formatting
│       ├── composables/toast.js   The small pop-up message
│       ├── components/            Reusable pieces: logo, badges, occupancy bar, icons
│       ├── layouts/               Resident portal frame (top menu), staff frame (side menu)
│       └── pages/                 Login pages + resident/ and staff/ pages
├── backend                        Laravel + MySQL (Jhon Lee, not added yet)
└── docs
    ├── project-status.md          What exists, tools, blockers, decisions needed
    ├── checklist.md               Phase checklist and group task list
    ├── group-notes.md             Members, roles, deadlines and what the group agreed in chat
    ├── thesis-requirements.md     Paper template + grading rubric summary + Chapter 3 checklist
    ├── erd-review.md              Review of ERD v1 with suggestions
    ├── ai-use-log.md              Record of AI/tools used, for the paper's Declaration page
    ├── prototype-notes.md         What each Figma screen contains, design colors and fonts
    ├── test-cases.md              What was tested and the results
    └── screenshots                Figma prototype, ERD, and our builds
```

Frontend and backend stay in separate folders, so two people don't edit the same files.

## Rules for the group

- Never put passwords, API keys, database passwords or SMS provider keys in frontend files. Anyone can read those in the browser. Keep them in the backend's `.env` file, which is never uploaded.
- Any sample or made-up data must be clearly labeled as sample. Never present it as real barangay records.
- Don't say a feature works until it has been tested. Record results in [docs/test-cases.md](docs/test-cases.md).
- Pull before you push (`git pull`), so you don't overwrite each other's work.

## Design reference

Figma prototype: https://crater-flaky-19710052.figma.site/

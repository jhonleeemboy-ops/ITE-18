# Disaster Ready

**Disaster Ready: A Household Profiling, Evacuation Center, and Relief Distribution Management System with SMS Alerts for Barangay Ampayon, Butuan City**

Group 4 college mini-thesis.

## Current status (2026-10-15)

| Part | Status |
|---|---|
| Login page (sign in + create account) | Built and tested. The forms check what you type but do **not** sign in or save anything, because there is no backend yet. |
| Resident portal pages | Not built yet |
| Staff portal pages | Not built yet |
| Backend, database, SMS | Not started here. Waiting on the group's decision (see [docs/project-status.md](docs/project-status.md)). |

## How to open the project

1. Open the `frontend` folder.
2. Double-click `index.html`. It opens in your browser.

That's all. No installation is needed for the frontend. The fonts load from Google Fonts, so you need internet for the page to look exactly right. Without internet it still works, but it uses a plainer font.

Optional: in VS Code you can use the **Live Server** extension, or run this in a terminal inside the `frontend` folder and open http://127.0.0.1:5510:

```
python -m http.server 5510
```

## Folder structure

```
ITE-18
├── frontend                    Website (Jhann Clevill)
│   ├── index.html              Login page (sign in + create account)
│   ├── css
│   │   ├── base.css            Shared styles: colors, fonts, header, buttons, form fields
│   │   └── login.css           Styles used only by the login page
│   ├── js
│   │   └── login.js            Form switching and validation for the login page
│   └── assets/icons/logo.svg   Logo (also used as the browser tab icon)
├── backend                     Server and database (Jhon Lee, not added yet)
└── docs
    ├── project-status.md       What exists, tools, blockers, decisions needed
    ├── checklist.md            Phase checklist and group task list
    ├── group-notes.md          Members, roles, links and what the group agreed in chat
    ├── prototype-notes.md      What each Figma screen contains, design colors and fonts
    ├── test-cases.md           What was tested and the results
    └── screenshots
        ├── figma-prototype     Every screen of the Figma prototype, for reference
        └── build-v1            Screenshots of our built login page
```

New frontend pages will go in `frontend/pages` when we build them. Frontend and backend stay in separate folders, so two people don't edit the same files.

## Rules for the group

- Never put passwords, API keys, database passwords or SMS provider keys in HTML, CSS or JavaScript files. Anyone can read those files in the browser.
- Any sample or made-up data must be clearly labeled as sample. Never present it as real barangay records.
- Don't say a feature works until it has been tested. Record results in [docs/test-cases.md](docs/test-cases.md).

## Design reference

Figma prototype: https://crater-flaky-19710052.figma.site/

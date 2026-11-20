# Chapter 3 draft: the parts that come from the system

**Draft, updated 2026-11-16.** Jhann Clevill + Jhon Lee. These are the Chapter 3 parts we can write from the code without waiting for the survey results. All versions, endpoints and rules below were checked against the repo (commit c8cac94, Jhon Lee's TextBee update) and the desktop setup.

*Notes in italics are for the group. Delete them before pasting into the paper.*

**Not in this draft (waiting for Jhon Lee's processing of the survey and interview):** 3.2 Respondents and sampling, 3.3 Requirements gathering, and 3.9 Evaluation plan. 3.1 (timeline), 3.4.3 (use cases), 3.5 diagrams (architecture, ERD, data dictionary) and 3.8 (testing plan) are also still to do.

*Facts from the barangay to use in 3.3 (follow-up answers in the group chat): Ampayon has 23 Puroks; about 500+ households (barangay secretary); counting affected families by hand takes 1–2 days; the official interviewed is the Barangay Secretary.*

---

## 3.4.1 Functional requirements

Priorities use MoSCoW: **Must** (the system fails without it), **Should** (important, but the system still works without it), **Could** (only if there is time).

*The "Now" column is for the group, not the paper. Every **Must** has to work end to end for every role by Nov 26 (rubric B; deadline extended from Nov 20). If one can't be finished, we should lower its priority honestly rather than claim it.*

**Table 5. Functional requirements**

| ID | Requirement | User role | Priority | Acceptance criterion | *Now* |
|---|---|---|---|---|---|
| FR-01 | The system shall let a resident create an account with their name, mobile number, password, Purok (1 to 23) and number of household members, which also creates their household record. | Resident | Must | A valid form returns the new account and signs the resident in. A mobile number that is already registered is rejected with a message. | *Done. Purok 1–23 since Nov 21 (tests P01–P03).* |
| FR-02 | The system shall let residents sign in with their mobile number and staff sign in with their email, each with a password. | Resident, Staff | Must | Correct details open the right portal. A wrong password shows "The mobile number/email or password is incorrect." | *Done* |
| FR-03 | The system shall allow only barangay staff and administrators to use staff functions. | Staff, Admin | Must | A resident's request to any staff function is refused (HTTP 403), and the staff pages do not open for residents. | *Done* |
| FR-04 | The system shall let a signed-in user sign out. | All | Must | After signing out, the user is returned to the sign-in page and their login token no longer works. | *Half done: the browser forgets the token, but the server still accepts it (see NFR-03)* |
| FR-05 | The system shall let staff view all household profiles, search them by household head and filter them by Purok. | Staff | Must | Searching a name shows only matching households. Choosing a Purok shows only that Purok. | *Done* |
| FR-06 | The system shall let a resident view their own household profile. | Resident | Must | The My Household page shows the resident's own household and no one else's. | *Done* |
| FR-07 | The system shall let a resident mark their household as safe, evacuated or needing help, and let staff update any household's status. | Resident, Staff | Must | The new status is saved and appears on the staff household list and dashboard. A resident cannot change another household. | *Done for residents. Staff: API done, no button on the staff page yet.* |
| FR-08 | The system shall record the members of each household with their name, age, relationship and whether they are a person with disability (PWD). Members aged 60 and above are labeled senior citizens. | Resident, Staff | Must | A member added on the My Household page appears in the list with "Senior" or "PWD" labels where they apply. A resident cannot see another household's members. | *Done on the resident page (tests N10–N13). The staff page doesn't show members yet. Supported by the interview: seniors and PWDs are assessed and evacuated first.* |
| FR-09 | The system shall show the evacuation centers with their capacity, current number of occupants and the Puroks assigned to each. | All | Must | The list matches the database, and the occupancy bar shows occupants out of capacity. | *Done* |
| FR-10 | The system shall let staff record people arriving at (check in) and leaving (check out) an evacuation center, and update the center's status automatically. | Staff | Must | The occupant count changes by the number entered. Checking in more people than the space left is refused with "Only N places left in this center." | *Done (tests N06–N09)* |
| FR-11 | The system shall let staff view relief items and the quantity in stock. | Staff | Must | The list shows each item with its unit and quantity left. | *Done* |
| FR-12 | The system shall let staff record a relief distribution to a household and reduce the stock automatically. | Staff | Must | The stock goes down by the quantity given. Giving more than the stock is refused with "Only N … left in stock." | *Done* |
| FR-13 | The system shall let staff view the history of relief distributions. | Staff | Must | The latest distributions are listed with household, item, quantity and date. | *Done* |
| FR-14 | The system shall let a resident send an assistance request (evacuation, relief, medical, rescue or hazard) with a description. Medical and rescue requests are marked high priority automatically. | Resident | Must | The request is saved as Pending, and a medical or rescue request shows High priority. | *Done* |
| FR-15 | The system shall let a resident see the status of their own assistance requests. | Resident | Must | Only the resident's own requests are listed, with their current status. | *Done* |
| FR-16 | The system shall let staff view all assistance requests, change their status and priority, and add notes. | Staff | Must | A status change made by staff is shown to the resident who sent the request. | *API done; the staff dashboard only reads requests, no page to update them yet* |
| FR-17 | The system shall show the barangay's advisories to all signed-in users, newest first. | All | Must | A new advisory appears at the top of the resident Alerts page. | *Done* |
| FR-18 | The system shall let staff write an alert for all residents or for one Purok, saving it as an advisory and counting the residents who will receive it. | Staff | Must | The alert appears in the advisories and in the SMS log with the number of recipients. | *Done (test N02)* |
| FR-19 | The system shall send each alert by SMS through the TextBee SMS gateway and record how many messages the gateway accepted and how many failed. | Staff | Must | Test phones receive the SMS, and the SMS log shows the sent and failed counts. | *Built by Jhon Lee. Not tested with a real phone yet (needs a TextBee key).* |
| FR-20 | The system shall let staff view the history of alerts sent (the SMS log). | Staff | Should | Each alert is listed with its title, number of recipients, sent, failed and date. | *Done* |
| FR-21 | The system shall show the emergency hotlines to all signed-in users. | All | Must | The hotlines list matches the numbers confirmed by the barangay. | *Done, but the numbers are sample data until the barangay confirms them* |
| FR-22 | The system shall show staff a dashboard summary: households in total and by Purok, households by safety status, evacuation center capacity and occupants, assistance requests by status, and active advisories. | Staff | Must | The numbers match the database counts. | *Done* |
| FR-23 | The system shall produce a summary report of affected families, evacuees and relief given, which staff can print or save. | Staff | Should | Staff can print a report for a chosen date range. | *Not started. The barangay reports affected-family counts to the city, and counting by hand takes 1–2 days (interview). The Figma prototype has a "Reports & Analytics" screen.* |
| FR-24 | The system shall let staff add a household that was listed by barangay personnel (a family without its own account). | Staff | Must | The new household appears in the household list and dashboard counts. | *Done* |
| FR-25 | The system shall let a resident correct their household details (household head, Purok, number of members). | Resident, Staff | Should | The changes appear on the staff household list. A resident cannot edit another household. | *Done for residents; staff can do it through the API only* |
| FR-26 | The system shall let staff add an evacuation center with its capacity and the Puroks it serves. | Staff | Should | The new center appears on the resident Evacuation page. A duplicate name is refused. | *Done* |
| FR-27 | The system shall let staff delete an alert, removing it from the resident portal. | Staff | Should | The deleted alert no longer appears for residents. Texts already sent cannot be recalled. | *Done (tests N04–N05)* |
| FR-28 | The system shall show the newest active advisory on the sign-in page, without signing in. | Public | Could | The sign-in page shows the same advisory as the resident Alerts page. | *Done (tests N01, B01, B11, B12)* |

---

## 3.4.2 Non-functional requirements

**Table 6. Non-functional requirements**

| ID | Category | Requirement | How it is measured | *Now* |
|---|---|---|---|---|
| NFR-01 | Security | Passwords shall be stored only as bcrypt hashes, never as plain text. | Database check of the `users` table. | *Verified Nov 9 (`$2y$12$…`)* |
| NFR-02 | Security | Every API endpoint except register, sign in and the latest advisory shall require a valid login token. | A request without a token returns HTTP 401. | *Verified (test C05)* |
| NFR-03 | Security | Signing out shall cancel the login token on the server, and tokens shall expire after a set time. | After sign out, the old token returns HTTP 401. | *Not yet: there is no sign-out endpoint, and tokens never expire (`'expiration' => null` in `config/sanctum.php`)* |
| NFR-04 | Security / privacy | Staff functions shall be refused for residents, and a resident shall see only their own household, household members and assistance requests. | Resident token on staff endpoints returns HTTP 403. | *Verified (tests C04, C07, N03, N04, N09, N12)* |
| NFR-05 | Security | Passwords, database credentials and the SMS gateway key shall be kept in the server's environment file (`.env`) and never in the repository or the frontend. | The repository contains no `.env` file or keys. | *True now. The TextBee key is read from `.env` (`TEXTBEE_API_KEY`).* |
| NFR-06 | Reliability | All input shall be checked on the server. Invalid input returns HTTP 422 with a message for each wrong field, and nothing is saved. | API tests with empty and wrong values. | *Verified (tests A02–A04, C06, N06)* |
| NFR-07 | Reliability | Recording a relief distribution or a check-in shall update the counts together or not at all, so stock and occupants can never go below zero or above capacity. | Requests that exceed the stock or the space left are refused. | *Built (database transactions with row locks). Verified for check-in (N06); not tested yet for relief.* |
| NFR-08 | Reliability | If the SMS gateway fails, the alert shall still be saved and shown in the resident portal, the failure shall be recorded in the SMS log, and staff shall see the reason in plain words (for example a wrong key, the gateway phone not ready, or the plan limit reached). | Send an alert with no key, a wrong key, and the gateway phone off. | *Built. Verified with no key (N02); wrong key and phone off not tested yet.* |
| NFR-09 | Usability | Every page shall work on screens from 320 px (small phones) to 1440 px wide without sideways scrolling. | Browser tests at 320, 375, 768 and 1440 px. | *Verified on the earlier sample-data pages (test V16); to be repeated on the new pages* |
| NFR-10 | Usability | Text shall be at least 12 px, and every form field shall have a label. Errors are shown under the field they belong to. | Page check. | *Done on the pages checked so far* |
| NFR-11 | Usability | The system shall reach a System Usability Scale (SUS) score of at least 68 (the commonly used average) with residents and barangay staff. | SUS questionnaire after the users try the system. | *Target. Part of the evaluation plan (3.9).* |
| NFR-12 | Performance | Lists and the dashboard shall load within 3 seconds on the deployed server over a mobile data connection. | Timed page loads on the deployed link. | *Target. Can only be measured after hosting.* |
| NFR-13 | Availability | The system shall be online at a public link from November 26, 2026. | The link opens from a phone on mobile data. | *Hosting not decided yet* |
| NFR-14 | Usability | Every page shall have a clear way back to the Home page (residents) or the Dashboard (staff). | Each page except Home/Dashboard shows a "Back to…" link that opens it. | *Done (tests B03–B10). Added because the barangay found it confusing where to go back.* |

---

## 3.4.4 Hardware and software requirements

**Users (deployed system)**

| Who | Needs |
|---|---|
| Barangay staff | A computer, tablet or smartphone with an up-to-date web browser (Chrome, Edge, Firefox or Safari) and an internet connection. |
| Residents using the portal | A smartphone or computer with a web browser and internet. |
| Residents receiving alerts | **Any mobile phone that can receive SMS.** No smartphone or internet is needed, so the alerts also reach residents without data. |
| SMS gateway (barangay) | One Android phone with a SIM that has SMS load, the TextBee app, and an internet connection. It sends the texts for the system. |

*Browsers actually tested so far: Chromium only (automated tests). Firefox, Safari and a real phone are not tested yet. The barangay's computers and internet were unclear in the interview, so we need to confirm them.*

**Server (deployed system):** PHP 8.3 or newer, MySQL 8, HTTPS, and a host that can run a Laravel application. *Host to be decided by the group.*

**Development**

| Item | Used |
|---|---|
| Computer | Windows 10/11 PC or laptop. *Each member fills in their own specs (CPU, RAM).* |
| Local server | Laragon 8.8 (PHP 8.3.36, MySQL 8.4.3, Composer 2.10.3) |
| Frontend tools | Node.js 24.20.0 with npm 11.19.0 |
| Editor and version control | Visual Studio Code, Git 2.55, GitHub |

---

## 3.5 System design: API endpoints

All endpoints start with `/api`. Every endpoint except register, sign in and the latest advisory needs the login token in the `Authorization: Bearer` header. "Signed-in" means any role (resident, staff or admin).

Status codes used: **200** OK, **201** created, **204** deleted, **401** not signed in, **403** the role is not allowed, **404** record not found, **422** invalid input.

**Table 8. API endpoints**

| Method | Endpoint | Who can use it | Status codes | Requirement |
|---|---|---|---|---|
| POST | /register | Public | 201, 422 | FR-01 |
| POST | /login | Public | 200, 422 | FR-02 |
| GET | /advisories/latest | Public | 200 | FR-28 |
| GET | /me | Signed-in | 200, 401 | FR-06 |
| GET | /households | Staff, Admin | 200, 401, 403 | FR-05 |
| POST | /households | Staff, Admin | 201, 401, 403, 422 | FR-24 |
| PUT | /households/{id} | Staff, Admin; residents for their own household only | 200, 401, 403, 404, 422 | FR-25 |
| PATCH | /households/{id}/safety | Staff, Admin; residents for their own household only | 200, 401, 403, 404, 422 | FR-07 |
| GET | /households/{id}/members | Staff, Admin; residents for their own household only | 200, 401, 403, 404 | FR-08 |
| POST | /households/{id}/members | Staff, Admin; residents for their own household only | 201, 401, 403, 404, 422 | FR-08 |
| DELETE | /household-members/{id} | Staff, Admin; residents for their own household only | 204, 401, 403, 404 | FR-08 |
| GET | /evacuation-centers | Signed-in | 200, 401 | FR-09 |
| POST | /evacuation-centers | Staff, Admin | 201, 401, 403, 422 | FR-26 |
| PATCH | /evacuation-centers/{id}/occupants | Staff, Admin | 200, 401, 403, 404, 422 | FR-10 |
| GET | /advisories | Signed-in | 200, 401 | FR-17 |
| GET | /emergency-contacts | Signed-in | 200, 401 | FR-21 |
| GET | /incidents | Signed-in (residents receive only their own) | 200, 401 | FR-15, FR-16 |
| POST | /incidents | Signed-in | 201, 401, 422 | FR-14 |
| PATCH | /incidents/{id} | Staff, Admin | 200, 401, 403, 404, 422 | FR-16 |
| POST | /incidents/{id}/notes | Staff, Admin | 201, 401, 403, 404, 422 | FR-16 |
| GET | /relief-items | Staff, Admin | 200, 401, 403 | FR-11 |
| GET | /relief-distributions | Staff, Admin | 200, 401, 403 | FR-13 |
| POST | /relief-distributions | Staff, Admin | 201, 401, 403, 422 | FR-12 |
| GET | /dashboard | Staff, Admin | 200, 401, 403 | FR-22 |
| POST | /sms-alerts | Staff, Admin | 201, 401, 403, 422 | FR-18, FR-19 |
| GET | /sms-alerts | Staff, Admin | 200, 401, 403 | FR-20 |
| DELETE | /sms-alerts/{id} | Staff, Admin | 204, 401, 403, 404 | FR-27 |

*Still missing for the requirements above: sign out (FR-04, NFR-03) and the summary report (FR-23). "Admin" currently has the same rights as "Staff". If the paper describes extra admin rights, the code needs them too.*

*Still to add in 3.5: the architecture diagram (Jhon Lee's, with "Vue" instead of "HTML / CSS / JavaScript" and "TextBee SMS gateway" as the SMS API), the ERD and data dictionary from the migrations (including the new `household_members` table), and the Figma link: https://crater-flaky-19710052.figma.site/*

---

## 3.6 Technologies

**Table 9. Technologies used**

| Technology | Version | Used for | Why we chose it | Licence |
|---|---|---|---|---|
| Vue | 3.5.45 | Frontend framework (pages and components) | Required to use a framework (instructor, Oct 22). Simple to learn, and each page is one file that is easy to read and defend. | MIT |
| Vue Router | 5.4.0 | Moving between pages, and blocking pages by role | Official router for Vue | MIT |
| Vite | 8.3.6 | Development server and building the final files | Fast reload while coding; official Vue tooling | MIT |
| Axios | 1.21.0 | Sending requests from the frontend to the API | Adds the login token to every request in one place | MIT |
| Plain CSS | — | Styling | Everyone in the group already knows it, and it is easy to debug (group decision, Nov 3) | — |
| Laravel | 13.40.0 | Backend API, validation, database access | Built-in validation, migrations, seeders and password hashing | MIT |
| Laravel Sanctum | 4.3.3 | Login tokens for the API | Official Laravel package for token login | MIT |
| PHP | 8.3.36 | Language of the backend | Required by Laravel 13; included in Laragon | PHP License 3.01 |
| MySQL Community Server | 8.4.3 | Database | Relational database with foreign keys; included in Laragon | GPL v2 |
| Node.js / npm | 24.20.0 / 11.19.0 | Running Vite and installing frontend packages | Needed by Vite | MIT / Artistic 2.0 |
| Composer | 2.10.3 | Installing backend packages | Standard PHP package manager | MIT |
| Git and GitHub | 2.55 | Version control and sharing the code | Shows each member's contributions (required for Appendix I) | GPL v2 (Git) |
| Google Fonts: Manrope, DM Sans | — | Fonts | Readable, free fonts used in the Figma prototype | SIL Open Font License |
| TextBee (textbee.dev) | API v1 | SMS gateway: an Android phone with a SIM sends the alerts as normal texts | Uses an ordinary prepaid SIM, so no contract with a telco is needed. If it fails, the alert is still saved and shown in the portal, and staff see why (NFR-08). | *Check TextBee's terms and free plan limits* |

*Laragon 8.8 is only a local development tool. Check its licence before listing it in the paper.*

*AI tools (Claude) go in the Declaration of Originality and Use of Tools, using [ai-use-log.md](ai-use-log.md), not in this table.*

---

## 3.7 Coding standards and version control

- **Folders:** the frontend (`frontend/`, Vue) and backend (`backend/`, Laravel) are kept separate, so two members don't edit the same files.
- **Frontend:** Vue single-file components, sorted into `pages/` (one per screen), `layouts/` (resident and staff frames), `components/` (reusable parts) and `utils/` (validation, formatting and fixed choices). CSS is in plain external files under `css/`.
- **Backend:** Laravel's standard structure: controllers in `app/Http/Controllers`, models in `app/Models`, outside services (the SMS gateway) in `app/Services`, every table change as a migration, and demo data in seeders.
- **Field names** follow the ERD (for example `mobile_number`, `head_name`, `purok`), the same in the database, the API and the frontend.
- **Version control:** one GitHub repository, https://github.com/jhonleeemboy-ops/ITE-18. Everyone works on the `main` branch, pulls before pushing, and commits from their own GitHub account. Commit messages say what changed and why.
- **Secrets:** `.env` files are never committed (listed in `.gitignore`). The SMS gateway key is only in `.env`.
- **Testing record:** tests and results are kept in `docs/test-cases.md`.

*Our repo has no licence file yet. We need to decide on one (for example MIT) or state "all rights reserved" in the paper.*

# Test cases and results

## Final system test (for Chapter 4)

**Tested:** 2026-11-24, on the desktop (AMD Ryzen 3 2200G, 14 GB RAM, Windows 10 Pro; Laragon 8.8 with PHP 8.3.36 and MySQL 8.4.3). The database was reset to the demo data first (`php artisan migrate:fresh --seed`) and again afterwards, so no test records are left. **No TextBee key** in `.env`, so no real SMS was sent (real SMS: S01–S02 on Nov 15, in the paper's Table 13).
**How:** 51 API test cases with PowerShell (`Invoke-WebRequest`) against the endpoints the pages use, with database checks through the MySQL client; then 12 browser checks with Playwright in Microsoft Edge (headless) at 320, 375, 768 and 1440 px, signing in through the real forms; then `npm run build`.
**Result:** 61 of 63 passed in the first run. G02 (text smaller than 12 px) was fixed in the CSS and passes now. G11 (load time) fails on the development server and passes with PHP OPcache on, which the hosting server must have.

### API (F01–F51)

| ID | Req. | Test | Result | Details |
|---|---|---|---|---|
| F01 | FR-02 | Resident signs in with mobile number | Pass | status 200, token returned |
| F02 | FR-02 | Staff signs in with email | Pass | status 200, role staff |
| F03 | FR-02 | Wrong password is refused | Pass | status 422: The mobile number/email or password is incorrect. |
| F04 | FR-01 | Register a resident (Purok 12, 3 members) | Pass | status 201, household created, no password in the answer |
| F05 | FR-01 | Register the same mobile number again | Pass | status 422: This mobile number is already registered. |
| F06 | FR-01, NFR-05 | Register with an empty form | Pass | status 422, errors for: name, mobile_number, password, purok |
| F07 | FR-01 | Register in Purok 24 (Ampayon has 23) | Pass | status 422 |
| F08 | NFR-02 | Every protected endpoint without a login token | Pass | 24 of 24 refused with 401 |
| F09 | FR-03, NFR-03 | Every staff-only endpoint with a resident's token | Pass | 13 of 13 refused with 403 |
| F10 | NFR-03 | A resident reads or changes another household | Pass | 5 of 5 refused with 403 |
| F11 | FR-15, NFR-03 | A resident's request list shows only their own requests | Pass | 2 listed, 0 belong to someone else |
| F12 | FR-05 | Search households by head name ('Juan') | Pass | 25 found, 0 not matching |
| F13 | FR-05 | Filter households by Purok 3 | Pass | 4 shown, database has 4 |
| F14 | FR-24 | Staff adds a household listed by personnel | Pass | status 201, households 26 -> 27 |
| F15 | NFR-05 | Staff adds a household with invalid input | Pass | status 422, nothing saved (27 households) |
| F16 | FR-25 | Resident corrects own household (members 5) | Pass | status 200, members now 5 |
| F17 | FR-07 | Resident marks household as evacuated; staff list shows it | Pass | status 200, staff list shows 'evacuated' |
| F18 | FR-08 | Resident adds 3 members (age 67, age 1, PWD) and lists them | Pass | statuses 201/201/201, 3 listed |
| F19 | FR-08, NFR-05 | Add a member with no name and age 130 | Pass | status 422, errors for: name, age |
| F20 | FR-08 | Resident removes a member | Pass | status 204, 2 left |
| F21 | FR-22 | Every dashboard number equals a direct database count | Pass | 21 of 21 figures match |
| F22 | FR-22 | Dashboard updates right after a household is added | Pass | total 27 -> 28, no manual count |
| F23 | FR-26 | Staff adds a center (capacity 100, Puroks 1 and 23) | Pass | status 201, center status 'standby' |
| F24 | FR-26 | Add a center with a name already used | Pass | status 422 |
| F25 | FR-09 | Residents see the new center with its Puroks | Pass | serves Purok 1 and 23 |
| F26 | FR-10 | Check in 50 people | Pass | occupants 50, status 'open' |
| F27 | FR-10 | Check in 40 more (90 of 100) | Pass | occupants 90, status 'near_capacity' |
| F28 | FR-10, NFR-06 | Check in 11 when only 10 places are left | Pass | status 422: Only 10 places left in this center.; occupants still 90 |
| F29 | FR-10 | Fill the center exactly (100 of 100) | Pass | occupants 100 |
| F30 | FR-10, NFR-06 | Check out 101 when 100 are inside | Pass | status 422: Only 100 people are in this center. |
| F31 | FR-10 | Check everyone out | Pass | occupants 0, status 'standby' |
| F32 | FR-10, NFR-06 | 60 random check-ins/outs (1-60 people each) | Pass | 32 accepted, 28 refused, 0 times the count was wrong or outside 0-100 |
| F33 | FR-11 | Staff views relief items and stock | Pass | 4 items; Rice: 500 kg |
| F34 | FR-12 | Release 25 kg of rice to a household | Pass | status 201, stock 500 -> 475 |
| F35 | FR-13 | The release is first in the distribution history | Pass | top entry: 25 kg Rice to 'Juan Dela Cruz' |
| F36 | FR-12, NFR-06 | Release 1 kg more than the stock | Pass | status 422: Only 475 kg left in stock.; stock still 475 |
| F37 | NFR-05 | Release 0 kg, and release to a household that does not exist | Pass | statuses 422 and 422 |
| F38 | FR-12, NFR-06 | Release the whole stock, then 1 kg more | Pass | stock 0 after the release; next release refused (422): Only 0 kg left in stock. |
| F39 | FR-14 | Resident sends a medical request | Pass | priority 'high', status 'pending' |
| F40 | FR-14 | Resident sends a relief request | Pass | priority 'medium' |
| F41 | NFR-05 | Request with a type that does not exist | Pass | status 422 |
| F42 | FR-16, FR-15 | Staff sets the request to in progress and adds a note; the resident sees it | Pass | resident sees 'in_progress' with 1 note |
| F43 | FR-18, NFR-07 | Staff sends an alert to all residents (no TextBee key on this computer) | Pass | status 201; recipients 2, sent 0; the answer does not claim an SMS was sent |
| F44 | FR-17, FR-28 | The alert is first on the resident's alerts and on the sign-in page | Pass | residents: 'FINAL TEST alert'; sign-in page: 'FINAL TEST alert' |
| F45 | FR-18 | Alert to Purok 20 (no registered residents there) | Pass | recipients 0 |
| F46 | FR-20 | The SMS log lists both alerts with their counts | Pass | 3 entries in the log |
| F47 | NFR-05 | Alert with an empty title and message | Pass | status 422 |
| F48 | FR-27 | Staff deletes an alert; residents no longer see it | Pass | status 204 |
| F49 | FR-21 | Residents see the emergency hotlines | Pass | 5 shown, database has 5 (sample numbers) |
| F50 | NFR-01 | Every password in the users table is a bcrypt hash | Pass | 4 of 4 accounts |
| F51 | NFR-04 | No .env file or SMS key in the GitHub repository | Pass | 0 .env files, 0 keys found |

### Browser (G01–G12)

| ID | Req. | Test | Result | Details |
|---|---|---|---|---|
| G01 | NFR-08 | All 13 pages at 320, 375, 768 and 1440 px: no sideways scrolling | Pass | 52 of 52 page views |
| G02 | NFR-09 | Smallest text on any page is at least 12 px | **Fail**, then Pass after the fix | First run: labels such as "Barangay Ampayon" under the logo, "Main menu", the eyebrow and chip labels were 10–11 px. Raised to 12 px; re-run: smallest text 12 px, and G01 still passes |
| G03 | NFR-09 | Every form field has a label (forms opened) | Pass | 240 field views checked, 0 without a label |
| G04 | FR-28 | Sign-in page shows the newest alert without signing in | Pass | shows "FINAL TEST alert" |
| G05 | NFR-09 | Empty Create account form: each error appears under its own field | Pass | 5 of 5 fields show their own message below them |
| G06 | NFR-02 | Signed-out visitor opens the resident portal | Pass | sent to #/ |
| G07 | FR-03 | Resident opens the staff dashboard address | Pass | sent to #/resident/home |
| G08 | FR-08 | My Household labels members aged 60+ as Senior, under 2 as Infant, and PWD | Pass | Senior true, Infant true, PWD true |
| G09 | FR-04 | Resident signs out | Pass | back on #/, token removed |
| G10 | NFR-10 | Back to Home / Back to Dashboard on every inner page opens the right page | Pass | 8 of 8 pages |
| G11 | NFR-12 | Lists and the dashboard load within 3 seconds | **Fail** on the development server, Pass with OPcache | Measured separately (5 full reloads per page, until the last API answer): resident Home 4.44 s average on `php artisan serve` without OPcache; 0.36 s with OPcache. See the table below |
| G12 | — | No errors in the browser console during all checks | Pass | none |

### Load time (NFR-12)

Time from opening the page (full reload) until the last API answer arrived; 5 loads per page at 1440 px; average and slowest, in seconds. Browser and server on the same computer, so the network delay of a real server is not included.

| Page | `php artisan serve` (no OPcache) | PHP built-in server with OPcache |
|---|---|---|
| Staff: Dashboard | 2.35 (2.86) | 0.32 (0.34) |
| Staff: Household Profiles | 0.63 (0.65) | 0.18 (0.26) |
| Staff: Evacuation Centers | 0.63 (0.64) | 0.16 (0.18) |
| Staff: Relief Distribution | 1.69 (1.70) | 0.26 (0.28) |
| Staff: SMS Alerts | 0.64 (0.65) | 0.16 (0.18) |
| Resident: Home | 4.44 (6.38) | 0.36 (0.39) |
| Resident: My Household | 1.25 (1.32) | 0.20 (0.22) |
| Resident: Evacuation | 1.13 (1.14) | 0.20 (0.22) |
| Resident: Alerts | 0.61 (0.63) | 0.16 (0.18) |

**Why it was slow:** `php artisan serve` answers one request at a time and Laragon's PHP has OPcache off, so each request takes about 0.5 s, and the resident Home asks for 5 things at once. With OPcache on (`php -d zend_extension=opcache -d opcache.enable=1 -d opcache.enable_cli=1 -S 127.0.0.1:8000 …/server.php` from `backend/public`), each request takes about 0.02 s.

**500 households** (generated rows across all 23 Puroks, deleted afterwards): `GET /households` took 0.39–0.42 s and `GET /dashboard` about 0.30 s on `php artisan serve`; the dashboard totals (500 households, 2,032 people, safety counts) matched the database; the Household Profiles page loaded in 0.75 s.

---

## Purok 1–23 (Ampayon has 23 Puroks)

**Tested:** 2026-11-21, on the desktop, with the backend and the Vue dev server running (demo data). **No TextBee key** in `.env`, so no real SMS was sent.
**Changed:** the API now accepts Purok 1 to 23 in register, add/edit household, add evacuation center and SMS alerts (it was 1 to 8). Every Purok list in the frontend now comes from one place, `frontend/src/utils/options.js`.
**How:** API calls with PowerShell (`Invoke-WebRequest`), then an automated browser test (Playwright with Microsoft Edge, headless) at 1440px and 375px wide, then `npm run build`. The test records (one resident, two households, one center, one alert) were deleted afterwards, and the demo resident's Purok was put back to 3.
**Not changed:** the demo data still only has households and centers in Purok 1–8 (it is sample data).

| ID | Test | Expected | Result |
|---|---|---|---|
| P01 | Register a resident in Purok 23 | 201, saved with Purok 23 | Pass |
| P02 | Register in Purok 24 | 422 "must be between 1 and 23" | Pass |
| P03 | Register in Purok 0 | 422 | Pass |
| P04 | Staff adds a household in Purok 23 | 201 | Pass |
| P05 | Staff adds a household in Purok 24 | 422 | Pass |
| P06 | Resident changes their own Purok to 23 | 200 | Pass |
| P07 | Resident changes their own Purok to 24 | 422 | Pass |
| P08 | Staff adds a center serving Puroks 22 and 23 | 201, serves 22 and 23 | Pass |
| P09 | Staff adds a center serving Purok 24 | 422 | Pass |
| P10 | Staff sends an alert to Purok 23 | 201, 1 recipient (the P01 resident), `sent` 0 because there is no TextBee key | Pass |
| P11 | Staff sends an alert to Purok 24 | 422 "Invalid Purok." | Pass |
| P12 | The Purok 23 resident looks at the centers | The P08 center serves their Purok | Pass |
| P13 | Create account form: Purok list | Purok 1 to Purok 23 | Pass (both widths) |
| P14 | My Household → Update profile: Purok list | Purok 1 to Purok 23 | Pass (both widths) |
| P15 | Staff Households: Purok filter and Add household list | Purok 1 to Purok 23 | Pass (both widths) |
| P16 | Staff Evacuation Centers → Add center | 23 Purok checkboxes, no sideways scrolling | Pass (both widths; 10 per row on desktop, 3 per row on a phone) |
| P17 | Staff SMS Alerts: Send to list | "Residents of Purok 1" to "Residents of Purok 23" | Pass (both widths) |
| P18 | All the pages above | No errors in the browser | Pass |
| P19 | `npm run build` | No errors | Pass |

Tests N14–N16 below (Purok 23 refused) now pass: see P04, P01 and P10.

## "Back to Home / Dashboard" links + real advisory on the sign-in page

**Tested:** 2026-11-16, on the desktop, with the backend and the Vue dev server running (demo data).
**How:** automated browser test (Playwright with Microsoft Edge, headless) at 1440px and 375px wide, signing in through the real forms; then `npm run build`.
**Why:** barangay feedback on the prototype: it was confusing where to go back or home (no specific screen).

| ID | Test | Expected | Result |
|---|---|---|---|
| B01 | Open the sign-in page | Shows the newest active advisory from `GET /advisories/latest` | Pass (both widths) |
| B02 | Same | No "Sample" tag anymore | Pass |
| B03 | Resident Home | No back link (you are already home) | Pass |
| B04 | Resident My Household, Evacuation, Assistance, Alerts | "Back to Home" at the top | Pass (4/4, both widths) |
| B05 | Same pages | No sideways scrolling | Pass |
| B06 | Click "Back to Home" | Home opens | Pass |
| B07 | Staff Dashboard | No back link | Pass |
| B08 | Staff Household Profiles, Evacuation Centers, Relief, SMS Alerts | "Back to Dashboard" at the top | Pass (4/4, both widths) |
| B09 | Same pages | No sideways scrolling | Pass |
| B10 | Click "Back to Dashboard" | Dashboard opens | Pass |
| B11 | Sign-in page when the API can't be reached | Advisory box hidden; sign-in form still works | Pass |
| B12 | Sign-in page when there is no active advisory (API sends `{}`) | Advisory box hidden | Pass |
| B13 | Browser console during all tests | No errors | Pass |
| B14 | `npm run build` | No errors | Pass |

Also removed: the old note under the sign-in form ("Demo: sign-in isn't connected to the server yet…"), because sign in is real now and the note was no longer true.

---

## TextBee SMS, household members, staff pages (Jhon Lee's update c8cac94)

**Tested:** 2026-11-16, on the desktop. Backend on http://127.0.0.1:8000 (Laragon MySQL 8.4, demo data, new `household_members` migration run). **No TextBee key** in `.env`, so no real SMS was sent.
**How:** API calls with PowerShell (`Invoke-WebRequest`) as the demo staff and resident, then `npm run build`. Test records (one alert, one member, one check-in) were removed or reversed afterwards.
**Not tested yet:** sending a real SMS through TextBee (needs an account, API key and an Android phone with a SIM); clicking through the new pages in a browser. *Jhann: add your own browser check here.*

| ID | Test | Expected | Result |
|---|---|---|---|
| N01 | `GET /advisories/latest` without signing in | 200 with the newest active advisory | Pass |
| N02 | Staff sends an alert with no TextBee key | 201; advisory saved; `sms.configured` false; `sent` 0, `sent_at` empty (no SMS claimed) | Pass |
| N03 | Resident sends an alert | 403 | Pass |
| N04 | Resident deletes an alert | 403 | Pass |
| N05 | Staff deletes the alert from N02 | 204, and the advisory no longer appears for residents | Pass |
| N06 | Check in more people than the space left in a center | 422 "Only 300 places left in this center." | Pass |
| N07 | Check in 1 person | 200, occupants 0 → 1 | Pass |
| N08 | Check out 1 person | 200, back to 0 | Pass |
| N09 | Resident checks people in | 403 | Pass |
| N10 | Resident lists their own household members | 200 | Pass |
| N11 | Resident adds a member (age 70, PWD) | 201 | Pass |
| N12 | Resident lists another household's members | 403 | Pass |
| N13 | Resident deletes the member from N11 | 204 | Pass |
| N14 | Staff adds a household in Purok 23 | Accepted (Ampayon has 23 Puroks) | **Fail:** 422 "The purok field must be between 1 and 8." |
| N15 | Resident registers in Purok 23 | Accepted | **Fail:** same 422 |
| N16 | Staff sends an alert to Purok 23 | Accepted | **Fail:** 422 "Invalid Purok." |
| N17 | `npm run build` | No errors | Pass |

N14–N16 fail because the system was built for Purok 1–8 (from the Figma prototype). The barangay says Ampayon has 23 Puroks. **Fixed on 2026-11-21:** see "Purok 1–23" at the top (P01, P04, P10).

---

## Frontend connected to the API (Jhon Lee's update) + API address fallback

**Tested:** 2026-11-12, on the desktop, with commit ed3f202 (Jhon Lee) + the fallback fix. Backend on http://127.0.0.1:8000 (Laragon MySQL 8.4, seeded demo data), Vue app on http://localhost:5173.
**How:** API calls with PowerShell (`Invoke-WebRequest`) using the same addresses the pages call, a database check, and `vite build` with and without `frontend/.env`.
**Not tested yet:** clicking through the pages in a browser after the update. *Jhann: add your own browser check here.*

### API calls the pages make

| ID | Test | Expected | Result |
|---|---|---|---|
| C01 | Staff sign in (`staff@brgy.test`) / resident sign in (`09171234567`) | 200 with `user` and `token` | Pass |
| C02 | Staff: `/dashboard`, `/advisories`, `/evacuation-centers`, `/incidents`, `/households`, `/sms-alerts` | 200 with data | Pass (6/6) |
| C03 | Resident: `/me`, `/evacuation-centers`, `/advisories`, `/emergency-contacts`, `/incidents` | 200 with data | Pass (5/5) |
| C04 | Resident token on `/sms-alerts` and `/dashboard` | 403 (staff only) | Pass |
| C05 | `/me` without a token | 401 | Pass |
| C06 | Staff sign in with a wrong password | 422 | Pass |
| C07 | Resident `/incidents` only shows their own requests | Database: both seeded incidents belong to the demo resident, so 2 of 2 is correct | Pass |
| C08 | CORS preflight from http://localhost:5173 | Allowed (`Access-Control-Allow-Origin: *`) | Pass |

### API address (`frontend/src/api.js`)

| ID | Test | Expected | Result |
|---|---|---|---|
| C09 | Before the fix, no `frontend/.env` (a fresh pull) | — | **Fail:** the app had no API address (`VITE_API_URL` missing), so sign in and every page would call the wrong server |
| C10 | After the fix, build without `frontend/.env` | Uses `http://127.0.0.1:8000/api` | Pass |
| C11 | After the fix, build with `VITE_API_URL` set (like the online server) | Uses that address instead | Pass |
| C12 | `npm run build` | No errors | Pass |

---

## Resident registration connected to the Laravel API

**Tested:** 2026-11-09, with Jhon Lee's backend (commit 5b8249a) running locally on http://127.0.0.1:8000 (Laragon MySQL, `migrate --seed`) and the Vue app on http://127.0.0.1:5173.
**How:** API tests with curl, then browser tests (Playwright, Chromium) through the real form, then a database check. Screenshots: `docs/screenshots/api-register/`.

### API (curl, `POST /api/register`)

| ID | Test | Expected | Result |
|---|---|---|---|
| A01 | Valid data (`purok: 3`, mobile `+63 999 000 0003`) | 201 with `user` (+ household) and `token`; no password in the response | Pass |
| A02 | Same mobile again | 422 "This mobile number is already registered." | Pass |
| A03 | Empty body | 422 with errors for name, mobile_number, password, purok | Pass |
| A04 | `purok: "Purok 3"` (text, what the old form sent) | 422 "must be an integer" (so the form must send a number) | Pass |
| A05 | `/api/me` with the token / without | 200 with the user / 401 | Pass |
| A06 | CORS preflight from http://localhost:5173 | Allowed (`Access-Control-Allow-Origin: *`) | Pass |

### Browser (the real form)

| ID | Test | Expected | Result |
|---|---|---|---|
| R01 | Submit empty form | 4 errors, **no request sent** to the server | Pass |
| R02 | Valid details, Purok 5 | Request body has `purok: 5` (number) and a clean 10-digit mobile | Pass |
| R03 | Same as R02 | Server answers 201 Created | Pass |
| R04 | Same as R02 | Token and user saved in the browser (`dr_token`, `dr_user`) | Pass |
| R05 | Same as R02 | "Account created! Welcome, …" | Pass |
| R06 | Same as R02 | Portal opens and greets the **new** user by name | Pass |
| R07 | Use the saved token on `/api/me` | 200 with the same user | Pass |
| R08 | Click Sign out | Token removed, back to sign in | Pass |
| R09 | Register the same mobile again | Server's 422 message under the mobile field, cursor moves there | Pass |
| R10 | Backend unreachable | Red "Can't reach the server…" message; button usable again | Pass |
| R11 | Slow server | Button disabled and shows "Creating account…" | Pass |
| R12 | Sign-in page | Still labeled demo (not connected yet) | Pass |
| R13 | Browser console | No unexpected errors or Vue warnings (only the deliberate failed requests from R09–R11) | Pass |

### Database check

- New users are saved with a **bcrypt-hashed password** (`$2y$12$…`, 60 characters), never plain text, and with the correct Purok on their household.
- All test accounts created during testing were deleted afterwards; the seeded demo data was kept.

---

## Vue frontend draft (sample data)

**Tested:** 2026-11-05
**How:** `npm run build` (no errors), then automated browser tests (Playwright, Chromium) on the dev server at 1440, 768, 375 and 320px wide, plus screenshots checked by eye (`docs/screenshots/vue-draft-v1/`).
**Not tested yet:** a real phone, Firefox, Safari, and anything involving the backend (there isn't one yet).

Final result: **all checks pass.**

| ID | What we test | Expected result | Result |
|---|---|---|---|
| V01 | Open all 13 pages | Each page shows its heading and the right browser tab title; portal/staff pages show the "sample data" banner | Pass (13/13) |
| V02 | Resident sign in with empty fields | "Enter your mobile number." and "Enter your password." | Pass |
| V03 | Same as V02 | Cursor jumps to the mobile number field | Pass |
| V04 | Invalid mobile `12345` | "Enter a valid mobile number…" | Pass |
| V05 | Valid mobile `0917 123 4567` + password | Demo message, then the resident portal opens | Pass |
| V06 | Click "Create an account" | Register form opens, focus on its heading | Pass |
| V07 | Register with empty fields | 4 errors: name, mobile, password, Purok | Pass |
| V08 | Register with valid details | "…Nothing was saved." | Pass |
| V09 | Staff sign in with `notanemail` | "Enter a valid email…" | Pass |
| V10 | Staff sign in with a valid email | Demo message, then the staff dashboard opens | Pass |
| V11 | Staff households: search "santos", filter Purok 3, search "zzzz" | 1 row, 2 rows, "No households match" | Pass |
| V12 | Resident assistance: submit empty, then valid | 2 errors; then the request is added on the page with "NOT sent to the barangay" | Pass |
| V13 | Staff SMS: submit empty, then valid | Errors; then "Demo: no SMS was sent…" (honest) | Pass |
| V14 | Open Relief Distribution | Highlighted in the staff menu | Pass |
| V15 | Open Alerts | Highlighted in the resident menu | Pass |
| V16 | All 13 pages at 1440, 768, 375 and 320px | No sideways scrolling | Pass (52/52) |
| V17 | Watch the browser console during all tests | No errors or Vue warnings | Pass |

### Problems found and fixed

| Problem | Fix |
|---|---|
| On phones, the staff dashboard advisory text was squeezed into a ~100px column by the date beside it | Moved the date under the message (now ~247px wide) |
| On phones, the sideways-scrolling menus showed a clunky scrollbar, and nothing hinted at more items | Hid the scrollbar and faded the right edge |

---

## Login page, first version (plain HTML, replaced by the Vue draft)

**Tested:** 2026-10-15
**How:** automated browser tests (Playwright, Chromium browser) at several screen sizes, plus checking screenshots by eye.
**Not tested yet:** a real phone, Firefox, Safari.

Final result: **all checks pass.**

| ID | What we test | Steps | Expected result | Result |
|---|---|---|---|---|
| T01 | Fonts load | Open the page | Manrope and DM Sans are used | Pass |
| T02 | Heading layout | Open at 1500, 1280, 1024 and 920px wide | "One community. / Ready for anything." stays on 2 lines | Pass |
| T03 | Empty sign in | Click "Sign in" with nothing typed | "Enter your mobile number." and "Enter your password." appear | Pass |
| T04 | Focus on error | Same as T03 | Cursor jumps to the mobile number field | Pass |
| T05 | Red error border | Same as T03 | Mobile and password boxes turn red, no double border | Pass |
| T06 | Error clears | Type in a field that has an error | The red message disappears | Pass |
| T07 | Invalid mobile numbers | Try `12345`, `8171234567`, `91712345678`, `abcdefghij` | "Enter a valid mobile number…" | Pass (all 4) |
| T08 | Valid mobile formats | Try `917 123 4567`, `09171234567`, `+63 917 123 4567`, `917-123-4567` with a password | No error; message says sign-in is not connected yet | Pass (all 4) |
| T09 | Open create account | Click "Create an account" | Register form shows, sign-in form hides, address ends in `#register` | Pass |
| T10 | Focus after switch | Same as T09 | Focus moves to the "Create your account" heading | Pass |
| T11 | Empty register | Click "Register household" with nothing filled in | 4 errors: name, mobile, password, Purok | Pass |
| T12 | Short password | Password `short` | "Use at least 8 characters." | Pass |
| T13 | Purok required | Leave Purok on "Select your Purok", then choose one | Error appears, then clears | Pass |
| T14 | Valid registration | Fill everything correctly | Message says registration is not connected yet and nothing was saved | Pass |
| T15 | Back to sign in | Click "Sign in" under the register form | Sign-in form shows again | Pass |
| T16 | Direct link | Open `index.html#register` | Page opens on the register form | Pass |
| T16b | Address change | Change `#signin` / `#register` in the address bar while the page is open | Form switches | Pass |
| T17 | Unbuilt pages | Click "Barangay staff login", "Evacuation centers", "Emergency hotlines" | Message: "… is not built yet." | Pass (all 3) |
| T18 | Enter key | Fill in sign in and press Enter | Form submits | Pass |
| T19 | Screen sizes | Open at 1500, 1024, 768, 414, 375, 360 and 320px | No sideways scrolling; header stays on one row | Pass |
| T20 | No errors | Watch the browser console during all tests | No errors, no missing files | Pass |
| T21 | Keyboard | Press Tab from the top of the page | Logo, staff login, evacuation, hotlines, mobile, password, sign in, create account (in that order) | Pass |
| T22 | Labels | Check every input and dropdown | Each has a matching label | Pass |

### Problems found during testing and how they were fixed

| Test | Problem | Fix |
|---|---|---|
| T16 | The form only switched to "Create account" when the page was first loaded with `#register`, not when the address changed while the page was open | Added a `hashchange` listener in `frontend/js/login.js` |
| T19 | On a 320px phone, "Staff login" wrapped onto two lines and "Disaster Ready" wrapped in the header | Button text no longer wraps; on phones "· Butuan City" is hidden and the logo is slightly smaller |
| T05 | First run reported a fail | Not a page bug: the border fades to red over 0.15 seconds and the test checked too early. A re-test with a short wait passed. |

### Known small issue

- On a 320px phone, the "Sample" tag next to the rainfall advisory makes the advisory text column narrow. It's still readable, and the tag will be removed once real alerts come from the backend.

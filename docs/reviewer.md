# Oral recitation reviewer: Disaster Ready

For every member, in case the instructor asks questions about the system. Updated 2026-11-16, matching commit c8cac94.

**How to use this:** read each part, then cover the answers and try saying them in your own words. Don't memorize word for word. If you don't know something, say so honestly and say how you would find out (for example "I would check the controller" or "I would test it in Postman"). That is better than guessing.

---

## 1. The system in 30 seconds

> Disaster Ready is a web system for Barangay Ampayon, Butuan City. It helps the barangay profile households, manage evacuation centers, record relief distribution and send SMS alerts to residents. Residents have their own portal, and barangay staff have a separate dashboard. It is built with Vue for the frontend, Laravel for the backend API, MySQL for the database, and TextBee for sending SMS.

**Full title:** Disaster Ready: A Household Profiling, Evacuation Center, and Relief Distribution Management System with SMS Alerts for Barangay Ampayon, Butuan City.

---

## 2. The problem (what the barangay told us)

From the interview with the **Barangay Secretary** and the follow-up questions:

| Finding | What it means for the system |
|---|---|
| Records are done **by hand** ("mano-mano") and are incomplete and outdated | Household profiling in a database |
| Counting affected families by hand takes **1–2 days** | The dashboard counts them instantly |
| Ampayon has **23 Puroks** and about **500+ households** | Households are grouped by Purok |
| Evacuation centers hold about **1.3k+ people (500+ households) in total** | Capacity and occupants per center |
| Warnings go through online posts, group chats, a megaphone and Purok presidents | SMS alerts reach any phone, even without internet |
| **Seniors and PWDs** are assessed and helped first | Household members with age and PWD status |
| Relief comes from the city; only listed families get it, recorded in a **logbook** | Relief distribution with stock that updates automatically |
| They report affected-family counts to the city | A summary report (planned, not built yet) |

Some things from the interview were unclear (the exact names of the centers, whether the barangay has a computer and internet). Say that honestly if asked.

---

## 3. Who uses it (roles)

| Role | Signs in with | Can do |
|---|---|---|
| **Resident** | Mobile number + password | Register, view and edit their own household, add household members, mark the household safe / evacuated / needs help, see evacuation centers, send assistance requests, read alerts and hotlines |
| **Staff** | Email + password | Everything on the staff dashboard: households, evacuation centers, relief, SMS alerts, dashboard numbers |
| **Admin** | Email + password | Right now, the same as staff |

**Demo accounts** (password `password123`): staff `staff@brgy.test`, admin `admin@brgy.test`, resident `09171234567`. All demo data is made up.

---

## 4. The modules (features)

**Resident portal (5 pages):** Home, My Household, Evacuation, Assistance, Alerts.
**Staff dashboard (5 pages):** Dashboard, Household Profiles, Evacuation Centers, Relief Distribution, SMS Alerts.

| Module | What it does |
|---|---|
| Household profiling | Household head, Purok, number of members, safety status. Members with name, age, relationship and PWD. Age 60+ is labeled **Senior**. |
| Evacuation centers | Capacity, occupants, Puroks served. Staff **check people in and out**. The status changes by itself: **standby** (empty), **open**, **near capacity** (90% full or more). You can't check in more people than the space left. |
| Relief distribution | Relief items with stock. Staff record what a household received, and the stock goes down automatically. Giving more than the stock is refused. |
| Assistance requests | Residents ask for evacuation, relief, medical, rescue or hazard help. **Medical and rescue are automatically high priority.** Residents only see their own requests. |
| SMS alerts | Staff write an alert for **all residents or one Purok**. It is saved as an advisory (shown in the portal) and sent by SMS through TextBee. The SMS log shows recipients, sent and failed. |
| Dashboard | Total households and people, households per Purok and per safety status, center capacity and occupants, requests per status, active advisories. |

---

## 5. How the parts connect (architecture)

```
Browser (Vue pages)
   │  Axios sends a request with the login token
   ▼
Laravel API  (checks the token, the role and the input)
   │                         │
   ▼                         ▼
MySQL database          TextBee SMS gateway → Android phone with a SIM → residents' phones
```

- The **frontend** (Vue) only shows things and sends requests. It never talks to the database directly.
- The **backend** (Laravel) does the logic and the security: who is signed in, what their role allows, and whether the input is valid.
- The **database** (MySQL) stores everything.
- **TextBee** turns an ordinary Android phone with a SIM into an SMS sender.

---

## 6. Technologies and why we chose them

| Technology | Why |
|---|---|
| **Vue 3** | The instructor required a framework. Each page is one `.vue` file (HTML, JavaScript and style together), which is easy to read. |
| **Vue Router** | Switches pages without reloading. It uses `#` addresses (hash history), which work on any host without server settings. |
| **Vite** | Runs the app while coding (refreshes when you save) and builds the final files. |
| **Axios** | Sends requests to the API. One file, `src/api.js`, adds the login token to every request. |
| **Plain CSS** | Everyone already knows it, and it's easy to debug (group decision). |
| **Laravel 13** | Built-in validation, password hashing, migrations and seeders. |
| **Laravel Sanctum** | Gives each signed-in user a login token for the API. |
| **MySQL 8.4** | Relational database with foreign keys, so records stay linked correctly. |
| **TextBee** | Sends SMS through a normal prepaid SIM. No contract with a telco needed. |
| **Laragon** | Runs PHP, MySQL and Composer on our computers. |
| **Git + GitHub** | Shares the code and shows who did what. |

---

## 7. What happens when… (main flows)

**A resident signs in**
1. The Vue page checks the fields first (for example, a valid mobile number).
2. Axios sends `POST /api/login` with the mobile number and password.
3. Laravel finds the user and compares the password with the stored **hash**.
4. If correct, Laravel creates a **token** and sends back the user and the token.
5. The browser saves the token. Every later request sends it as `Authorization: Bearer <token>`.
6. If wrong, Laravel answers **422** with "The mobile number/email or password is incorrect." It doesn't say which one is wrong, so attackers can't find out which numbers are registered.

**A resident registers**
- `POST /api/register`. Laravel cleans the mobile number (`0917…`, `+63917…` and `917…` all become `9171234567`), refuses a number that already exists, creates the **household** and the **user** (role resident), then returns a token (**201 Created**).

**Staff send an SMS alert**
1. `POST /api/sms-alerts` with title, message, send to (all or a Purok) and level.
2. Laravel checks the user is staff (otherwise **403**).
3. It collects the mobile numbers of the residents (in that Purok, if one was chosen).
4. It **saves the advisory and the SMS log first**, so the alert is never lost.
5. If a TextBee key is set, it sends the text in groups of 50, as "BRGY AMPAYON: title. message".
6. It updates the log with how many were sent and how many failed, and tells the page what really happened. **The page never claims an SMS was sent when it wasn't.**

**Staff give relief**
- `POST /api/relief-distributions`. Inside one **database transaction**, Laravel **locks** the item row, checks the stock, reduces it and saves the record. Both happen or neither happens, so the stock can never go below zero, even if two staff click at the same time.

**Staff check people into a center**
- `PATCH /api/evacuation-centers/{id}/occupants` with `check_in` or `check_out` and the number of people. Same idea: transaction + lock, and it refuses if there isn't enough space.

---

## 8. Security (they will likely ask)

| Question | Answer |
|---|---|
| How are passwords stored? | As **bcrypt hashes**, never plain text. The User model has `'password' => 'hashed'`, so Laravel hashes it automatically. We checked the database: they start with `$2y$12$`. |
| How does the API know who you are? | A **Sanctum token**, sent in the `Authorization` header. The database only stores a hash of the token. |
| How are roles enforced? | **On the server.** Staff-only functions start with `abort_unless($request->user()->isStaff(), 403)`. `isStaff()` is true for the roles staff and admin. |
| What about the Vue route guard? | `router.js` sends people to the right sign-in page, but that is only for convenience. Anyone can change the browser, so the real protection is in Laravel. |
| What if there's no token or it's wrong? | Laravel answers **401**. `api.js` then deletes the saved token and goes back to sign in. |
| Where are the secrets? | In `backend/.env` (database password, TextBee key). It is in `.gitignore`, so it is never uploaded to GitHub. |
| How is input checked? | Laravel `validate()` rules in each controller. Wrong input gets **422** with a message per field, and nothing is saved. The frontend also checks, but only to help the user. |
| Can a resident see other people's data? | No. Residents only get their own household, members and requests. We tested this (403 for another household). |

---

## 9. Database (main tables)

| Table | Holds | Linked to |
|---|---|---|
| `users` | Name, mobile number or email, password hash, role | a household (residents) |
| `households` | Head name, Purok, number of members, safety status | — |
| `household_members` | Name, age, relationship, PWD | a household |
| `evacuation_centers` | Name, capacity, occupants, status | — |
| `center_purok` | Which Puroks each center serves | a center |
| `incidents` | Assistance requests: type, description, status, priority | the user who sent it |
| `incident_notes` | Staff notes on a request | an incident |
| `advisories` | Alerts: title, message, level, status | — |
| `sms_logs` | Recipients, sent, failed, time sent | an advisory |
| `relief_items` | Item, unit, quantity in stock | — |
| `relief_distributions` | Who received what, how many, when | a household and an item |
| `emergency_contacts` | Hotlines (sample numbers for now) | — |
| `personal_access_tokens` | Login tokens (Sanctum) | a user |

- **Foreign keys** keep records linked. For example, deleting a household also deletes its members (`cascadeOnDelete`).
- **Migrations** are PHP files that create the tables, so everyone gets the same database with `php artisan migrate`.
- **Seeders** fill in the demo data (`php artisan migrate --seed`).

---

## 10. API basics

- **REST API:** each address is a thing (`/households`), and the method says what to do with it.
- **Methods:** `GET` read, `POST` create, `PUT` replace/edit, `PATCH` change part of it, `DELETE` remove.
- **Status codes:**

| Code | Meaning | Example in our system |
|---|---|---|
| 200 | OK | Getting the household list |
| 201 | Created | Registering, sending an alert |
| 204 | Deleted, nothing to return | Deleting an alert |
| 401 | Not signed in | No token |
| 403 | Signed in, but your role isn't allowed | A resident opening `/dashboard` |
| 404 | Not found | A household ID that doesn't exist |
| 422 | Invalid input | Empty name, wrong password, not enough stock |

The full list of endpoints is in Table 8 of [chapter3-draft.md](chapter3-draft.md).

---

## 11. Testing

- All tests and results are in [test-cases.md](test-cases.md).
- **Kinds of tests we did:** automated browser tests on the pages at phone and desktop sizes, API tests for both roles (correct and wrong input, wrong role, no token), and database checks (passwords are hashed).
- **Bugs that testing found:**
  - Pages had no API address on a fresh download because `frontend/.env` isn't uploaded (test C09). **Fix:** `api.js` falls back to `http://127.0.0.1:8000/api`.
  - The system only accepts Purok 1–8, but Ampayon has 23 Puroks (tests N14–N16). **Still to fix.**

---

## 12. Be ready to explain your own part

Every member must explain their own code. Prepare yours.

**Jhann Clevill (design, frontend, coordination):**
- The first login page, then moving it to **Vue** with 13 pages based on the Figma prototype.
- Form checks in `utils/validation.js`: required fields, Philippine mobile numbers, password at least 8 characters, Purok chosen.
- Connecting **Create account** to `POST /api/register`, with an error under each field.
- The **API address fallback** in `src/api.js` (the C09 bug above).
- Testing records, the docs, and the Chapter 3 draft.
- *Be honest:* Jhon Lee built most of the backend and connected most pages to the API. You can still explain how they work using this reviewer.

**Jhon Lee:** Laravel backend, database, API, TextBee SMS, connecting the pages. **Marc:** Chapter 1, barangay data gathering. **Humphrey:** Chapter 2, barangay data gathering.

---

## 13. Known limitations (say them honestly)

| Limitation | What to say |
|---|---|
| Only Purok 1–8 accepted | "The prototype had 8 Puroks. The barangay told us there are 23, so we're updating the validation." |
| Sign out only clears the browser | "The token still works on the server, and tokens don't expire. Next step: a logout endpoint that deletes the token, and an expiry time." |
| "Sent" SMS | "Sent means TextBee accepted the message. It doesn't prove the resident's phone received it." |
| Hotlines are sample numbers | "They need to be confirmed by the barangay before real use." |
| No summary report yet | "Planned. The barangay reports affected-family counts to the city." |
| Admin = staff | "Right now admin has the same rights as staff." |
| Needs internet and a running phone for SMS | "If the gateway fails, the alert is still saved and shown in the portal, and staff see the reason." |
| CORS allows every website (`*`) | "For the online version we should only allow our own frontend address." |

---

## 14. Practice questions

Cover the answers and try first.

1. **What problem does your system solve?** Manual, outdated records; counting affected families takes 1–2 days; warnings don't reach everyone. The system keeps records in one database, counts instantly, and sends SMS to any phone.
2. **Who are the users?** Residents, barangay staff, and admin.
3. **Why SMS and not just the website?** Not everyone has internet or a smartphone. Any phone can receive SMS. The interviewee estimated SMS would help about 80%.
4. **Why Vue?** The instructor required a framework, and Vue is simple: one file per page.
5. **Why Laravel?** It has validation, password hashing, migrations and token login built in.
6. **What is an API?** The set of addresses the frontend calls to get or save data. Ours returns JSON.
7. **What is a token?** A long random key the server gives you after signing in. You send it with every request instead of your password.
8. **What is the difference between 401 and 403?** 401: you're not signed in. 403: you're signed in, but your role isn't allowed.
9. **Where do you check the role?** In the Laravel controllers with `abort_unless(... isStaff(), 403)`. The Vue guard is only for convenience.
10. **How are passwords protected?** Bcrypt hashing through the `hashed` cast. Never stored as plain text.
11. **What is a migration?** A file that creates or changes a table, so every computer gets the same database.
12. **What is a seeder?** A file that fills the database with demo data.
13. **What is a foreign key?** A column that links to another table's record, such as `household_id` in `household_members`.
14. **What is a database transaction, and where do you use it?** Several changes that succeed or fail together. Relief (stock), check-in (occupants), and saving an alert with its log.
15. **What happens if TextBee is down?** The alert is still saved and shown in the portal. The log records the failures, and staff see the reason.
16. **How do you know an SMS was sent?** The log shows how many TextBee accepted. It can't prove delivery to each phone.
17. **How does the system decide priority?** Medical and rescue requests are automatically high, the others medium. Staff can change it.
18. **How does the center status change?** Empty = standby, 90% or more = near capacity, otherwise open. It's computed after every check-in or check-out.
19. **How do you stop two staff from giving the same last bag of rice?** The row is locked during the transaction, so the second request sees the new stock and is refused.
20. **How did you gather requirements?** Survey and interview at the Ampayon Barangay Hall, and feedback on the Figma prototype.
21. **What feedback did the barangay give?** It was confusing where to go back or home, they asked what the admin notification was for, and something about inputting households.
22. **How do you keep secrets safe?** In `.env`, which is never uploaded to GitHub.
23. **How did you test?** API tests per role, browser tests at different screen sizes, and database checks. Results are in `docs/test-cases.md`.
24. **What would you improve next?** 23 Puroks, a logout that cancels the token, a summary report, and confirmed hotlines and centers.
25. **What AI tools did you use?** Claude, for code, testing and docs. Every use is listed in `docs/ai-use-log.md` with how we checked the output.

---

## 15. For the debugging exam (35%, individual)

The instructor plants bugs in a backend. Steps:

1. **Reproduce it:** call the endpoint in Postman (or the page) and note the status code and message.
2. **Read the status code:** 401 means a token problem. 403 means a role check. 404 means a wrong route or ID. 422 means validation. 500 means a crash in the code.
3. **Find the code:** `routes/api.php` shows which controller and function handle the address.
4. **Look at the log** for 500 errors: `backend/storage/logs/laravel.log` (the last lines show the error and the line number).
5. **Check the database** in HeidiSQL: is the data really there? Is the column name the same as in the code?
6. **Fix the cause,** not only the symptom. Then **retest** the same request and **write it in the bug log**: what was wrong, the cause, the fix, the retest result.

**Useful commands** (in `backend/`):
- `php artisan route:list`: every route with its method and controller
- `php artisan migrate:fresh --seed`: rebuild the database with demo data (this erases all data)
- `php artisan tinker`: try PHP code on the real database
- `php artisan config:clear`: after changing `.env`

**Common planted bugs to look for:**
- A wrong validation rule (for example `between:1,8`)
- A missing role check
- A wrong column or table name
- A route with the wrong method (GET vs POST)
- `>` vs `>=` in a comparison
- A missing `->save()`, or a missing field in `$fillable`
- Wrong database settings in `.env`

# Chapter 4 draft: Results and Discussion

*Draft for the paper, written from the final test run on November 24, 2026 and the earlier test records in [test-cases.md](test-cases.md). Table and figure numbers continue from Chapter 3 (last table: Table 14; last figure: Figure 5). Text in [square brackets] must be filled in by the group. The "Notes for the group" at the end are not part of the paper.*

---

# CHAPTER 4

RESULTS AND DISCUSSION

This chapter presents the system that was developed, traces each requirement to the part of the system that implements it and the tests that checked it, reports the results of testing and of the validation with the Barangay Secretary, and discusses the results against the objectives in Section 1.3.

## 4.1 The Developed System

Disaster Ready was completed as a web application with two parts: a resident portal and a staff dashboard. Residents create an account with their mobile number and purok, and then use five pages: Home, My Household, Evacuation, Assistance and Alerts. Barangay staff and administrators sign in with their email address and use five pages: Dashboard, Household Profiles, Evacuation Centers, Relief Distribution and SMS Alerts. Every page reads and writes its data through the 27 endpoints of the Laravel REST API listed in Table 11, and alerts are sent as text messages through the TextBee SMS gateway. Table 15 summarizes the modules of the system and the requirements each one implements.

*Table 15. Modules of the developed system*

| Module | Users | Pages | Main functions | Requirement IDs |
|---|---|---|---|---|
| Sign-in and accounts | Residents, staff | Sign in, Create account, Staff sign in | Resident registration with name, mobile number, password, purok (1 to 23) and number of household members; sign-in by mobile number (residents) or email (staff); sign-out; the newest advisory shown before signing in | FR-01 to FR-04, FR-28 |
| Household profiling | Residents, staff | My Household; Household Profiles | Household profile and safety status; family members with age, relationship and PWD status, labeled Senior, Infant or PWD; search by household head and filter by purok; households added by staff for families without an account | FR-05 to FR-08, FR-24, FR-25 |
| Dashboard | Staff | Dashboard | Households in total, by purok and by safety status; total residents; evacuation center capacity and occupants; open assistance requests; the active advisory | FR-22 |
| Evacuation centers | Residents, staff | Evacuation; Evacuation Centers | Centers with capacity, occupants and the puroks they serve; the resident's assigned center; check-in and check-out with automatic status (standby, open, near capacity); new centers | FR-09, FR-10, FR-26 |
| Relief distribution | Staff | Relief Distribution | Relief items and stock; release to a household with automatic stock reduction; distribution history | FR-11 to FR-13 |
| Assistance requests | Residents, staff | Assistance; Dashboard | Requests for evacuation, relief, medical, rescue or hazard assistance, with automatic high priority for medical and rescue requests; status shown to the resident | FR-14 to FR-16 |
| Advisories and SMS alerts | Residents, staff | Alerts, Home; SMS Alerts | Alerts written by staff for all residents or one purok, saved as advisories and sent by SMS; SMS log with recipients, sent and failed counts; deletion of an alert | FR-17 to FR-20, FR-27 |
| Emergency hotlines | Residents | Home | Directory of emergency numbers | FR-21 |

The system is available online at [online link], and the demonstration accounts for residents and staff are listed in Appendix D. The source code is in the GitHub repository given in Section 3.7. Figures 6 to 12 show the main screens of the final version with demonstration data; the names, households and evacuation centers on them are made up and are not barangay records.

[Figure 6: insert "01 Sign in.png"]

*Figure 6. Resident sign-in page with the newest advisory (demonstration data)*

The sign-in page shows the barangay's newest active advisory to anyone who opens the system, before signing in (FR-28). Residents sign in with their mobile number, and new residents create an account from the same page.

[Figure 7: insert "02 Resident home.png"]

*Figure 7. Resident Home page (demonstration data)*

After signing in, a resident sees the active advisory, a summary of their household, their assigned evacuation center with its current occupancy, the status of their assistance requests, the barangay's announcements and the emergency hotlines.

[Figure 8: insert "03 My Household.png"]

*Figure 8. My Household page with the family member list (demonstration data)*

On My Household, the resident keeps the household profile and safety status up to date and lists every member of the household. Members aged 60 and above are labeled Senior, members under 2 are labeled Infant, and persons with disability are labeled PWD, so that responders can see who needs help first (FR-08).

[Figure 9: insert "04 Staff dashboard.png"]

*Figure 9. Staff Dashboard (demonstration data)*

The Dashboard gives staff the totals that the barangay previously counted by hand: registered households and how many are evacuated, total residents, evacuation centers in use with their occupancy, and open assistance requests. All figures are computed from the records each time the page is opened (FR-22).

[Figure 10: insert "05 Evacuation Centers.png"]

*Figure 10. Evacuation Centers page (demonstration data)*

Staff record the number of people arriving at and leaving each center. The occupancy bar and the status (standby, open or near capacity) are updated automatically, and a check-in that would exceed the capacity is refused (FR-10).

[Figure 11: insert "06 Relief Distribution.png"]

*Figure 11. Relief Distribution page (demonstration data)*

Staff record each release of relief goods to a household. The stock of the item is reduced at the same time, a release larger than the stock is refused, and the release appears at the top of the distribution history (FR-11 to FR-13).

[Figure 12: insert "07 SMS Alerts.png"]

*Figure 12. SMS Alerts page (demonstration data)*

Staff write an alert for all residents or for one purok. The alert is saved as an advisory and sent through the TextBee gateway, and the SMS log shows the number of recipients and how many messages were sent or failed. The page states plainly when no SMS was sent, so that it never reports an alert as texted when it was not (FR-18 to FR-20).

## 4.2 Requirements Traceability

Table 16 traces each requirement in Tables 5 and 6 to the page and API endpoint that implement it, the test cases that checked it, and its status at the end of the project. Test cases F01 to F51 and G01 to G12 belong to the final test run described in Section 4.3; test cases S01 and S02 are the SMS tests with real phones in Table 13.

*Table 16. Requirements traceability matrix*

| Req. ID | Requirement (short) | Page | API endpoint | Test cases | Status |
|---|---|---|---|---|---|
| FR-01 | Resident account creates the household (purok 1 to 23) | Create account | POST /register | F04–F07 | Met |
| FR-02 | Sign in by mobile number or email | Sign in; Staff sign in | POST /login | F01–F03 | Met |
| FR-03 | Staff functions for staff and administrators only | All staff pages | All staff endpoints | F09, G07 | Met |
| FR-04 | Sign out | Resident and staff menus | — | G09 | Met |
| FR-05 | View, search and filter households | Household Profiles | GET /households | F12, F13 | Met |
| FR-06 | Resident views own household | My Household | GET /me | F10, G08 | Met |
| FR-07 | Resident sets safety status | My Household | PATCH /households/{id}/safety | F17 | Met |
| FR-08 | Household members with Senior, Infant and PWD labels | My Household | GET, POST /households/{id}/members; DELETE /household-members/{id} | F18–F20, G08 | Met |
| FR-09 | Centers with capacity, occupants and puroks | Evacuation; Evacuation Centers | GET /evacuation-centers | F25 | Met |
| FR-10 | Check-in and check-out with automatic status | Evacuation Centers | PATCH /evacuation-centers/{id}/occupants | F26–F32 | Met |
| FR-11 | Relief items and stock | Relief Distribution | GET /relief-items | F33 | Met |
| FR-12 | Release relief and reduce stock | Relief Distribution | POST /relief-distributions | F34, F36–F38 | Met |
| FR-13 | Distribution history | Relief Distribution | GET /relief-distributions | F35 | Met |
| FR-14 | Resident sends an assistance request | Assistance | POST /incidents | F39–F41 | Met |
| FR-15 | Resident sees own requests and their status | Assistance; Home | GET /incidents | F11, F42 | Met |
| FR-16 | Staff update requests and add notes | Dashboard (list only) | PATCH /incidents/{id}; POST /incidents/{id}/notes | F42 | Partly met: works in the API; no staff screen yet |
| FR-17 | Advisories for signed-in users, newest first | Alerts; Home | GET /advisories | F44 | Met |
| FR-18 | Alert for all residents or one purok | SMS Alerts | POST /sms-alerts | F43, F45 | Met |
| FR-19 | SMS through TextBee with sent and failed counts | SMS Alerts | POST /sms-alerts | S01, S02 | Met (tested November 15, 2026) |
| FR-20 | SMS log | SMS Alerts | GET /sms-alerts | F46 | Met |
| FR-21 | Emergency hotlines | Home | GET /emergency-contacts | F49 | Met (numbers are sample data) |
| FR-22 | Dashboard summary from the records | Dashboard | GET /dashboard | F21, F22 | Met |
| FR-23 | Printable summary report | — | — | — | Not built (later version) |
| FR-24 | Staff add a household | Household Profiles | POST /households | F14, F15 | Met |
| FR-25 | Resident corrects household details | My Household | PUT /households/{id} | F16 | Met for residents; staff through the API only |
| FR-26 | Staff add an evacuation center | Evacuation Centers | POST /evacuation-centers | F23–F25 | Met |
| FR-27 | Staff delete an alert | SMS Alerts | DELETE /sms-alerts/{id} | F48 | Met |
| FR-28 | Newest advisory on the sign-in page | Sign in | GET /advisories/latest | F44, G04 | Met |
| NFR-01 | Passwords stored as bcrypt hashes | — | — | F50 | Met |
| NFR-02 | Login token required | All signed-in pages | 24 protected endpoints | F08, G06 | Met |
| NFR-03 | Residents refused on staff functions and other households | — | — | F09–F11 | Met |
| NFR-04 | No .env or keys in the repository | — | — | F51 | Met |
| NFR-05 | Server-side validation; nothing saved on error | All forms | All endpoints with input | F06, F15, F19, F37, F41, F47 | Met |
| NFR-06 | Stock and occupants never below zero or above capacity | Relief Distribution; Evacuation Centers | — | F28, F30, F32, F36, F38 | Met |
| NFR-07 | Alert kept and failure explained when the gateway fails | SMS Alerts | POST /sms-alerts | F43, S02 | Met |
| NFR-08 | 320 to 1440 px without sideways scrolling | All pages | — | G01 | Met |
| NFR-09 | Text at least 12 px; labeled fields; errors under the field | All pages | — | G02, G03, G05 | Met after a fix (Table 18) |
| NFR-10 | Way back to Home or Dashboard on every page | All inner pages | — | G10 | Met |
| NFR-11 | SUS score of at least 68 | — | — | — | Not yet measured (planned evaluation) |
| NFR-12 | Lists and dashboard load within 3 seconds | Data pages | — | G11 | Met with PHP OPcache; to be confirmed on the hosting server (Table 19) |

All 21 functional requirements with Must priority were met. Of the 28 functional requirements, 26 were met, one was partly met (FR-16, which has no staff screen yet) and one was not built (FR-23, planned for a later version). Of the 12 non-functional requirements, ten were met, NFR-12 was met only after the server was configured as a production server would be, and NFR-11 has not yet been measured because the usability evaluation is planned for the next phase (Section 3.9).

## 4.3 Testing Results

The final version of the system was tested on November 24, 2026 on a computer with an AMD Ryzen 3 2200G processor, 14 GB of RAM and Windows 10 Pro, using Laragon 8.8 (PHP 8.3.36, MySQL 8.4.3). Before the test run, the database was reset to the demonstration data (three accounts, 25 households, three evacuation centers, four relief items and five hotlines). The API was tested with 51 test cases sent from PowerShell to the endpoints in Table 11, covering valid requests, invalid input, requests without a login token and requests from a role that is not allowed. The pages were tested with 12 automated checks in Microsoft Edge through Playwright, signing in through the real forms at widths of 320, 375, 768 and 1440 px. No TextBee key was set on the test computer, so no text messages were sent during this run; sending to real phones had been tested on November 15, 2026 (S01 and S02 in Table 13). All records created by the tests were removed afterwards by resetting the demonstration data. Table 17 summarizes the results; the complete list of test cases is in Appendix F.

*Table 17. Summary of the final test run*

| Area | Test IDs | Test cases | Passed | Failed |
|---|---|---|---|---|
| Sign-in and registration | F01–F07 | 7 | 7 | 0 |
| Access control and data protection | F08–F11, F50, F51 | 6 | 6 | 0 |
| Household profiling | F12–F20 | 9 | 9 | 0 |
| Dashboard | F21–F22 | 2 | 2 | 0 |
| Evacuation centers | F23–F32 | 10 | 10 | 0 |
| Relief distribution | F33–F38 | 6 | 6 | 0 |
| Assistance requests | F39–F42 | 4 | 4 | 0 |
| Advisories, SMS alerts and hotlines | F43–F49 | 7 | 7 | 0 |
| Pages in the browser (layout, labels, navigation, sign-out) | G01–G10, G12 | 11 | 11 | 0 |
| Load time | G11 | 1 | 1 | 0 |
| SMS to real phones (November 15, 2026) | S01, S02 | 2 | 2 | 0 |
| **Total** | | **65** | **65** | **0** |

The totals in Table 17 are the results after the two defects found in this run were resolved. In the first run, 61 of the 63 test cases passed. G02 failed because some small labels, such as "Barangay Ampayon" under the system name and "Main menu" in the staff menu, were 10 to 11 px, below the 12 px required by NFR-09; they were raised to 12 px, and G02 and G01 passed when run again. G11 failed on the development server because the resident Home page took 4.44 seconds on average to load, which exceeds the 3-second target of NFR-12; the cause and the result after correcting the server configuration are discussed below.

Several test cases checked that a whole class of requests is handled correctly rather than a single example. F08 sent a request without a login token to each of the 24 protected endpoints, and all 24 were refused with HTTP 401. F09 sent a resident's token to each of the 13 staff-only endpoints, and all 13 were refused with HTTP 403; F10 used a resident's token to read or change another household in five ways, and all five were refused. F21 compared all 21 figures on the staff dashboard with counts taken directly from the database, and all 21 matched. F32 recorded 60 random check-ins and check-outs of 1 to 60 people at a center with a capacity of 100: 32 were accepted and 28 were refused, and after every request the number of occupants was equal to the expected count and stayed between 0 and 100.

Table 18 lists the defects found during development and testing and how each was resolved.

*Table 18. Defects found and how they were resolved*

| No. | Found by | Defect | Resolution | Re-test |
|---|---|---|---|---|
| 1 | A04, November 9, 2026 | The registration form sent the purok as text ("Purok 3"), and the API refused it. | The form sends the purok as a number. | R02, R03: Pass |
| 2 | C09, November 12, 2026 | Without a frontend .env file, the pages did not know the address of the API. | A default API address was added to the frontend's API client. | C10–C12: Pass |
| 3 | S01, November 15, 2026 | Messages sent through the gateway phone's second SIM failed for lack of credit. | The gateway was set to use the first SIM. | S01: Pass |
| 4 | Validation, November 16, 2026 | The inner pages had no direct way back to the Home page or the Dashboard. | "Back to Home" and "Back to Dashboard" links were added to every inner page. | B01–B14, G10: Pass |
| 5 | B01, November 16, 2026 | The sign-in page showed a sample advisory instead of the barangay's newest advisory. | The page reads the newest active advisory from GET /advisories/latest. | B01, B11, B12, G04: Pass |
| 6 | N14–N16, November 16, 2026 | The system accepted only Purok 1 to 8, but Ampayon has 23 puroks. | The accepted range was changed to 1 to 23 in the API and in every purok list. | P01–P19, F07: Pass |
| 7 | G02, November 24, 2026 | Some labels were 10 to 11 px, below the 12 px minimum (NFR-09). | The labels were raised to 12 px. | G01, G02: Pass |
| 8 | G11, November 24, 2026 | The resident Home page took 4.44 s on average to load on the development server, above the 3-second target (NFR-12). | PHP's OPcache, which is normally on in production servers, was enabled; the hosting server must have it on. | G11: Pass (0.36 s); to be re-measured on the hosting server |

Load time (NFR-12) was measured as the time from opening a page until the last answer from the API had arrived, five times for each page. Table 19 shows the results. On the development server, which handles one request at a time and runs PHP without OPcache, every request took about half a second, so the pages that make several requests at once were the slowest: the resident Home page needed 4.44 seconds on average and the staff Dashboard 2.35 seconds. With OPcache enabled, every page loaded in less than 0.4 seconds. To check the system at the size of the barangay, the households table was also filled with 500 households across all 23 puroks; the household list was returned in 0.39 to 0.42 seconds and the dashboard figures in about 0.30 seconds on the development server, and the dashboard totals still matched the database. These figures were measured on one computer with no network between the browser and the server, so the load times must be measured again on the online link.

*Table 19. Page load times in seconds (average of five loads, slowest in parentheses)*

| Page | Development server (no OPcache) | With OPcache |
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

## 4.4 Evaluation Results

The system was validated on November 16, 2026 with the Barangay Secretary, as described in Section 3.9. Table 20 summarizes her feedback and the action taken on each point.

*Table 20. Feedback from the validation and the action taken*

| Feedback | Action taken | Status |
|---|---|---|
| The system is useful and needed, because household counting is manual and the household records are incomplete. | None needed. The finding supports Objectives 1 and 2. | — |
| The inner pages offered no direct way back to the home page. | "Back to Home" links were added to the resident pages and "Back to Dashboard" links to the staff pages (NFR-10, defect 4 in Table 18). | Resolved |
| She asked what the notification on the administrator account was for. | [State the answer given. The notification appeared only in the Figma prototype and is not part of the developed system.] | Clarified |
| [Feedback about entering household records, to be completed by the proponents who conducted the validation.] | [Action taken] | [Status] |

The quantitative evaluation with the ISO/IEC 25010 questionnaire and the System Usability Scale (NFR-11) described in Section 3.9 has not yet been conducted, so no means or standard deviations are reported in this chapter. When it is conducted, the results will be interpreted with Table 14.

## 4.5 Discussion

Table 21 compares the result for each specific objective with the measure of success set in Table 1.

*Table 21. Results by objective*

| Objective | Measure of success (Table 1) | Result | Status |
|---|---|---|---|
| 1. Household profiling module | All module test cases passed. | 13 of 13 passed (F04–F07, F12–F20), with G08 for the member labels. | Met |
| 2. Dashboard counts from the records | Dashboard totals match the database in all test cases. | 21 of 21 figures matched (F21); totals updated as soon as a household was added (F22), and still matched with 500 households. | Met |
| 3. Evacuation center module | Occupancy never exceeds capacity in any test case. | Occupancy stayed between 0 and the capacity in all 10 test cases, including 60 random check-ins and check-outs (F23–F32). | Met |
| 4. Relief distribution module | Every release appears in the history; releases beyond the stock are refused. | Every release appeared first in the history (F35); releases beyond the stock were refused and the stock never went below zero (F36, F38). | Met |
| 5. SMS alerts | Test messages delivered to the test phones; counts recorded for each alert. | Delivered to the proponents' phones through the first SIM (S01); a wrong key was reported as failed with a plain message (S02); every alert was saved with its counts (F43–F48). | Met |
| 6. Security | 100% of requests without the required role rejected. | 24 of 24 requests without a token, 13 of 13 resident requests to staff functions and 5 of 5 requests on another household were refused (F08–F10); 100% of passwords were hashed (F50). | Met |
| 7. Validation with barangay personnel | Feedback recorded and acted on. | Validated with the Barangay Secretary; her navigation feedback was acted on (Table 20). | Met, with one informant |

The household profiling module (Objective 1) addresses the first problem in Section 1.2, an incomplete household profile that is not updated regularly. Residents can now keep their own household and family members up to date, and staff can add the households of families without an account, so the profile no longer depends only on data requested from the PSA. Recording each member's age and PWD status, with the Senior, Infant and PWD labels, gives the barangay a written record of the residents who, according to the Secretary, are at present identified only face to face during a disaster. The change from Purok 1 to 8 to Purok 1 to 23 (defect 6) shows the value of checking the design against the barangay's own information: the first version followed the prototype and would have excluded fifteen puroks.

The dashboard (Objective 2) answers the second problem, the one to two days the barangay needs to count affected households. Because every figure is computed from the records when the page is opened, the count of evacuated households and of households that need help is available as soon as residents or staff update the safety status, and the test with 500 households showed that the figures remain correct at the size of the barangay. The time saved, however, depends on the records being complete and up to date; the dashboard counts only the households that are registered, and their safety status is only as current as the last update made by the household or by staff.

The evacuation center and relief distribution modules (Objectives 3 and 4) replace observation and the logbook with records that are checked by the system. Both modules update their counts inside a database transaction and refuse a request that would push the occupants above the capacity or the stock below zero, which is why no sequence of requests in F32 or F36 to F38 produced an incorrect count. The distribution history also gives the barangay the list of households that received assistance, which at present has to be prepared separately each time the city asks for it. Recording newly received relief stock and printing reports for the city (FR-23) were left for a later version (Section 1.5), so the stock in the system still has to be entered by an administrator outside the pages.

The SMS module (Objective 5) adds a channel that reaches residents who are not online or not at home. The tests confirmed that alerts are delivered when the gateway phone, its SIM and its credit are in order, and also that the system behaves safely when they are not: the alert is still saved and shown in the resident portal, and staff are told in plain words why no message was sent. This matters because the Secretary estimated that SMS could reach only about 80% of residents, so the barangay must still be able to fall back on megaphones, the rescue vehicle and purok officers.

The security tests (Objective 6) show that the API, and not only the pages, enforces who may see and change each record, which responds to the Secretary's request that sensitive data be limited to authorized officials. Two weaknesses remain: signing out removes the login token from the browser but does not cancel it on the server, and tokens do not expire. Finally, the validation (Objective 7) confirmed that the system addresses the needs the barangay described, but it rests on one key informant, and the planned evaluation with more barangay personnel and residents is needed before the results can be generalized.

## 4.6 Limitations of the Results

The results above are subject to the following limitations.

- The final test run was done on the proponents' development computer, with the browser and the server on the same machine. Load times on the online server will include the delay of the network and must be measured again on the deployed link, with OPcache enabled on the server.
- The tests used demonstration data, not barangay records. The load test with 500 households used generated records and covered the household list and the dashboard only.
- Only Microsoft Edge was used in the automated browser tests. Firefox, Safari and real phones have not been tested.
- Text messages to real phones were tested once, on November 15, 2026, with the proponents' own phones and the free TextBee plan, which allows 300 messages a month. Delivery to many residents at once, and on different mobile networks, has not been tested.
- The validation involved one key informant, and the questionnaire-based evaluation (ISO/IEC 25010 and SUS) has not yet been conducted, so usability and user satisfaction were not measured.
- Requests from several staff members at the same time were not tested, although the check-in and relief functions lock the record they change.
- FR-16 has no staff screen, FR-23 was not built, the emergency hotline numbers are sample data, and login tokens are not cancelled on the server when a user signs out.

---

## Notes for the group (not for the paper)

1. **Online link.** Fill in "[online link]" in 4.1 after hosting. When the system is online, change these sentences, which say it will not be deployed: Section 1.5 ("…and is not intended to be deployed") and Section 3.4.4 ("…was not deployed to a server"). Then measure the load times again on the online link (Table 19). The host must have PHP OPcache turned on.
2. **Figures 6 to 12.** The screenshots are in the "Disaster Ready screenshots" folder on Jhann's Desktop, retaken on November 24 from the final version. Ch 3, Section 3.5 also says "[Insert screenshots … as Figure 6 onward]". Put the screenshots in 4.1 only, and replace that sentence in 3.5 with: "The screens built from it are presented in Section 4.1 (Figures 6 to 12)."
3. **Table numbers.** This draft uses Tables 15 to 21 and Figures 6 to 12. If a table or figure is added to Chapters 1 to 3, renumber these.
4. **Table 20.** Marc and Humphrey: fill in the answer about the administrator notification and the feedback about entering households. Section 3.9 still says navigation was "the one issue she raised"; change it so that it matches Table 20.
5. **Section 3.8 and Table 13.** Section 3.8 can mention the final test run ("The final version was tested again on November 24, 2026; the results are in Section 4.3."). Test IDs U12, S01 and S02 in Table 13 are not in docs/test-cases.md; whoever ran them should add them, so that Appendix F is complete.
6. **Appendix F.** Use docs/test-cases.md (all test cases with their results).
7. **Chapter 5.** The summary, conclusions and recommendations can now be written from 4.5 and 4.6.

# Figma prototype notes

Prototype link: https://crater-flaky-19710052.figma.site/
Reviewed: 2026-10-15
Screenshots: `docs/screenshots/figma-prototype/`

**Everything in the prototype is sample data.** Names, numbers, dates (it says "Monday, August 26, 2024") and phone numbers are made up, unless the group confirms otherwise.

## Screens and what they show

These lists are useful when planning pages and database fields.

### Public

**Sign in** (`login-reference-from-group.png`)
- Mobile number (+63), password
- Rainfall advisory box, links to "Evacuation centers" and "Emergency hotlines" (they do nothing in the prototype)
- "Barangay staff login" button (in the prototype it opens the staff dashboard with no login)

**Create account** (`figma-register.png`)
- Full name, mobile number, create password, home address (Purok 1 to 8)

### Resident portal

Top menu: Home, My Household, Evacuation, Assistance, Alerts, and a "Sign out" button.

**Home** (`figma-citizen-portal.png`)
- Active weather advisory ("Household notified by SMS")
- Summary cards: household profile (members, % complete, verified), assigned evacuation center, relief assistance requests
- Barangay announcements
- Emergency contacts: Barangay Rescue, Butuan CDRRMO, 911 (**numbers not verified**)

**My Household** (`figma-citizen-household.png`)
- Household ID (format shown: `AMP-2024-0831`)
- Head of household, mobile number, home address (Purok), number of members, vulnerable members (for example "1 senior, 1 child"), emergency contact (name + number)
- Family readiness checklist: profile complete, emergency contacts added, medical needs recorded, evacuation center assigned, go-bag checklist

**Evacuation** (`figma-citizen-evacuation.png`)
- Assigned center based on the resident's Purok
- Center name, location, distance, capacity (for example 312 of 500), status (Open)
- Facilities: medical station, accessible, food & water, pet area
- Route with distance and walking and vehicle times (this needs map data, so it may be out of scope)

**Assistance** (`figma-citizen-assistance.png`)
- Type of assistance: evacuation, relief, medical, rescue request, report a hazard
- Description ("Tell us what you need"), submit
- List of the resident's requests with status (for example Approved)
- Reminder: "For life-threatening emergencies, call 911"

**Alerts** (`figma-citizen-alerts.png`)
- List of notices: type (weather advisory, announcement), time, title, message

### Staff portal

Left sidebar: Dashboard, Household Profiles, Evacuation Centers, Relief Distribution, SMS Alerts, Reports & Analytics. Also shows the signed-in staff member (name + role) and a search box.

**Dashboard** (`figma-staff-dashboard.png`)
- Buttons: Add household, Send SMS alert
- Active advisory banner
- Totals: registered households, total residents, evacuation centers, relief packs ready
- Evacuation center capacity, community readiness %, recent household profiles, SMS activity

**Household Profiles** (`figma-staff-households.png`)
- Totals: households, vulnerable residents, verified records (and pending review), Puroks covered
- Household list: family name, Purok, members, status (High risk / Priority / Registered)

**Evacuation Centers** (`figma-staff-evacuation.png`)
- Each center: name, status (Operational / Standby / Near capacity), occupancy (current / capacity)
- Buttons: Add center, Check in evacuees, Manage

**Relief Distribution** (`figma-staff-relief.png`)
- Stock: family packs, water bottles; families served; pending requests
- Citizen assistance requests list with status, "Process" button
- "New distribution" button

**SMS Alerts** (`figma-staff-sms.png`)
- Compose alert button
- Past campaigns: title, number of recipients, date/time, % delivered
- Totals: messages sent, delivery rate, active numbers

**Reports & Analytics** (`figma-staff-reports.png`)
- Readiness score, profile coverage, SMS delivery, response time
- Six-month trend chart, priority indicators, "Export report"

## Possible database tables (starting point only)

Based on the screens above. **This is not the final design.** The backend member decides, together with the group's ERD.

- Users / accounts (resident or staff, mobile number, password, role)
- Households (head, Purok, contact number, status)
- Household members (name, age group, vulnerability such as senior, child, PWD or pregnant, medical needs)
- Emergency contacts
- Evacuation centers (name, location, capacity, current occupancy, status, facilities)
- Evacuee check-ins (household, center, date)
- Relief distributions (household, items, quantity, date, staff who released it)
- Assistance requests (household, type, description, status, date)
- Alerts / announcements (type, title, message, date, sent by SMS or not)

## Design: colors and fonts

Copied from the prototype into `frontend/css/base.css` (section 1).

| Use | Color |
|---|---|
| Main teal (buttons, links) | `#08776D` |
| Logo teal | `#0B7E71` |
| Headings | `#17343D` |
| Body text (muted) | `#5E7379` (prototype `#687D83`, darkened slightly for readability) |
| Page background | `#F4F7F6` |
| Card | `#FFFFFF`, border `#DCE7E4` |
| Advisory amber | `#E5A43B` icon, `#FFF9EF` background, `#EFD4AA` border |

Fonts (Google Fonts):
- **Manrope ExtraBold (800)** for headings and the logo name
- **DM Sans** Regular (400), Medium (500) and Bold (700) for everything else

Icons are simple line icons (shield with a check, map, arrow) copied from the prototype.

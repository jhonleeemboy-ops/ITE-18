# What the paper template requires

Summary of "ITE18_Mini-Thesis_Paper_Template.docx" (ITE 18, Applications Development and Emerging Technologies), focused on what affects the **system** and **Chapter 3**. Read the full template before writing.

## Deadlines (instructor announcement, Oct 15)

- Paper soft copy: **November 19, 2026**
- Link of the functional and usable system: **November 20, 2026, 11:59 PM** (the link closes afterwards)
- Presentation of the final project + **individual hands-on debugging**: **November 23 to December 15, 2026**. Our slot is most likely **Dec 7** (the date Jhon Lee entered in the scheduling sheet).

**The system must be online (deployed), not just running on a laptop.**

Instructor's materials (Oct 8 post): "ITE18 Finals Requirements" video (18 min), "Final Project_Monitoring" sheet, the paper template, and the project rubrics. The video and the sheet haven't been reviewed here.

## How we are graded (ITE18 Project Rubrics)

| Part | Weight | Scored for | What matters most |
|---|---|---|---|
| A. Research paper | 15% | Group | Real evidence from Ampayon, measurable objectives, real users, 10+ sources, testable requirements, honest results, **AI use fully declared** |
| B. System | 30% | Group | All "Must" requirements work end to end for every role; clean backend structure; database with keys/constraints/migrations/seed data; RESTful API with docs; SMS API that handles failures; server-side login + roles, hashed passwords, secrets in env variables; usability tested with real users (e.g. SUS); test cases + Postman tests + defect log; deployed link + README + user manual; **regular commits from every member** |
| C. Presentation and defense | 20% | Group + individual | Live demo for each role (main flow, one error path, backend side); **each member explains their own code**; every member presents |
| D. Hands-on debugging | **35%** | **Individual** | Each member fixes planted bugs in a backend during a timed exam: reproduce, use Postman/logs/database queries, fix the root cause, retest, write a bug log |

**The biggest single part (35%) is individual backend debugging.** Every member needs to understand the backend, not just Jhon Lee. Grades are adjusted per person using the GitHub history and Appendix I.

## Our copy of the paper is missing two chapters

The instructor's template has **Chapter 4 (Results and Discussion)** and **Chapter 5 (Summary, Conclusions and Recommendations)**. The group's Google Doc copy stops after Chapter 3, so these need to be added and assigned.

- Ch 4: 4.1 the developed system (screenshots per module, requirement IDs, deployed link, demo accounts), 4.2 traceability table (requirement → module/endpoint → test case → status), 4.3 testing results (pass/fail counts, defects and fixes), 4.4 user evaluation results (mean, SD, interpretation), 4.5 discussion per objective, 4.6 limitations
- Ch 5: 5.1 summary of findings, 5.2 one conclusion per objective, 5.3 recommendations

## Structure of the paper

Title page, Declaration of Originality and Use of Tools, Abstract, Table of Contents, Chapter 1 (Introduction), Chapter 2 (Review of Related Literature and Systems), Chapter 3 (Methodology and System Specification), References, Appendices A to I.

Format: long bond, Times New Roman 12, 1.5 spacing, APA 7th, at least 10 references (mostly from the last 10 years).

## Things that apply to everyone

- **Declaration of use of tools:** list every AI assistant, code generator, template or service used, what it was used for, and how the output was checked or changed. Claude is one of them. See [ai-use-log.md](ai-use-log.md).
- **Appendix I, individual contributions:** "Contributions should be visible in the repository history" and are used when grades are adjusted. **Each member should commit their own work from their own GitHub account.**
- **Chapter 1 needs real evidence** that the problem exists in Ampayon: numbers, records, interviews, observations or news from the place itself.

## Chapter 3 checklist (Jhann Clevill + Jhon Lee)

| Section | What it needs | What we already have |
|---|---|---|
| 3.1 Development approach | Method (e.g. Agile/iterative), Table 4: timeline with sprints, dates, deliverables, person in charge | [checklist.md](checklist.md) phases |
| 3.2 Respondents and sampling | Who took part, how many, how chosen | Nothing yet (needs barangay contact) |
| 3.3 Requirements gathering | Interviews/surveys/observation with real users, instruments in Appendix B | Nothing yet |
| 3.4.1 Functional requirements | Table 5: ID (FR-01…), "The system shall…", user role, MoSCoW priority, acceptance criterion | Feature list A–F in [project-status.md](project-status.md); login rules in the login page |
| 3.4.2 Non-functional requirements | Table 6: measurable, e.g. response time; **passwords hashed; protected routes need login and roles** | Login validation rules |
| 3.4.3 Use cases | Use case diagram + Table 7 per key use case (main, alternate, exception flows) | Nothing yet |
| 3.4.4 Hardware/software | Minimum devices, browsers, network, for development and deployed versions | Tools list in [project-status.md](project-status.md) |
| 3.5 System design | Architecture diagram, **ERD + data dictionary**, DFD or activity diagrams, **Table 8: API endpoints** (method, endpoint, role, status codes, requirement ID), wireframes / Figma link | Figma link, screen and field list in [prototype-notes.md](prototype-notes.md); ERD from Jhon Lee pending |
| 3.6 Technologies | Table 9: each technology, **version**, reason, **licence**; the emerging tech or third-party API (SMS) and **what happens when it fails** | Partly: HTML/CSS/JS, Node.js 24, Google Fonts |
| 3.7 Coding standards and version control | Naming, folder structure, branching and commit rules, code review, repo link, licence of our code | Folder structure in [README](../README.md); repo link |
| 3.8 Testing plan | Unit, integration, API tests (e.g. Postman), security checks, user acceptance testing; test cases linked to requirement IDs | 24 login tests in [test-cases.md](test-cases.md) (need requirement IDs added) |
| 3.9 Evaluation plan | SUS or ISO/IEC 25010 questionnaire, rating scale and interpretation | Nothing yet |
| 3.10 Ethics and data privacy | Consent, **Data Privacy Act (RA 10173)**, fictional or anonymized test data | Sample data is already labeled as sample |

## Appendices that come from the system

| Appendix | Needs |
|---|---|
| D | Repo link, README, **deployment link**, **demo accounts** |
| E | API documentation (e.g. Postman collection export) and database schema |
| F | Full test cases, results and bug log |
| G | User manual |
| H | Project timeline (Gantt chart) |

# What the paper template requires

Summary of "ITE18_Mini-Thesis_Paper_Template.docx" (ITE 18, Applications Development and Emerging Technologies), focused on what affects the **system** and **Chapter 3**. Read the full template before writing.

## Deadlines

- Paper soft copy: **November 19, 2026**
- Link of the working system: **November 20, 2026, 11:59 PM** (the link closes afterwards)

**The system must be online (deployed), not just running on a laptop.**

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

# AI and tool use log

The paper's "Declaration of Originality and Use of Tools" asks us to list every AI assistant, code generator, template or third-party service, what it was used for, and how we checked or changed the output. This log keeps that record as we go, so the table is easy to fill in later.

Each member should add their own rows for tools they used.

| Date | Who | Tool / source | What it was used for | How we checked or changed the output |
|---|---|---|---|---|
| 2026-10-15 | Jhann Clevill | Claude (Anthropic), in VS Code | Reviewed the Figma prototype and recorded its screens, colors and fonts | Compared against the prototype screenshots |
| 2026-10-15 | Jhann Clevill | Claude (Anthropic), in VS Code | Wrote the login page code (`frontend/index.html`, CSS, JS) from the Figma design | 24 automated browser tests (see [test-cases.md](test-cases.md)), screenshots checked on desktop and phone sizes. *Jhann: add your own browser check here.* |
| 2026-10-15 | Jhann Clevill | Claude (Anthropic), in VS Code | Drafted the project docs (README, status, checklist, notes) and set up Git/GitHub | Facts checked against the group chat screenshots. *Jhann: add your own review here.* |
| 2026-11-04 | Jhann Clevill | Claude (Anthropic), in VS Code | Installed and configured Laragon, set up GitHub on this PC | Checked that PHP, Composer, MySQL and Apache respond |
| 2026-11-05 | Jhann Clevill | Claude (Anthropic), in VS Code | Reviewed ERD v1 and wrote suggestions (docs/erd-review.md) | *Jhann/Jhon Lee: note which suggestions were accepted* |
| 2026-11-05 | Jhann Clevill | Claude (Anthropic), in VS Code | Built the Vue frontend draft (13 pages, sample data) based on the Figma prototype | Build check + 17 automated browser tests at 4 screen sizes (docs/test-cases.md). *Jhann: add your own browser check here.* |
| 2026-11-05 | Jhann Clevill | Vue 3, Vue Router, Vite (open-source, MIT licence) | Frontend framework, page routing, dev server and build | Installed from npm, 0 vulnerabilities reported |
| 2026-11-09 | Jhann Clevill | Claude (Anthropic), in VS Code | Set up Jhon Lee's Laravel backend locally; connected the Vue registration form to `POST /api/register` with Axios (token saving, error handling) | 6 API tests + 13 browser tests + a database check that passwords are hashed (docs/test-cases.md). *Jhann: add your own check here.* |
| 2026-11-09 | Jhann Clevill | Axios (open-source, MIT licence) | Sending requests from Vue to the Laravel API | Installed from npm, 0 vulnerabilities reported |
| 2026-10-15 | Jhann Clevill | Figma Make (Jhon Lee's prototype) | Design reference for colors, layout and screens | Rebuilt by hand in HTML/CSS/JS, not copied |
| 2026-10-15 | Jhann Clevill | Google Fonts (Manrope, DM Sans) | Fonts on the website | Both fonts use the SIL Open Font License |

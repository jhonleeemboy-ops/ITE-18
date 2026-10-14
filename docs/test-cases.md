# Test cases and results

## Login page (`frontend/index.html`)

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

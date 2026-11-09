// Remembers who is signed in: the login token and the user from the API.
// They're kept in the browser's localStorage, so they survive a page refresh.
//
// Note for the defense: localStorage is simple and fine for this project.
// The trade-off is that any script on the page can read it, so the site
// must never load untrusted scripts.

const TOKEN_KEY = "dr_token";
const USER_KEY = "dr_user";

export function saveSession(token, user) {
  localStorage.setItem(TOKEN_KEY, token);
  localStorage.setItem(USER_KEY, JSON.stringify(user));
}

export function getToken() {
  return localStorage.getItem(TOKEN_KEY);
}

// Returns the saved user, or null if nobody is signed in
export function getUser() {
  try {
    return JSON.parse(localStorage.getItem(USER_KEY));
  } catch {
    return null;
  }
}

export function clearSession() {
  localStorage.removeItem(TOKEN_KEY);
  localStorage.removeItem(USER_KEY);
}

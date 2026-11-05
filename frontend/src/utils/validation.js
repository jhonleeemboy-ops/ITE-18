// Form checks shared by the sign-in, register and staff pages.
// Each check returns an error message, or "" when the value is OK.

// Turns what the user typed into 10 digits.
// Example: "0917 123 4567" or "+63 917 123 4567" becomes "9171234567"
export function cleanMobileNumber(text) {
  let digits = text.replace(/\D/g, ""); // remove spaces, dashes, "+", etc.

  if (digits.startsWith("63")) {
    digits = digits.slice(2); // typed with the country code: 63 917...
  } else if (digits.startsWith("0")) {
    digits = digits.slice(1); // typed the local way: 0917...
  }

  return digits;
}

// A Philippine mobile number has 10 digits after +63 and starts with 9
export function isValidMobileNumber(digits) {
  return /^9\d{9}$/.test(digits);
}

export function mobileError(text) {
  if (text.trim() === "") return "Enter your mobile number.";
  if (!isValidMobileNumber(cleanMobileNumber(text))) return "Enter a valid mobile number, like 917 123 4567.";
  return "";
}

export function requiredError(text, message) {
  return text.trim() === "" ? message : "";
}

export function newPasswordError(text) {
  if (text === "") return "Create a password.";
  if (text.length < 8) return "Use at least 8 characters.";
  return "";
}

export function emailError(text) {
  if (text.trim() === "") return "Enter your email.";
  if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(text.trim())) return "Enter a valid email, like name@example.com.";
  return "";
}

// Puts the cursor in the first field that has an error.
// Returns true if there was an error.
export function focusFirstError(errors, fieldIds) {
  const firstId = fieldIds.find((id) => errors[id]);
  if (firstId) {
    document.getElementById(firstId)?.focus();
    return true;
  }
  return false;
}

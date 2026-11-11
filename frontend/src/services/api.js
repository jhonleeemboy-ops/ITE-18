// One shared Axios "client" for talking to the Laravel API (backend/).
//
// Usage in a page:
//   import api from "../services/api.js";
//   const { data } = await api.post("/register", { ... });
import axios from "axios";
import { getToken } from "./session.js";

const api = axios.create({
  // The API address. Change it in frontend/.env (VITE_API_URL) when the backend is online.
  baseURL: import.meta.env.VITE_API_URL || "http://127.0.0.1:8000/api",
  // Ask Laravel to always answer in JSON (also for errors)
  headers: { Accept: "application/json" },
  // Give up after 10 seconds instead of waiting forever
  timeout: 10000,
});

// Before every request, attach the login token if we have one.
// Laravel Sanctum reads it from the "Authorization: Bearer ..." header.
api.interceptors.request.use((config) => {
  const token = getToken();
  if (token) {
    config.headers.Authorization = `Bearer ${token}`;
  }
  return config;
});

// Turns an Axios error into something a form can show:
//   fieldErrors: { mobile_number: ["This mobile number is already registered."], ... }
//   message:     one sentence for the whole form (when it's not about one field)
export function readApiError(error) {
  // 422 = Laravel validation failed: messages per field
  if (error.response?.status === 422) {
    return { fieldErrors: error.response.data.errors || {}, message: "" };
  }

  // Any other answer from the server (e.g. 500)
  if (error.response) {
    return {
      fieldErrors: {},
      message: `The server had a problem (error ${error.response.status}). Please try again.`,
    };
  }

  // No answer at all: backend not running, no internet, or too slow
  return {
    fieldErrors: {},
    message: "Can't reach the server. Check your internet connection or make sure the backend is running.",
  };
}

export default api;

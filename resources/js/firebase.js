// Import the functions you need from the SDKs you need
import { initializeApp } from "firebase/app";
import { getAnalytics } from "firebase/analytics";
import { getMessaging, getToken, onMessage } from "firebase/messaging";

// Your web app's Firebase configuration
const firebaseConfig = {
  apiKey: "AIzaSyDuLr9q0g5O5T58P8uUoK23el-7tGcgSNg",
  authDomain: "nagarik-plus.firebaseapp.com",
  projectId: "nagarik-plus",
  storageBucket: "nagarik-plus.firebasestorage.app",
  messagingSenderId: "756270018867",
  appId: "1:756270018867:web:60e789408c2c0ef11e4e46",
  measurementId: "G-C03YTZ5KJD"
};

// Initialize Firebase
const app = initializeApp(firebaseConfig);

// Initialize Analytics (supported in browser environment)
let analytics;
if (typeof window !== "undefined" && "location" in window) {
  analytics = getAnalytics(app);
}

// Initialize Messaging (FCM)
let messaging;
try {
  messaging = getMessaging(app);
} catch (err) {
  console.warn("Firebase Messaging not supported in this environment:", err);
}

export { app, analytics, messaging, firebaseConfig };

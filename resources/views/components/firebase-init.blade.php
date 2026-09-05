{{-- Firebase SDK CDN & Initialization Component --}}
<script type="module">
  import { initializeApp } from "https://www.gstatic.com/firebasejs/10.8.0/firebase-app.js";
  import { getAnalytics } from "https://www.gstatic.com/firebasejs/10.8.0/firebase-analytics.js";
  import { getMessaging, getToken, onMessage } from "https://www.gstatic.com/firebasejs/10.8.0/firebase-messaging.js";

  const firebaseConfig = {
    apiKey: "{{ config('services.firebase.api_key', 'AIzaSyDuLr9q0g5O5T58P8uUoK23el-7tGcgSNg') }}",
    authDomain: "{{ config('services.firebase.auth_domain', 'nagarik-plus.firebaseapp.com') }}",
    projectId: "{{ config('services.firebase.project_id', 'nagarik-plus') }}",
    storageBucket: "{{ config('services.firebase.storage_bucket', 'nagarik-plus.firebasestorage.app') }}",
    messagingSenderId: "{{ config('services.firebase.messaging_sender_id', '756270018867') }}",
    appId: "{{ config('services.firebase.app_id', '1:756270018867:web:60e789408c2c0ef11e4e46') }}",
    measurementId: "{{ config('services.firebase.measurement_id', 'G-C03YTZ5KJD') }}"
  };

  // Initialize Firebase
  const app = initializeApp(firebaseConfig);
  const analytics = getAnalytics(app);

  window.firebaseApp = app;
  window.firebaseAnalytics = analytics;
</script>

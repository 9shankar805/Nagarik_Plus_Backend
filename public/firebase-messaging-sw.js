importScripts('https://www.gstatic.com/firebasejs/10.8.0/firebase-app-compat.js');
importScripts('https://www.gstatic.com/firebasejs/10.8.0/firebase-messaging-compat.js');

const firebaseConfig = {
  apiKey: "AIzaSyDuLr9q0g5O5T58P8uUoK23el-7tGcgSNg",
  authDomain: "nagarik-plus.firebaseapp.com",
  projectId: "nagarik-plus",
  storageBucket: "nagarik-plus.firebasestorage.app",
  messagingSenderId: "756270018867",
  appId: "1:756270018867:web:60e789408c2c0ef11e4e46",
  measurementId: "G-C03YTZ5KJD"
};

firebase.initializeApp(firebaseConfig);

const messaging = firebase.messaging();

messaging.onBackgroundMessage((payload) => {
  console.log('[firebase-messaging-sw.js] Received background message ', payload);
  const notificationTitle = payload.notification.title || 'Nagarik+ Notification';
  const notificationOptions = {
    body: payload.notification.body || '',
    icon: '/icon.png'
  };

  self.registration.showNotification(notificationTitle, notificationOptions);
});

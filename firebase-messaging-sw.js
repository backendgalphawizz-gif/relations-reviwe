// firebase-messaging-sw.js

// Import the necessary functions from the Firebase SDK
// importScripts('https://www.gstatic.com/firebasejs/10.12.2/firebase-app.js');
// importScripts('https://www.gstatic.com/firebasejs/10.12.2/firebase-messaging.js');

importScripts('https://www.gstatic.com/firebasejs/12.4.0/firebase-app-compat.js');
importScripts('https://www.gstatic.com/firebasejs/12.4.0/firebase-messaging-compat.js');

// Initialize the Firebase app in the service worker by passing the generated config
const firebaseConfig = {
    apiKey: "AIzaSyAbVv-H2kbOH1REQ2ggNc7xxg0Bh9LfT28",
    authDomain: "realtionship-849b1.firebaseapp.com",
    databaseURL: "https://realtionship-849b1-default-rtdb.firebaseio.com",
    projectId: "realtionship-849b1",
    storageBucket: "realtionship-849b1.firebasestorage.app",
    messagingSenderId: "884911350693",
    appId: "1:884911350693:web:86f89b3a009d9fe8823e4c",
    measurementId: "G-2QD0G6R41N"
};

firebase.initializeApp(firebaseConfig);

// Retrieve an instance of Firebase Messaging so that it can handle background messages
const messaging = firebase.messaging();

messaging.onBackgroundMessage((payload) => {
    console.log('[firebase-messaging-sw.js] Received background message ', payload);
    const notificationTitle = payload.notification.title;
    const notificationOptions = {
        body: payload.notification.title,
        icon: '/firebase-logo.png' // Replace with your icon
    };

    self.registration.showNotification(notificationTitle, notificationOptions);
});

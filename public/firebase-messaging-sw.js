importScripts('https://www.gstatic.com/firebasejs/12.4.0/firebase-app-compat.js');
importScripts('https://www.gstatic.com/firebasejs/12.4.0/firebase-messaging-compat.js');

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

const messaging = firebase.messaging();

messaging.onBackgroundMessage((payload) => {
    console.log('[firebase-messaging-sw.js] background message', payload);

    let dataBody = payload?.data?.body || {};
    if (typeof dataBody === 'string') {
        try { dataBody = JSON.parse(dataBody); } catch (e) { dataBody = {}; }
    }

    const notificationTitle = payload?.notification?.title
        || (Number(dataBody?.notificationType) === 2 ? 'Incoming Call' : 'Notification');
    const notificationBody = payload?.notification?.body
        || dataBody?.description
        || (dataBody?.userName
            ? (dataBody.userName + ' is calling' + (dataBody.call_type ? ' (' + dataBody.call_type + ')' : ''))
            : (Number(dataBody?.notificationType) === 2 ? 'You have an incoming call' : ''));

    self.registration.showNotification(notificationTitle, {
        body: notificationBody,
        icon: '/assets/img/mainLogo.png',
        requireInteraction: Number(dataBody?.notificationType) === 2,
        data: {
            link: dataBody?.link || '/advisor/dashboard',
            notificationType: dataBody?.notificationType || null,
            callId: dataBody?.callId || null,
        },
    });
});

self.addEventListener('notificationclick', (event) => {
    event.notification.close();
    const link = (event.notification.data && event.notification.data.link)
        ? event.notification.data.link
        : '/advisor/dashboard';

    event.waitUntil(
        clients.matchAll({ type: 'window', includeUncontrolled: true }).then((clientList) => {
            const prefersAdmin = String(link).includes('/admin');
            for (const client of clientList) {
                const isAdminClient = client.url.includes('/admin');
                const isAdvisorClient = client.url.includes('/advisor');
                if (((prefersAdmin && isAdminClient) || (!prefersAdmin && isAdvisorClient)) && 'focus' in client) {
                    client.navigate(link);
                    return client.focus();
                }
            }
            if (clients.openWindow) {
                return clients.openWindow(link);
            }
        })
    );
});

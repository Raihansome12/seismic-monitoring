import Echo from 'laravel-echo';
import Pusher from 'pusher-js';
window.Pusher = Pusher;

// console.log('Initializing Echo with config:', {
//     key: import.meta.env.VITE_REVERB_APP_KEY,
//     wsHost: import.meta.env.VITE_REVERB_HOST,
//     wsPort: import.meta.env.VITE_REVERB_PORT,
//     scheme: import.meta.env.VITE_REVERB_SCHEME
// });

const echo = new Echo({
    broadcaster: 'reverb',
    key: import.meta.env.VITE_REVERB_APP_KEY,
    wsHost: import.meta.env.VITE_REVERB_HOST,
    wsPort: import.meta.env.VITE_REVERB_PORT ?? 80,
    wssPort: import.meta.env.VITE_REVERB_PORT ?? 443,
    forceTLS: (import.meta.env.VITE_REVERB_SCHEME ?? 'https') === 'https',
    enabledTransports: ['ws', 'wss'],
});


// Add connection status logging
echo.connector.pusher.connection.bind('connected', () => {
    console.log('WebSocket connected successfully');
});

echo.connector.pusher.connection.bind('disconnected', () => {
    console.log('WebSocket disconnected');
});

echo.connector.pusher.connection.bind('error', (error) => {
    console.error('WebSocket error:', error);
});

// Test channel subscription
echo.channel('seismic-data')
    .listen('NewSeismicDataReceived', (e) => {
        console.log('Received seismic data event:', e);
    });

window.Echo = echo;




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

window.Echo = echo;

// Subscribe to the channel and log subscription
// const channel = echo.channel('gps-channel');
// console.log('Subscribing to gps-channel');

// channel.listen('.NewGpsDataReceived', (e) => {
//     console.log('New GPS data received on channel:', e);
//     const locationData = {
//         latitude: e.latitude,
//         longitude: e.longitude,
//         reading_times: e.reading_times
//     };

//     // if (window.Livewire && typeof window.Livewire.emit === 'function') {
//     //     console.log('Livewire ready. Emitting handleNewLocation...');
//     //     window.Livewire.emit('handleNewLocation', locationData);
//     // } else {
//     //     console.warn('Livewire belum siap, retry emit dalam 500ms...');
//     //     const retryEmit = setInterval(() => {
//     //         if (window.Livewire && typeof window.Livewire.emit === 'function') {
//     //             console.log('Retry berhasil. Emitting handleNewLocation...');
//     //             window.Livewire.emit('handleNewLocation', locationData);
//     //             clearInterval(retryEmit);
//     //         }
//     //     }, 500);
//     // }
// });




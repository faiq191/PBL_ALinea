 import Echo from 'laravel-echo';

import Pusher from 'pusher-js';
window.Pusher = Pusher;

window.Echo = new Echo({
    broadcaster: 'reverb',
    key: window.laravelReverb?.key || import.meta.env.VITE_REVERB_APP_KEY,
    wsHost: window.laravelReverb?.host || import.meta.env.VITE_REVERB_HOST,
    wsPort: window.laravelReverb?.port || import.meta.env.VITE_REVERB_PORT || 80,
    wssPort: window.laravelReverb?.port || import.meta.env.VITE_REVERB_PORT || 443,
    forceTLS: (window.laravelReverb?.scheme || import.meta.env.VITE_REVERB_SCHEME || 'https') === 'https',
    enabledTransports: ['ws', 'wss'],
});

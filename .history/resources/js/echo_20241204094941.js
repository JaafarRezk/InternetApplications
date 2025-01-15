
import Echo from 'laravel-echo';
 
import Pusher from 'pusher-js';
window.Pusher = Pusher;
 
window.Echo = new Echo({
    broadcaster: 'pusher',
    key: '64bfa1c78333446b2e60',
    cluster: 'us2',
    forceTLS: false
});
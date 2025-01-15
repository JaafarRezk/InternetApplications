import './bootstrap';
window.Echo.private('notifications')
    .listen('NewNotificationEvent', (data) => {
        console.log('Received notification:', data.message);
    });

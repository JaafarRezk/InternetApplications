<!DOCTYPE html>
<head>
    <title>Pusher Test</title>
    <script src="https://js.pusher.com/8.2.0/pusher.min.js"></script>
    <script>

        var pusher = new Pusher('a385df151dbe1f06f420', {
            cluster: 'us2',
            authEndpoint: 'http://127.0.0.1:8000/api/sendGroupInvitation',
            auth: {
                headers: {
'Content-Type': 'application/json',
                }
            }
        });

        var channel = pusher.subscribe('notifications');

        channel.bind('non-belonging-animals', function(data) {
            alert(JSON.stringify(data));
        });

        pusher.connection.bind('error', function(error) {
            console.error('Pusher error:', error);
        });
    </script>
</head>
<body>
<h1>Pusher Test</h1>
<p>
    Try publishing an event to channel <code>my-channel</code>
    with event name <code>my-event</code>.
</p>
</body>
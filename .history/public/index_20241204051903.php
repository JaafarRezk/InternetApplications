<!DOCTYPE html>
<head>
  <title>Pusher Test</title>
  <script src="https://js.pusher.com/8.2.0/pusher.min.js"></script>
  <script>

    // Enable pusher logging - don't include this in production
    Pusher.logToConsole = true;

    var pusher = new Pusher('64bfa1c78333446b2e60', {
      forceTLS: true,
      cluster: 'us2',
      authEndpoint: '/broadcasting/auth', 
    });

    const userId = 'current-user-id'; // استبدل بـ معرف المستخدم

    var channel = pusher.subscribe('private-user-${userId}');
    channel.bind('group-invitation', function(data) {
      alert(`You have been invited to join the group: ${data.groupName} by ${data.inviterName}`);
      console.log('Invitation data:', data);
    
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
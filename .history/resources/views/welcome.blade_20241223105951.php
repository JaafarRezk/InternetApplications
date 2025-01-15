<!DOCTYPE html>
<head>
  <title>Pusher Test</title>
  <script src="https://js.pusher.com/8.2.0/pusher.min.js"></script>
  <script>

    // Enable pusher logging - don't include this in production
    Pusher.logToConsole = true;

    var pusher = new Pusher('2bd542e901b01838f4a7', {
      cluster: 'eu'
    });

    var channel = pusher.subscribe(`private-user-${invitedUserId}`);
    channel.bind('GroupInvitationEvent', function(data) {
      alert(JSON.stringify(data));
      console.log(data.message);
    });

    channel.bind("GroupInvitationEvent", (data) => {
      console.log("Received notification: ", data.message);
      alert(JSON.stringify(data.message));
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
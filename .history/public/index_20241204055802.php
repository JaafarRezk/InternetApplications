<!DOCTYPE html>
<head>
    <title>Pusher Test</title>
    <script src="https://js.pusher.com/8.2.0/pusher.min.js"></script>
    <script>
        // Enable pusher logging - don't include this in production
        Pusher.logToConsole = true;

        // جلب معرف المستخدم من الجلسة أو الـ localStorage بعد تسجيل الدخول
        const userId = localStorage.getItem('user_id'); // استبدل بهذا القيمة الفعلية

        if (!userId) {
            console.log('User not logged in');
            return;
        }

        var pusher = new Pusher('64bfa1c78333446b2e60', {
            forceTLS: true,
            cluster: 'us2',
            authEndpoint: '/broadcasting/auth', // تأكد من أنك قد أعددت هذه النقطة في Laravel
        });

        // الاشتراك في قناة خاصة باستخدام معرف المستخدم
        var channel = pusher.subscribe(`private-user-${userId}`);
        
        // استقبال الحدث "group-invitation" عند وصول دعوة للمجموعة
        channel.bind('group-invitation', function(data) {
            alert(`You have been invited to join the group: ${data.groupName} by ${data.inviterName}`);
            console.log('Invitation data:', data);
        });
    </script>
</head>
<body>
    <h1>Pusher Test</h1>
    <p>
        Try publishing an event to channel <code>private-user-${userId}</code>
        with event name <code>group-invitation</code>.
    </p>
</body>

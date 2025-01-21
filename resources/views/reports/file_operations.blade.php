<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>File Operations Report</title>
</head>
<body>
    <h1>File Operations Report</h1>
    <table border="1">
        <thead>
            <tr>
                <th>Date</th>
                <th>Operation</th>
                <th>File ID</th>
                <th>User ID</th>
                <th>Status</th>
                <th>Group ID</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($logs as $log)
                <tr>
                    <td>{{ $log->date }}</td>
                    <td>{{ $log->operation }}</td>
                    <td>{{ $log->file_id }}</td>
                    <td>{{ $log->user_id }}</td>
                    <td>{{ $log->status }}</td>
                    <td>{{ $log->group_id }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>

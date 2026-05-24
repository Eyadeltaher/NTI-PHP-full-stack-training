<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>All Customers</title>
    <style>
        table { width: 85%; margin: 20px auto; border-collapse: collapse; font-family: Arial, sans-serif; }
        th, td { border: 1px solid #dddddd; text-align: left; padding: 10px; }
        th { background-color: #4CAF50; color: white; }
        tr:nth-child(even) { background-color: #f2f2f2; }
        h1 { text-align: center; color: #4CAF50; }
    </style>
</head>
<body>

    <h1>All Customers Directory</h1>

    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Customer Name</th>
                <th>Email</th>
                <th>Phone</th>
                <th>City</th>
            </tr>
        </thead>
        <tbody>
            @foreach($customers as $customer)
                <tr>
                    <td>{{ $customer->id }}</td>
                    <td>{{ $customer->customerName }}</td>
                    <td>{{ $customer->customerEmail }}</td>
                    <td>{{ $customer->customerPhone }}</td>
                    <td>{{ $customer->customerCity }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

</body>
</html>
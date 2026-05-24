<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Cairo Customers</title>
    <style>
        table { width: 70%; margin: 20px auto; border-collapse: collapse; font-family: Arial, sans-serif; }
        th, td { border: 1px solid #dddddd; text-align: left; padding: 10px; }
        th { background-color: #008CBA; color: white; }
        tr:nth-child(even) { background-color: #f2f2f2; }
        h1 { text-align: center; color: #008CBA; }
    </style>
</head>
<body>

    <h1>Customers Living in Cairo</h1>

    <table>
        <thead>
            <tr>
                <th>Customer Name</th>
                <th>Email</th>
                <th>City</th>
            </tr>
        </thead>
        <tbody>
            @foreach($customers as $customer)
                <tr>
                    <td>{{ $customer->customerName }}</td>
                    <td>{{ $customer->customerEmail }}</td>
                    <td><strong style="color: #008CBA;">{{ $customer->customerCity }}</strong></td>
                </tr>
            @endforeach
        </tbody>
    </table>

</body>
</html>
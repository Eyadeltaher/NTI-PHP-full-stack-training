<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Expensive Products</title>
    <style>
        table { width: 60%; margin: 20px auto; border-collapse: collapse; font-family: Arial, sans-serif; }
        th, td { border: 1px solid #dddddd; text-align: left; padding: 12px; }
        th { background-color: #ff6b6b; color: white; }
        tr:nth-child(even) { background-color: #f9f9f9; }
        h1 { text-align: center; color: #ff6b6b; }
    </style>
</head>
<body>

    <h1>Premium Products (Above $100)</h1>

    <table>
        <thead>
            <tr>
                <th>Product Name</th>
                <th>Price</th>
            </tr>
        </thead>
        <tbody>
            @foreach($products as $product)
                <tr>
                    <td>{{ $product->name }}</td>
                    <td>${{ number_format($product->price, 2) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

</body>
</html>
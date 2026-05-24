<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>All Products</title>
    <style>
        table { width: 80%; margin: 20px auto; border-collapse: collapse; font-family: Arial, sans-serif; }
        th, td { border: 1px solid #dddddd; text-align: left; padding: 12px; }
        th { background-color: #f2f2f2; }
        tr:nth-child(even) { background-color: #f9f9f9; }
        h1 { text-align: center; color: #333; }
    </style>
</head>
<body>

    <h1>All Products List</h1>

    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Product Name</th>
                <th>Description</th>
                <th>Price</th>
            </tr>
        </thead>
        <tbody>
            @foreach($products as $product)
                <tr>
                    <td>{{ $product->id }}</td>
                    <td>{{ $product->name }}</td>
                    <td>{{ $product->desc }}</td>
                    <td>${{ number_format($product->price, 2) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

</body>
</html>
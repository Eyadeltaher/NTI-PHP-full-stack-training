<?php
require('db_connection.php');

$query = "SELECT * FROM MOCK_DATA";
$result = mysqli_query($connection, $query);
$data = mysqli_fetch_all($result, MYSQLI_ASSOC);
$counter = 0;
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Customer Data</title>
    <style>
        table {
            width: 100%;
            border-collapse: collapse;
        }

        th,
        td {
            padding: 12px;
            border: 1px solid #ddd;
            text-align: left;
        }
    </style>
</head>

<body>

    <h2>Mock Data Records</h2>
    <a href="add_customer.php?" style="color: yellow; text-decoration: none; font-weight: bold; margin: 10px; background-color:green">Add</a>

    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>First Name</th>
                <th>Last Name</th>
                <th>Email</th>
                <th>Gender</th>
                <th>Salary</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($data as $row): ?>
                <tr>
                    <td><?php echo $counter++; ?></td>
                    <td><?php echo $row['first_name']; ?></td>
                    <td><?php echo $row['last_name']; ?></td>
                    <td><?php echo $row['email']; ?></td>
                    <td><?php echo $row['gender']; ?></td>
                    <td>$<?php echo number_format($row['salary']); ?></td>
                    <td>
                        <a href="edit.php?id=<?php echo $row['id']; ?>" style="color: green; text-decoration: none; font-weight: bold; margin-right: 10px;">Update</a>
                        <a href="delete.php?id=<?php echo $row['id']; ?>"
                            style="color: red; text-decoration: none; font-weight: bold;">
                            Delete
                        </a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

</body>

</html>
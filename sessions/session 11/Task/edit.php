<?php
require('db_connection.php');

$id = $_GET['id'];
$query = "SELECT * FROM MOCK_DATA WHERE id = $id";
$result = mysqli_query($connection, $query);
if (mysqli_num_rows($result) == 0) {
    header("location:index.php");
    exit();
}
$row = mysqli_fetch_assoc($result);
?>

<!DOCTYPE html>
<html>

<head>
    <title>Edit Record</title>
    <style>
        body {
            font-family: sans-serif;
            padding: 20px;
        }

        .form-box {
            max-width: 400px;
            border: 1px solid #ccc;
            padding: 20px;
            border-radius: 5px;
        }

        input {
            width: 100%;
            margin-bottom: 10px;
            padding: 8px;
            box-sizing: border-box;
        }

        input:invalid {
            border: 2px solid #dc3545;
        }

        button {
            background: blue;
            color: white;
            border: none;
            padding: 10px;
            cursor: pointer;
            width: 100%;
        }

        button:hover {
            background: darkblue;
        }
    </style>
</head>

<body>

    <div class="form-box">
        <h2>Edit Customer</h2>
        <form action="update.php" method="POST">
            <input type="hidden" name="id" value="<?php echo htmlspecialchars($row['id']); ?>">

            <label>First Name</label>
            <input type="text" name="first_name" value="<?php echo htmlspecialchars($row['first_name']); ?>" required minlength="2" maxlength="50" pattern="[a-zA-Z\s]+" title="First name must contain only letters and spaces">

            <label>Last Name</label>
            <input type="text" name="last_name" value="<?php echo htmlspecialchars($row['last_name']); ?>" required minlength="2" maxlength="50" pattern="[a-zA-Z\s]+" title="Last name must contain only letters and spaces">

            <label>Email</label>
            <input type="email" name="email" value="<?php echo htmlspecialchars($row['email']); ?>" required maxlength="100">

            <label>Salary</label>
            <input type="number" name="salary" value="<?php echo htmlspecialchars($row['salary']); ?>" required min="0" step="0.01">

            <button type="submit">Update Record</button>
        </form>
    </div>

</body>

</html>
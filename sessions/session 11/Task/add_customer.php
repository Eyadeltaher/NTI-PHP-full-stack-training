<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Add New Record</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            padding: 50px;
        }

        .form-container {
            background: white;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            max-width: 400px;
            margin: auto;
        }

        h2 {
            text-align: center;
            color: #333;
        }

        .form-group {
            margin-bottom: 15px;
        }

        label {
            display: block;
            margin-bottom: 5px;
            color: #666;
        }

        input[type="text"],
        input[type="email"],
        input[type="number"],
        select {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
            box-sizing: border-box;
        }

        input:invalid {
            border-color: #dc3545;
        }

        button {
            width: 100%;
            padding: 10px;
            background: #28a745;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
        }

        button:hover {
            background: #218838;
        }
    </style>
</head>

<body>

    <div class="form-container">
        <h2>Add New Customer</h2>
        <form action="insert.php" method="POST">
            <div class="form-group">
                <label>ID</label>
                <input type="number" name="id" required min="1">
            </div>
            <div class="form-group">
                <label>First Name</label>
                <input type="text" name="first_name" required minlength="2" maxlength="50" pattern="[a-zA-Z\s]+" title="First name must contain only letters and spaces">
            </div>
            <div class="form-group">
                <label>Last Name</label>
                <input type="text" name="last_name" required minlength="2" maxlength="50" pattern="[a-zA-Z\s]+" title="Last name must contain only letters and spaces">
            </div>
            <div class="form-group">
                <label>Email</label>
                <input type="email" name="email" required maxlength="100">
            </div>
            <div class="form-group">
                <label>Gender</label>
                <select name="gender">
                    <option value="Male">Male</option>
                    <option value="Female">Female</option>
                    <option value="Agender">Agender</option>
                    <option value="Bigender">Bigender</option>
                </select>
            </div>
            <div class="form-group">
                <label>Salary</label>
                <input type="number" name="salary" required min="0" step="0.01">
            </div>
            <button type="submit">Save Record</button>
        </form>
    </div>

</body>

</html>
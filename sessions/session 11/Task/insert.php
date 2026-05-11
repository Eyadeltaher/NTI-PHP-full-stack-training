<?php
require('db_connection.php');

$errors = array();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    // Validate and sanitize ID
    if (empty($_POST['id'])) {
        $errors[] = "ID is required";
    } else {
        $id = $_POST['id'];
        if (!is_numeric($id) || $id <= 0) {
            $errors[] = "ID must be a positive number";
        }
    }

    // Validate and sanitize First Name
    if (empty($_POST['first_name'])) {
        $errors[] = "First Name is required";
    } else {
        $first_name = trim($_POST['first_name']);
        if (strlen($first_name) < 2) {
            $errors[] = "First Name must be at least 2 characters long";
        } elseif (strlen($first_name) > 50) {
            $errors[] = "First Name must not exceed 50 characters";
        } elseif (!preg_match("/^[a-zA-Z\s]+$/", $first_name)) {
            $errors[] = "First Name can only contain letters and spaces";
        }
        $first_name = mysqli_real_escape_string($connection, $first_name);
    }

    // Validate and sanitize Last Name
    if (empty($_POST['last_name'])) {
        $errors[] = "Last Name is required";
    } else {
        $last_name = trim($_POST['last_name']);
        if (strlen($last_name) < 2) {
            $errors[] = "Last Name must be at least 2 characters long";
        } elseif (strlen($last_name) > 50) {
            $errors[] = "Last Name must not exceed 50 characters";
        } elseif (!preg_match("/^[a-zA-Z\s]+$/", $last_name)) {
            $errors[] = "Last Name can only contain letters and spaces";
        }
        $last_name = mysqli_real_escape_string($connection, $last_name);
    }

    // Validate and sanitize Email
    if (empty($_POST['email'])) {
        $errors[] = "Email is required";
    } else {
        $email = trim($_POST['email']);
        if (strlen($email) > 100) {
            $errors[] = "Email must not exceed 100 characters";
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors[] = "Invalid email format";
        }
        $email = mysqli_real_escape_string($connection, $email);
    }

    // Validate and sanitize Gender
    $gender = isset($_POST['gender']) ? mysqli_real_escape_string($connection, $_POST['gender']) : "Male";
    $valid_genders = array("Male", "Female", "Agender", "Bigender");
    if (!in_array($gender, $valid_genders)) {
        $errors[] = "Invalid gender selected";
    }

    // Validate and sanitize Salary
    if (empty($_POST['salary'])) {
        $errors[] = "Salary is required";
    } else {
        $salary = $_POST['salary'];
        if (!is_numeric($salary) || $salary < 0) {
            $errors[] = "Salary must be a positive number";
        }
        $salary = mysqli_real_escape_string($connection, $salary);
    }

    // If no errors, insert the record
    if (empty($errors)) {
        $query = "INSERT INTO MOCK_DATA (id, first_name, last_name, email, gender, salary) 
                  VALUES ($id, '$first_name', '$last_name', '$email', '$gender', $salary)";

        if (mysqli_query($connection, $query)) {
            header("Location: index.php?status=inserted");
            exit();
        } else {
            $errors[] = "Error inserting record: " . mysqli_error($connection);
        }
    }

    // If there are errors, show them
    if (!empty($errors)) {
        echo "<div style='background-color: #f8d7da; padding: 15px; border: 1px solid #f5c6cb; border-radius: 4px; margin-bottom: 20px;'>";
        echo "<h3 style='color: #721c24; margin-top: 0;'>Validation Errors:</h3>";
        echo "<ul style='color: #721c24; margin: 0;'>";
        foreach ($errors as $error) {
            echo "<li>" . htmlspecialchars($error) . "</li>";
        }
        echo "</ul>";
        echo "</div>";
        echo "<a href='add_customer.php' style='padding: 10px 20px; background: #007bff; color: white; text-decoration: none; border-radius: 4px;'>Go Back</a>";
    }
}
?>

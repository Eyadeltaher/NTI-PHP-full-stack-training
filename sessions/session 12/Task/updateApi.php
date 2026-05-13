<?php
require('db_connection.php');
header('Content-Type: application/x-www-form-urlencoded');

// Manually parse PUT data
$putData = [];
parse_str(file_get_contents("php://input"), $_PUT);

$errors = [];

if ($_SERVER["REQUEST_METHOD"] == "PUT") {

    if (empty($_PUT['id'])) {
        $errors[] = "ID is required";
    } else {
        $id = $_PUT['id'];
        if (!is_numeric($id) || $id <= 0) {
            $errors[] = "ID must be a positive number";
        }
    }

    if (empty($_PUT['first_name'])) {
        $errors[] = "First Name is required";
    } else {
        $first_name = trim($_PUT['first_name']);
        if (strlen($first_name) < 2) {
            $errors[] = "First Name must be at least 2 characters long";
        } elseif (strlen($first_name) > 50) {
            $errors[] = "First Name must not exceed 50 characters";
        } elseif (!preg_match("/^[a-zA-Z\s]+$/", $first_name)) {
            $errors[] = "First Name can only contain letters and spaces";
        }
        $first_name = mysqli_real_escape_string($connection, $first_name);
    }

    if (empty($_PUT['last_name'])) {
        $errors[] = "Last Name is required";
    } else {
        $last_name = trim($_PUT['last_name']);
        if (strlen($last_name) < 2) {
            $errors[] = "Last Name must be at least 2 characters long";
        } elseif (strlen($last_name) > 50) {
            $errors[] = "Last Name must not exceed 50 characters";
        } elseif (!preg_match("/^[a-zA-Z\s]+$/", $last_name)) {
            $errors[] = "Last Name can only contain letters and spaces";
        }
        $last_name = mysqli_real_escape_string($connection, $last_name);
    }

    if (empty($_PUT['email'])) {
        $errors[] = "Email is required";
    } else {
        $email = trim($_PUT['email']);
        if (strlen($email) > 100) {
            $errors[] = "Email must not exceed 100 characters";
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors[] = "Invalid email format";
        }
        $email = mysqli_real_escape_string($connection, $email);
    }

    if (empty($_PUT['salary'])) {
        $errors[] = "Salary is required";
    } else {
        $salary = $_PUT['salary'];
        if (!is_numeric($salary) || $salary < 0) {
            $errors[] = "Salary must be a positive number";
        }
        $salary = mysqli_real_escape_string($connection, $salary);
    }

    if (empty($errors)) {
        $query = "UPDATE MOCK_DATA SET 
                  first_name = '$first_name', 
                  last_name = '$last_name', 
                  email = '$email', 
                  salary = '$salary' 
                  WHERE id = $id";

        if (mysqli_query($connection, $query)) {
            http_response_code(200);
            echo json_encode(['message' => 'Record updated successfully']);
        } else {
            http_response_code(500);
            $errors[] = "Error updating record: " . mysqli_error($connection);
        }
    } else {
        http_response_code(400);
        echo json_encode(['errors' => $errors]);
    }

} else {
    http_response_code(405);
    echo json_encode(['message' => 'Invalid request method']);
}

?>
<?php
require('db_connection.php');
header('Content-Type: application/json');

$errors = [];

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    if (empty($_POST['id'])) {
        $errors[] = "ID is required";
    } else {
        $id = $_POST['id'];
        if (!is_numeric($id) || $id <= 0) {
            $errors[] = "ID must be a positive number";
        }
    }

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

    $gender = isset($_POST['gender']) ? mysqli_real_escape_string($connection, $_POST['gender']) : "Male";
    $valid_genders = array("Male", "Female", "Agender", "Bigender");
    if (!in_array($gender, $valid_genders)) {
        $errors[] = "Invalid gender selected";
    }

    if (empty($_POST['salary'])) {
        $errors[] = "Salary is required";
    } else {
        $salary = $_POST['salary'];
        if (!is_numeric($salary) || $salary < 0) {
            $errors[] = "Salary must be a positive number";
        }
        $salary = mysqli_real_escape_string($connection, $salary);
    }

    if (empty($errors)) {
        $query = "INSERT INTO MOCK_DATA (id, first_name, last_name, email, gender, salary) 
                  VALUES ($id, '$first_name', '$last_name', '$email', '$gender', $salary)";

        if (mysqli_query($connection, $query)) {
            http_response_code(201);
            echo json_encode(['message' => 'Record inserted successfully']);
        } else {
            http_response_code(500);
            $errors[] = "Error inserting record: " . mysqli_error($connection);
        }
    } else {
        http_response_code(400);
        echo json_encode(['errors' => $errors]);
    }
} else {
    http_response_code(405);
    echo json_encode(['message' => 'Invalid request method']);
}

// if in postman i will take the data inserted in json format i need to decode it before inserting it into the database
// $input = json_decode(file_get_contents('php://input'), true);

?>
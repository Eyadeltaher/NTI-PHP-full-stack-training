<?php
require('db_connection.php');
header('Content-Type: application/json');

if (!isset($_GET['id'])) {
    http_response_code(400);
    echo json_encode(['message' => 'id is required']);
    exit();
}

$id = $_GET['id'];

if (!is_numeric($id)) {
    http_response_code(400);
    echo json_encode(['message' => 'id must be a number']);
    exit();
}

$query = "SELECT * FROM MOCK_DATA WHERE id = $id";
$result = mysqli_query($connection, $query);

if (mysqli_num_rows($result) == 0) {
    http_response_code(404);
    echo json_encode(['message' => 'no record found with this id']);
    exit();
}

$data = mysqli_fetch_assoc($result);
echo json_encode($data);



?>
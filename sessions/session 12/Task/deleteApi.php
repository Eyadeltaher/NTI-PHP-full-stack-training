<?php
require('db_connection.php');
header('Content-Type: application/json');


if (!isset($_GET['id'])) {
    http_response_code(400);
    echo json_encode(['errors' => ['ID parameter is required.']]);
    exit();
}


$id = mysqli_real_escape_string($connection, $_GET['id']);
$query = "DELETE FROM MOCK_DATA WHERE id = $id";

if (mysqli_query($connection, $query)) {
    http_response_code(200);
    echo json_encode(['message' => 'Record deleted successfully']);
    exit();
} else {
    http_response_code(500);
    echo json_encode(['errors' => ['Error deleting record: ' . mysqli_error($connection)]]);
}

?>
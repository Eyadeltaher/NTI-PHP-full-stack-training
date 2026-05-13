<?php
require('db_connection.php');

header('Content-Type: application/json');
$query = "SELECT * FROM MOCK_DATA";
$result = mysqli_query($connection, $query);
$data = mysqli_fetch_all($result, MYSQLI_ASSOC);

echo json_encode($data);

?>
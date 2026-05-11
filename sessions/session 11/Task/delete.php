<?php
require('db_connection.php');

if (isset($_GET['id'])) {
    $id = mysqli_real_escape_string($connection, $_GET['id']);

    $query = "DELETE FROM MOCK_DATA WHERE id = $id";

    if (mysqli_query($connection, $query)) {
        header("Location: index.php?message=deleted");
        exit();
    } else {
        echo "Error deleting record: " . mysqli_error($connection);
    }
} else {
    echo "No ID provided.";
}
?>
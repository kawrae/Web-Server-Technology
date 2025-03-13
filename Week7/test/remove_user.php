<?php
include 'config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $user_id = $_POST['user_id'];

    // Remove user from the database
    $sql = "DELETE FROM user_form WHERE id='$user_id'";
    if ($conn->query($sql) === TRUE) {
        echo "Record deleted successfully";
        header('location:main.php');
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
}
?>

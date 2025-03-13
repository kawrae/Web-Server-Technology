<?php
include 'config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $user_id = $_POST['user_id'];
    $new_name = $_POST['new_name'];
    $new_email = $_POST['new_email'];

    // Update user in the database
    $sql = "UPDATE user_form SET name='$new_name', email='$new_email' WHERE id='$user_id'";
    if ($conn->query($sql) === TRUE) {
        echo "Record updated successfully";
        header('location:main.php');
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
}
?>

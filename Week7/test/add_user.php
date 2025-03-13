<?php
include 'config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST['name'];
    $email = $_POST['email'];

    // Insert new user into the database
    $sql = "INSERT INTO user_form (name, email) VALUES ('$name', '$email')";
    if ($conn->query($sql) === TRUE) {
        echo "New record created successfully";
        header('location:main.php');
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
}
?>

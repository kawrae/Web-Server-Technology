<?php
include 'config.php';
session_start();

if (isset($_POST['submit'])) {
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $pass = mysqli_real_escape_string($conn, $_POST['password']);

    $select = mysqli_query($conn, "SELECT * FROM user_form WHERE email = '$email'") or die('query failed');

    if (mysqli_num_rows($select) > 0) {
        $row = mysqli_fetch_assoc($select);

        if (password_verify($pass, $row['password'])) {
            $_SESSION['user_id'] = $row['id'];
            echo "<script>alert('Login successful!'); window.location.href='profile.php';</script>";
        } else {
            echo "<script>alert('Incorrect email or password!');</script>";
        }
    } else {
        echo "<script>alert('Incorrect email or password!');</script>";
    }
}
?>



<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="style.css">
</head>

<body>

    <div class="form-container">
        <form action="login.php" method="POST" enctype="multipart/form-data">
            <h3>Login Now</h3>
            <input type="email" name="email" placeholder="Enter email" class="box" required>
            <input type="password" name="password" placeholder="Enter password" class="box" required>
            <input type="submit" name="submit" value="Login Now" class="btn">
            <p>Don't have an account? <a href="register.php">Register now</a></p>
        </form>
    </div>

</body>

</html>
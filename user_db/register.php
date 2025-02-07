<?php
include 'config.php';

if (isset($_POST['submit'])) {
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $pass = mysqli_real_escape_string($conn, $_POST['password']);
    $confirm_pass = mysqli_real_escape_string($conn, $_POST['confirm_password']);
    $image = $_FILES['image']['name'];
    $image_size = $_FILES['image']['size'];
    $image_tmp_name = $_FILES['image']['tmp_name'];
    $image_folder = 'uploaded_img/' . $image;

    $select = mysqli_query($conn, "SELECT * FROM user_form WHERE email = '$email'") or die('query failed');

    if (mysqli_num_rows($select) > 0) {
        echo "<script>alert('User already exists!');</script>";
    } elseif ($pass !== $confirm_pass) {
        echo "<script>alert('Passwords do not match!');</script>";
    } else {
        $hashed_password = password_hash($pass, PASSWORD_DEFAULT);

        $insert = mysqli_query($conn, "INSERT INTO user_form(name, email, password, image) VALUES('$name', '$email', '$hashed_password', '$image')") or die('query failed');

        if ($insert) {
            move_uploaded_file($image_tmp_name, $image_folder);
            echo "<script>alert('Registered successfully!'); window.location.href='login.php';</script>";
        } else {
            echo "<script>alert('Registration failed!');</script>";
        }
    }
}
?>



<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>register</title>

    <!-- custom css file link  -->
    <link rel="stylesheet" href="style.css">

</head>

<body>

    <div class="form-container">

        <form action="register.php" method="POST" enctype="multipart/form-data">
            <h3>Register Now</h3>
            <input type="text" name="name" placeholder="Enter your name" class="box" required>
            <input type="email" name="email" placeholder="Enter your email" class="box" required>
            <input type="password" name="password" placeholder="Enter your password" class="box" required>
            <input type="password" name="confirm_password" placeholder="Confirm your password" class="box" required>
            <input type="file" name="image" class="box" required>
            <input type="submit" name="submit" value="Register" class="btn">
            <p>Already have an account? <a href="login.php">Login now</a></p>
        </form>


    </div>

</body>

</html>
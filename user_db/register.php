<?php
session_start(); // Start session to track form state

require 'phpmailer/PHPMailer.php';
require 'phpmailer/SMTP.php';
require 'phpmailer/Exception.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

include 'config.php';

$message = [];

if (!isset($_POST['submit']) && !isset($_POST['check'])) {
    unset($_SESSION['show_otp']);
    unset($_SESSION['email']);
}

if(isset($_POST['submit'])) {
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $pass = mysqli_real_escape_string($conn, $_POST['password']);
    $cpass = mysqli_real_escape_string($conn, $_POST['cpassword']);

    $hashed_pass = password_hash($pass, PASSWORD_DEFAULT);

    $image = $_FILES['image']['name'];
    $image_size = $_FILES['image']['size'];
    $image_tmp_name = $_FILES['image']['tmp_name'];
    $image_folder = 'uploaded_img/'.$image;
    $code = rand(100000, 999999); // 6-digit OTP

    $select = mysqli_query($conn, "SELECT * FROM `user_temp` WHERE email = '$email'") or die('Query failed');

    if(mysqli_num_rows($select) > 0) {
        $message[] = 'User already exists! Complete OTP verification.';
        $_SESSION['show_otp'] = true;
    } else {
        if($pass != $cpass) {
            $message[] = 'Confirm password does not match!';
        } elseif($image_size > 2000000) {
            $message[] = 'Image size is too large!';
        } else {
            // Temporarily store user data before full registration
            $insert = mysqli_query($conn, "INSERT INTO `user_temp`(name, email, password, image, code) 
            VALUES ('$name', '$email', '$hashed_pass', '$image', '$code')") or die('Query failed');
            
            if($insert) {
                move_uploaded_file($image_tmp_name, $image_folder);

                // Email Verification
                $mail = new PHPMailer(true);

                try {
                    $mail->isSMTP();
                    $mail->Host = 'smtp.gmail.com';
                    $mail->SMTPAuth = true;
                    $mail->Username = 'yergransphatrhymes@gmail.com';
                    $mail->Password = 'piqs zaua khix gbdx'; 
                    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                    $mail->Port = 587;

                    // Email Content
                    $mail->setFrom('yergransphatrhymes@gmail.com', 'Admin');
                    $mail->addAddress($email);
                    $mail->Subject = "Email Verification Code";
                    $mail->Body = "Your verification code is: $code";

                    if ($mail->send()) {
                        $_SESSION['show_otp'] = true;
                        $_SESSION['email'] = $email;
                        $message[] = 'Please check your email and enter the OTP to verify!';
                    } else {
                        $message[] = 'Failed while sending code!';
                    }
                } catch (Exception $e) {
                    $message[] = "Mailer Error: {$mail->ErrorInfo}";
                }
            } else {
                $message[] = 'Registration failed!';
            }
        }
    }
}

// OTP Verification
if(isset($_POST['check'])) {
    $OTP = mysqli_real_escape_string($conn, $_POST['OTP']);
    $email = $_SESSION['email'];
    
    $check_code = "SELECT * FROM `user_temp` WHERE email = '$email' AND code = '$OTP'";
    $code_res = mysqli_query($conn, $check_code);
    
    if(mysqli_num_rows($code_res) > 0) {
        $fetch_data = mysqli_fetch_assoc($code_res);
        $name = $fetch_data['name'];
        $hashed_pass = $fetch_data['password'];
        $image = $fetch_data['image'];

        // Move user to final users table
        $final_insert = mysqli_query($conn, "INSERT INTO `user_form` (name, email, password, image) 
            VALUES ('$name', '$email', '$hashed_pass', '$image')") or die('Query failed');

        // Delete the temporary record
        mysqli_query($conn, "DELETE FROM `user_temp` WHERE email = '$email'");

        unset($_SESSION['show_otp']); 
        unset($_SESSION['email']);

        $message[] = 'OTP is correct! Registration complete!';
        header('location: login.php');
        exit();
    } else {
        $message[] = "Wrong OTP, try again!";
        $_SESSION['show_otp'] = true; 
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<div class="form-container">
    <!-- Registration Form -->
    <form id="register-form" action="" method="post" enctype="multipart/form-data" 
        style="<?= !isset($_SESSION['show_otp']) ? 'display:block;' : 'display:none;'; ?>">
        <h3>Step 1: Register Now</h3>
        <?php
        if (!empty($message)) {
            foreach ($message as $msg) {
                echo '<div class="message">'.$msg.'</div>';
            }
        }
        ?>
        <input type="text" name="name" placeholder="Enter username" class="box" required>
        <input type="email" name="email" placeholder="Enter email" class="box" required>
        <input type="password" name="password" placeholder="Enter password" class="box" required>
        <input type="password" name="cpassword" placeholder="Confirm password" class="box" required>
        <input type="file" name="image" class="box" accept="image/jpg, image/jpeg, image/png">
        <input type="submit" name="submit" value="Submit now to receive email" class="btn">
    </form>

    <!-- OTP Verification Form -->
    <form id="otp-form" action="" method="post" 
        style="<?= isset($_SESSION['show_otp']) ? 'display:block;' : 'display:none;'; ?>">
        <h3>Step 2: Enter OTP to Verify Your Email</h3>
        <?php
        if (!empty($message)) {
            foreach ($message as $msg) {
                echo '<div class="message">'.$msg.'</div>';
            }
        }
        ?>
        <input type="text" name="OTP" placeholder="Enter OTP" class="box" required>
        <input type="submit" name="check" value="Register now" class="btn">
        <p>Already have an account? <a href="login.php">Login now</a></p>
    </form>
</div>
</body>
</html>

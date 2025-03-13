<?php

$show_reg_form = true;  // Default: show registration form
$show_otp_form = false; // Default: hide OTP form
$message = [];
session_start();
include 'config.php';

if(isset($_POST['submit'])){

    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $pass = mysqli_real_escape_string($conn, md5($_POST['password']));
    $cpass = mysqli_real_escape_string($conn, md5($_POST['cpassword']));
    $image = $_FILES['image']['name'];
    $image_size = $_FILES['image']['size'];
    $image_tmp_name = $_FILES['image']['tmp_name'];
    $image_folder = 'uploaded_img/'.$image;
    $code = rand(999999, 111111);
    $_SESSION['name'] = $name;
    $_SESSION['email'] = $email;
    $_SESSION['pass'] = $pass;
    $_SESSION['image'] = $image;
    $_SESSION['image_size'] = $image_size;
    $_SESSION['image_tmp_name'] = $image_tmp_name;
    $_SESSION['image_folder'] = $image_folder;
    $_SESSION['code'] = $code;

    $select = mysqli_query($conn, "SELECT * FROM `user_form` WHERE email = '$email'") or die('query failed');

    if(mysqli_num_rows($select) > 0){
        $message[] = 'user already exists!';
    }else{
        if($pass != $cpass){
            $message[] = 'confirm password not matched!';
        }elseif($image_size > 2000000){
            $message[] = 'image size is too large!';
        }else{
                       //handel Email verification
                $subject = "Email Verification Code";
                $message1 = "Your verification code is $code";
                $sender = "From: quickhand2021@gmail.com";

                if(mail($email, $subject, $message1, $sender)){
                    $message[] = 'Now please check your email, enter the OTP to verify and complete your registration!';
                    $show_reg_form = false; // Hide registration form
                    $show_otp_form = true;  // Show OTP form

                }else{
                    $message[] = 'Failed while sending code!';
                }

        }
    }
}

// user click verification code submit button
if(isset($_POST['check'])){
    $OTP = mysqli_real_escape_string($conn, $_POST['OTP']);

    // $check_code = "SELECT * FROM `user_form` WHERE code = '$OTP'";
    // $code_res = mysqli_query($conn, $check_code);

    if($_SESSION['code'] == $OTP){
        
        $insert = mysqli_query($conn, "INSERT INTO `user_form`(name, email, password, image, code) 
VALUES('{$_SESSION['name']}', '{$_SESSION['email']}', '{$_SESSION['pass']}', '{$_SESSION['image']}', '{$_SESSION['code']}')") 
or die('Query failed: ' . mysqli_error($conn));

        if($insert){
            move_uploaded_file($_SESSION['image_tmp_name'], $_SESSION['image_folder'] );
                $message[] = 'registration Completed successfully!';

                //header('location:login.php');
                echo "<script>
                alert('Registration completed successfully! Redirecting to login...');
                window.location.href='login.php';
            </script>";
            exit();
        }else{
            $message[] = 'registration failed!';
        }     
    }else{
        $message[] = " This is a wrong OTP. $OTP, try again!";
        $show_reg_form = false; // Ensure registration form stays hidden
        $show_otp_form = true;  // Keep OTP form visible
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

    <form id="regForm" action="" method="post" enctype="multipart/form-data" style="display:<?php echo $show_reg_form ? 'block' : 'none'; ?>">
        <h3>Step 1: register now</h3>
        <?php
        if(isset($message)){
            foreach($message as $msg){
                echo '<div class="message">'.$msg.'</div>';
            }
        }
        ?>
        <input type="text" name="name" placeholder="enter username" class="box" required>
        <input type="email" name="email" placeholder="enter email" class="box" required>
        <input type="password" name="password" placeholder="enter password" class="box" required>
        <input type="password" name="cpassword" placeholder="confirm password" class="box" required>
        <input type="file" name="image" accept="image/jpg, image/jpeg, image/png">
        <input type="submit" name="submit" value="Submit now to receive email" class="btn">
        <p>already have an account? <a href="login.php">login now</a></p>
    </form>
   

    <form id="OTPform" action="" method="post" enctype="multipart/form-data" style="display:<?php echo $show_otp_form ? 'block' : 'none'; ?>">
        <h3>Step 2: Enter OTP to verify your email</h3>
        <?php
        if(isset($message)){
            foreach($message as $msg){
                echo '<div class="message">'.$msg.'</div>';
            }
        }
        ?>
        <input type="text" name="OTP" placeholder="enter OTP" class="box" required>
        <input type="submit" name="check" value="register now" class="btn">
        <p>already have an account? <a href="login.php">login now</a></p>
    </form>

</div>

</body>
</html>

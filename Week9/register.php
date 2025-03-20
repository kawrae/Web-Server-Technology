<?php

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

   $select = mysqli_query($conn, "SELECT * FROM `user_form` WHERE email = '$email' AND password = '$pass'") or die('query failed');

   if(mysqli_num_rows($select) > 0){
      $message[] = 'user already exist'; 
   }else{
      if($pass != $cpass){
         $message[] = 'confirm password not matched!';
      }elseif($image_size > 2000000){
         $message[] = 'image size is too large!';
      }else{
         $insert = mysqli_query($conn, "INSERT INTO `user_form`(name, email, password, image, code) VALUES('$name', '$email', '$pass', '$image', '$code')") or die('query failed');

         if($insert){
            move_uploaded_file($image_tmp_name, $image_folder);
            $subject = "Email Verification Code";
            $message1 = "Your verification code is $code";
            $sender = "From: ghadamkhair2023@gmail.com";
            if(mail($email, $subject, $message1, $sender)){
               $message[] = 'Now please check your email, enter the OTP to verify and complete your registration!';
            }else{
               $message[] = "Failed while sending code!";
            }

         }else{
            $message[] = 'registeration failed!';
         }
      }
   }
}

//if user click verification code submit button
if(isset($_POST['check'])){
$OTP=  mysqli_real_escape_string($conn, $_POST['OTP']);

$check_code = "SELECT * FROM user_form WHERE code = $OTP";
 $code_res = mysqli_query($conn, $check_code);

if(mysqli_num_rows($code_res) > 0){
   $message[] = 'OTP is correct!';
   header('location:login.php');

}else{

   $message[] = "wrong OTP";
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

   <form action="" method="post" enctype="multipart/form-data">
      <h3>Step 1: register now</h3>
 
      <input type="text" name="name" placeholder="enter username" class="box" required>
      <input type="email" name="email" placeholder="enter email" class="box" required>
      <input type="password" name="password" placeholder="enter password" class="box" required>
      <input type="password" name="cpassword" placeholder="confirm password" class="box" required>
      <input type="file" name="image" class="box" accept="image/jpg, image/jpeg, image/png">
      <input type="submit" name="submit" value="Submit now TO receive email" class="btn">
   </form>


   <form action="" method="post" enctype="multipart/form-data">
      <h3>Step 2: Enter OTP to verify your email</h3>
      <?php
      if(isset($message)){
         foreach($message as $message){
            echo '<div class="message">'.$message.'</div>';
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
<?php

include 'config.php';
session_start();
$user_id = $_SESSION['user_id'];

if (!isset($user_id)) {
   header('location:login.php');
}
;

if (isset($GET['logout'])) {
   unset($user_id);
   session_destroy();
   header('location:login.php');
}

?>


<!DOCTYPE html>
<html lang="en">

<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>home</title>

   <!-- custom css file link  -->
   <link rel="stylesheet" href="style.css">

</head>

<body>

   <div class="container">

      <div class="profile">
         <a href="update_profile.php" class="btn">update profile</a>
         <a href="logout.php" class="delete-btn">logout</a>
         <p>new <a href="login.php">login</a> or <a href="register.php">register</a></p>
      </div>

   </div>

</body>

</html>
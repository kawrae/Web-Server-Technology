<?php

$conn = mysqli_connect('localhost','root','','user_db') or die('connection failed');

require_once 'vendor/autoload.php';

$google_client = new Google_Client();

$google_client->setClientId('437117701977-niegsdua9oo0as6q5f49mqlqj04opbmh.apps.googleusercontent.com');

$google_client->setClientSecret('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');

$google_client->setRedirectUri('https://localhost/week6/test/bmi_calculator.php');

$google_client->addScope('email');

$google_client->addScope('profile');

$login_button = $google_client->createAuthUrl();
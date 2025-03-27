<?php

$conn = mysqli_connect('localhost','root','','user_db') or die('connection failed');

//Include Google Client Library for PHP autoload file
require_once 'vendor/autoload.php';

//Make object of Google API Client for call Google API
$google_client = new Google_Client();

//Set the OAuth 2.0 Client ID
$google_client->setClientId('552131264257-db2k40cbield70kl4lqvi1h5akhs18uj.apps.googleusercontent.com');

//Set the OAuth 2.0 Client Secret key
$google_client->setClientSecret('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');

//Set the OAuth 2.0 Redirect URI
$google_client->setRedirectUri('https://localhost/code/loginsystem/Controller.php');

// to get the email and profile
$google_client->addScope('email');
$google_client->addScope('profile');

$login_button = $google_client->createAuthUrl();

?>
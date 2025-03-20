<?php

$session_path = sys_get_temp_dir();

if (session_status() == PHP_SESSION_NONE) {
    session_save_path($session_path);
    session_start();
}

error_log("Session path: " . session_save_path());

$conn = mysqli_connect('localhost', 'root', '', 'user_db') or die('connection failed');

// Include Google Client Library for PHP autoload file
require_once 'vendor/autoload.php';

// Make object of Google API Client for calling Google API
$google_client = new Google_Client();

// Set the OAuth 2.0 Client ID
$google_client->setClientId('google-client-id');

// Set the OAuth 2.0 Client Secret key
$google_client->setClientSecret('client-secret-key');

// Set the OAuth 2.0 Redirect URI
$google_client->setRedirectUri('http://localhost/code/week9/profile.php');

// To get the email and profile 
$google_client->addScope('email');
$google_client->addScope('profile');

// Handle Google OAuth login response
if (isset($_GET['code'])) {
    $token = $google_client->fetchAccessTokenWithAuthCode($_GET['code']);

    if (!isset($token['error'])) {
        $google_client->setAccessToken($token['access_token']);
        $_SESSION['token'] = $token['access_token'];

        // Get user details from Google
        $oauth2 = new Google_Service_Oauth2($google_client);
        $user_info = $oauth2->userinfo->get();

        // Store user data in session
        $_SESSION['user_id'] = $user_info->id;
        $_SESSION['user_name'] = $user_info->name;
        $_SESSION['user_email'] = $user_info->email;
        $_SESSION['user_picture'] = $user_info->picture;

        // Debug: Print session before redirect
        echo "<pre>";
        print_r($_SESSION);
        echo "</pre>";
        exit();
        
        // Redirect to profile page
        header('Location: profile.php');
        exit();
    }
}

// Generate Google Login URL
$login_button = $google_client->createAuthUrl();

?>

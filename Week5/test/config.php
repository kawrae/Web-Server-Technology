<?php

//start session on web page
session_start();

//config.php

//Include Google Client Library for PHP autoload file
require_once 'vendor/autoload.php';

//Make object of Google API Client for call Google API
$google_client = new Google_Client();

//Set the OAuth 2.0 Client ID
$google_client->setClientId('710442901579-6eq4a2j7f2cdm6ikvgunos2i3f70bb67.apps.googleusercontent.com');

//Set the OAuth 2.0 Client Secret key
$google_client->setClientSecret('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');

//Set the OAuth 2.0 Redirect URI
$google_client->setRedirectUri('https://localhost/week5/test/');

// to get the email and profile 
$google_client->addScope('email');

$google_client->addScope('profile');

?>
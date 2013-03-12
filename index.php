<?php
session_start();
/* oauth library from - https://github.com/abraham/twitteroauth */
require("libraries/oauth/twitteroauth/twitteroauth.php");
require('config.php');

$_SESSION['userData']= array();
$userData= $_SESSION['userData'];

$connection = new TwitterOAuth($consumer_key, $consumer_secret);
$request_token = $connection->getRequestToken();

$userData['Twitter_Request_Token'] = $token = $request_token['oauth_token'];
$userData['Twitter_Request_Token_Secret'] = $request_token['oauth_token_secret'];

$authenticateUrl = $connection->getAuthorizeURL($token);

$_SESSION['userData']= $userData;

header("Location: $authenticateUrl");
exit;


?>
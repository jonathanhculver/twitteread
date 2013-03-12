<?php
session_start();
/* oauth library from - https://github.com/abraham/twitteroauth */
require("../libraries/oauth/twitteroauth/twitteroauth.php");
require('../config.php');
require('../api.php');

$userData= $_SESSION['userData'];

$connection= new TwitterOAuth($consumer_key, $consumer_secret, $userData['Twitter_Access_Token'], $userData['Twitter_Access_Token_Secret']);

print json_encode(postUser($_POST['screenName'], $connection));


?>
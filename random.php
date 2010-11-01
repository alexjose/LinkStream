<?php
session_start();
include 'lib/EpiCurl.php';
include 'lib/EpiOAuth.php';
include 'lib/EpiTwitter.php';
include 'lib/secret.php';
include 'inc/User.php';

$user_obj = new User();
$user_obj->oauth_token = $_SESSION['oauth_token'];
$user_obj->oauth_token_secret=$_SESSION['oauth_token_secret'];
$twitterObj = new EpiTwitter($consumer_key, $consumer_secret, $user_obj->oauth_token, $user_obj->oauth_token_secret);

$twitterInfo= $twitterObj->get_statusesFriends();
echo "<h1>Your friends are</h1><ul>";
foreach($twitterInfo as $friend) {
  echo "<li><img src=\"{$friend->profile_image_url}\" hspace=\"4\">{$friend->screen_name}</li>";
}
echo "</ul>";
?>

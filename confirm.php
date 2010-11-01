<?php
session_start();
include 'lib/EpiCurl.php';
include 'lib/EpiOAuth.php';
include 'lib/EpiTwitter.php';
include 'lib/secret.php';
include 'inc/User.php';

$twitterObj = new EpiTwitter($consumer_key, $consumer_secret);
$newUser = new User();
if (!isset($_SESSION['signed'])) {
    $twitterObj->setToken($_GET['oauth_token']);
    $token = $twitterObj->getAccessToken();
    $twitterObj->setToken($token->oauth_token, $token->oauth_token_secret);

//var_dump($token);

$newUser->id = $token->user_id;
$newUser->handle = $token->screen_name;
$newUser->oauth_token = $token->oauth_token;
$newUser->oauth_token_secret = $token->oauth_token_secret;
$newUser->signin_User();
header("Location: index.php");
}
//else{
//    $newUser->id = $_SESSION['uid'];
//    $newUser->oauth_token=$_SESSION['oauth_token'];
//    $newUser->oauth_token_secret=$_SESSION['oauth_token_secret'];
//}
////var_dump($token);
//// save to cookies
////setcookie('oauth_token', $token->oauth_token);
////setcookie('oauth_token_secret', $token->oauth_token_secret);
//
//$twitterObj = new EpiTwitter($consumer_key, $consumer_secret, $newUser->oauth_token, $newUser->oauth_token_secret);
//
//$twitterInfo = $twitterObj->get_accountVerify_credentials();
//
//
////var_dump($twitterInfo);
//echo "<h1>Your twitter username is {$twitterInfo->name} and your profile picture is <img src=\"{$twitterInfo->profile_image_url}\"></h1>
//<p><a href=\"random.php\">Go to another page and load your friends list from your cookie</a></p>";
?>

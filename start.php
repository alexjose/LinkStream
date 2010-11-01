<?php
include_once 'lib/EpiCurl.php';
include_once 'lib/EpiOAuth.php';
include_once 'lib/EpiTwitter.php';
include_once 'lib/secret.php';

$twitterObj = new EpiTwitter($consumer_key, $consumer_secret);

echo '<a href="' . $twitterObj->getAuthenticateUrl() . '">Sign In with Twitter</a>';
?>


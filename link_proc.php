<?php

session_start();
include_once 'inc/Link.php';
include_once 'inc/Stream.php';
$newLink = new Link();
$newStream = new Stream();

$newStream->category = mysql_real_escape_string($_REQUEST['cat']);
$newStream->topic = mysql_real_escape_string($_REQUEST['t']);

if (isset($_REQUEST['link_id'])) {
    $newStream->link_id = $_REQUEST['link_id'];
    if ($newStream->category == '')
        $newStream->category = $newStream->get_Category($newStream->link_id);
    if ($newStream->topic == '')
        $newStream->topic = $newStream->get_Topic($newStream->link_id);
    if (!$newStream->is_Valid($newStream->link_id)) {
        $newStream->insert_Stream();
        if ($newStream->user_id != 0) {
            include 'lib/EpiCurl.php';
            include 'lib/EpiOAuth.php';
            include 'lib/EpiTwitter.php';
            include 'lib/secret.php';
            include_once 'inc/User.php';
            $user_obj = new User();
            $user_obj->oauth_token = $_SESSION['oauth_token'];
            $user_obj->oauth_token_secret = $_SESSION['oauth_token_secret'];
            $twitterObj = new EpiTwitter($consumer_key, $consumer_secret, $user_obj->oauth_token, $user_obj->oauth_token_secret);
            $newLink->id = $newStream->link_id;
            $newLink->get_LinkDetails();
            if($newLink->short_url=='')
                    $newLink->set_ShortUrl ();
            $tweet_msg = $newLink->title . " " . $newLink->short_url;
            $twitterObj->post_statusesUpdate(array('status' => $tweet_msg));
        }
    }
} else {
    $newLink->url = mysql_real_escape_string($_REQUEST['url']);
    $newLink->title = mysql_real_escape_string($_REQUEST['title']);
    $newLink->desc = mysql_real_escape_string($_REQUEST['desc']);
    $newLink->img = mysql_real_escape_string($_REQUEST['img']);

    if ($newLink->img != 'undefined')
        $newLink->img = $newLink->resize_Image($newLink->img, $newLink->url);

    $newStream->link_id = $newLink->insertLink();
    $newStream->insert_Stream();
    if ($newStream->user_id != 0) {
        include 'lib/EpiCurl.php';
        include 'lib/EpiOAuth.php';
        include 'lib/EpiTwitter.php';
        include 'lib/secret.php';
        include_once 'inc/User.php';
        $user_obj = new User();
        $user_obj->oauth_token = $_SESSION['oauth_token'];
        $user_obj->oauth_token_secret = $_SESSION['oauth_token_secret'];
        $twitterObj = new EpiTwitter($consumer_key, $consumer_secret, $user_obj->oauth_token, $user_obj->oauth_token_secret);
        $newLink->id = $newStream->link_id;
        $newLink->get_LinkDetails();
        $tweet_msg = $newLink->title . " " . $newLink->short_url;
        $twitterObj->post_statusesUpdate(array('status' => $tweet_msg));
    }
}
?>
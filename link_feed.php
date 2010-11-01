<?php
include_once 'inc/User.php';
include_once 'inc/Link.php';
include_once 'inc/Stream.php';
$link_obj = new Link();
$user_obj = new User();
$stream_obj = new Stream();
//date_default_timezone_set('Asia/Kolkata');
//define('site', 'Chumaru');
//define('server', 'localhost');
//define('username', 'root');
//define('password', '');
//define('database', 'chumaru');
//$db_pdo = new PDO("mysql:host=" . server . ";dbname=" . database, username, password);
//mysql_connect(server, username, password);
$id = null;
if (isset($_REQUEST['id']))
    $id = $_REQUEST['id'];
//echo $id;
if (isset($_GET['c']))
    $links = $stream_obj->get_Stream($id, 'category', $_GET['c']);
elseif (isset($_GET['t']))
    $links = $stream_obj->get_Stream($id, 'topic', $_GET['t']);
elseif (isset($_GET['u']))
    $links = $stream_obj->get_Stream($id, 'user', $_GET['u']);
else
    $links = $stream_obj->get_Stream($id);
foreach ($links as $link) {
    $stream_obj->Init($link['id']);
    $link_obj->id = $stream_obj->link_id;
    $link_obj->get_LinkDetails();
?>
    <div class="link" id="link-<?php echo $stream_obj->id ?>">
        <div id="linkinfo">
        <?php
        if (($link_obj->img != 'undefined') && (file_exists($link_obj->img))) {
            $img_src = $link_obj->img;
            list($width, $height) = getimagesize($img_src);
            if ($width > $height)
                $dimension = 'width="60px"';
            else
                $dimension = 'height="60px"';
        } else {
            $img_src = 'img/no_image.gif';
            $width = '60px';
        }
        ?>
        <img src="<?php echo $img_src ?>" width="60px" height="60px"  />
        <div id="link_title">
            <a href="<?php echo $link_obj->url ?>" target="_blank"><?php echo stripslashes($link_obj->title) ?></a><br />
        </div>
        <div id="link_details">
            <?php echo '<font color="#E37400"><strong>' . $link_obj->get_Domain($link_obj->url) . '</strong></font>' ?><br />
            <?php echo stripslashes($link_obj->desc) ?><br />
            <i><?php echo $link_obj->my_date_diff($stream_obj->date) ?></i> by <strong><a href="?u=<?php echo $user_obj->get_Name($stream_obj->user_id) ?>"><?php echo $user_obj->get_Name($stream_obj->user_id) ?></a></strong>
        </div>
    </div>
    <div class="social">
        <div id="count"><?php echo $stream_obj->get_StreamCount($stream_obj->link_id)?><input class="stream_button" id="<?php echo $stream_obj->link_id?>" type="submit" value="Stream" />
        </div>
    </div>
    <br clear="all" />
    <a href="http://twitter.com/share" class="twitter-share-button" data-count="horizontal" data-url="<?php echo $link_obj->url ?>" data-text="<?php echo stripslashes($link_obj->title) ?>">Tweet</a><script type="text/javascript" src="http://platform.twitter.com/widgets.js"></script>
</div>
<hr />
<?php } ?>

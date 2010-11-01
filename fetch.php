<?php
session_start();
if (!isset($_SESSION['signed'])) {?>
    <div align="center">You are not signed in. <?php include 'start.php' ?></div>
<?php    exit(0);
}
include_once 'inc/Link.php';
$newLink = new Link();
$newLink->url = $_REQUEST['url'];
$newLink->url = $newLink->checkValues($newLink->url);


if (!$newLink->get_LinkDetailsByUrl())
    $newLink->url = $newLink->gen_RealLink($newLink->url);
//echo $newLink->url;
if (!$newLink->get_LinkDetails()) {
    $url = $newLink->url;
    $headers = get_headers($url, 1);

    function fetch_record($path) {
        $data = file_get_contents($path);
        return $data;
    }

    if (is_int(strpos($headers[0], '200 OK'))) {
        $string = fetch_record($url);


/// fecth title
        $title_regex = "/<title>(.+)<\/title>/i";
        preg_match_all($title_regex, $string, $title, PREG_PATTERN_ORDER);
        $url_title = $title[1];


/// fecth decription
        $tags = get_meta_tags($url);

        if (@$url_title[0] == '')
            $url_title[0] = @$tags['title'];

// fetch images
        $image_regex = '/<img[^>]*' . 'src=[\"|\'](.*)[\"|\']/Ui';
        preg_match_all($image_regex, $string, $img, PREG_PATTERN_ORDER);
        $images_array = $img[1];
?>

        <div class="images">
    <?php
        $k = 1;
        for ($i = 0; $i <= sizeof($images_array); $i++) {
            if (@$images_array[$i]) {
//                    if(@file_exists($images_array[$i]))
                if (@getimagesize($images_array[$i])) {
                    list($width, $height, $type, $attr) = getimagesize($images_array[$i]);
                    if ($width >= 150 && $height >= 150) {
                        echo "<img src='" . @$images_array[$i] . "' width='100' id='" . $k . "' >";

                        if ($k++ == 6)
                            break;
                    }
                }
            }
        }
    ?>
    <!--<img src="ajax.jpg"  alt="" />-->
        <input type="hidden" name="total_images" id="total_images" value="<?php echo--$k ?>" />
    </div>
    <div class="info">

        <label class="title">
        <?php echo @$url_title[0]; ?>
    </label>
    <br clear="all" />
    <label class="url">
        <?php echo substr($url, 0, 35); ?>
    </label>
    <br clear="all" /><br clear="all" />
    <label class="desc">
        <?php echo @$tags['description']; ?>
    </label>
    <br clear="all" /><br clear="all" />

    <label style="float:left"><img src="css/images/prev.png" id="prev" alt="" /><img src="css/images/next.png" id="next" alt="" /></label>

    <label class="totalimg">
			Total <?php echo $k ?> images
    </label>
    <br clear="all" />
    <form name="newlink" id="newlink" method="POST">
        <input type="hidden" id="url" name="url" value="<?php echo $url ?>" />
        <input type="hidden" name="title" value ="<?php echo $url_title[0] ?>" />
        <input type="hidden" name="desc" value="<?php echo @$tags['description']; ?>" />
    </form>
    <?php //echo $images_array[0];  ?>
    <?php //var_dump(getimagesize($images_array[0]));  ?>
    </div>
    <div class="info_2">
        <select name="category" id="category">
            <option selected="selected">Select Category</option>
            <option>Business</option>
            <option>Entertainment</option>
            <option>Gaming</option>
            <option>Lifestyle</option>
            <option>Offbeat</option>
            <option>Politics</option>
            <option>Science</option>
            <option>Sports</option>
            <option>Technology</option>
            <option>World News</option>
        </select><br /><br />
        Topic:
        <input type="text" name="topic" value="" id="topic" />
        <br /><br />
        <input type="button" name="share" value="Share" id="share" />
    </div>
<?php } else {
?>
        <div class="info">
            <label class="title">Error</label>
            <br clear="all" />
        </div>
<?php
    }
} else {
?>
    <div class="images">
        <img src="<?php echo $newLink->img ?>" width="100" id="1" >
        <input type="hidden" name="total_images" id="total_images" value="1" />
    </div>
    <div class="info">
        <label class="title">
        <?php echo $newLink->title ?>
    </label>
    <br />
    <label class="url">
        <?php echo $newLink->url ?>
    </label>
    <br />
    <label class="desc">
        <?php echo $newLink->desc ?>
    </label>
    <br />
    <label style="float:left"><img src="css/images/prev.png" id="prev" alt="" /><img src="css/images/next.png" id="next" alt="" /></label>

    <label class="totalimg">
			Total 1 image
    </label>
    <form name="newlink" id="newlink" method="POST" action="#">
        <input type="hidden" id="link_id" name="link_id" value="<?php echo $newLink->id ?>" />
    </form>

</div>
<div class="info_2">
    <select name="category" id="category">
        <option selected="selected">Select Category</option>
        <option>Business</option>
        <option>Entertainment</option>
        <option>Gaming</option>
        <option>Lifestyle</option>
        <option>Offbeat</option>
        <option>Politics</option>
        <option>Science</option>
        <option>Sports</option>
        <option>Technology</option>
        <option>World News</option>
    </select><br /><br />
    Topic:
    <input type="text" name="topic" value="" id="topic" />
    <br /><br />
    <input type="button" name="share" value="Share" id="share" />
</div>
<?php } ?>
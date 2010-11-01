<?php

/**
 * Description of Link
 *
 * @author Alex Jose
 */
include 'SimpleImage.php';
include_once 'Config.php';

class Link extends DBCon {

    public $id, $url, $short_url, $title, $desc, $date, $img, $category, $topic;

    public function gen_RealLink($url) {
        if ($header = get_headers($url, 1)) {
            if (isset($header['Location'])) {
                $url = $header['Location'];
                if (is_array($url)) {
                    $this->gen_RealLink($header['Location'][0]);
                }
            }
        }
        else
            $url = "Error";
        if (is_array($url))
            $url = $url[count($url) - 1];
        return $url;
    }

    public function resize_Image($img, $url) {
        $img_info = pathinfo($img);
        $img_name = time();
        $img_ext = $img_info['extension'];
        $image = new SimpleImage();
        $image->load($img);
        if ($image->getHeight() > $image->getWidth())
            $image->resizeToHeight(100);
        else
            $image->resizeToWidth(100);
        $image->save("img/$img_name.$img_ext");
        return("img/$img_name.$img_ext");
    }

    function checkValues($value) {
        $value = trim($value);
        if (get_magic_quotes_gpc ()) {
            $value = stripslashes($value);
        }
        $value = strtr($value, array_flip(get_html_translation_table(HTML_ENTITIES)));
        $value = strip_tags($value);
        $value = htmlspecialchars($value);
        return $value;
    }

    public function get_LinkDetails() {
        $statement = "SELECT * FROM `links` WHERE `id`='$this->id'";
//        echo $statement;
        $statement = $this->db_pdo->query($statement);
        if ($statement->rowCount() >= 1) {
            $row = $statement->fetch();
            $this->id = $row['id'];
            $this->url = $row['url'];
            $this->short_url = $row['short_url'];
            $this->title = $row['title'];
            $this->desc = $row['description'];
            $this->img = $row['image'];
            $this->date = $row['date'];
//            $this->category = $row['category'];
//            $this->topic = $row['topic'];
            return true;
        }
        else
            return false;
    }

    public function get_LinkDetailsByUrl() {
        $statement = "SELECT * FROM `links` WHERE `url`='$this->url'";
//        echo $statement;
        $statement = $this->db_pdo->query($statement);
        if ($statement->rowCount() >= 1) {
            $row = $statement->fetch();
            $this->id = $row['id'];
            $this->title = $row['title'];
            $this->desc = $row['description'];
            $this->img = $row['image'];
            $this->date = $row['date'];
//            $this->category = $row['category'];
//            $this->topic = $row['topic'];
            return true;
        }
        else
            return false;
    }

    public function insertLink() {
        if (isset($_SESSION['signed']))
            $uid = $_SESSION['uid'];
        else
            $uid = 0;
        $this->short_url = $this->make_bitly_url($this->url, 'alexjose', 'R_5f1bc3aceb3b5c7b9bcbc1f4cf665b9e', 'json');

        $statement = "INSERT INTO `links` (`uid`, `url`, `short_url`, `title`, `description`,
    `image`) VALUES
        ($uid, '$this->url','$this->short_url', '$this->title','$this->desc','$this->img')";
//        echo $statement;
        $this->db_pdo->query($statement);
        return $this->db_pdo->lastInsertId();
    }

    public function get_Links($id, $type=null, $CatOrTopic= null) {
        $cond = '';
        if ($type != null) {
            if ($type == 'category')
                $cond = "AND `category` = '$CatOrTopic'";
            else
                $cond = "AND `topic` = '$CatOrTopic'";
        }

        if ($id == null) {
            $statement = "SELECT MAX(id) FROM `streams`";
            $statement = $this->db_pdo->query($statement);
            $result = $statement->fetch();
            $id = $result[0];
//            $statement = "SELECT `id`, `uid`, `url`,`title`,`description`,`image`,`date` FROM
//    `links` WHERE `id`<=$id $cond ORDER BY `id` DESC LIMIT 10";
            $statement = "SELECT `id`,`link_id`,`user_id` FROM
            `streams` WHERE `id`<=$id $cond ORDER BY `id` DESC LIMIT 10";
            $result = $this->db_pdo->query($statement);
        }
        else
            $statement = "SELECT `id`, `uid`,`url`,`title`,`description`,`image`,`date` FROM
    `links` WHERE `id`>$id $cond LIMIT 1";
//        echo $statement;
        $result = $this->db_pdo->query($statement);
        $links = $result->fetchAll();
        return $links;
    }

    public function get_Domain($url) {
        if (filter_var($url, FILTER_VALIDATE_URL, FILTER_FLAG_HOST_REQUIRED) === FALSE) {
            return false;
        }
        $parts = parse_url($url);
        return $parts['scheme'] . '://' . $parts['host'];
    }

    public function my_date_diff($start, $end="NOW") {
        $sdate = strtotime($start);
        $edate = strtotime($end);

        $time = $edate - $sdate;
        if ($time >= 0 && $time <= 59) {
            // Seconds
            $timeshift = $time . ' seconds ago';
        } elseif ($time >= 60 && $time <= 3599) {
            // Minutes + Seconds
            $pmin = ($edate - $sdate) / 60;
            $premin = explode('.', $pmin);

            $presec = $pmin - $premin[0];
            $sec = $presec * 60;

            $timeshift = $premin[0] . ' min ' . round($sec, 0) . ' sec ago';
        } elseif ($time >= 3600 && $time <= 86399) {
            // Hours + Minutes
            $phour = ($edate - $sdate) / 3600;
            $prehour = explode('.', $phour);

            $premin = $phour - $prehour[0];
            $min = explode('.', $premin * 60);

            $presec = '0.' . $min[1];
            $sec = $presec * 60;

            $timeshift = $prehour[0] . ' hrs ' . $min[0] . ' min ' . round($sec, 0) . ' sec ago';
        } elseif ($time >= 86400) {
            // Days + Hours + Minutes
            //$pday = ($edate - $sdate) / 86400;
            //$preday = explode('.',$pday);
            //$phour = $pday-$preday[0];
            //$prehour = explode('.',$phour*24);
            //$premin = ($phour*24)-$prehour[0];
            //$min = explode('.',$premin*60);
            //$presec = '0.'.$min[1];
            //$sec = $presec*60;
            //$timeshift = $preday[0].' days '.$prehour[0].' hrs '.$min[0].' min '.round($sec,0).' sec ';
            $timeshift = date('g:s A dS M Y', $sdate);
        }
        return $timeshift;
    }

    public function make_bitly_url($url, $login, $appkey, $format = 'xml', $version = '2.0.1') {
        //create the URL
        $bitly = 'http://api.bit.ly/shorten?version=' . $version . '&longUrl=' . urlencode($url) . '&login=' . $login . '&apiKey=' . $appkey . '&format=' . $format;

        //get the url
        //could also use cURL here
        $response = file_get_contents($bitly);

        //parse depending on desired format
        if (strtolower($format) == 'json') {
            $json = @json_decode($response, true);
            return $json['results'][$url]['shortUrl'];
        } else {
            $xml = simplexml_load_string($response);
            return 'http://bit.ly/' . $xml->results->nodeKeyVal->hash;
        }
    }

    public function set_ShortUrl() {
        $this->short_url = $this->make_bitly_url($this->url, 'alexjose', 'R_5f1bc3aceb3b5c7b9bcbc1f4cf665b9e', 'json');
        $statement = "UPDATE ON `link` SET `short_url` ='$this->short_url WHERE `url` = '$this->url'";
//        echo $statement;
        $statement = $this->db_pdo->query($statement);
    }

}

?>

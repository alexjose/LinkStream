<?php

/**
 * Description of Stream
 *
 * @author Alex Jose
 */
include_once 'Config.php';
include_once 'User.php';

class Stream extends User {

    public $id, $link_id, $user_id, $category, $topic, $date;

    public function Init($id) {
        $statement = "SELECT * FROM `streams` WHERE `id`=$id";
        $statement = $this->db_pdo->query($statement);
        if ($statement->rowCount() >= 1) {
            $row = $statement->fetch();
            $this->id = $row['id'];
            $this->link_id = $row['link_id'];
            $this->user_id = $row['user_id'];
            $this->category = $row['category'];
            $this->topic = $row['topic'];
            $this->date = $row['date'];
            return true;
        }
        else
            return false;
    }

    public function get_Categories() {
        $statement = "SELECT DISTINCT(`category`) FROM `streams` WHERE `category`!='' ORDER BY `category`";
        $statement = $this->db_pdo->query($statement);
        $result = $statement->fetchAll();
        return $result;
    }

    public function get_Stream($id, $type=null, $term= null) {
        $cond = '';
        if ($type != null) {
            if ($type == 'category')
                $cond = "AND `category` = '$term'";
            else if ($type == 'topic')
                $cond = "AND `topic` = '$term'";
            else {
                $cond = "AND `user_id` = " . $this->get_UserID($term);
            }
        }
        if ($id == null) {
            $statement = "SELECT MAX(id) FROM `streams`";
            $statement = $this->db_pdo->query($statement);
            $result = $statement->fetch();
            $id = $result[0]-10;
            if ($id == '')
                exit(0);
//            $statement = "SELECT `id`, `uid`, `url`,`title`,`description`,`image`,`date` FROM
//    `links` WHERE `id`<=$id $cond ORDER BY `id` DESC LIMIT 10";
            $statement = "SELECT `id` FROM
            `streams` WHERE `id`<=$id $cond ORDER BY `id` DESC LIMIT 10";
            $result = $this->db_pdo->query($statement);
        }
        else
            $statement = "SELECT `id` FROM
    `streams` WHERE `id`>$id $cond LIMIT 1";
//        echo $statement;
        $result = $this->db_pdo->query($statement);
        $links = $result->fetchAll();
        return $links;
    }

    public function insert_Stream() {
        if (isset($_SESSION['signed']))
            $this->user_id = $_SESSION['uid'];
        else
            $this->user_id = 0;

        $statement = "INSERT INTO `streams` (`user_id`, `link_id`, `category`, `topic`) VALUES
        ($this->user_id, $this->link_id, '$this->category', '$this->topic')";
        $this->db_pdo->query($statement);
    }

    public function get_TrendsInCat($cat) {
        $date = strtotime('-1 week', strtotime("NOW"));
        $date = date('Y-m-j H:i:s', $date);
        $statement = "SELECT `topic` FROM `streams` WHERE `category`='$cat' AND `topic`!='' AND `date`>'$date' GROUP BY `topic` ORDER BY COUNT(*) DESC LIMIT 5";
        $statement = $this->db_pdo->query($statement);
        $result = $statement->fetchAll();
        return $result;
    }

    public function get_StreamCount($linkid) {
        $statement = "SELECT COUNT(*) FROM `streams` WHERE `link_id`=$linkid";
        $statement = $this->db_pdo->query($statement);
        $result = $statement->fetch();
        return $result[0];
    }

    public function get_Category($linkid) {
        $statement = "SELECT `category` FROM `streams` WHERE `link_id`=$linkid AND `category`!='' ORDER BY `id` DESC LIMIT 1";
        $statement = $this->db_pdo->query($statement);
        $result = $statement->fetch();
        return $result[0];
    }

    public function get_Topic($linkid) {
        $statement = "SELECT `topic` FROM `streams` WHERE `link_id`=$linkid AND `topic`!='' ORDER BY `id` DESC LIMIT 1";
        $statement = $this->db_pdo->query($statement);
        $result = $statement->fetch();
        return $result[0];
    }

    public function is_Valid($linkid) {
        $statement = "SELECT `link_id` FROM `streams` ORDER BY `id` DESC LIMIT 5";
//        echo $statement;

        $statement = $this->db_pdo->query($statement);
        $result = $statement->fetchAll();
        for ($x = 0; $x < $statement->rowCount(); $x++) {
            if ($result[$x][0] == $linkid)
                return false;
        }
        return TRUE;
    }

}

?>
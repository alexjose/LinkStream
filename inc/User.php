<?php

/**
 * Description of User
 *
 * @author Alex Jose
 */
include_once 'Config.php';

class User extends DBCon {

    public $id, $handle, $oauth_token, $oauth_token_secret, $date;

    public function signin_User() {
        $this->is_NewUser();
        $_SESSION['signed'] = true;
        $_SESSION['uid'] = $this->id;
        $_SESSION['oauth_token'] = $this->oauth_token;
        $_SESSION['oauth_token_secret'] = $this->oauth_token_secret;
    }

    public function is_NewUser() {
        $statement = "SELECT `id` FROM `users` WHERE `id`=$this->id";
        $result = $this->db_pdo->query($statement);
        if ($result->rowCount() == 1)
            return false;
        else {
            $this->create_User();
            return true;
        }
    }

    public function create_User() {
        $statement = "INSERT INTO `users` (`id`,`handle`) VALUES ($this->id,'$this->handle')";
//        echo $statement;
        $this->db_pdo->query($statement);
    }

    public function get_Name($id) {
        if ($id == 0)
            return 'Public';
        else {
            $statement = "SELECT `handle` FROM `users` WHERE `id`=$id";
            $result = $this->db_pdo->query($statement);
            $row = $result->fetch();
            return $row[0];
        }
    }

    public function get_UserID($handle) {
        if ($handle == 'Public')
            return 0;
        else {
            $statement = "SELECT `id` FROM `users` WHERE `handle`='$handle'";
            $result = $this->db_pdo->query($statement);
            $row = $result->fetch();
            return $row[0];
        }
    }

}

?>

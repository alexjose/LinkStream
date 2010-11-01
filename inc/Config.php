<?php
//date_default_timezone_set('Asia/Kolkata');
//define('site', 'Chumaru');
define('server', 'localhost');
//define('username', 'root');
//define('password', '');
//define('database', 'linkstream');

define('username', 'rockingt_chumaru');
define('password', '77689*2');
define('database', 'rockingt_linkstream');

$db_pdo = new PDO("mysql:host=" . server . ";dbname=" . database, username, password);
mysql_connect(server, username, password);

class DBCon {

    public function __construct() {
        $this->db_pdo = new PDO("mysql:host=" . server . ";dbname=" . database, username, password);
        mysql_connect(server, username, password);
    }

}

?>
<?php
include_once '../inc/Config.php';
$db_obj = new DBCon();

$term = $_REQUEST["term"];
$term = mysql_real_escape_string($term);

$statement = "SELECT DISTINCT(`topic`) FROM `streams` WHERE `topic`!='' AND `topic` LIKE '$term%'";
$result = $db_obj->db_pdo->query($statement);
$result = $result->fetchAll();
for($x=0;$x<count($result);$x++){
    $topics[$x] = $result[$x][0];
}
echo(json_encode($topics));
?>

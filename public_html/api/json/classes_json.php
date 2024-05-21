<?php
$root = $_SERVER['DOCUMENT_ROOT'];
require_once $root . '/modules/parser/MySqliAdapter.php';

$query = "SELECT * FROM `classes`;";
$adapter = new MySqliAdapter("127.0.0.1","cx19991_bdmobi","4vW1VaPf","cx19991_bdmobi");
$twoDimArr = $adapter->get_sql_query_result($query, MYSQLI_ASSOC);
header('Content-Type: application/json; charset=utf-8');
$json = json_encode($twoDimArr, JSON_UNESCAPED_UNICODE);
echo $json;
?>
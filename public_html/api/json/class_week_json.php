<?php
$root = $_SERVER['DOCUMENT_ROOT'];
require_once $root . '/modules/parser/ScheduleParser.php';

$date;
$id;

if (isset($_GET["date"]) && isset($_GET["id"]))
{
    $date = $_GET["date"];
    $id = $_GET["id"];
}
else exit ("Один из параметров остался пустым!");

$parser = new ScheduleParser("127.0.0.1","cx19991_bdmobi","4vW1VaPf","cx19991_bdmobi");
$threedimResult = $parser->get_schedule_week_class($date, (int)$id);

header('Content-Type: application/json; charset=utf-8');
$json = json_encode($threedimResult, JSON_UNESCAPED_UNICODE);
echo $json;
?>
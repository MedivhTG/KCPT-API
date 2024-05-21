<?php
$root = $_SERVER['DOCUMENT_ROOT'];
require_once $root . '/modules/parser/ScheduleParser.php';

$date;
$class;

if (isset($_GET["date"]) && isset($_GET["class"]))
{
    $date = $_GET["date"];
    $class = $_GET["class"];
}
else exit ("Один из параметров остался пустым!");

echo "Группа: {$class}" . "</br></br></br>";

$parser = new ScheduleParser("127.0.0.1","cx19991_bdmobi","4vW1VaPf","cx19991_bdmobi");
$xlsx = glob($rootPath . "/resources/changes/*");
$parser->xlsx_get($xlsx[0]);
$threedimResult = $parser->get_schedule_week_class($date, $class);

foreach ($threedimResult as $days){
    if ($days == null){
        continue;
    }
    echo "День " . $days[0]['День'] . "</br>";
    foreach ($days as $rows){
        foreach ($rows as $column){
             echo $column . " "; 
        }
        echo "</br>";
    }
    echo "</br></br>";
}
?>
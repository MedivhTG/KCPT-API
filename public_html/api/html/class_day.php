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

$day = date('w', strtotime($date));
echo "Дата {$date}, день {$day}, группа {$class}" . "</br>";

$parser = new ScheduleParser("127.0.0.1","cx19991_bdmobi","4vW1VaPf","cx19991_bdmobi");
$xlsx = glob($rootPath . "/resources/changes/*");
$parser->xlsx_get($xlsx[0]);
$twodimResult = $parser->get_schedule_day_class($date, $class);

foreach ($twodimResult as $rowKey => $rowVal){
    foreach ($rowVal as $columnKey => $columnVal) {
        echo $columnVal . " ";
    }
    echo "</br>";
}
?>
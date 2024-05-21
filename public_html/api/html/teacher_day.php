<?php
$root = $_SERVER['DOCUMENT_ROOT'];
require_once $root . '/modules/parser/ScheduleParser.php';

$date;
$surname;
$firstName;
$secondName;

if (isset($_GET["date"]) && isset($_GET["surname"]) && isset($_GET["firstName"]) && isset($_GET["secondName"]))
{
    $date = $_GET["date"];
    $surname = $_GET["surname"];
    $firstName = $_GET["firstName"];
    $secondName = $_GET["secondName"];
}
else exit ("Один из параметров остался пустым!");

$day = date('w', strtotime($date));
echo "Дата: {$date}, день {$day}. Расписание для {$surname} {$firstName} {$secondName}" . "</br></br>";

$parser = new ScheduleParser("127.0.0.1","cx19991_bdmobi","4vW1VaPf","cx19991_bdmobi");
$xlsx = glob($rootPath . "/resources/changes/*");
$parser->xlsx_get($xlsx[0]);
$twodimResult = $parser->get_schedule_day_teacher($date, $surname, $firstName, $secondName);

foreach ($twodimResult as $row){
    foreach ($row as $column) {
        echo $column . " ";
    }
    echo "</br>";
}
?>
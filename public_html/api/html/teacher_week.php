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

echo $surname . " " . $firstName . " " . $secondName . "</br>";

$parser = new ScheduleParser("127.0.0.1","cx19991_bdmobi","4vW1VaPf","cx19991_bdmobi");
$xlsx = glob($rootPath . "/resources/changes/*");
$parser->xlsx_get($xlsx[0]);
$threedimResult = $parser->get_schedule_week_teacher($date, $surname, $firstName, $secondName);

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
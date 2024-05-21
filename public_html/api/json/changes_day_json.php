<?php
    $date;

    if (isset($_GET["date"]))
    {
        $date = $_GET["date"];
    }
    else exit ("Даты нет!");

    $root = $_SERVER['DOCUMENT_ROOT'];
    require_once $root.'/modules/parser/MySqliAdapter.php';
    $adapter = new MySqliAdapter("127.0.0.1","cx19991_bdmobi","4vW1VaPf","cx19991_bdmobi");

    $twodimArr = $adapter->get_sql_query_result("SELECT * FROM `changes` WHERE `Date`='".$date."' ORDER BY `ClassID`, `Hour`, `SubGroup`;", MYSQLI_ASSOC);
    header('Content-Type: application/json; charset=utf-8');
    $json = json_encode($twodimArr, JSON_UNESCAPED_UNICODE);
    echo $json;
?>
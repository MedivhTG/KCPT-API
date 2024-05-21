<?php
    $root = $_SERVER['DOCUMENT_ROOT'];
    require_once $root . '/modules/parser/MySqliAdapter.php';
    $adapter = new MySqliAdapter("127.0.0.1","cx19991_bdmobi","4vW1VaPf","cx19991_bdmobi");

    if(!isset($_POST['id'])) return -1;
    else 
    {
        $adapter->edit("DELETE FROM `changes` WHERE `changes`.`ID`=".$_POST['id'].";");
    }

?>
<?php
    $root = $_SERVER['DOCUMENT_ROOT'];
    require_once $root . '/modules/parser/MySqliAdapter.php';
    $adapter = new MySqliAdapter("127.0.0.1","cx19991_bdmobi","4vW1VaPf","cx19991_bdmobi");
    $classId = $_POST['class'];
    $subgroup = $_POST['subgroup'];
    $date = $_POST['date'];
    $Hide = $_POST['Hide'];
    $hour;

    if ($_POST['hourPart2'] != "0" && $_POST['hourPart1']) $hour = $_POST['hourPart1']."-".$_POST['hourPart2'];
    else $hour = $_POST['hourPart1']; 
    $subjectId = $_POST['subject'];
    $teacherId = $_POST['teacher'];
    $roomId = $_POST['room'];
    $status = $_POST['status'];
    $subgroupCh = $_POST['subgroupCh'];
    $hourCh;
    if ($_POST['hourPart2Ch'] != "0" && $_POST['hourPart1Ch']) $hourCh = $_POST['hourPart1Ch']."-".$_POST['hourPart2Ch'];
    else $hour = $_POST['hourPart1Ch'];
    $subjectIdCh = $_POST['subjectCh'];
    $teacherIdCh = $_POST['teacherCh'];
    $roomIdCh = $_POST['roomCh'];

    if ($_POST['action'] == "add")
    {      
        $query = "INSERT INTO `changes`(ClassID, SubGroup, Date, Hour, 
        SubjectID, TeacherID, RoomID, Status, SubGroupChange, 
        HourChange, SubjectChangeID, TeacherChangeID, RoomChangeID, Hide) VALUES 
        (".$classId.", ".$subgroup.", '".$date."', '".$hour."', 
        ".$subjectId.", ".$teacherId.", ".$roomId.", '".$status."', 
        ".$subgroupCh.", '".$hourCh."', ".$subjectIdCh.", 
        ".$teacherIdCh.", ".$roomIdCh.", ".$Hide.");";
        $adapter->edit($query);
    }

    else if ($_POST['action'] == "update")
    {
        $query = "UPDATE `changes` SET `Hide`=".$Hide.", `ClassID`=".$classId.", `SubGroup`=".$subgroup.
        ", `Date`='".$date."', `Hour`=".$hour.", `SubjectID`=".$subjectId.
        ", `TeacherID`=".$teacherId.", `RoomID`=".$roomId.", `Status`='".$status.
        "', `SubGroupChange`=".$subgroupCh.", `HourChange`='".$hourCh.
        "', `SubjectChangeID`=".$subjectIdCh.", `TeacherChangeID`=".$teacherIdCh.
        ", `RoomChangeID`=".$roomIdCh.
        " WHERE `ID`=".$_POST['id'];
        $adapter->edit($query);
    }
?>
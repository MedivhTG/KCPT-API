<!doctype html>
<html>
    <meta content="text/html; charset=UTF-8" http-equiv="content-type" />
    <head>       
        <title>Изменения: печать</title>
        <link rel="stylesheet" href="/style/changes_print.css"/>
    </head>
    <body>
        <table>
            <caption>ГАПОУ ТО «Колледж цифровых и педагогических технологий»<br></br>ИЗМЕНЕНИЯ В РАСПИСАНИИ<br></br>
                <u>на 
                    <?php
                        if (!isset($_GET["date"])) echo "...";
                        else
                        {
                            $date = $_GET["date"];
                            $months = array(
                            "1"=>"января","2"=>"февраля","3"=>"марта",
                            "4"=>"апреля","5"=>"мая", "6"=>"июня",
                            "7"=>"июля","8"=>"августа","9"=>"сентября",
                            "10"=>"октября","11"=>"ноября","12"=>"декабря");
                            $weekDays = array(
                                "воскресенье",
                                "понедельник",
                                "вторник",
                                "среда",
                                "четверг",
                                "пятница",
                                "суббота"
                            );
                            $monthName = $months[date("n", strtotime($date))];
                            $day = date("d", strtotime($date));
                            $dayOfWeek = $weekDays[date("w", strtotime($date))];
                            $year = date("Y", strtotime($date));
                            echo $day." ".$monthName." ".$year." года (".$dayOfWeek.")";
                        }
                    ?>
                </u><br></br>
            </caption>
            <thead>
                <tr>
                    <th class="h1">Группа</th>
                    <th class="h2">№ урока</th>
                    <th class="h3">По расписанию</th>
                    <th class="h4">Изменения</th>
                    <th class="h5">Кабинет</th>
                </tr>
            </thead>
            <tbody>
                <?php                   
                    $root = $_SERVER['DOCUMENT_ROOT'];
                    require_once $root.'/modules/parser/MySqliAdapter.php';
                    $adapter = new MySqliAdapter("127.0.0.1","cx19991_bdmobi","4vW1VaPf","cx19991_bdmobi");
                    if (!isset($_GET["date"])) return;

                    $date = $_GET["date"];                   
                    $twodimArr = $adapter->get_sql_query_result("SELECT * FROM `changes` WHERE `Date`='".$date."' ORDER BY `ClassID`, `Hour`, `SubGroup`;", MYSQLI_ASSOC);
                    if ($twodimArr != null)
                    {
                        $checkClass = $twodimArr[0]['ClassID'];
                        $isFirstClass = true;
                        $echoRow = "";
                        foreach ($twodimArr as $row)
                        {
                            $className = $adapter->get_sql_query_result("SELECT `Name` FROM `classes` WHERE `ID`=".$row['ClassID'].";", MYSQLI_ASSOC)[0]['Name'];
                            $subjectName = $adapter->get_sql_query_result("SELECT `FullName` FROM `subjects` WHERE `ID`=".$row['SubjectID'].";", MYSQLI_ASSOC)[0]['FullName'];
                            $teacher = $adapter->get_sql_query_result("SELECT `Surname`, `FirstName`, `SecondName` FROM `teachers` WHERE `ID`=".$row['TeacherID'].";", MYSQLI_ASSOC)[0];
                            $teacherCh = $adapter->get_sql_query_result("SELECT `Surname`, `FirstName`, `SecondName` FROM `teachers` WHERE `ID`=".$row['TeacherChangeID'].";", MYSQLI_ASSOC)[0];
                            $subjectNameCh = $adapter->get_sql_query_result("SELECT `FullName` FROM `subjects` WHERE `ID`=".$row['SubjectChangeID'].";", MYSQLI_ASSOC)[0]['FullName'];
                            $roomCh = $adapter->get_sql_query_result("SELECT `Name` FROM `rooms` WHERE `ID`=".$row['RoomChangeID'].";", MYSQLI_ASSOC)[0]['Name'];

                            $borderStyle = " class=\"c\"";
                            if ($checkClass != $row['ClassID'])
                            {
                                $checkClass = $row['ClassID'];
                                $classNameCell = $className;
                                $isFirstClass = true;
                                $borderStyle = "";
                            }

                            $classNameCell = "";
                            
                            if ($isFirstClass == true)
                            {
                                $isFirstClass = false;
                                $classNameCell = $className;
                            }

                            $replacementCell = "";
                            $statusCell = "";
                            $scheduleCell = "";
                            $subGroup = "";
                            $subGroupCh = "";
                            if ((int)$row['SubGroup']>0) $subGroup = $row['SubGroup'].".";
                            if ((int)$row['SubGroupChange']>0) $subGroupCh = $row['SubGroupChange'].".";
                            if ($subjectNameCh != null) $replacementCell = $replacementCell."<br><br>".$subGroupCh.$subjectNameCh;
                            if ($teacherCh["Surname"] != null) $replacementCell = $replacementCell."<br><br>".$teacherCh['Surname']." ".mb_substr($teacherCh['FirstName'], 0, 1, "utf-8").".".mb_substr($teacherCh['SecondName'], 0, 1, "utf-8").".";
                            if ($row['Status'] == "Перенос" && $row['HourChange'] != null) $statusCell = $statusCell."Переносится на ".$row['HourChange']." урок";                          
                            else $statusCell = $row['Status'];
                            if ($row['Status'] == "Будет") $scheduleCell = "-";
                            else $scheduleCell = $subGroup.$subjectName."<br><br>".$teacher['Surname']." ".mb_substr($teacher['FirstName'], 0, 1, "utf-8").".".mb_substr($teacher['SecondName'], 0, 1, "utf-8").".";
                            

                            $echoRow = $echoRow."<th".$borderStyle.">".$classNameCell."</th>".
                            "<th".$borderStyle.">".$row['Hour']."</th>".
                            "<th".$borderStyle.">".$scheduleCell."</th>".
                            "<th".$borderStyle.">".$statusCell.$replacementCell."</th>".
                            "<th".$borderStyle.">".$roomCh."</th>";

                            
                            echo "<tr>".$echoRow."</tr>";
                            $echoRow = "";
                        }
                    }                   
                ?>
            </tbody>
        </table>
    </body>
</html>
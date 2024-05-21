<!doctype html>
<html>
    <head>
        <title>Изменения: администрирование</title>
        <link rel="stylesheet" href="/style/changes_form.css"/>
    </head>

    <body>

        <h1 class="header">Панель администратора</h1>
        <label for="date-selector" class="label">Дата изменений</label>
        <form action="" method="post">
            <input type="date" id="date-selector" class="date" 
            value=<?php 
            if (!isset($_GET["date"])) echo "\"".date('Y-m-d')."\"";
            else echo "\"".$_GET["date"]."\""?>></br>
        </form>
        <a id="print" class="print">Печать</a></br>
        <a class="add" href="add.php?date=<?php echo $_GET["date"]?>">Добавить</a>
        <table id="table">
            <thead>
                <tr>
                    <th class="h1">#</th>
                    <th class="h2">Группа</th>
                    <th class="h3">№ урока</th>
                    <th class="h4">По расписанию</th>
                    <th class="h5">Изменения</th>
                    <th class="h6">Кабинет</th>
                    <th>ИЗМЕНИТЬ</th>
                    <th>УДАЛИТЬ</th>
                </tr>
            </thead>
            <tbody>
                <?php
                    $root = $_SERVER['DOCUMENT_ROOT'];
                    require_once $root . '/modules/parser/MySqliAdapter.php';
                    $adapter = new MySqliAdapter("127.0.0.1","cx19991_bdmobi","4vW1VaPf","cx19991_bdmobi");

                    if (!isset($_GET["date"])) echo "";
                    $date = $_GET["date"];
                    
                    $twodimArr = $adapter->get_sql_query_result("SELECT * FROM `changes` WHERE `Date`='".$date."' ORDER BY `ClassID`, `Hour`, `SubGroup`;", MYSQLI_ASSOC);
                    if ($twodimArr != null)
                    {
                        $echoRow = "";
                        $count = 1;
                        foreach ($twodimArr as $row)
                        {
                            $className = $adapter->get_sql_query_result("SELECT `Name` FROM `classes` WHERE `ID`=".$row['ClassID'].";", MYSQLI_ASSOC)[0]['Name'];
                            $subjectName = $adapter->get_sql_query_result("SELECT `FullName` FROM `subjects` WHERE `ID`=".$row['SubjectID'].";", MYSQLI_ASSOC)[0]['FullName'];
                            $teacher = $adapter->get_sql_query_result("SELECT `Surname`, `FirstName`, `SecondName` FROM `teachers` WHERE `ID`=".$row['TeacherID'].";", MYSQLI_ASSOC)[0];
                            $teacherCh = $adapter->get_sql_query_result("SELECT `Surname`, `FirstName`, `SecondName` FROM `teachers` WHERE `ID`=".$row['TeacherChangeID'].";", MYSQLI_ASSOC)[0];
                            $subjectNameCh = $adapter->get_sql_query_result("SELECT `FullName` FROM `subjects` WHERE `ID`=".$row['SubjectChangeID'].";", MYSQLI_ASSOC)[0]['FullName'];
                            $roomCh = $adapter->get_sql_query_result("SELECT `Name` FROM `rooms` WHERE `ID`=".$row['RoomChangeID'].";", MYSQLI_ASSOC)[0]['Name'];

                            $replacementCell = "";
                            $statusCell = "";
                            $scheduleCell = "";
                            $subGroup = "";
                            $subGroupCh = "";

                            if ((int)$row['SubGroup']>0) $subGroup = $row['SubGroup'].".";
                            if ((int)$row['SubGroupChange']>0) $subGroupCh = $row['SubGroupChange'].".";
                            if ($subjectNameCh != null) $replacementCell = "<br><br>".$subGroupCh.$subjectNameCh;
                            if ($teacherCh["Surname"] != null) $replacementCell = $replacementCell."<br><br>".$teacherCh['Surname']." ".mb_substr($teacherCh['FirstName'], 0, 1, "utf-8").".".mb_substr($teacherCh['SecondName'], 0, 1, "utf-8").".";
                            if ($row['Status'] == "Перенос" && $row['HourChange'] != null) $statusCell = $statusCell."Переносится на ".$row['HourChange']." урок";                          
                            else $statusCell = $row['Status'];
                            if ($row['Status'] == "Будет") $scheduleCell = "-";
                            else $scheduleCell = $subGroup.$subjectName."<br><br>".$teacher['Surname']." ".mb_substr($teacher['FirstName'], 0, 1, "utf-8").".".mb_substr($teacher['SecondName'], 0, 1, "utf-8").".";

                            //$dataGET = "id=".$row['ID']."&classId=".$row['ClassID']."&subgroup=".$row['SubGroup'].
                            //"&date=".$row['Date']."&hour=".$row['Hour']."&subjectId=".$row['SubjectID']."&teacherId=".$row['TeacherID'].
                            //"&roomId=".$row['RoomID']."&status=".$row['Status']."&subgroupCh=".$row['SubGroupChange'].
                            //"&hourCh=".$row['HourChange']."&subjectChId=".$row['SubjectChangeID'].
                            //"&teacherChId=".$row['TeacherChangeID']."&roomChId=".$row['RoomChangeID'];
                            

                            $echoRow = "<td>".$count."</td>".
                            "<td>".$className."</td>".
                            "<td>".$row['Hour']."</td>".
                            "<td>".$scheduleCell."</td>".
                            "<td>".$statusCell.$replacementCell."</td>".
                            "<td>".$roomCh."</td>".
                            "<td><a href=\"edit.php?id=".$row['ID']."\" class=\"edit\">Изменить</a></td>".
                            "<td><button onclick=\"deleteRecord(".$row['ID'].")\" class='delete'>Удалить</button></td>";

                            echo "<tr>".$echoRow."</tr>";

                            ++$count;
                        }
                    }
                ?>   
            </tbody>
        </table>
        <script>
                var dateInput = document.getElementById("date-selector");
                var link = document.getElementById("print");
                
                link.addEventListener("click", 
                    function(event) {
                    event.stopImmediatePropagation();
                    var dateValue = dateInput.value;
                    var linkHref = "changes_print.php?date=" + dateValue;
                    link.setAttribute("href", linkHref);
                });


                dateInput.addEventListener('change',
                    () => {
                        const dateValue = dateInput.value;
                        const url = `index_changes.php?date=${encodeURIComponent(dateValue)}`;
                        document.forms[0].action = url;
                        document.forms[0].submit();
                    });

                function deleteRecord(id) {
                    if (confirm('Вы уверены, что хотите удалить данную запись?')) {
                        const xhr = new XMLHttpRequest();
                        xhr.open('POST', 'delete.php');
                        xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
                        xhr.send('id=' + encodeURIComponent(id));

                        const dateValue = dateInput.value;
                        const url = `index_changes.php?date=${encodeURIComponent(dateValue)}`;
                        document.forms[0].action = url;
                        document.forms[0].submit();
                    }
                }
            </script>
        
    </body>   
</html>
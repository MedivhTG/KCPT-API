<!doctype html>
<html>
    <head>
        <title>Изменение</title>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    </head>
    <body>
        <style>
            .label {
                display: block;
            }
        </style>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>
        <p>Правила:</br>
        1. Если изменения на 1 урок, то оставьте второе поле диапазона без числа</br>
        2. Если урок новый, то номера уроков <u>по расписанию</u> выбрать нужно в любом случае</br>
        3. В полях, где есть опция "ОТСУТСТВУЕТ" следует выбирать оное, если нужно статус "Будет" или же поле
        (например кабинет) не указано в постоянном расписании</br>
        4. В полях, где есть опция "БЕЗ ИЗМЕНЕНИЙ" следует выбирать оное, если того требует статус, например:
        при замене кабинета следует выбрать данную опцию только в полях "Предмет" и "Преподаватель"</br>
        5. Заполняйте поля с определенным статусом по инструкциям в пользовательской документации (ОСОБОЕ ВНИМАНИЕ К ДИАПАЗОНАМ)
        </p>
        <form action="submit_changes.php" method="POST">
            <label for="date-selector" class="label">Дата изменения</label>
            <input type="date" id="date-selector" value="<?php echo $_GET['date'] ?>"/></br></br>
            <label for="class-selector" class="label">Группа</label>
            <select id="class-selector">
            <?php
                $root = $_SERVER['DOCUMENT_ROOT'];
                require_once $root . '/modules/parser/MySqliAdapter.php';

                $adapter = new MySqliAdapter("127.0.0.1","cx19991_bdmobi","4vW1VaPf","cx19991_bdmobi");
                $classes2Arr = $adapter->get_sql_query_result("SELECT * FROM `classes`;", MYSQLI_ASSOC);
                $changesArr = $adapter->get_sql_query_result("SELECT * FROM `changes` WHERE `ID` = ".$_GET['id'].";", MYSQLI_ASSOC);

                //$encodeArr = json_encode($changesArr);
                //echo "<script>console.log('{$encodeArr}' );</script>";

                $_POST['changesArr'] = $changesArr;

                $options = "";
                foreach ($classes2Arr as $class)
                {
                    if ($changesArr[0]['ClassID'] == $class['ID']) $options = $options.'<option value="'.$class['ID'].'" selected>'.$class['Name'].'</option>';
                    else $options = $options.'<option value="'.$class['ID'].'">'.$class['Name'].'</option>';       
                }
                echo $options;
            ?>
            </select></br></br>
            <label for="subgroup-selector" class="label">Подгруппа (по расписанию)</label>
            <select id="subgroup-selector">
                <?php
                    $options = "";
                    for ($i = 0; $i <= 4; ++$i)
                    {                   
                        if ($i == 0 && $_POST['changesArr'][0]['SubGroup'] == $i) $options = $options."<option value=\"0\" selected>НЕТ</option>";
                        else if ($i == 0) $options = $options."<option value=\"0\">НЕТ</option>";
                        else if ($_POST['changesArr'][0]['SubGroup'] == $i) $options = $options."<option value=\"".$i."\" selected>".$i."</option>";
                        else $options = $options."<option value=\"".$i."\">".$i."</option>";
                    }
                    echo $options;
                ?>
            </select></br></br>
            <span>Диапазон уроков по номеру (по расписанию)</span></br>
            <label for="hour-selector-part1">Начало</label>
            <select class="form-select" id="hour-selector-part1" >
                <?php
                    $options = "";
                    $hours = explode("-", $_POST['changesArr'][0]['Hour']);
                    for ($i = 1; $i <= 12; ++$i)
                    {
                        if ($hours[0] == $i) $options = $options."<option value=\"".$i."\" selected>".$i."</option>";
                        else $options = $options."<option value=\"".$i."\">".$i."</option>";
                    }
                    echo $options;
                ?>
            </select>
            <label class="form-label" for="hour-selector-part2">и конец</label>
            <select class="form-select" id="hour-selector-part2">
                <?php
                    $options = "";
                    $hours = explode("-", $_POST['changesArr'][0]['Hour']);
                    $selection = -1;
                    if (isset($hours[1])) $selection = (int)$hours[1];
                    for ($i = 0; $i <= 12; ++$i)
                    {
                        if ($i == 0 && $selection == $i) $options = $options."<option value=\"0\" selected>-</option>";
                        else if ($i == 0) $options = $options."<option value=\"0\">-</option>";
                        else if ($selection == $i) $options = $options."<option value=\"".$i."\" selected>".$i."</option>";
                        else $options = $options."<option value=\"".$i."\">".$i."</option>";
                    }
                    echo $options;
                ?>
            </select></br></br>
            <label for="subject-selector" class="label">Предмет (по расписанию)</label>
            <select id="subject-selector">
                <?php
                    $root = $_SERVER['DOCUMENT_ROOT'];
                    require_once $root . '/modules/parser/MySqliAdapter.php';

                    $adapter = new MySqliAdapter("127.0.0.1","cx19991_bdmobi","4vW1VaPf","cx19991_bdmobi");
                    $query = "SELECT * FROM `subjects`;";
                    $twodimArr = $adapter->get_sql_query_result($query, MYSQLI_ASSOC);

                    $options = "";
                    $nullOption = true;
                    foreach ($twodimArr as $row)
                    {
                        if ($nullOption == true && ($_GET['subjectId'] == "" || $_GET['subjectId'] = "NULL"))
                        {
                            $options = $options."<option value=\"NULL\" selected>ОТСУТСТВУЕТ</option>";
                            $nullOption = false;
                        }
                        else if ($nullOption == true)
                        {
                            $options = $options."<option value=\"NULL\">ОТСУТСТВУЕТ</option>";
                            $nullOption = false;
                        }
                        else if ($_GET['subjectId'] == $row['ID']) $options = $options.'<option value="'.$row['ID'].'" selected>'.$row['FullName'].'</option>';
                        else $options = $options.'<option value="'.$row['ID'].'">'.$row['FullName'].'</option>';
                    }
                    echo $options;
                ?>
            </select></br></br>
            <label for="teacher-selector" class="label">Преподаватель (по расписанию)</label>
            <select id="teacher-selector">
                <?php
                    $root = $_SERVER['DOCUMENT_ROOT'];
                    require_once $root . '/modules/parser/MySqliAdapter.php';

                    $adapter = new MySqliAdapter("127.0.0.1","cx19991_bdmobi","4vW1VaPf","cx19991_bdmobi");
                    $query = "SELECT * FROM `teachers`;";
                    $twodimArr = $adapter->get_sql_query_result($query, MYSQLI_ASSOC);

                    $options = "";
                    $nullOption = true;
                    foreach ($twodimArr as $row)
                    {
                        if ($nullOption == true && ($_GET['teacherId'] == "" || $_GET['teacherId'] = "NULL"))
                        {
                            $options = $options."<option value=\"NULL\" selected>ОТСУТСТВУЕТ</option>";
                            $nullOption = false;
                        }
                        else if ($nullOption == true)
                        {
                            $options = $options."<option value=\"NULL\">ОТСУТСТВУЕТ</option>";
                            $nullOption = false;
                        }
                        else if ($_GET['teacherId'] == $row['ID']) $options = $options.'<option value="'.$row['ID'].
                        '" selected>'.$row['Surname']." ".$row['FirstName']."".$row['SecondName'].'</option>';
                        else $options = $options.'<option value="'.$row['ID'].'">'.$row['Surname']." ".$row['FirstName']." ".$row['SecondName'].'</option>';
                    }
                    echo $options;
                ?>
            </select></br></br>
            <label for="room-selector" class="label">Кабинет (по расписанию)</label>
            <select id="room-selector">
                <?php
                    $root = $_SERVER['DOCUMENT_ROOT'];
                    require_once $root . '/modules/parser/MySqliAdapter.php';

                    $adapter = new MySqliAdapter("127.0.0.1","cx19991_bdmobi","4vW1VaPf","cx19991_bdmobi");
                    $query = "SELECT * FROM `rooms`;";
                    $twodimArr = $adapter->get_sql_query_result($query, MYSQLI_ASSOC);

                    $options = "";
                    $nullOption = true;
                    foreach ($twodimArr as $row)
                    {
                        if ($nullOption == true && ($_GET['roomId'] == "" || $_GET['roomId'] = "NULL"))
                        {
                            $options = $options."<option value=\"NULL\" selected>ОТСУТСТВУЕТ</option>";
                            $nullOption = false;
                        }
                        else if ($nullOption == true)
                        {
                            $options = $options."<option value=\"NULL\">ОТСУТСТВУЕТ</option>";
                            $nullOption = false;
                        }
                        else if ($_GET['roomId'] == $row['ID']) $options = $options.'<option value="'.$row['ID'].'" selected>'.$row['Name'].'</option>';
                        else $options = $options.'<option value="'.$row['ID'].'">'.$row['Name'].'</option>';
                    }
                    echo $options;
                ?>
            </select></br></br>
            <label for="status-selector" class="label">Статус изменения</label>
            <select id="status-selector">
                <option <?php if ($_GET['status'] == "Будет") echo "selected" ?>>Будет</option>
                <option <?php if ($_GET['status'] == "Отмена") echo "selected" ?>>Отмена</option>
                <option <?php if ($_GET['status'] == "Перенос") echo "selected" ?>>Перенос</option>
                <option <?php if ($_GET['status'] == "Замена кабинета") echo "selected" ?>>Замена кабинета</option>
                <option <?php if ($_GET['status'] == "Замена") echo "selected" ?>>Замена</option>
            </select></br></br>
            <label for="subgroup-change-selector" class="label">Подгруппа</label>
            <select id="subgroup-change-selector">
                <?php
                    $options = "";
                    for ($i = 0; $i <= 4; ++$i)
                    {                   
                        if ($i == 0 && $_GET['subgroupCh'] == $i) $options = $options."<option value=\"NULL\" selected>НЕТ</option>";
                        else if ($i == 0) $options = $options."<option value=\"NULL\">НЕТ</option>";
                        else if ($_GET['subgroupCh'] == $i) $options = $options."<option value=\"".$i."\" selected>".$i."</option>";
                        else $options = $options."<option value=\"".$i."\">".$i."</option>";
                    }
                    echo $options;
                ?>
            </select></br></br>
            <span>Диапазон уроков по номеру</span></br>
            <label for="hour-change-selector-part1">Начало</label>
            <select class="form-select" id="hour-change-selector-part1" >
                <?php
                    $options = "";
                    $hours = explode("-", $_GET['hourCh']);
                    for ($i = 0; $i <= 12; ++$i)
                    {
                        if ($i == 0 && $hours[0] == $i) $options = $options."<option value=\"0\" selected>БЕЗ ИЗМЕНЕНИЙ</option>";
                        else if ($i == 0) $options = $options."<option value=\"0\">БЕЗ ИЗМЕНЕНИЙ</option>";
                        else if ($hours[0] == $i) $options = $options."<option value=\"".$i."\" selected>".$i."</option>";
                        else $options = $options."<option value=\"".$i."\">".$i."</option>";
                    }
                    echo $options;
                ?>
            </select>
            <label class="form-label" for="hour-change-selector-part2">и конец</label>
            <select class="form-select" id="hour-change-selector-part2">
                <?php
                    $options = "";
                    $hours = explode("-", $_GET['hourCh']);
                    $selection = -1;
                    if (isset($hours[1])) $selection = (int)$hours[1];
                    for ($i = 0; $i <= 12; ++$i)
                    {
                        if ($i == 0 && $selection == $i) $options = $options."<option value=\"0\" selected>БЕЗ ИЗМЕНЕНИЙ</option>";
                        else if ($i == 0) $options = $options."<option value=\"0\">БЕЗ ИЗМЕНЕНИЙ</option>";
                        else if ($selection == $i) $options = $options."<option value=\"".$i."\" selected>".$i."</option>";
                        else $options = $options."<option value=\"".$i."\">".$i."</option>";
                    }
                    echo $options;
                ?>
            </select></br></br>
            <label for="subject-change-selector" class="label">Предмет</label>
            <select id="subject-change-selector">
                <?php
                    $root = $_SERVER['DOCUMENT_ROOT'];
                    require_once $root . '/modules/parser/MySqliAdapter.php';

                    $adapter = new MySqliAdapter("127.0.0.1","cx19991_bdmobi","4vW1VaPf","cx19991_bdmobi");
                    $query = "SELECT * FROM `subjects`;";
                    $twodimArr = $adapter->get_sql_query_result($query, MYSQLI_ASSOC);

                    $options = "";
                    $nullOption = true;
                    foreach ($twodimArr as $row)
                    {
                        if ($nullOption == true && ($_GET['subjectChId'] == "" || $_GET['subjectChId'] = "NULL"))
                        {
                            $options = $options."<option value=\"NULL\" selected>БЕЗ ИЗМЕНЕНИЙ</option>";
                            $nullOption = false;
                        }
                        else if ($nullOption == true)
                        {
                            $options = $options."<option value=\"NULL\">БЕЗ ИЗМЕНЕНИЙ</option>";
                            $nullOption = false;
                        }
                        else if ($_GET['subjectChId'] == $row['ID']) $options = $options.'<option value="'.$row['ID'].'" selected>'.$row['FullName'].'</option>';
                        else $options = $options.'<option value="'.$row['ID'].'">'.$row['FullName'].'</option>';
                    }
                    echo $options;
                ?>
            </select></br></br>
            <label for="teacher-change-selector" class="label">Преподаватель</label>
            <select id="teacher-change-selector">
                <?php
                    $root = $_SERVER['DOCUMENT_ROOT'];
                    require_once $root . '/modules/parser/MySqliAdapter.php';

                    $adapter = new MySqliAdapter("127.0.0.1","cx19991_bdmobi","4vW1VaPf","cx19991_bdmobi");
                    $query = "SELECT * FROM `teachers`;";
                    $twodimArr = $adapter->get_sql_query_result($query, MYSQLI_ASSOC);

                    $options = "";
                    $nullOption = true;
                    foreach ($twodimArr as $row)
                    {
                        if ($nullOption == true && ($_GET['teacherChId'] == "" || $_GET['teacherChId'] = "NULL"))
                        {
                            $options = $options."<option value=\"NULL\" selected>БЕЗ ИЗМЕНЕНИЙ</option>";
                            $nullOption = false;
                        }
                        else if ($nullOption == true)
                        {
                            $options = $options."<option value=\"NULL\">БЕЗ ИЗМЕНЕНИЙ</option>";
                            $nullOption = false;
                        }
                        else if ($_GET['teacherChId'] == $row['ID']) $options = $options.'<option value="'.$row['ID'].
                        '" selected>'.$row['Surname']." ".$row['FirstName']."".$row['SecondName'].'</option>';
                        else $options = $options.'<option value="'.$row['ID'].'">'.$row['Surname']." ".$row['FirstName']." ".$row['SecondName'].'</option>';
                    }
                    echo $options;
                ?>
            </select></br></br>
            <label for="room-change-selector" class="label">Кабинет</label>
            <select id="room-change-selector">
                <?php
                    $root = $_SERVER['DOCUMENT_ROOT'];
                    require_once $root . '/modules/parser/MySqliAdapter.php';

                    $adapter = new MySqliAdapter("127.0.0.1","cx19991_bdmobi","4vW1VaPf","cx19991_bdmobi");
                    $query = "SELECT * FROM `rooms`;";
                    $twodimArr = $adapter->get_sql_query_result($query, MYSQLI_ASSOC);

                    $options = "";
                    $nullOption = true;
                    foreach ($twodimArr as $row)
                    {
                        if ($nullOption == true && ($_GET['roomChId'] == "" || $_GET['roomChId'] = "NULL"))
                        {
                            $options = $options."<option value=\"NULL\" selected>БЕЗ ИЗМЕНЕНИЙ</option>";
                            $nullOption = false;
                        }
                        else if ($nullOption == true)
                        {
                            $options = $options."<option value=\"NULL\">БЕЗ ИЗМЕНЕНИЙ</option>";
                            $nullOption = false;
                        }
                        else if ($_GET['roomChId'] == $row['ID']) $options = $options.'<option value="'.$row['ID'].'" selected>'.$row['Name'].'</option>';
                        else $options = $options.'<option value="'.$row['ID'].'">'.$row['Name'].'</option>';
                    }
                    echo $options;
                ?>
            </select></br></br>
            <div></div></br>
            <button type="submit" name="submit">Изменить</button>
        </form>
        <script>
        document.querySelector('form').addEventListener('submit', function(e) {
            e.preventDefault();
            // URL-адрес со строкой запроса "?param1=value1&param2=value2"
            const queryString = window.location.search;

            // Создаем объект URLSearchParams из строки запроса
            const urlParams = new URLSearchParams(queryString);

            // Получаем значение параметра "param1"
            //const param1 = urlParams.get('id');

            
            


            var id = urlParams.get('id');
            var date = document.querySelector('input[type="date"]').value;           
            var classX = document.querySelector('#class-selector').value;
            var subgroup = document.querySelector('#subgroup-selector').value;
            var hourPart1 = document.querySelector('#hour-selector-part1').value;
            var hourPart2 = document.querySelector('#hour-selector-part2').value;
            var subject = document.querySelector('#subject-selector').value;
            var teacher = document.querySelector('#teacher-selector').value;
            var room = document.querySelector('#room-selector').value;
            var status = document.querySelector('#status-selector').value;
            var subgroupCh = document.querySelector('#subgroup-change-selector').value;
            var hourPart1Ch = document.querySelector('#hour-change-selector-part1').value;
            var hourPart2Ch = document.querySelector('#hour-change-selector-part2').value;
            var subjectCh = document.querySelector('#subject-change-selector').value;
            var teacherCh = document.querySelector('#teacher-change-selector').value;
            var roomCh = document.querySelector('#room-change-selector').value;

            var xhr = new XMLHttpRequest();
            xhr.open('POST', 'submit_changes.php');
            xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
            xhr.onload = function() {
                if (xhr.status === 200) {
                    console.log(xhr.responseText);
                }
                else 
                {
                    console.log(xhr.responseText);
                    alert("Что-то пошло не так...");
                    return;
                }
            };
            xhr.send('action=update' + '&id=' + encodeURIComponent(id) + '&teacher=' + encodeURIComponent(teacher) +
            '&date=' + encodeURIComponent(date) + '&class=' + encodeURIComponent(classX) +
            '&subgroup=' + encodeURIComponent(subgroup) + '&hourPart1=' + encodeURIComponent(hourPart1) +
            '&hourPart2=' + encodeURIComponent(hourPart2) + '&subject=' + encodeURIComponent(subject) +
            '&teacher=' + encodeURIComponent(teacher) + '&room=' + encodeURIComponent(room) +
            '&status=' + encodeURIComponent(status) + '&subgroupCh=' + encodeURIComponent(subgroupCh) +
            '&hourPart1Ch=' + encodeURIComponent(hourPart1Ch) + '&hourPart2Ch=' + encodeURIComponent(hourPart2Ch) +
            '&subjectCh=' + encodeURIComponent(subjectCh) + '&teacherCh=' + encodeURIComponent(teacherCh) +
            '&roomCh=' + encodeURIComponent(roomCh)
            );
            alert("Изменение успешно!");
            window.location.href = '/admin/changes_form/index_changes.php?date=' + encodeURIComponent(date);
        });
        
    </script>
    </body>
</html>
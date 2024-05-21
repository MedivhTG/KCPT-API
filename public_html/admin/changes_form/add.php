<!doctype html>
<html>
    <head>
        <title>Добавление</title>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
        <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
        <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
        <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    </head>
    <body>
    
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
            <input type="date" id="date-selector" class="form-control" value="<?php if (!isset($_GET["date"])) echo date('Y-m-d'); else echo $_GET["date"]; ?>"/></br></br>
            <label for="class-selector" class="label">Группа</label>
            <select id="class-selector">
                <?php
                $root = $_SERVER['DOCUMENT_ROOT'];
                require_once $root . '/modules/extensions/FormGenerator.php';
                
                echo generate_select_options("classes", array("Name"), "ID");
                ?>
            </select></br></br>
            <label for="subgroup-selector" class="label">Подгруппа (по расписанию)</label>
            <select id="subgroup-selector">
                <option value="0">НЕТ</option>
                <option value="1">1</option>
                <option value="2">2</option>
                <option value="3">3</option>
                <option value="4">4</option>
            </select></br></br>
            <span>Диапазон уроков по номеру (по расписанию)</span></br>
            <label for="hour-selector-part1">Начало</label>
            <select class="form-select" id="hour-selector-part1" >
                <option value="1">1</option>
                <option value="2">2</option>
                <option value="3">3</option>
                <option value="4">4</option>
                <option value="5">5</option>
                <option value="6">6</option>
                <option value="7">7</option>
                <option value="8">8</option>
                <option value="9">9</option>
                <option value="10">10</option>
                <option value="11">11</option>
                <option value="12">12</option>
            </select>
            <label class="form-label" for="hour-selector-part2">и конец</label>
            <select class="form-select" id="hour-selector-part2">
                <option value="0">-</option>
                <option value="1">1</option>
                <option value="2">2</option>
                <option value="3">3</option>
                <option value="4">4</option>
                <option value="5">5</option>
                <option value="6">6</option>
                <option value="7">7</option>
                <option value="8">8</option>
                <option value="9">9</option>
                <option value="10">10</option>
                <option value="11">11</option>
                <option value="12">12</option>
            </select></br></br>
            <label for="subject-selector" class="label">Предмет (по расписанию)</label>
            <select id="subject-selector" class="form-select">
                <option value="NULL">ОТСУТСТВУЕТ</option>
                <?php
                    $root = $_SERVER['DOCUMENT_ROOT'];
                    require_once $root . '/modules/extensions/FormGenerator.php';
                    
                    echo generate_select_options("subjects", array("FullName"), "ID");    
                ?>
            </select></br></br>
            <label for="teacher-selector" class="label">Преподаватель (по расписанию)</label>
            <select id="teacher-selector">
                <option value="NULL">ОТСУТСТВУЕТ</option>
                <?php
                    $root = $_SERVER['DOCUMENT_ROOT'];
                    require_once $root . '/modules/extensions/FormGenerator.php';
                    
                    echo generate_select_options("teachers", array("Surname", "FirstName", "SecondName"), "ID");    
                ?>
            </select></br></br>
            <label for="room-selector" class="label">Кабинет (по расписанию)</label>
            <select id="room-selector">
                <option value="NULL">ОТСУТСТВУЕТ</option>
                <?php
                    $root = $_SERVER['DOCUMENT_ROOT'];
                    require_once $root . '/modules/parser/MySqliAdapter.php';

                    $adapter = new MySqliAdapter("127.0.0.1","cx19991_bdmobi","4vW1VaPf","cx19991_bdmobi");
                    $query = "SELECT * FROM `rooms`;";
                    $twoDimArr = $adapter->get_sql_query_result($query, MYSQLI_ASSOC);

                    $options = "";
                    foreach ($twoDimArr as $row)
                    {
                        if ($row['ID'] == -1) continue;
                        $options = $options.'<option value="'.$row['ID'].'">'.$row['Name'].'</option>';  
                    }
                    echo $options;
                ?>
            </select></br></br>
            <label for="status-selector" class="label">Статус изменения</label>
            <select id="status-selector">
                <option>Будет</option>
                <option>Отмена</option>
                <option>Перенос</option>
                <option>Замена кабинета</option>
                <option>Замена</option>
            </select></br></br>
            <label for="change-subgroup-selector" class="label">Подгруппа</label>
            <select id="change-subgroup-selector">
                <option value="NULL">Нет</option>
                <option value="1">1</option>
                <option value="2">2</option>
                <option value="3">3</option>
                <option value="4">4</option>
            </select></br></br>
            <span>Диапазон уроков по номеру</span></br>
            <label for="hour-change-selector-part1">Начало</label>
            <select id="hour-change-selector-part1">
                <option value="0">БЕЗ ИЗМЕНЕНИЙ</option>
                <option value="1">1</option>
                <option value="2">2</option>
                <option value="3">3</option>
                <option value="4">4</option>
                <option value="5">5</option>
                <option value="6">6</option>
                <option value="7">7</option>
                <option value="8">8</option>
                <option value="9">9</option>
                <option value="10">10</option>
                <option value="11">11</option>
                <option value="12">12</option>
            </select>
            <label for="hour-change-selector-part2">и конец</label>
            <select id="hour-change-selector-part2">
                <option value="0">БЕЗ ИЗМЕНЕНИЙ</option>
                <option value="1">1</option>
                <option value="2">2</option>
                <option value="3">3</option>
                <option value="4">4</option>
                <option value="5">5</option>
                <option value="6">6</option>
                <option value="7">7</option>
                <option value="8">8</option>
                <option value="9">9</option>
                <option value="10">10</option>
                <option value="11">11</option>
                <option value="12">12</option>
            </select></br></br>
            <label for="subject-change-selector" class="label">Предмет</label>
            <select id="subject-change-selector" class="form-select">
                <option value="NULL">БЕЗ ИЗМЕНЕНИЙ</option>
                <?php
                    $root = $_SERVER['DOCUMENT_ROOT'];
                    require_once $root . '/modules/extensions/FormGenerator.php';
                    
                    echo generate_select_options("subjects", array("FullName"), "ID");
                ?>
            </select></br></br>
            <label for="teacher-change-selector" class="label">Преподаватель</label>
            <select id="teacher-change-selector">
                <option value="NULL">БЕЗ ИЗМЕНЕНИЙ</option>
                <?php
                    $root = $_SERVER['DOCUMENT_ROOT'];
                    require_once $root . '/modules/extensions/FormGenerator.php';
                    
                    echo generate_select_options("teachers", array("Surname", "FirstName", "SecondName"), "ID");
                ?>
            </select></br></br>
            <label for="room-change-selector" class="label">Кабинет</label>
            <select id="room-change-selector">
                <option value="NULL">БЕЗ ИЗМЕНЕНИЙ</option>
                <?php
                    $root = $_SERVER['DOCUMENT_ROOT'];
                    require_once $root . '/modules/parser/MySqliAdapter.php';

                    $adapter = new MySqliAdapter("127.0.0.1","cx19991_bdmobi","4vW1VaPf","cx19991_bdmobi");
                    $query = "SELECT * FROM `rooms`;";
                    $twoDimArr = $adapter->get_sql_query_result($query, MYSQLI_ASSOC);

                    $options = "";
                    foreach ($twoDimArr as $row)
                    {
                        if ($row['ID'] == -1) continue;
                        $options = $options.'<option value="'.$row['ID'].'">'.$row['Name'].'</option>';  
                    }
                    echo $options;
                ?>
            </select>
            <div></div></br>
        </br></br>           
            <button type="submit" name="submit">Добавить</button>
        </form>
    </body>
    <style>
        .label {
            display: block;
        }
    </style>
    <script>
        document.querySelector('form').addEventListener('submit', function(e) {
            e.preventDefault();

            var date = document.querySelector('input[type="date"]').value;           
            var classX = document.querySelector('#class-selector').value;
            var subgroup = document.querySelector('#subgroup-selector').value;
            var hourPart1 = document.querySelector('#hour-selector-part1').value;
            var hourPart2 = document.querySelector('#hour-selector-part2').value;
            var subject = document.querySelector('#subject-selector').value;
            var teacher = document.querySelector('#teacher-selector').value;
            var room = document.querySelector('#room-selector').value;
            var status = document.querySelector('#status-selector').value;
            var subgroupCh = document.querySelector('#change-subgroup-selector').value;
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
            xhr.send('action=add' + '&teacher=' + encodeURIComponent(teacher) +
            '&date=' + encodeURIComponent(date) + '&class=' + encodeURIComponent(classX) +
            '&subgroup=' + encodeURIComponent(subgroup) + '&hourPart1=' + encodeURIComponent(hourPart1) +
            '&hourPart2=' + encodeURIComponent(hourPart2) + '&subject=' + encodeURIComponent(subject) +
            '&teacher=' + encodeURIComponent(teacher) + '&room=' + encodeURIComponent(room) +
            '&status=' + encodeURIComponent(status) + '&subgroupCh=' + encodeURIComponent(subgroupCh) +
            '&hourPart1Ch=' + encodeURIComponent(hourPart1Ch) + '&hourPart2Ch=' + encodeURIComponent(hourPart2Ch) +
            '&subjectCh=' + encodeURIComponent(subjectCh) + '&teacherCh=' + encodeURIComponent(teacherCh) +
            '&roomCh=' + encodeURIComponent(roomCh)
            );
            alert("Добавление успешно!");
            window.location.href = '/admin/changes_form/index_changes.php?date=' + encodeURIComponent(date);
        });
    </script>

<script>
 $(function(){
  $("#subject-selector").select2();
  $("#subject-change-selector").select2();
 }); 
</script>
</html>
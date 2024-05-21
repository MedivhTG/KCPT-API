<?php
require_once 'MySqliAdapter.php';

class ScheduleParser {
    public MySqliAdapter $adapter;
    public SimpleXMLElement $xml;
    public $defaultEmpty = -1;
   
    function __construct(string $servername, string $username, string $password, string $dbname){
        $this->adapter = new MySqliAdapter($servername, $username, $password, $dbname);
    }
    
    function __destruct(){
    }
    
    /**
     * Стирает все данные из таблиц текущей БД (исключая изменения).
     */
    function erase_all_data(){
        $this->adapter->delete_all_database_data(array("changes"));
    }
    
    /**
     * Конвертирует дату формата 'd.m.y' в формат 'Y-m-d'.
     * @param $date Дата формата 'd.m.Y'.
     * @return Дата формата 'Y-m-d'.
     */
    function convert_date_format(string $date) : string {
        $date_ts = DateTime::createFromFormat('d.m.Y', $date);
        $newDate = $date_ts->format('Y-m-d');
        return $newDate;
    }
    
    
    /**
     * Строит модель SimpleXML xml-документа.
     * $param $fileAddr Физический адрес файла.
     */  
    function xml_get(string $fileAddr){
        $this->xml = simplexml_load_file($fileAddr) or die("Error: cannot parse the xml object");
    }
    
    function  xml_export_table_classes(){
        $tableName = "classes";
        $columnNames = $this->adapter->get_column_names($tableName);
        
        $xmlClasses = $this->xml->classes->xpath('//class');
        
        $columnValues = array();
        for ($i = 0; $i < count($xmlClasses); ++$i){
            array_push($columnValues, $xmlClasses[$i]->id);
            array_push($columnValues, $xmlClasses[$i]->name);
        }
        
        $this->adapter->insert($tableName, $columnNames, $columnValues);
    }
    
    function xml_export_table_loads(){
        $tableName = "loads";
        $columnNames = $this->adapter->get_column_names($tableName);
        
        $xmlLoads = $this->xml->loads->xpath('//load');         
        
        $columnValues = array();
        for ($i = 0; $i < count($xmlLoads); ++$i){
            array_push($columnValues, $xmlLoads[$i]->id);
        }
        
        $this->adapter->insert($tableName, $columnNames, $columnValues);
    }
    
    function xml_export_table_loadsClasses(){
        $tableName = "loads-classes";
        $columnNames = $this->adapter->get_column_names($tableName, false);
        
        $xmlLoads = $this->xml->loads->xpath('//load');
        
        $columnValues = array();
        for ($i = 0; $i < count($xmlLoads); ++$i){
            $xmlKlassIdList = $xmlLoads[$i]->klass_id_list;    
            for ($j = 0; $j < count($xmlKlassIdList->int); ++$j){
                array_push($columnValues, $xmlLoads[$i]->id);
                array_push($columnValues, $xmlKlassIdList->int[$j]);
            }
        }
        
        $this->adapter->insert($tableName, $columnNames, $columnValues);
    }
    
    function xml_export_table_subjects(){
        $tableName = "subjects";
        $columnNames = $this->adapter->get_column_names($tableName);
        
        $xmlSubjects = $this->xml->subjects->xpath('//subject');
        
        $columnValues = array();
        for ($i = 0; $i < count($xmlSubjects); ++$i){
            array_push($columnValues, $xmlSubjects[$i]->id);
            array_push($columnValues, $xmlSubjects[$i]->full_name);
        }
        
        $this->adapter->insert($tableName, $columnNames, $columnValues);
    }
    
    function xml_export_table_rooms(){
        $tableName = "rooms";
        $columnNames = $this->adapter->get_column_names($tableName);
        
        $xmlRooms = $this->xml->rooms->xpath('//room');
        
        $columnValues = array();
        for ($i = 0; $i < count($xmlRooms); ++$i){
            array_push($columnValues, $xmlRooms[$i]->id);
            array_push($columnValues, $xmlRooms[$i]->name);
        }
        
        $this->adapter->insert($tableName, $columnNames, $columnValues);
        
        $rooms = $this->adapter->select_all_table_records($tableName);
        $nullRoomExists = false;
        foreach($rooms as $row){
              foreach($row as $column){
                 if ($column == $this->defaultEmpty){
                     $nullRoomExists = true;
                     goto ex;
                }
            }
        }
        ex: {}
        if ($nullRoomExists == false){
           $values = [$this->defaultEmpty, ""];
           $this->adapter->insert($tableName, $columnNames, $values);
        }
    }
    
    function xml_export_table_schedules(){
        $tableName = "schedules";
        $columnNames = $this->adapter->get_column_names($tableName, false);
        
        $xmlSchedules = $this->xml->scheds->xpath('//sched');
        $columnValues = array();
        for ($i = 0; $i < count($xmlSchedules); ++$i){
            array_push($columnValues, $xmlSchedules[$i]->day);
            array_push($columnValues, $xmlSchedules[$i]->hour);
            array_push($columnValues, $xmlSchedules[$i]->group);
            array_push($columnValues, $xmlSchedules[$i]->load_id);
            if ($xmlSchedules[$i]->room_id != -1){
                array_push($columnValues, $xmlSchedules[$i]->room_id);
            }
            else{
                array_push($columnValues, $this->defaultEmpty);
            }
            array_push($columnValues, $this->convert_date_format($xmlSchedules[$i]->begin_date));
            array_push($columnValues, $this->convert_date_format($xmlSchedules[$i]->end_date));
        }
        
        $this->adapter->insert($tableName, $columnNames, $columnValues);
    }
    
    function xml_export_table_teachers(){
        $tableName = "teachers";
        $columnNames = $this->adapter->get_column_names($tableName);
        
        $xmlTeachers = $this->xml->teachers->xpath('//teacher');
        $columnValues = array();
        for ($i = 0; $i < count($xmlTeachers); ++$i){
            array_push($columnValues, $xmlTeachers[$i]->person->id);
            array_push($columnValues, $xmlTeachers[$i]->person->surname);
            array_push($columnValues, $xmlTeachers[$i]->person->first_name);
            array_push($columnValues, $xmlTeachers[$i]->person->second_name);
        }
        
        $this->adapter->insert($tableName, $columnNames, $columnValues);
    }
    
    function xml_export_table_groups(){               
        $xmlLoads = $this->xml->loads->xpath('//load');
        
        $idGroupCounter = 0;
        for ($i = 0; $i < count($xmlLoads); ++$i){
            $xmlGroups = $xmlLoads[$i]->groups->children();
            $subgroup = 0;
            // if (count($xmlGroups) > 1) $subgroup = 1;
            // else $subgroup = 0;
            for ($j = 0; $j < count($xmlGroups); ++$j){
                if (isset($xmlGroups[$j]->id) == false){
                    $xmlLoads[$i]->groups->group[$j]->addChild("id", "{$idGroupCounter}");
                }
                if (isset($xmlGroups[$j]->subgroup) == false){
                    $xmlLoads[$i]->groups->group[$j]->addChild("subgroup", "{$subgroup}");
                }
                ++$idGroupCounter;
                ++$subgroup;
            }
        }
        
        //groups
        $tableName = "groups";
        $columnNames = $this->adapter->get_column_names($tableName);       
        $columnValues = array();
        for ($i = 0; $i < count($xmlLoads); ++$i){
            $xmlGroups = $xmlLoads[$i]->groups->children();
            for ($j = 0; $j < count($xmlGroups); ++$j){
                array_push($columnValues, $xmlGroups->group[$j]->id);
                array_push($columnValues, $xmlLoads[$i]->id);
                array_push($columnValues, $xmlGroups->group[$j]->teacher_id);
                array_push($columnValues, $xmlGroups->group[$j]->subject_id);
                array_push($columnValues, $xmlGroups->group[$j]->subgroup);
            }
        }       
        $this->adapter->insert($tableName, $columnNames, $columnValues);
    }

    function xml_export_all_tables()
    {
        $this->xml_export_table_classes();
        $this->xml_export_table_loads();
        $this->xml_export_table_loadsClasses();
        $this->xml_export_table_subjects();
        $this->xml_export_table_rooms();
        $this->xml_export_table_schedules();
        $this->xml_export_table_teachers();
        $this->xml_export_table_groups();
    }
 

    /**
     * Возвращает шаблон выборки данных по всем таблица по указнной дате.
     * @param $shortDate Дата в формате 'Y-m-d', пример -> 2023-03-12.
     * @return Часть SQL-запроса в виде строки.
     */
    function select_all_data_template(string $shortDate) : string{
        return "SELECT `classes`.`Name` AS `Группа`, "
               . "`groups`.`SubGroup` AS `Подгруппа`, "
               . "'{$shortDate}' AS `Дата`, "
               . "`schedules`.`Day` AS `День`, "
               . "`schedules`.`Hour` AS `Урок`, "
               . "`teachers`.`Surname` AS `Фамилия`, "
               . "`teachers`.`FirstName` AS `Имя`, "
               . "`teachers`.`SecondName` AS `Отчество`, "
               . "`subjects`.`FullName` AS `Предмет`, "
               . "`rooms`.`Name` AS `Кабинет` "
                . "FROM `schedules` "
                . "JOIN `loads` ON `schedules`.`LoadID` = `loads`.`ID` "
                . "JOIN `loads-classes` ON `loads`.`ID` = `loads-classes`.`LoadID` "
                . "JOIN `classes` ON `loads-classes`.`ClassID` = `classes`.`ID` "
                . "JOIN `groups` ON `loads`.`ID` = `groups`.`LoadID` "
                . "JOIN `teachers` ON `groups`.`TeacherID` = `teachers`.`ID` "
                . "JOIN `subjects` ON `groups`.`SubjectID` = `subjects`.`ID` "
                . "JOIN `rooms` ON `schedules`.`RoomID` = `rooms`.`ID` ";
    }
    
    /**
     * Осуществляет сортировку двумерного ассоциативного массива по заданному ключу.
     * @param $twodimArray Двумерный ассоциативный массив.
     * @param $key Ключ массива.
     * @return Двумерный ассоциативный массив.
     */
    function customSort(array $twodimArray, $key) : array {
            $additionalKeyArray = [];
            foreach ($twodimArray as $columnKey => $columnValue){
                $additionalKeyArray[$columnKey] = $columnValue[$key];
            }
            array_multisort($additionalKeyArray, SORT_ASC, $twodimArray);
        return $twodimArray;
    }

    function apply_changes2($twodimResult, string $date, string $changeOption="class") : array
    {
        $changes = $this->adapter->get_sql_query_result("SELECT * FROM `changes` WHERE `Date`='".$date."' ORDER BY `ClassID`, `Hour`, `SubGroup`;", MYSQLI_ASSOC);
        if (count($changes) <= 0) return $twodimResult;

        foreach ($changes as $row)
        {
            $className = $this->adapter->get_sql_query_result("SELECT `Name` FROM `classes` WHERE `ID`=".$row['ClassID'].";", MYSQLI_ASSOC)[0]['Name'];
            $subgroup = $row['SubGroup'];
            $subgroupCh = $row['SubGroupChange'];
            $subjectName = $this->adapter->get_sql_query_result("SELECT `FullName` FROM `subjects` WHERE `ID`=".$row['SubjectID'].";", MYSQLI_ASSOC)[0]['FullName'];
            $subjectNameCh = $this->adapter->get_sql_query_result("SELECT `FullName` FROM `subjects` WHERE `ID`=".$row['SubjectChangeID'].";", MYSQLI_ASSOC)[0]['FullName'];
            $teacher = $this->adapter->get_sql_query_result("SELECT `Surname`, `FirstName`, `SecondName` FROM `teachers` WHERE `ID`=".$row['TeacherID'].";", MYSQLI_ASSOC)[0];
            $surname = $teacher['Surname'];
            $firstName = $teacher['FirstName'];
            $secondName = $teacher['SecondName'];
            $teacherCh = $this->adapter->get_sql_query_result("SELECT `Surname`, `FirstName`, `SecondName` FROM `teachers` WHERE `ID`=".$row['TeacherChangeID'].";", MYSQLI_ASSOC)[0];
            $surnameCh = $teacherCh['Surname'];
            $firstNameCh = $teacherCh['FirstName'];
            $secondNameCh = $teacherCh['SecondName'];
            $roomName = $this->adapter->get_sql_query_result("SELECT `Name` FROM `rooms` WHERE `ID`=".$row['RoomID'].";", MYSQLI_ASSOC)[0]['Name'];
            $roomNameCh = $this->adapter->get_sql_query_result("SELECT `Name` FROM `rooms` WHERE `ID`=".$row['RoomChangeID'].";", MYSQLI_ASSOC)[0]['Name'];
            $status = $row['Status'];
            $hours = explode('-', $row['Hour']);
            $hoursRange = array();
            if (count($hours) > 1)
            {
                for ($i = $hours[0]; $i <= $hours[1]; ++$i)
                {
                    array_push($hoursRange, $i);
                }
            }
            $hoursCh = explode('-', $row['HourChange']);
            $hoursRangeCh = array();
            if (count($hoursCh) > 1)
            {
                for ($i = $hoursCh[0]; $i <= $hoursCh[1]; ++$i)
                {
                    array_push($hoursRangeCh, $i);
                }
            }

            if (count($hoursRange) <= 0)
            {               
                for ($i = 0; $i < count($twodimResult); ++$i)
                {
                    if ($twodimResult[$i]['Группа'] == $className && 
                    $twodimResult[$i]['Подгруппа'] == $subgroup && $twodimResult[$i]['Урок'] == $hours[0] && 
                    $twodimResult[$i]['Предмет'] == $subjectName && $twodimResult[$i]['Фамилия'] == $surname && 
                    $twodimResult[$i]['Имя'] == $firstName && $twodimResult[$i]['Отчество'] == $secondName && 
                    $twodimResult[$i]['Кабинет'] == $roomName)
                    {
                        switch($status){
                            case "Замена":
                                $twodimResult[$i]['Предмет'] = $subjectNameCh;
                                $twodimResult[$i]['Фамилия'] = $surnameCh;
                                $twodimResult[$i]['Имя'] = $firstNameCh;
                                $twodimResult[$i]['Отчество'] = $secondNameCh;
                                $twodimResult[$i]['Кабинет'] = $roomNameCh;
                                $twodimResult[$i]['Подгруппа'] = $subgroupCh;
                                break 2;
                            case "Замена кабинета":
                                $twodimResult[$i]['Кабинет'] = $roomNameCh;
                                break 2;
                            case "Перенос":
                                $twodimResult[$i]['Урок'] = $hoursCh[0];
                                break 2;
                            case "Отмена":
                                unset($twodimResult[$i]);
                                $twodimResult = array_values($twodimResult);
                                break 2;
                        }
                        $twodimResult[$i]['ИЗМЕНЕНИЕ'] = "Да";
                    }
                    else if ($status == "Будет" && $changeOption=="class")
                    {
                        $dayOfWeek = date('w', strtotime($date));
                        array_push($twodimResult, ["Группа" => $className, "Подгруппа" => $subgroupCh, 
                        "Дата" => $date, "День" => $dayOfWeek, "Урок" => $hoursCh[0], 
                        "Фамилия" => $surnameCh, "Имя" => $firstNameCh, "Отчество" => $secondNameCh, 
                        "Предмет" => $subjectNameCh, "Кабинет" => $roomNameCh, "ИЗМЕНЕНИЕ" => "Да"]);
                        break;
                    }
                    else if ($status == "Будет" && $changeOption=="teacher" && $twodimResult[0]['Фамилия'] == $surnameCh &&
                    $twodimResult[0]['Имя'] == $firstNameCh && $twodimResult[0]['Отчество'] == $secondNameCh)
                    {
                        $dayOfWeek = date('w', strtotime($date));
                        array_push($twodimResult, ["Группа" => $className, "Подгруппа" => $subgroupCh, 
                        "Дата" => $date, "День" => $dayOfWeek, "Урок" => $hoursCh[0], 
                        "Фамилия" => $surnameCh, "Имя" => $firstNameCh, "Отчество" => $secondNameCh, 
                        "Предмет" => $subjectNameCh, "Кабинет" => $roomNameCh, "ИЗМЕНЕНИЕ" => "Да"]);
                        break;
                    }
                    else $twodimResult[$i]['ИЗМЕНЕНИЕ'] = "Нет";
                }              
            }
            else
            {
                for($current = 0; $current < count($hoursRange); ++$current)
                {
                    for ($i = 0; $i < count($twodimResult); ++$i)
                    {
                        if ($twodimResult[$i]['Группа'] == $className && 
                        $twodimResult[$i]['Подгруппа'] == $subgroup && $twodimResult[$i]['Урок'] == $hoursRange[$current] && 
                        $twodimResult[$i]['Предмет'] == $subjectName && $twodimResult[$i]['Фамилия'] == $surname && 
                        $twodimResult[$i]['Имя'] == $firstName && $twodimResult[$i]['Отчество'] == $secondName && 
                        $twodimResult[$i]['Кабинет'] == $roomName)
                        {
                            switch($status){
                                case "Замена":
                                    $twodimResult[$i]['Предмет'] = $subjectNameCh;
                                    $twodimResult[$i]['Фамилия'] = $surnameCh;
                                    $twodimResult[$i]['Имя'] = $firstNameCh;
                                    $twodimResult[$i]['Отчество'] = $secondNameCh;
                                    $twodimResult[$i]['Кабинет'] = $roomNameCh;
                                    $twodimResult[$i]['Подгруппа'] = $subgroupCh;
                                    break 2;
                                case "Замена кабинета":
                                    $twodimResult[$i]['Кабинет'] = $roomNameCh;
                                    break 2;
                                case "Перенос":
                                    $twodimResult[$i]['Урок'] = $hoursRangeCh[$current];
                                    break 2;
                                case "Отмена":
                                    unset($twodimResult[$i]);
                                    $twodimResult = array_values($twodimResult);
                                    break 2;
                            }
                            $twodimResult[$i]['ИЗМЕНЕНИЕ'] = "Да";
                        }
                        else if ($status == "Будет" && $changeOption=="class")
                        {
                            $dayOfWeek = date('w', strtotime($date));
                            array_push($twodimResult, ["Группа" => $className, "Подгруппа" => $subgroupCh, 
                            "Дата" => $date, "День" => $dayOfWeek, "Урок" => $hoursRangeCh[$current], 
                            "Фамилия" => $surnameCh, "Имя" => $firstNameCh, "Отчество" => $secondNameCh, 
                            "Предмет" => $subjectNameCh, "Кабинет" => $roomNameCh, "ИЗМЕНЕНИЕ" => "Да"]);
                            break;
                        }
                        else if ($status == "Будет" && $changeOption=="teacher" && $twodimResult[0]['Фамилия'] == $surnameCh &&
                        $twodimResult[0]['Имя'] == $firstNameCh && $twodimResult[0]['Отчество'] == $secondNameCh)
                        {
                            $dayOfWeek = date('w', strtotime($date));
                            array_push($twodimResult, ["Группа" => $className, "Подгруппа" => $subgroupCh, 
                            "Дата" => $date, "День" => $dayOfWeek, "Урок" => $hoursCh[0], 
                            "Фамилия" => $surnameCh, "Имя" => $firstNameCh, "Отчество" => $secondNameCh, 
                            "Предмет" => $subjectNameCh, "Кабинет" => $roomNameCh, "ИЗМЕНЕНИЕ" => "Да"]);
                            break;
                        }
                        else $twodimResult[$i]['ИЗМЕНЕНИЕ'] = "Нет";
                    }
                }
            }          
        }

        $twodimResult = $this->customSort($twodimResult, "Урок");

        return $twodimResult;
    }
    
    /**
     * Возвращает двумерный массив данных, содержащий информацию о расписании на день с изменениями для конкретной группы (1 ранг - элементы расписания, 2 ранг - информация по элементу расписания: группа, подгруппа, дата, день, урок, фамилия, имя, отчество, предмет, кабинет).
     * @param string $shortDate Дата в формате 'Y-m-d', пример -> 2023-03-12.
     * @param $classFullName Название учебной группы.
     * @return Двумерный массив данных расписания.
     */
    function get_schedule_day_class(string $shortDate, int $id) : array
    {
       $day = date('w', strtotime($shortDate));
       $query = $this->select_all_data_template($shortDate)
               . "WHERE '{$shortDate}' BETWEEN `schedules`.`BeginDate` AND `schedules`.`EndDate` "
               . "AND `schedules`.`Day` = {$day} AND `classes`.`ID` = {$id} AND `schedules`.`Group` = `groups`.`SubGroup` "
               . "ORDER BY `schedules`.`Hour`, `schedules`.`Group`, `classes`.`Name`;";
        $twodimResult = $this->adapter->get_sql_query_result($query, MYSQLI_ASSOC);
        return $this->apply_changes2($twodimResult, $shortDate, "class");
    }
    
    /**
     * Возвращает трехмерный массив данных, содержащий информацию о расписании на неделю для конкретной группы (1 ранг - дни, 2 ранг - элементы расписания, 3 ранг - информация по элементу расписания: группа, подгруппа, дата, день, урок, фамилия, имя, отчество, предмет, кабинет).
     * @param string $shortDate Дата в формате 'Y-m-d', пример -> 2023-03-12. ВНИМАНИЕ: дата должна быть понедельником по календарю!
     * @param $classFullName Название учебной группы.
     * @return Трехмерный массив данных расписания.
     */
    function get_schedule_week_class(string $shortDate, int $id) : array
    {       
        if (date('w', strtotime($shortDate)) === '1'){
            $day = 1;
            $weekThreeDimArray = array();
            while($day < 7){
                array_push($weekThreeDimArray, $this->get_schedule_day_class($shortDate, $id));
                $shortDate = date('Y-m-d', strtotime($shortDate . ' +1 day'));
                ++$day;
            }
            return $weekThreeDimArray;
        }
        else{
            exit ("Параметр \"date\" не является ПОНЕДЕЛЬНИКОМ по календарю!");
        }
    }
    
    /**
     * Возвращает двумерный массив данных, содержащий информацию о расписании на день с изменениями для конкретного преподавателя (1 ранг - элементы расписания, 2 ранг - информация по элементу расписания: группа, подгруппа, дата, день, урок, фамилия, имя, отчество, предмет, кабинет).
     * @param string $shortDate Дата в формате 'Y-m-d', пример -> 2023-03-12.
     * @param string $surname Фамилия преподавателя.
     * @param string $firstName Имя преподавателя.
     * @param string $secondName Отчество преподавателя.
     * @return Двумерный массив данных расписания.
     */
    function get_schedule_day_teacher(string $shortDate, int $id) : array
    {
        $day = date('w', strtotime($shortDate));
        $query = $this->select_all_data_template($shortDate)
                . "WHERE '{$shortDate}' BETWEEN `schedules`.`BeginDate` AND `schedules`.`EndDate` "
                . "AND `schedules`.`Day` = {$day}  AND `schedules`.`Group` = `groups`.`SubGroup`"
                . "AND `teachers`.`ID` = {$id} "
                . "ORDER BY `schedules`.`Hour`, `schedules`.`Group`, `classes`.`Name`;";
        $twodimResult = $this->adapter->get_sql_query_result($query, MYSQLI_ASSOC);
        return $this->apply_changes2($twodimResult, $shortDate, "teacher");
    }
    
    /**
     * Возвращает трехмерный массив данных, содержащий информацию о расписании на неделю для конкретного преподавателя (1 ранг - дни, 2 ранг - элементы расписания, 3 ранг - информация по элементу расписания: группа, подгруппа, дата, день, урок, фамилия, имя, отчество, предмет, кабинет).
     * @param string $shortDate Дата в формате 'Y-m-d', пример -> 2023-03-12. ВНИМАНИЕ: дата должна быть понедельником по календарю!
     * @param string $surname Фамилия преподавателя.
     * @param string $firstName Имя преподавателя.
     * @param string $secondName Отчество преподавателя.
     * @return Трехмерный массив данных расписания.
     */
    function get_schedule_week_teacher(string $shortDate, int $id)
    {
        if (date('w', strtotime($shortDate)) === '1'){
            $day = 1;
            $weekThreeDimArray = array();
            while($day < 7){
                array_push($weekThreeDimArray, $this->get_schedule_day_teacher($shortDate, $id));
                $shortDate = date('Y-m-d', strtotime($shortDate . ' +1 day'));
                ++$day;
            }
            return $weekThreeDimArray;
        }
        else{
            exit ("Параметр \"date\" не является ПОНЕДЕЛЬНИКОМ по календарю!");
        }
    }
}

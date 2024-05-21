<?php

class MySqliAdapter {
    private mysqli $mysqliConnection;
    private string $dbname;
    
    function __construct(string $servername, string $username, string $password, string $dbname) {
        $this->mysqliConnection = new mysqli($servername, $username, $password, $dbname);
        $this->dbname = $dbname;
        mysqli_set_charset($this->mysqliConnection, "utf8");
    }
    
    function __destruct(){
    }
    
    /**
     * Возвращает данные по выборке.
     * @param $query SQL-запрос.
     * @return Одномерный ассоциативный массив данных со строгой последовательностью записей выборки.
     */
    private function get_select_result(string $query): array {
        $result = mysqli_query($this->mysqliConnection, $query);
        $twodimArray = mysqli_fetch_all($result, MYSQLI_ASSOC);
        $onedimArray = array();
        foreach ($twodimArray as $row){
            foreach ($row as $column){
                array_push($onedimArray, $column);
            }
        }
        return $onedimArray;
    }
    
    /**
     * Возвращает имена таблица в текущей БД.
     * @return Одномерный ассоциативный массив данных с именами таблиц.
     */   
    function get_table_names(): array {
        $query = "SELECT table_name FROM information_schema.tables WHERE table_schema = '{$this->dbname}';";
        $tableNames = $this->get_select_result($query);
        return $tableNames;
    }
    
    /**
     * Возвращает имена атрибутов по имени таблицы.
     * @param $tableName Имя таблицы.
     * @param $includeAI Флаг включения в выборку атрибутов с автоинкрементацией, true - включать, false - не включать. 
     * @return Одномерный ассоциативный массив данных с именами колонок выбраной таблицы.
     */
    function get_column_names(string $tableName, bool $includeAI = true): array {
        $query = "";
        if ($includeAI == false){
            $query = "SELECT column_name FROM information_schema.columns WHERE table_schema = '{$this->dbname}' AND table_name = '{$tableName}' AND extra NOT LIKE '%auto_increment%';";
        }
        else{
            $query = "SELECT column_name FROM information_schema.columns WHERE table_schema = '{$this->dbname}' AND table_name = '{$tableName}';";
        }
   
        $columnNames = $this->get_select_result($query);
        return $columnNames;
    }
    
    /**
     * Выполняет SQL-запрос, который не возвращает данные (например: UPDATE, DELETE, TRUNCATE...).
     * @param $query SQL-запрос.
     */
    function edit(string $query){
        mysqli_query($this->mysqliConnection, $query);
    }
    
    /**
     * Возвращает последовательность атрибутов в виде части SQL-запроса.
     * @param $columns Массив атрибутов таблицы.
     * @return Строчное представление части SQL-запроса.
     */
    private function get_enum_string_columns(array $columns) : string{
        $result =  "(";
        for ($i = 0; $i < count($columns); ++$i){
            if ($i != count($columns) - 1) {
                $result = "{$result}`{$columns[$i]}`,";
                continue;                
            }
            elseif ($i == count($columns) - 1) {
                $result = "{$result}`{$columns[$i]}`";    
                continue;
                }
        }
        $result = "{$result})";
        return $result;
    }
    
    /**
     * Возвращает последовательность значений атрибутов в виде части SQL-запроса,
     * их может быть больше чем атрибутов в таблице, но обязательно кратно им по количеству.
     * @param $values Массив значений атрибутов.
     * @param $rowSplitVal Целочисленное значение, указывающее на сколько групп нужно поделить значения для построение строчного SQL-запроса: 0 - не делить, 1 и более - деление на данное кол-во.
     * @return Строчное представление части SQL-запроса.
     */
    private function get_enum_string_values(array $values, int $rowSplitVal) : string{
        $result = "";
        if ($rowSplitVal == 0){ //if no splitting
            $result = "(";
            for ($i = 0; $i < count($values); ++$i){
                if ($i != count($values) - 1){
                    $result = "{$result}'{$values[$i]}',";
                    continue;
                }
                elseif ($i == count($values) - 1) {
                    $result = "{$result}'{$values[$i]}'";    
                    continue;
                }
            }
            $result = "{$result})";
        }
        elseif (count($values) % $rowSplitVal == 0){ //if splitting
            
            if ($rowSplitVal == 1){ //if splitting in one
                for ($i = 0; $i < count($values); ++$i){
                    if ($i == count($values) - 1){
                        $result = "{$result}('{$values[$i]}')";
                    }
                    else {
                        $result = "{$result}('{$values[$i]}'), ";
                    }
                }
            }
            else { //splitting multiple
            for ($i = 0; $i < count($values); ++$i){
                    if ($i == 0){
                        $result = "{$result}(";
                    }              
                
                    if (($i+1) % $rowSplitVal != 0) {
                        $result = "{$result}'{$values[$i]}',";
                    }
                    elseif ((($i+1) % $rowSplitVal == 0) && ($i != (count($values) - 1))){ 
                        $result = "{$result}'{$values[$i]}'), (";
                    }
                    else{
                        $result = "{$result}'{$values[$i]}')";
                    }
                }
            }
        }
        return $result;
    }
    
    /**
     * Осуществляет вставку данных в виде атрибутов и их значений в таблицу по ее имени.
     * @param $tableName Имя таблицы.
     * @param $columns Имена атрибутов.
     * @param $values Значения атрибутов.
     */
    function insert(string $tableName, array $columns, array $values){
        if (count($columns) == count($values)) {
            $this->edit("INSERT INTO `{$tableName}` {$this->get_enum_string_columns($columns)} VALUES {$this->get_enum_string_values($values, 0)};");
        }
        elseif ((count($values) % count($columns)) == 0) {
            $this->edit("INSERT INTO `{$tableName}` {$this->get_enum_string_columns($columns)} VALUES {$this->get_enum_string_values($values, count($columns))};");           
        }
        else{
            $columnsCount = count($columns);
            $valuesCount = count($values);
            exit("Error: количество колонок и соответсвующих значений не эквивалентны или не кратны друг другу (колонки - {$columnsCount}, значения - {$valuesCount})");
        }
    }
    
    /**
     * @todo NOT YET IMPLEMENTED!
     */
    function update() {
    }
    
    /**
     * @todo NOT YET IMPLEMENTED!
     */
    function delete() {
    }
    
     /**
     * Удаляет все данные из таблицы, при этом откатывая атрибуты с автоинкрементацией к начальному индексу.
     * @param $tableName Имя таблицы.
     */
    function delete_all_table_records(string $tableName){
        $this->edit("TRUNCATE TABLE `{$tableName}`;");
    }
    
    /**
     * Удаляет все данные из БД, игнорируя внешние ключи.
     * @param $exceptions Имена таблиц, которые следует исключить при удалении данных.
     */
    function delete_all_database_data(array $exceptions){
        $tableNames = $this->get_table_names();
        $this->edit("SET FOREIGN_KEY_CHECKS = 0;");
        foreach ($tableNames as $name) {
            if(!in_array($name, $exceptions)) $this->delete_all_table_records($name);
        }
        $this->edit("SET FOREIGN_KEY_CHECKS = 1;");
    }
    
    /**
     * Возвращает выборку данных по SQL-запросу.
     * @param $query SQL-запрос.
     * @param $fetchMode Способ компоновоки данных выборки в двумерном массиве: ассоциативный, обычный, оба варианта.
     * @return Двумерный массив данных выборки.
     */
    function get_sql_query_result(string $query, int $fetchMode = MYSQLI_NUM){
        $result = mysqli_query($this->mysqliConnection, $query);
        $twodimArray = mysqli_fetch_all($result, $fetchMode);
        return $twodimArray;
    }
    
    /**
     * Возвращает выборку данных по всем атрибутам по имени таблицы.
     * @param $tableName Имя таблицы.
     * @return Двумерный массив данных выборки.
     */
    function select_all_table_records(string $tableName){
        $query = "SELECT * FROM `{$tableName}`;";
        return $this->get_sql_query_result($query);
    }
}

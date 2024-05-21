<?php
    function generate_select_options(string $dbTable, array $columns, string $keyColumn = null) : string
    {
        foreach ($columns as $element)
        {
            if (gettype($element) !== "string") throw new Exception("Element type of columns array was not string");
        }
        $root = $_SERVER['DOCUMENT_ROOT'];
        require_once $root . '/modules/parser/MySqliAdapter.php';

        $query = "SELECT * FROM $dbTable;";
        $adapter = new MySqliAdapter("127.0.0.1","cx19991_bdmobi","4vW1VaPf","cx19991_bdmobi");
        $twoDimArr = $adapter->get_sql_query_result($query, MYSQLI_ASSOC);
        $options = "";
        foreach ($twoDimArr as $row)
        {
            $columnsVals = "";
            $arrLen = count($columns);
            for ($i = 0; $i < $arrLen; ++$i)
            {
                if ($i == $arrLen-1) $columnsVals = $columnsVals." ".$row[$columns[$i]];
                else $columnsVals = $columnsVals.$row[$columns[$i]]." ";
            }
            if ($keyColumn != null) $options = $options.'<option value="'.$row[$keyColumn].'">'.$columnsVals.'</option>';
            else $options = $options.'<option>'.$columnsVals.'</option>';               
        }
        return $options;
    }
?>
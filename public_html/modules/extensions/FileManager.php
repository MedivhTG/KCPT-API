<?php
function erase_all_files_except_new_one(string $dirPath)
{
    $resources = glob($dirPath."/*");
    $datetimeArray = array();
    foreach ($resources as $resource) {
        if (is_file($resource)) {
            array_push($datetimeArray, new DateTime(date("Y-m-d H:i:s", filemtime($resource))));
        }
    }
    rsort($datetimeArray);
    if (count($resources) > 1)
    {
        for ($i = 0; $i < count($resources); $i++)
        {
            if ($datetimeArray[0] != new DateTime(date("Y-m-d H:i:s", filemtime($resources[$i]))))
            {
                unlink($resources[$i]);
            }				
        }
    }
}

?>
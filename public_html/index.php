<!doctype html>
<html>
    <head>
        <title>КЦПТ - Расписание</title>
        <linkClass rel="stylesheet" href="style/main.css">
    </head>
    <body>   
        <a href="admin/xml/index_xml.php" class="button">Перейти на страницу загрузки xml-документа</a></br></br>      
        <a href="admin/changes_form/index_changes.php?date=<?php echo date('Y-m-d');?>" class="button">Перейти на страницу изменения расписания</a></br></br></br></br>
        <input type="date" value="<?php echo date('Y-m-d'); ?>" id="date"></date></br></br>
        <a href="schedule_forms/class.php" class="button" id="class">Расписание для группы</a></br></br>       
        <a href="schedule_forms/teacher.php" class="button">Расписание для преподавателя</a>

        <script>
            var dateInput = document.getElementById("date");
            var linkClass = document.getElementById("class");

            linkClass.addEventListener("click", 
                function(event) {
                event.stopImmediatePropagation();
                var dateValue = dateInput.value;
                var linkHref = "schedule_forms/class.php?date=" + dateValue;
                linkClass.setAttribute("href", linkHref);
            });
        </script>
    </body>
</html>
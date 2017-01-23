<?php

    include("connectionStrings.php");
    

    insert_query("INSERT INTO testtable (value) VALUES ('2222')");
    insert_queryE("INSERT INTO testtable (value) VALUES ('1111')", "testtable", 1);
    
    $result = select_query("SELECT * FROM testtable");
    while ($row = mysql_fetch_assoc($result)) {
        echo $row['value'] . "<br>";
    }
        
?>
<?php

    include("conn2.php");
    

    //insert_query("INSERT INTO testtable (value) VALUES ('2222')");
    //insert_queryE("INSERT INTO testtable (value) VALUES ('1111')", "testtable", 1);
    
    $result = select_queryE("SELECT * FROM testtable WHERE key = '1'",1);
    while ($row = mysql_fetch_assoc($result)) {
        echo $row['key'] . "<br>";
    }
        
?>
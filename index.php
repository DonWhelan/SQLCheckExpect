<?php

    include("connectionStrings.php");
    
    $result = select_queryE("SELECT * FROM testtable",4);
    while ($row = mysql_fetch_assoc($result)) {
        echo $row['value'] . "<br>";
    }
    
    
        
?>
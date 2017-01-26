<?php
    function connectionCredentials(){
        define("HOST", "");
        define("USER", "");
        define("PASS", "");
        define("DB", "");
    }
    
    function selectConnectionCredentials(){
        define("HOST", "");
        define("USER", "");
        define("PASS", "");
        define("DB", "");
    }
    
    function connectionString(){
        global $connection;
        $connection = mysql_connect(HOST, USER, PASS);
        if (!$connection) {
            trigger_error("Could not reach database!<br/>");
            include("logs/logsMail-1dir.php");
            exit();
        }
        $db_selected = mysql_select_db(DB,$connection);
        if (!$db_selected) {
            trigger_error("Could not reach database!<br/>");
            include("logs/logsMail-1dir.php");
            exit();
        }    
    }
    
    function select_queryE($select_query,$expectedResult) {
        selectConnectionCredentials();
        connectionString();
        $result = mysql_query($select_query); 
        $numRows = mysql_num_rows($result); 
        if (! $result){
            echo('Database error: ' . mysql_error());
            exit;
        }        
        if($numRows != $expectedResult){
            include("logs/logsMail.php");
            return $result;
            mysql_close($connection);
            exit;
        } 
        
        //Warning: mysql_close() expects parameter 1 to be resource, null given in /home/ubuntu/workspace/connectionStrings.php on line 58
        mysql_close($connection);
        // var_dump($connection);  /home/ubuntu/workspace/connectionStrings.php:185:null
        return $result;
    }
    
?>
<?php

    function selectConnectionString(){
        define("sHOST", "sHOST");
        define("sUSER", "sUSER");
        define("sPASS", "sPASS");
        define("sDB", "sDB");
        $connection = mysqli_connect(sHOST, sUSER, sPASS);
        if (!$connection) {
            trigger_error("Could not reach database!<br/>");
            include("logs/logsMail-1dir.php");
            exit();
        }
        $db_selected = mysqli_select_db($connection, sDB);
        if (!$db_selected) {
            trigger_error("Could not reach database!<br/>");
            include("logs/logsMail-1dir.php");
            exit();
        } 
        return $connection;
    }
    
    function insertConnectionString(){
        define("iHOST", "iHOST.ie");
        define("iUSER", "iUSER");
        define("iPASS", "iPASS");
        define("iDB", "iDB");
        $connection = mysqli_connect(iHOST, iUSER, iPASS);
        if (!$connection) {
            trigger_error("Could not reach database!<br/>");
            include("logs/logsMail-1dir.php");
            exit();
        }
        $db_selected = mysqli_select_db($connection, iDB);
        if (!$db_selected) {
            trigger_error("Could not reach database!<br/>");
            include("logs/logsMail-1dir.php");
            exit();
        } 
        return $connection;
    }
    
    function updateConnectionString(){
        define("uHOST", "uHOST.ie");
        define("uUSER", "uUSER");
        define("uPASS", "uPASS");
        define("uDB", "uDB");
        $connection = mysqli_connect(uHOST, uUSER, uPASS);
        if (!$connection) {
            trigger_error("Could not reach database!<br/>");
            include("logs/logsMail-1dir.php");
            exit();
        }
        $db_selected = mysqli_select_db($connection, uDB);
        if (!$db_selected) {
            trigger_error("Could not reach database!<br/>");
            include("logs/logsMail-1dir.php");
            exit();
        } 
        return $connection;
    }
    
    function deleteConnectionString(){
        define("dHOST", "dHOST.ie");
        define("dUSER", "dUSER");
        define("dPASS", "dPASS");
        define("dDB", "dDB");
        $connection = mysqli_connect(dHOST, dUSER, dPASS);
        if (!$connection) {
            trigger_error("Could not reach database!<br/>");
            include("logs/logsMail-1dir.php");
            exit();
        }
        $db_selected = mysqli_select_db($connection, dDB);
        if (!$db_selected) {
            trigger_error("Could not reach database!<br/>");
            include("logs/logsMail-1dir.php");
            exit();
        } 
        return $connection;
    }    
     
    function escape_data($dataFromForms) {
        if (function_exists('mysql_real_escape_string')) {
            $dataFromForms = mysqli_real_escape_string (trim($dataFromForms), $connection);
            $dataFromForms = strip_tags($dataFromForms);
        } else {
            $dataFromForms = mysqli_escape_string (trim($dataFromForms));
            $dataFromForms = strip_tags($dataFromForms);
        }
        return $dataFromForms;
    }
    
    function get_client_ip_env() {
        $ipaddress = '';
        if (getenv('HTTP_CLIENT_IP'))
            $ipaddress = getenv('HTTP_CLIENT_IP');
        else if(getenv('HTTP_X_FORWARDED_FOR'))
            $ipaddress = getenv('HTTP_X_FORWARDED_FOR');
        else if(getenv('HTTP_X_FORWARDED'))
            $ipaddress = getenv('HTTP_X_FORWARDED');
        else if(getenv('HTTP_FORWARDED_FOR'))
            $ipaddress = getenv('HTTP_FORWARDED_FOR');
        else if(getenv('HTTP_FORWARDED'))
            $ipaddress = getenv('HTTP_FORWARDED');
        else if(getenv('REMOTE_ADDR'))
            $ipaddress = getenv('REMOTE_ADDR');
        else
            $ipaddress = 'UNKNOWN';
        return $ipaddress;
    }
    
    function select_sqli($select_query) {
        $connection = selectConnectionString();
        $queryresult = mysqli_query($connection, $select_query); 
        if (! $queryresult){
            echo('Database error: ' . mysqli_error($connection));
            exit;
        }
        mysqli_close($connection);
        return $queryresult;
    }
    
    function select_sqliLog($select_query,$expectedResult) {
        $connection = selectConnectionString();
        $queryresult = mysqli_query($connection, $select_query); 
        $numRows = mysqli_affected_rows($connection);
        if (! $queryresult){
            echo('Database error: ' . mysqli_error($connection));
            exit;
        }   
        if($numRows != $expectedResult){
            include("logs/logsMail.php");
        }
        mysqli_close($connection);
        return $queryresult;
    }
    
    function select_sqliLogMax($select_query,$maxResult) {
        $connection = selectConnectionString();
        $queryresult = mysqli_query($connection, $select_query); 
        $numRows = mysqli_affected_rows($connection);
        if (! $queryresult){
            echo('Database error: ' . mysqli_error($connection));
            exit;
        }   
        if($numRows > $maxResult){
            include("logs/logsMail.php");
        }
        mysqli_close($connection);
        return $queryresult;
    }
    
    function select_sqliTransaction($select_query,$expectedResult) {
        $connection = selectConnectionString();
        mysqli_autocommit($connection,FALSE);
        mysqli_query($connection,"start transaction");
        $queryresult = mysqli_query($connection, $select_query); 
        $numRows = mysqli_affected_rows($connection);
        if (! $queryresult){
            echo('Database error: ' . mysqli_error($connection));
            exit;
        }   
        if($numRows != $expectedResult){
            include("logs/logsMail.php");
            mysqli_query($connection,"rollback");
        }else{
            mysqli_query($connection,"commit");
        }
        mysqli_close($connection);
        return $queryresult;
    }
    
    function insert_sqli($insert_query) {
        $connection = insertConnectionString();
        $queryresult = mysqli_query($connection, $insert_query) 
        or die(mysqli_error($connection));
        mysqli_close($connection);
        return $queryresult;

    }
    
    function insert_sqliLog($insert_query, $table, $expectedResult) {
        $connection = insertConnectionString();
        
        $sql = "Select * FROM $table";
        $result = mysqli_query($connection,$sql); 
        $rowsBefore = mysqli_num_rows($result);
        
        $queryresult = mysqli_query($connection, $insert_query) 
        or die(mysqli_error($connection));
        $affectedRows = mysqli_affected_rows($connection);
        
        $sql = "Select * FROM $table";
        $result = mysqli_query($connection,$sql); 
        $rowsAfter = mysqli_num_rows($result);
         
        if($rowsBefore != ($rowsAfter-$expectedResult) || $affectedRows != $expectedResult){
            include("logs/logsMail.php");
        }
        mysqli_close($connection);
        return $queryresult;
    }
    
    function insert_sqliLog($insert_query, $table, $expectedResult) {
        $connection = insertConnectionString();
        
        $sql = "Select * FROM $table";
        $result = mysqli_query($connection,$sql); 
        $rowsBefore = mysqli_num_rows($result);
        
        $queryresult = mysqli_query($connection, $insert_query) 
        or die(mysqli_error($connection));
        $affectedRows = mysqli_affected_rows($connection);
        
        $sql = "Select * FROM $table";
        $result = mysqli_query($connection,$sql); 
        $rowsAfter = mysqli_num_rows($result);
         
        if($rowsBefore != ($rowsAfter-$expectedResult) || $affectedRows != $expectedResult){
            include("logs/logsMail.php");
        }
        mysqli_close($connection);
        return $queryresult;
    }
    
    function insert_sqliTransaction($insert_query, $table, $expectedResult) {
        $connection = insertConnectionString();
        mysqli_autocommit($connection,FALSE);
        mysqli_query($connection,"start transaction"); 
        
        $sql = "Select * FROM $table";
        $result = mysqli_query($connection,$sql); 
        $rowsBefore = mysqli_num_rows($result);
        
        $queryresult = mysqli_query($connection, $insert_query) 
        or die(mysqli_error($connection));
        $affectedRows = mysqli_affected_rows($connection);
        
        $sql = "Select * FROM $table";
        $result = mysqli_query($connection,$sql); 
        $rowsAfter = mysqli_num_rows($result);
         
        if($rowsBefore != ($rowsAfter-$expectedResult) || $affectedRows != $expectedResult){
            include("logs/logsMail.php");
            mysqli_query($connection,"rollback");
        }else{
            mysqli_query($connection,"commit");
        }
        mysqli_close($connection);
        return $queryresult;
    }
    
    function update_sqli($update_query){
        $connection = updateConnectionString();
        $queryresult = mysqli_query($connection, $update_query)
        or die(mysqli_error($connection));
        mysqli_close($connection);
        return $queryresult;
    }
    
    function update_sqliLog($update_query, $table, $expectedResult){
        $connection = updateConnectionString();
        
        $sql = "Select * FROM $table";
        $result = mysqli_query($connection,$sql); 
        $rowsBefore = mysqli_num_rows($result);
        
        $queryresult = mysqli_query($connection, $update_query) 
        or die(mysqli_error($connection));
        $affectedRows = mysqli_affected_rows($connection);
        
        $sql = "Select * FROM $table";
        $result = mysqli_query($connection,$sql); 
        $rowsAfter = mysqli_num_rows($result);
        
        if(($rowsBefore != $rowsAfter) || ($affectedRows != $expectedResult)){
            if($affectedRows != 0){
                include("logs/logsMail.php");
            }   
        }
        mysqli_close($connection);
        return $queryresult;
    }
    
    function update_sqliTransaction($update_query, $table, $expectedResult){
        $connection = updateConnectionString();
        mysqli_autocommit($connection,FALSE);
        mysqli_query($connection,"start transaction");         
        
        $sql = "Select * FROM $table";
        $result = mysqli_query($connection,$sql); 
        $rowsBefore = mysqli_num_rows($result);
        
        $queryresult = mysqli_query($connection, $update_query) 
        or die(mysqli_error($connection));
        $affectedRows = mysqli_affected_rows($connection);
        
        $sql = "Select * FROM $table";
        $result = mysqli_query($connection,$sql); 
        $rowsAfter = mysqli_num_rows($result);
        
        if(($rowsBefore != $rowsAfter) || ($affectedRows != $expectedResult)){
            if($affectedRows != 0){
                include("logs/logsMail.php");
                mysqli_query($connection,"rollback");
            }else{
                mysqli_query($connection,"commit");
            }    
        }else{
            mysqli_query($connection,"commit");
        }
        mysqli_close($connection);
        return $queryresult;
    }    
    
    function delete_sqli($delete_query){
        $connection = deleteConnectionString();
        $queryresult = mysqli_query($connection, $delete_query)
        or die(mysqli_error($connection));
        mysqli_close($connection);
        return $queryresult;
    }
    
    function delete_sqliLog($delete_query, $table, $expectedResult){
        $connection = deleteConnectionString();
        
        $sql = "Select * FROM $table";
        $result = mysqli_query($connection,$sql); 
        $rowsBefore = mysqli_num_rows($result);
        
        $queryresult = mysqli_query($connection, $delete_query) 
        or die(mysqli_error($connection));
        $affectedRows = mysqli_affected_rows($connection);
        
        $sql = "Select * FROM $table";
        $result = mysqli_query($connection,$sql); 
        $rowsAfter = mysqli_num_rows($result);
        
        if(($rowsBefore != ($rowsAfter + $expectedResult)) || ($affectedRows != $expectedResult)){
            if($affectedRows != 0){
                include("logs/logsMail.php");
            }    
        }
        mysqli_close($connection);
        return $queryresult;
    }
    
    function delete_sqliTransaction($delete_query, $table, $expectedResult){
        $connection = deleteConnectionString();
        mysqli_autocommit($connection,FALSE);
        mysqli_query($connection,"start transaction");         
        
        $sql = "Select * FROM $table";
        $result = mysqli_query($connection,$sql); 
        $rowsBefore = mysqli_num_rows($result);
        
        $queryresult = mysqli_query($connection, $delete_query) 
        or die(mysqli_error($connection));
        $affectedRows = mysqli_affected_rows($connection);
        
        $sql = "Select * FROM $table";
        $result = mysqli_query($connection,$sql); 
        $rowsAfter = mysqli_num_rows($result);
        
        if(($rowsBefore != ($rowsAfter + $expectedResult)) || ($affectedRows != $expectedResult)){
            if($affectedRows != 0){
                include("logs/logsMail.php");
                mysqli_query($connection,"rollback");
            }else{
                mysqli_query($connection,"commit");
            }    
        }else{
            mysqli_query($connection,"commit");
        }
        mysqli_close($connection);
        return $queryresult;
    }
    
?>

<?php

    /* --------------- This file containes the connection details to SQL server ----------------------------
     * We create a escape_data() function that scrubs user input of unwanted characters
     * We also have a get_client_ip_env() function that returns the clients IP address
     * -----------------------------------------------------------------------------------------------------
     */
    
    // DB details are defined as constants rather than variables, to stop values from being altered.
    
    function selectConnectionString(){
        //Permission to SELECT and LOCK
        define("sHOST", "dublinscoffee.ie");
        define("sUSER", "dubli653_SELECT");
        define("sPASS", "Password1");
        define("sDB", "dubli653_ncirl");
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
        //Permission to SELECT LOCK and INSERT
        define("iHOST", "dublinscoffee.ie");
        define("iUSER", "dubli653_INSERT");
        define("iPASS", "Password1");
        define("iDB", "dubli653_ncirl");
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
        //Permission to SELECT LOCK and UPDATE
        define("uHOST", "dublinscoffee.ie");
        define("uUSER", "dubli653_UPDATE");
        define("uPASS", "Password1");
        define("uDB", "dubli653_ncirl");
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
        //Permission to SELECT LOCK and DELETE
        define("dHOST", "dublinscoffee.ie");
        define("dUSER", "dubli653_DELETE");
        define("dPASS", "Password1");
        define("dDB", "dubli653_ncirl");
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
    
    /*
     *  --escape_data function strips text that is being sent to the DB of harmful tags and characters --
     *
     *  mysql_real_escape_string() is a more secure method of scrubbing data so we check if it is available, as it rely's on a connection to the DB
     *  If available we trim() to remove whitespace, then put pass through the mysql_real_escape_string() to address the threat of SQL injection.
     *  The data is then run through strip_tags() to remove HTML tags like "<script>" to address XSS attacks.
     *
     *  If mysql_real_escape_string() in unavailable we do the same steps but using mysql_escape_string().
     *
     *  !This function should be used for all text sent to the DB or files on the web/file directory!
     *  //ref: http://www.newthinktank.com/2011/01/php-security/
     */
     
    function escape_data($dataFromForms) {
        if (function_exists('mysql_real_escape_string')) {
            //global $connection;
            $dataFromForms = mysqli_real_escape_string (trim($dataFromForms), $connection);
            $dataFromForms = strip_tags($dataFromForms);
        } else {
            $dataFromForms = mysqli_escape_string (trim($dataFromForms));
            $dataFromForms = strip_tags($dataFromForms);
        }
        return $dataFromForms;
    }
    
    //gets client IP - ref: https://www.virendrachandak.com/techtalk/getting-real-client-ip-address-in-php-2/
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
    
    /* ---------------------  SELECT  ---------------------------------*/
    // ------- select_sqli()
    // $result = select_sqli("SELECT * FROM testtable where value=101");
    // while ($row = mysqli_fetch_assoc($result)) {
    //     echo $row['key'] . "<br>";
    // }
    
    // ------- select_sqliLog()
    // $result = select_sqliLog("SELECT * FROM testtable where value=101", 2);
    // while ($row = mysqli_fetch_assoc($result)) {
    //     echo $row['key'] . "<br>";
    // }
    
    // ------- select_sqliTransaction()
    // $result = select_sqliTransaction("SELECT * FROM testtable where value=101", 2);
    // while ($row = mysqli_fetch_assoc($result)) {
    //     echo $row['key'] . "<br>";
    // }

    /* ---------------------  INSERT  ---------------------------------*/
    // ------ insert_sqli()
    // insert_sqli("INSERT INTO testtable (value) VALUES ('1001')");
    
    // ------ insert_sqliLog()
     //insert_sqliLog("INSERT INTO testtable (value) VALUES ('1002')","testtable",2);

    // ------ insert_sqliTransaction()
     //insert_sqliTransaction("INSERT INTO testtable (value) VALUES ('1003')","testtable",2);
    
    
    /* ---------------------  UPDATE  ---------------------------------*/
    // ------ update_sqli()    
    // update_query("UPDATE testtable SET value=105 WHERE value=106");
    
    // ------ update_sqliLog()  
    // update_queryE("UPDATE testtable SET value=105 WHERE value=106","testtable",1);
    
    // ------ update_sqliTransaction() 
    // update_queryE("UPDATE testtable SET value=105 WHERE value=106","testtable",1);
    
    /* ---------------------  Delete  ---------------------------------*/   
    // ------ delete_sqli()  
    // delete_query("DELETE FROM testtable WHERE value=106");
    
    // ------ delete_sqliLog()  
    // delete_queryE("DELETE FROM testtable WHERE value=104","testtable",1);

    // ------ delete_sqliTransaction()  
    // delete_queryE("DELETE FROM testtable WHERE value=104","testtable",1);
    
    
    
    
    
?>
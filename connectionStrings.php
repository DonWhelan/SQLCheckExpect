<?php

    /* --------------- This file containes the connection details to SQL server ----------------------------
     * We create a escape_data() function that scrubs user input of unwanted characters
     * We also have a get_client_ip_env() function that returns the clients IP address
     * -----------------------------------------------------------------------------------------------------
     */
    
    // DB details are defined as constants rather than variables, to stop values from being altered.

    
    function connectionCredentials(){
        define("HOST", "dublinscoffee.iess");
        define("USER", "dubli653_dib");
        define("PASS", "0u.ipTVc)zpq");
        define("DB", "dubli653_ncirl");
        //echo "a";
    }
    
    function selectConnectionCredentials(){
        define("HOST", "dublinscoffee.ie");
        define("USER", "dubli653_dib");
        define("PASS", "0u.ipTVc)zpq");
        define("DB", "dubli653_ncirl");
        //echo "b";
    }
    
    function insertConnectionCredentials(){
        define("HOST", "dublinscoffee.ie");
        define("USER", "dubli653_dib");
        define("PASS", "0u.ipTVc)zpq");
        define("DB", "dubli653_ncirl");
        //echo "c";
    }
    
    function connectionString(){
        //mysql_query() only allows one query to be sent to the DB, and not mutible
        //global $connection;
        $connection = mysqli_connect(HOST, USER, PASS);
        if (!$connection) {
            trigger_error("Could not reach database!<br/>");
            include("logs/logsMail-1dir.php");
            exit();
        }
        $db_selected = mysqli_select_db($connection, DB);
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
    
    function select_query($select_query) {
        selectConnectionCredentials();
        $connection = connectionString();
        $result = mysqli_query($connection, $select_query); 
        $numRows = mysqli_num_rows($result); 
        if (! $result){
            echo('Database error: ' . mysqli_error());
            exit;
        }
        //echo "rows: " . $numRows . "<br>";
        mysqli_close($connection);
        return $result;
    }
    
    function select_queryE($select_query,$expectedResult) {
        selectConnectionCredentials();
        $connection = connectionString();
        mysqli_autocommit($connection,FALSE);
        mysqli_query($connection,"start transaction");
        $result = mysqli_query($connection, $select_query); 
        $numRows = mysqli_affected_rows($connection);
        echo $numRows;
        if (! $result){
            echo('Database error: ' . mysqli_error());
            exit;
        }   
        if($numRows != $expectedResult){
            include("logs/logsMail.php");
            mysqli_query($connection,"rollback");
        }else{
            mysqli_query($connection,"commit");
        }
        mysqli_close($connection);
        return $result;
    }
    
    function insert_query($insert_query) {
        insertConnectionCredentials();
        //connectionString;
        $connection = connectionString();
        mysqli_query($connection, $insert_query) 
        or die(mysqli_error($connection));
        mysqli_close($connection);

    }
    
    function insert_queryE($insert_query, $table, $expectedResult) {
        insertConnectionCredentials();
        $connection = connectionString();
        mysqli_autocommit($connection,FALSE);
        mysqli_query($connection,"start transaction"); 
        
        $sql = "Select * FROM $table";
        $result = mysqli_query($connection,$sql); 
        $rowsBefore = mysqli_num_rows($result);
        
        mysqli_query($connection, $insert_query) 
        or die(mysqli_error($connection));
        $affectedRows = mysqli_affected_rows($connection);
        
        $sql = "Select * FROM $table";
        $result = mysqli_query($connection,$sql); 
        $rowsAfter = mysqli_num_rows($result);

        /*
         * if anything other than $expectedResult rows being added happens 
         * we log a security incedent and email alert the admin, and rollback the previous transaction
         */
         
        if($rowsBefore != ($rowsAfter-$expectedResult) && $affectedRows != $expectedResult){
            include("logs/logsMail.php");
            mysqli_query($connection,"rollback");
        }else{
            mysqli_query($connection,"commit");
        }
        mysqli_close($connection);
    }
    
    

    /* SELECT */
    // $result = select_query("SELECT * FROM testtable WHERE value = 2222");
    // while ($row = mysqli_fetch_assoc($result)) {
    //     echo $row['key'] . "<br>";
    // }
    
    /* SELECT EXACT */
    // $result = select_queryE("SELECT * FROM testtable",5);
    // while ($row = mysqli_fetch_assoc($result)) {
    //     echo $row['key'] . "<br>";
    // }

    /* INSERT */
    //insert_query("INSERT INTO testtable (value) VALUES ('104')");
    
    /* INSERT Exact */
    //insert_queryE("INSERT INTO testtable (value) VALUES ('104')","testtable",1);

?>
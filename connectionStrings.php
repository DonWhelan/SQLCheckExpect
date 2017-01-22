<?php

    /* --------------- This file containes the connection details to SQL server ----------------------------
     * We create a escape_data() function that scrubs user input of unwanted characters
     * We also have a get_client_ip_env() function that returns the clients IP address
     * -----------------------------------------------------------------------------------------------------
     */
    
    // DB details are defined as constants rather than variables, to stop values from being altered.
     
    $multibleCredentials = false;
    
    function connectionCredentials(){
        define("HOST", "dublinscoffee.ie");
        define("USER", "dubli653_dib");
        define("PASS", "0u.ipTVc)zpq");
        define("DB", "dubli653_ncirl");
    }
    
    function selectConnectionCredentials(){
        define("HOST", "dublinscoffee.ie");
        define("USER", "dubli653_dib");
        define("PASS", "0u.ipTVc)zpq");
        define("DB", "dubli653_ncirl");
    }
    
    function connectionString(){
        //mysql_query() only allows one query to be sent to the DB, and not mutible
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
            global $connection;
            $dataFromForms = mysql_real_escape_string (trim($dataFromForms), $connection);
            $dataFromForms = strip_tags($dataFromForms);
        } else {
            $dataFromForms = mysql_escape_string (trim($dataFromForms));
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
        if($multibleCredentials){
            selectConnectionCredentials();
        }else{
            connectionCredentials();
        }    
        connectionString();
        $result = mysql_query($select_query); 
        if (! $result){
            echo('Database error: ' . mysql_error());
            exit;
        }
        return $result;
    }
    
    function select_queryE($select_query,$expectedResult) {
        if($multibleCredentials){
            selectConnectionCredentials();
        }else{
            connectionCredentials();
        } 
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
        return $result;
    }
    
?>
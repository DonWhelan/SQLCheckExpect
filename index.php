<?php

    include("conn2.php");
    

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
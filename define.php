<?php

function a(){
    define("HOST", "dublinscoffee.ie");
    global $test;
    $test = "kkkk";
}
function b(){
    //global $host;
     define("HOST", "dublinssssscoffee.ie");
     return HOST;
}

function c(){
    a();
    echo HOST;
    echo $test;
}

c();
?>
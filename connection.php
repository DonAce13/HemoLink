<?php

    $database= new mysqli("192.168.100.156","root","","HemoLink_Database");
    if ($database->connect_error){
        die("Connection failed:  ".$database->connect_error);
    }

?>
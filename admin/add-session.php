<?php

    session_start();

    if(isset($_SESSION["user"])){
        if(($_SESSION["user"])=="" or $_SESSION['usertype']!='a'){
            header("location: ../login.php");
        }

    }else{
        header("location: ../login.php");
    }
    
    
    if($_POST){
        //import database
        include("../connection.php");
        $title=$_POST["title"];
        $docid=$_POST["docid"];
        $nop=$_POST["nop"];
        $date=$_POST["date"];
        $time=$_POST["time"];
        $session_duration=$_POST["session_duration"];
        $end_time=$_POST["end_time"];
        $sql="insert into schedule (docid,title,scheduledate,scheduletime,nop,session_duration,end_time) values ($docid,'$title','$date','$time',$nop,$session_duration,'$end_time');";
        $result= $database->query($sql);
        header("location: schedule.php?action=session-added&title=$title");
        
    }


?>
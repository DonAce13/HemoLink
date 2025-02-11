<?php

    //learn from w3schools.com

    session_start();

    if(isset($_SESSION["user"])){
        if(($_SESSION["user"])=="" or $_SESSION['usertype']!='p'){
            header("location: ../login.php");
        }else{
            $useremail=$_SESSION["user"];
        }

    }else{
        header("location: ../login.php");
    }
    

    //import database
    include("../connection.php");
    $sqlmain= "select * from patient where pemail=?";
    $stmt = $database->prepare($sqlmain);
    $stmt->bind_param("s",$useremail);
    $stmt->execute();
    $userrow = $stmt->get_result();
    $userfetch=$userrow->fetch_assoc();
    $userid= $userfetch["pid"];
    $username=$userfetch["pname"];


    if($_POST){
        if(isset($_POST["booknow"])){
            $apponum = $_POST["apponum"];
            $scheduleid = $_POST["scheduleid"];
            $date = $_POST["date"];
            $scheduletime = $_POST["scheduletime"];
            $is_self = $_POST["is_self"];
            $other_patient_name = $_POST["other_patient_name"];
            $description = $_POST["description"];
            $philhealth_id = $_POST["philhealth_id"];
            $age = $_POST["age"];
            $status = 'scheduled';
            $is_confirmed = 0;

            $sql2 = "INSERT INTO appointment (pid, apponum, scheduleid, appodate, scheduletime, is_self, other_patient_name, description, philhealth_id, age, status, is_confirmed) \
                     VALUES ($userid, $apponum, $scheduleid, '$date', '$scheduletime', $is_self, '$other_patient_name', '$description', '$philhealth_id', $age, '$status', $is_confirmed)";

            if ($database->query($sql2) === TRUE) {
                header("location: appointment.php?action=booking-added&id=".$apponum."&titleget=none");
            } else {
                echo "Error: " . $sql2 . "<br>" . $database->error;
            }
        }
    }
 ?>
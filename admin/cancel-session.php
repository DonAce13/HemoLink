<?php

session_start();

if(isset($_SESSION["user"])) {
    if(($_SESSION["user"])=="" or $_SESSION['usertype']!='a') {
        header("location: ../login.php");
    }
} else {
    header("location: ../login.php");
}

if($_GET) {
    // Import database connection
    include("../connection.php");
    
    // Get the schedule ID from URL
    $id = $_GET["id"];
    
    // Fetch the session details to archive it
    $sql = $database->query("SELECT * FROM schedule WHERE scheduleid='$id'");
    
    if($sql->num_rows > 0) {
        // Fetch session data
        $row = $sql->fetch_assoc();
        $scheduleid = $row['scheduleid'];
        $docid = $row['docid'];
        $title = $row['title'];
        $scheduledate = $row['scheduledate'];
        $scheduletime = $row['scheduletime'];
        $nop = $row['nop'];
        
        // Insert data into archived_schedule (soft delete)
        $archive_sql = "INSERT INTO archived_schedule (scheduleid, docid, title, scheduledate, scheduletime, nop, deleted_at) 
                        VALUES ('$scheduleid', '$docid', '$title', '$scheduledate', '$scheduletime', '$nop', NOW())";
        $database->query($archive_sql);
        
        // After archiving the session, delete it from the schedule table
        $delete_sql = $database->query("DELETE FROM schedule WHERE scheduleid='$id'");
        
        // Redirect to the schedule page
        header("location: schedule.php");
    } else {
        // If the session doesn't exist in the schedule, redirect to the schedule page
        header("location: schedule.php");
    }
}

?>

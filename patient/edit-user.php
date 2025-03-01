<?php
// Import database
include("../connection.php");

if ($_POST) {
    // Check for all required fields
    if (empty($_POST['name']) || empty($_POST['email']) || empty($_POST['phone_number']) || empty($_POST['current_password'])) {
        $error = '3'; // Set error for missing fields
    } else {
        // Proceed with processing the form
        $name = $_POST['name'];
        $oldemail = $_POST['oldemail'];
        $address = $_POST['address'];
        $email = $_POST['email'];
        $phone_number = $_POST['phone_number'];
        $password = $_POST['password'];
        $cpassword = $_POST['cpassword'];
        $current_password = $_POST['current_password'];
        $id = $_POST['id00'];

        if ($password == $cpassword) {
            // Check if user exists and validate current password
            $sql = "SELECT ppassword FROM patient WHERE pid = ?";
            $stmt = $database->prepare($sql);
            $stmt->bind_param("i", $id);
            $stmt->execute();
            $result = $stmt->get_result();
            $row = $result->fetch_assoc();

            if (password_verify($current_password, $row['ppassword'])) {
                // Update patient details
                $sql1 = "UPDATE patient SET pemail=?, pname=?, ppassword=?, phone_number=?, paddress=? WHERE pid=?";
                $stmt = $database->prepare($sql1);
                $stmt->bind_param("ssssi", $email, $name, $password, $phone_number, $address, $id);
                $stmt->execute();

                // Update webuser email
                $sql1 = "UPDATE webuser SET email=? WHERE email=?";
                $stmt = $database->prepare($sql1);
                $stmt->bind_param("ss", $email, $oldemail);
                $stmt->execute();
                $error = '4';
            } else {
                // Handle incorrect current password
                echo '<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>';
                echo '<script>Swal.fire({
                    title: "Error!",
                    text: "Current password is incorrect.",
                    icon: "error",
                    confirmButtonText: "OK"
                });</script>';
            }
        } else {
            $error = '2'; // Passwords do not match
        }
    }
} // Removed else statement to avoid setting error 3 unnecessarily

header("location: settings.php?action=edit&error=" . $error . "&id=" . $id);
?>
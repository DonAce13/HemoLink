<?php
// Start the session to check for user login
session_start();

if (isset($_SESSION["user"])) {
    if ($_SESSION["user"] == "" || $_SESSION['usertype'] != 'p') {
        header("location: ../login.php");
        exit;
    }
} else {
    header("location: ../login.php");
    exit;
}

// Include the database connection file
include("../connection.php");  // Make sure this path is correct

// Assuming $patientEmail is fetched from the database
$query = "SELECT pemail, pname FROM patient WHERE pemail = ?";  // Use 'pemail' as per the patient table schema

// Prepare a secure query using prepared statements
$stmt = $database->prepare($query);
$stmt->bind_param("s", $_SESSION["user"]);
$stmt->execute();
$result = $stmt->get_result();

// Check if query was successful and fetch the email and name
if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $patientEmail = $row['pemail']; // Retrieve the patient's email
    $patientName = $row['pname'];   // Retrieve the patient's name
} else {
    // Handle the case where no patient is found
    echo "Error: Patient email not found in the database.";
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/png" href="../img/bg01.png">
    <link rel="icon" type="image/png" href="../img/bg01.png">
    <link rel="shortcut icon" type="image/png" href="../img/bg01.png">
    <link rel="stylesheet" href="../css/animations.css">  
    <link rel="stylesheet" href="../css/main.css">  
    <link rel="stylesheet" href="../css/admin.css">
        
    <title>Dashboard</title>
    <style>
        .dashbord-tables {
            animation: transitionIn-Y-over 0.5s;
        }
        /* --- Mobile Sticky Hamburger Header --- */
        @media (max-width: 992px) {
            #mobile-hamburger-header {
                display: block !important;
                position: sticky;
                top: 0;
                left: 0;
                width: 100vw;
                height: 54px;
                background: #2d6a4f;
                z-index: 2000;
                box-shadow: 0 2px 8px rgba(0,0,0,0.09);
            }
            .hamburger {
                position: fixed;
                top: 8px;
                left: 18px;
                z-index: 2100;
                background: #fff;
                border-radius: 8px;
                box-shadow: 0 2px 8px rgba(0,0,0,0.09);
                padding: 8px 10px;
                display: flex;
                flex-direction: column;
                align-items: center;
                justify-content: center;
                width: 46px;
                height: 38px;
                transition: box-shadow 0.2s;
            }
            .hamburger .bar {
                width: 28px;
                height: 4px;
                margin: 3px 0;
                background: #2d6a4f;
                border-radius: 2px;
                transition: all 0.3s;
            }
            .menu {
                position: fixed;
                top: 54px;
                left: 0;
                width: 75vw;
                max-width: 320px;
                height: 100vh;
                background: #fff;
                box-shadow: 2px 0 8px rgba(0,0,0,0.08);
                z-index: 2050;
                transform: translateX(-100%);
                transition: transform 0.3s;
                opacity: 0;
                pointer-events: none;
            }
            .menu.show {
                transform: translateX(0);
                opacity: 1;
                pointer-events: auto;
                background: #40916c !important;
            }
        }
        @media (min-width: 993px) {
            #mobile-hamburger-header {
                display: none !important;
            }
            .hamburger {
                display: none !important;
            }
            .menu {
                position: static;
                transform: none;
                opacity: 1;
                pointer-events: auto;
                box-shadow: none;
                max-width: none;
                height: auto;
            }
        }
        /* --- End Mobile Sticky Hamburger Header --- */
        .filter-container {
            animation: transitionIn-Y-bottom 0.5s;
        }
        .sub-table, .anime {
            animation: transitionIn-Y-bottom 0.5s;
        }

        /* New styles for sessions layout */
        .sessions-container {
            display: flex;
            flex-direction: column;
            gap: 20px;
            padding: 20px;
        }

        .days-row {
            display: flex;
            flex-wrap: wrap;
            justify-content: space-between;
            gap: 10px;
            margin-bottom: 20px;
        }

        .day-sessions {
            flex: 1;
            min-width: 200px;
            background-color: #ffffff;
            border-radius: 12px;
            box-shadow: 0 6px 12px rgba(0,0,0,0.1);
            overflow: hidden;
            transition: all 0.3s ease;
            border: 1px solid #e0e0e0;
        }

        .day-sessions:hover {
            transform: translateY(-10px);
            box-shadow: 0 12px 20px rgba(0,0,0,0.15);
            border-color: #2d6a4f;
        }

        .day-header {
            background-color: #2d6a4f;
            color: white;
            padding: 15px 20px;
            margin: 0;
            text-align: center;
            font-weight: bold;
            font-size: 1.1rem;
            letter-spacing: 0.5px;
        }

        .sessions-table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
        }

        .sessions-table thead {
            background-color: #f4f4f4;
        }

        .sessions-table th {
            padding: 12px;
            text-align: center;
            font-size: 0.9em;
            color: #2d6a4f;
            border-bottom: 2px solid #e0e0e0;
        }

        .sessions-table td {
            padding: 12px;
            border-bottom: 1px solid #e0e0e0;
            text-align: center;
            font-size: 0.9em;
            transition: background-color 0.3s ease;
        }

        .sessions-table tr:hover td {
            background-color: #f0f0f0;
        }

        .sessions-table tr:nth-child(even) {
            background-color: #f9f9f9;
        }

        .sessions-table .login-btn {
            width: 100%;
            padding: 8px 12px;
            font-size: 0.9em;
            transition: all 0.3s ease;
        }

        .sessions-table .login-btn.book-now {
            background-color: #2d6a4f;
            color: white;
        }

        .sessions-table .login-btn.book-now:hover {
            background-color: #1f4d37;
            transform: translateY(-2px);
        }

        .sessions-table .login-btn.session-full {
            background-color: #6c757d;
            color: white;
            cursor: not-allowed;
        }

        .sessions-container {
            padding: 20px;
            background-color: #f8f9fa;
            border-radius: 15px;
        }

        .days-row {
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
            justify-content: center;
        }

        @media (max-width: 1200px) {
            .days-row {
                flex-direction: column;
            }

            .day-sessions {
                width: 100%;
            }
            
        }

        @media (max-width: 768px) {
            .sessions-table {
                font-size: 0.8em;
            }

            .sessions-table th, 
            .sessions-table td {
                padding: 8px;
            }

            .day-header {
                padding: 10px 15px;
                font-size: 1rem;
            }
            .calendar-container {
                max-width: 500px !important;
                margin: 0 auto;
                padding: 4.5px;
                background-color: #f9f9f9;
                border-radius: 10px;
                box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        }

        }

        /* Next appointment section styles */
        .next-appointment-container {
            background-color: #f9f9f9;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
            margin-top: 20px;
            overflow: hidden;
            transition: transform 0.3s ease;
            width: 100%;
        }

        .next-appointment-container:hover {
            transform: scale(1.02);
        }

        .next-appointment-header {
            background-color: #2d6a4f;
            color: white;
            padding: 10px 20px;
            text-align: center;
        }

        .next-appointment-title {
            font-size: 1em;
            font-weight: bold;
            margin: 0;
        }

        .next-appointment-body {
            padding: 15px;
            display: flex;
            align-items: center;
            flex-wrap: wrap;
        }

        .next-appointment-details {
            display: flex;
            align-items: center;
            width: 100%;
            gap: 15px;
        }

        .appointment-number {
            flex-shrink: 0;
        }

        .appointment-number .badge {
            background-color: #2d6a4f;
            color: white;
            padding: 10px 15px;
            border-radius: 5px;
            font-weight: bold;
            font-size: 1.2em;
        }

        .appointment-info {
            flex-grow: 1;
            min-width: 0;
        }

        .appointment-info h4 {
            margin: 0 0 5px 0;
            color: #333;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .appointment-info p {
            margin: 0 0 5px 0;
            color: #666;
        }

        .appointment-info small {
            color: #888;
            font-size: 0.9em;
        }

        .no-appointments {
            text-align: center;
            padding: 20px;
            background-color: #f9f9f9;
            border-radius: 10px;
            color: #666;
        }

        /* Responsive adjustments */
        @media (max-width: 768px) {
            .next-appointment-details {
                flex-direction: column;
                text-align: center;
                gap: 10px;
            }

            .appointment-number {
                margin-bottom: 10px;
            }

            .appointment-number .badge {
                font-size: 1em;
                padding: 8px 12px;
            }

            .appointment-info h4 {
                font-size: 0.9em;
            }

            .appointment-info p,
            .appointment-info small {
                font-size: 0.8em;
            }
        }

        @media (max-width: 480px) {
            .next-appointment-body {
                padding: 10px;
            }

            .appointment-number .badge {
                font-size: 0.9em;
                padding: 6px 10px;
            }
        }

        /* Enhanced Responsive Upcoming Booking Table Styles */
        .abc.scroll {
            max-height: 600px;
            overflow-y: auto;
            overflow-x: hidden;
            padding: 10px;
        }

        .sub-table.scrolldown {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
            margin-bottom: 20px;
        }

        .sub-table.scrolldown thead {
            position: sticky;
            top: 0;
            background-color: #f9f9f9;
            z-index: 10;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }

        .sub-table.scrolldown .table-headin {
            background-color: #2d6a4f;
            color: white;
            padding: 12px;
            text-align: center;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .sub-table.scrolldown tbody tr {
            transition: background-color 0.3s ease;
            border-bottom: 1px solid #e0e0e0;
        }

        .sub-table.scrolldown tbody tr:nth-child(even) {
            background-color: #f5f5f5;
        }

        .sub-table.scrolldown tbody tr:hover {
            background-color: #e0e0e0;
        }

        /* Responsive Table Design */
        @media (max-width: 768px) {
            .abc.scroll {
                max-height: 500px;
                padding: 5px;
            }

            .sub-table.scrolldown {
                font-size: 0.9em;
            }

            .sub-table.scrolldown thead {
                position: static;
            }

            .sub-table.scrolldown td, 
            .sub-table.scrolldown th {
                padding: 12px 8px;
                text-align: center;
            }

            /* Card-like Mobile Layout */
            .sub-table.scrolldown thead,
            .sub-table.scrolldown tbody,
            .sub-table.scrolldown tr,
            .sub-table.scrolldown td,
            .sub-table.scrolldown th {
                display: block;
                width: 100%;
            }

            .sub-table.scrolldown tr {
                background-color: #fff;
                border: 1px solid #e0e0e0;
                border-radius: 8px;
                margin-bottom: 15px;
                box-shadow: 0 2px 4px rgba(0,0,0,0.05);
                overflow: hidden;
            }

            .sub-table.scrolldown thead tr {
                display: none;
            }

            .sub-table.scrolldown tr td {
                border: none;
                position: relative;
                padding: 10px 15px;
                text-align: right;
                display: flex;
                justify-content: space-between;
                align-items: center;
            }

            .sub-table.scrolldown tr td:before {
                content: attr(data-label);
                font-weight: bold;
                color: #2d6a4f;
                text-align: left;
                flex-grow: 1;
                padding-right: 10px;
            }
            #calendar-grid div:hover {
                transform: translateY(-3px); /* Move up slightly on hover */
                transition: transform 0.2s ease; /* Smooth transition */
                background-color:rgb(141, 0, 14);
            }

            .sub-table.scrolldown td:nth-of-type(1):before { content: "Appoint. Number"; }
            .sub-table.scrolldown td:nth-of-type(2):before { content: "Session TANGF INA MO Title"; }
            .sub-table.scrolldown td:nth-of-type(3):before { content: "Doctor"; }
            .sub-table.scrolldown td:nth-of-type(4):before { content: "Scheduled Date & Time"; }
            .sub-table.scrolldown td:nth-of-type(5):before { content: "Booked For"; }
        }

        @media (max-width: 480px) {
            .sub-table.scrolldown {
                font-size: 0.8em;
            }

            .sub-table.scrolldown tr {
                margin-bottom: 10px;
            }

            .sub-table.scrolldown td, 
            .sub-table.scrolldown th {
                padding: 8px 10px;
            }

            .sub-table.scrolldown tr td:before {
                font-size: 0.9em;
            }
        }

        /* No Bookings Enhanced Styles */
        .no-bookings-container {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 40px 20px;
            text-align: center;
            background-color: #f9f9f9;
            border-radius: 10px;
            gap: 20px;
        }

        .no-bookings-container img {
            max-width: 200px;
            width: 50%;
            margin-bottom: 20px;
        }

        .no-bookings-container .heading-main12 {
            color: #2d6a4f;
            font-size: 1.2em;
            margin-bottom: 20px;
        }

        .no-bookings-container .login-btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 12px 24px;
            background-color: #2d6a4f;
            color: white;
            text-decoration: none;
            border-radius: 6px;
            transition: all 0.3s ease;
            font-size: 1em;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        }

        .no-bookings-container .login-btn:hover {
            background-color: #1f4d37;
            transform: translateY(-2px);
            box-shadow: 0 6px 8px rgba(0,0,0,0.15);
        }

        /* Appointment Status Styling */
        .appointment-status {
            display: inline-block;
            margin-top: 10px;
            padding: 5px 10px;
            border-radius: 4px;
            font-size: 0.8em;
            font-weight: bold;
        }

        .status-approved {
            background-color: #28a745;
            color: white;
        }

        .status-pending {
            background-color: #ffc107;
            color: #212529;
        }

        .status-unknown {
            background-color: #6c757d;
            color: white;
        }

        /* Added CSS for booking-full button state */
        .booking-full {
            background-color: #6c757d;
            color: white;
            cursor: not-allowed;
            opacity: 0.7;
        }

        .booking-full:hover {
            background-color: #6c757d;
            transform: none;
            box-shadow: none;
        }

        /* Calendar Styles */
        #calendar-container {
            max-width: 1400px;
            margin: 0 auto;
            padding: 20px;
            background-color: #f9f9f9;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        }

        #calendar {
            height: 500px;
        }

        .fc-event {
            cursor: pointer;
            background-color: #2d6a4f;
            border-color: #2d6a4f;
        }

        #sessions-modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            overflow: auto;
            background-color: rgba(0,0,0,0.4);
        }

        .sessions-modal-content {
            background-color: #fefefe;
            margin: 15% auto;
            padding: 20px;
            border: 1px solid #888;
            width: 80%;
            max-width: 600px;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0,0,0,0.2);
        }

        .sessions-modal-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 1px solid #e0e0e0;
            padding-bottom: 10px;
        }

        .sessions-modal-close {
            color: #aaa;
            font-size: 28px;
            font-weight: bold;
            cursor: pointer;
        }

        .sessions-modal-body {
            margin-top: 20px;
        }

        .no-sessions-message {
            text-align: center;
            color: #6c757d;
            padding: 20px;
        }
        #calendar-container {
            background-color: #f9f9f9;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
            padding: 20px;
        }

        #calendar {
            max-width: 100%;
            height: 500px;
        }

        .fc-event {
            cursor: pointer;
            background-color: #2d6a4f !important;
            border-color: #2d6a4f !important;
        }

        .fc-day-today {
            background-color: #e6f3e6 !important;
        }

        .sessions-table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
        }

        .sessions-table thead {
            background-color: #f4f4f4;
        }

        .sessions-table th, .sessions-table td {
            padding: 10px;
            border-bottom: 1px solid #e0e0e0;
            text-align: center;
        }

        .btn-book {
            background-color: #2d6a4f;
            color: white;
            border: none;
            padding: 8px 15px;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        .btn-book:hover {
            background-color: #1f4d37;
        }

        .btn-book-disabled {
            background-color: #6c757d;
            color: white;
            cursor: not-allowed;
        }
        
        /* Enhanced Calendar Styles */
        .calendar-container {
            background-color: #f8f9fa;
            border-radius: 15px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
            transition: all 0.3s ease;
        }
        .calendar-header {
            background-color: #2d6a4f;
            color: white;
            padding: 15px;
            border-top-left-radius: 15px;
            border-top-right-radius: 15px;
        }
        .calendar-grid div {
            transition: all 0.3s ease;
        }
        .calendar-grid div:hover {
            transform: translateY(-3px); /* Move up slightly on hover */
            transition: transform 0.2s ease; /* Smooth transition */
        }
        .calendar-grid div.has-session {
            background-color: #e6f3e6 !important;
            color: #2d6a4f !important;
            font-weight: bold;
        }
        .calendar-grid div.no-session {
            color: #cccccc;
            cursor: default;
        }

        #calendar-grid {
            display: grid;
            grid-template-columns: repeat(7, 1fr);
            gap: 4px;
            text-align: center;
            min-height: 320px;
            background: #fff;
            border-radius: 0 0 15px 15px;
            padding: 10px 0 20px 0;
        }
        /* Day coloring: Sunday to Saturday */
        #calendar-grid div:nth-child(7n+1) { background: #e3f2fd !important; }   /* Sunday - Light Blue */
        #calendar-grid div:nth-child(7n+2) { background: #bbdefb !important; }   /* Monday - Blue */
        #calendar-grid div:nth-child(7n+3) { background: #fce4ec !important; }   /* Tuesday - Pink */
        #calendar-grid div:nth-child(7n+4) { background: #fff9c4 !important; }   /* Wednesday - Yellow */
        #calendar-grid div:nth-child(7n+5) { background: #c8e6c9 !important; }   /* Thursday - Green */
        #calendar-grid div:nth-child(7n+6) { background: #ffe0b2 !important; }   /* Friday - Orange */
        #calendar-grid div:nth-child(7n+7) { background: #ede7f6 !important; }   /* Saturday - Purple */

        .calendar-legend {
            display: flex;
            flex-wrap: wrap;
            gap: 15px;
            align-items: center;
            justify-content: center;
            margin-top: 16px;
            font-size: 15px;
            width: 100%;
            text-align: center;
        }
        @media (max-width: 600px) {
            .calendar-legend {
                flex-direction: column;
                gap: 8px;
                font-size: 13px;
            }
            .calendar-legend span {
                margin-bottom: 2px;
            }
        }
        .calendar-legend span {
            display: inline-block;
            width: 22px;
            height: 22px;
            vertical-align: middle;
            margin-right: 6px;
            border-radius: 4px;
            border: 1px solid #ccc;
        }

        .cancel-button {
            transition: transform 0.2s ease; /* Smooth transition */
        }

        .cancel-button:hover {
            transform: translateY(-5px); /* Move up slightly on hover */
            background-color:rgb(158, 0, 24)!important;
        }

        @media (max-width: 768px) {
            .next-appointment-container {
                width: 320px; /* Adjust width for smaller screens */
                margin: 0 auto; /* Center the container */
            }
        }

        @media (max-width: 600px) {
            .custom-session-table {
                overflow-x: auto;
                -webkit-overflow-scrolling: touch;
                max-width: 100vw !important;
            }
            .custom-session-table table {
                min-width: 350px !important;
                width: 100% !important;
                font-size: 0.85rem !important;
            }
            .custom-session-table th,
            .custom-session-table td {
                padding: 4px 6px !important;
                font-size: 0.85rem !important;
                white-space: normal !important;
                text-align: left !important;
            }
            .custom-session-table th {
                font-size: 0.9rem !important;
            }
            .login-btn {
                font-size: 0.85rem !important;
                padding: 6px 8px !important;
                min-width: 80px;
                border-radius: 6px !important;
                text-align: center;
                display: inline-block;
            }
            .custom-session-table .book-now,
            .custom-session-table .session-full {
                width: 100% !important;
                min-width: 0 !important;
                font-size: 0.85rem !important;
                padding: 6px 8px !important;
                border-radius: 6px !important;
            }
        }

        @media (max-width: 600px) {
            .swal2-popup.custom-swal-popup {
                width: 100vw !important;
                max-width: 100vw !important;
                padding: -2px !important;
            }
        }

        @media (max-width: 600px) {
            .table-responsivecustom-session-table{
                padding: 10px !important;
                font-size: 0.95rem !important;
                min-width: 0 !important;
                width: 100% !important;
                box-sizing: border-box;
                margin-bottom: 8px;
                display: block;
                text-align: center;
            }
        }

        @media (max-width: 900px) {
            /* iOS-style header for small screens */
            .ios-header {
                position: fixed;
                top: 0;
                left: 0;
                width: 100vw;
                height: 56px;
                background: #f1f1f1;
                display: flex;
                align-items: center;
                z-index: 1002;
                box-shadow: 0 2px 8px rgba(0,0,0,0.04);
                border-bottom: 1px solid #e0e0e0;
            }
            .ios-header-title {
                font-size: 1.1rem;
                font-weight: 600;
                color: #2d6a4f;
                margin-left: 18px;
                letter-spacing: 0.5px;
            }
            /* Removed conflicting .hamburger CSS here */
            /* Sidebar menu default styles */
            .sidebar-menu {
                transition: transform 0.3s ease, opacity 0.3s;
            }
            /* Responsive styles for sidebar-menu only */
            .sidebar-menu {
                position: fixed;
                top: 0;
                left: 0;
                width: 220px;
                height: 100%;
                background: #fff;
                box-shadow: 2px 0 8px rgba(0,0,0,0.1);
                transform: translateX(-100%);
                opacity: 0;
                pointer-events: none;
                z-index: 1000;
            }
            .sidebar-menu.show {
                transform: translateX(0);
                opacity: 1;
                pointer-events: auto;
            }
            /* Optional: animate menu rows */
            .sidebar-menu .menu-row {
                opacity: 0;
                transform: translateX(-30px);
                transition: opacity 0.3s, transform 0.3s;
            }
            .sidebar-menu.show .menu-row {
                opacity: 1;
                transform: translateX(0);
            }
        }
        @media (min-width: 901px) {
            .ios-header, .hamburger { display: none !important; }
            .container { padding-top: 0 !important; }
        }
    </style>
    <!-- FullCalendar CSS and JS -->
    <link href='https://cdn.jsdelivr.net/npm/fullcalendar@5.10.2/main.min.css' rel='stylesheet' />
    <script src='https://cdn.jsdelivr.net/npm/fullcalendar@5.10.2/main.min.js'></script>
    <!-- SweetAlert2 CSS and JS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.all.min.js"></script>
    <!-- SweetAlert2 CDN -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
    function confirmLogout() {
        Swal.fire({
            title: "Are you sure?",
            text: "Do you really want to log out?",
            icon: "warning",
            showCancelButton: true,
            confirmButtonText: "Yes, log out",
            cancelButtonText: "No, stay logged in",
            reverseButtons: true
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = "../logout.php";
            }
        });
    }
    </script>
</head>
<body>
    <?php
    // Check if a session is already started to avoid the warning
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    // Check if the user is logged in and is a patient
    if (!isset($_SESSION["user"]) || empty($_SESSION["user"]) || $_SESSION['usertype'] != 'p') {
        header("location: ../login.php");
        exit;
    }

    $useremail = $_SESSION["user"];

    // Import the database connection
    include("../connection.php");

    // Fetch patient details securely using prepared statements
    $sqlmain = "SELECT * FROM patient WHERE pemail = ?";
    $stmt = $database->prepare($sqlmain);
    $stmt->bind_param("s", $useremail);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $userfetch = $result->fetch_assoc();
        $userid = $userfetch["pid"];
        $username = $userfetch["pname"];
    } else {
        // Handle the case where no patient is found
        $username = "Unknown User";
        $userid = 0;
    }


    if (isset($_SESSION["login_success"]) && !isset($_SESSION["alert_shown"])) {
        $userType = $_SESSION["user_type"] ?? "Patient";
        $userName = $_SESSION["user_name"] ?? $username;
        echo "
        <script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                Swal.fire({
                    title: 'Welcome to Mabayuan Health Care!',
                    text: 'You have successfully logged in as {$userType}',
                    icon: 'success',
                    confirmButtonText: 'Continue',
                    confirmButtonColor: '#2d6a4f'
                });
            });
        </script>";
        $_SESSION["alert_shown"] = true;
    }
    ?>
    <!-- Sticky Mobile Hamburger Header -->
    <div id="mobile-hamburger-header" style="display:none; align-items:center; justify-content:center;">
        <div style="display:flex;align-items:center;justify-content:center;height:54px;width:100vw;">
            <span style="color:#fff;font-size:1.25em;font-weight:bold;letter-spacing:1px;line-height:1;">Mabayuan Health Care</span>
        </div>
    </div>
  <div class="container">
            <div class="hamburger" id="hamburger">
                <div class="bar"></div>
                <div class="bar"></div>
                <div class="bar"></div>
            </div>
            <div class="menu" id="menu">
                <table class="menu-container" border="0">
                    <tr>
                        <td style="padding:10px" colspan="2">
                            <table border="0" class="profile-container">
                                <tr>
                                    <td width="30%" style="padding-left:20px">
                                        <img src="../img/user.png" alt="" width="100%" style="border-radius:50%">
                                    </td>
                                    <td style="padding:0px;margin:0px;">
                                        <p class="profile-title"><?php echo $username ?></p>
                                        <p class="profile-subtitle"><?php echo $useremail ?></p>
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan="2">
                                        <button onclick="confirmLogout()" class="logout-btn btn-primary-soft btn">Log out</button>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                    <tr class="menu-row">
                        <td class="menu-btn menu-icon-dashbord menu-active menu-icon-settings-active">
                            <a href="index.php" class="non-style-link-menu"><div><p class="menu-text">Home</p></a></div>
                        </td>
                    </tr>
                    <tr class="menu-row">
                        <td class="menu-btn menu-icon-appoinment">
                            <a href="appointment.php" class="non-style-link-menu"><div><p class="menu-text">Booking History</p></a></div>
                        </td>
                    </tr>
                    <tr class="menu-row">
                        <td class="menu-btn menu-icon-settings">
                            <a href="settings.php" class="non-style-link-menu non-style-link-menu-active"><div><p class="menu-text">Settings</p></a></div>
                        </td>
                    </tr>
                </table>
            </div>
            <script>
    // Hamburger and menu logic (single declaration, 992px breakpoint)
    const hamburger = document.getElementById('hamburger');
    const menu = document.getElementById('menu');
    // Toggle menu and lock scroll on mobile
    hamburger.addEventListener('click', () => {
        hamburger.classList.toggle('active');
        menu.classList.toggle('show');
        if (window.innerWidth <= 992) {
            if (menu.classList.contains('show')) {
                document.body.style.overflow = 'hidden';
            } else {
                document.body.style.overflow = '';
            }
        }
    });
    // Restore scroll on resize
    window.addEventListener('resize', function() {
        if (window.innerWidth > 992) {
            document.body.style.overflow = '';
        } else if (!menu.classList.contains('show')) {
            document.body.style.overflow = '';
        }
    });
    // Responsive display logic (optional: if you want to hide/show hamburger/menu based on width)
    function updateHeaderMenuDisplay() {
        if (window.innerWidth <= 992) {
            if (document.getElementById('mobile-hamburger-header')) {
                document.getElementById('mobile-hamburger-header').style.display = 'flex';
            }
            hamburger.style.display = 'flex';
        } else {
            if (document.getElementById('mobile-hamburger-header')) {
                document.getElementById('mobile-hamburger-header').style.display = 'none';
            }
            hamburger.style.display = 'none';
            menu.classList.remove('show');
            document.body.style.overflow = '';
        }
    }
    window.addEventListener('resize', updateHeaderMenuDisplay);
    document.addEventListener('DOMContentLoaded', updateHeaderMenuDisplay);
            </script>





        <div class="dash-body" style="margin-top: 15px">
            <table border="0" width="100%" style=" border-spacing: 0;margin:0;padding:0;" >
                        

                            
                <tr>
                    <td colspan="4" >
                        
                    <center>

                    <table class="filter-container doctor-header patient-header" style="border: none;width:95%" border="0" >
                    <tr>
                        <td >
                            <h3>Welcome!</h3>
                            <h1><?php echo $username  ?>.</h1>
                    </tr>
                    <td class="nav-bar" >
                                


                                <?php
                                // Define current time and date with timezone
                                date_default_timezone_set('Asia/Manila');
                                $today = date('Y-m-d');
                                $current_datetime = date('Y-m-d H:i:s');
                                $current_time = date('H:i:s');

                                // Enhanced next appointment query
                                $next_appointment_query = "
                                    SELECT 
                                        schedule.scheduleid, 
                                        schedule.title, 
                                        schedule.scheduledate, 
                                        schedule.scheduletime, 
                                        doctor.docname,
                                        appointment.apponum,
                                        appointment.appoid
                                    FROM 
                                        schedule 
                                    JOIN 
                                        appointment ON schedule.scheduleid = appointment.scheduleid 
                                    JOIN 
                                        doctor ON schedule.docid = doctor.docid
                                    WHERE 
                                        appointment.pid = ? 
                                        AND appointment.is_confirmed = 1 
                                        AND schedule.scheduledate >= CURDATE()
                                    ORDER BY 
                                        schedule.scheduledate ASC, 
                                        schedule.scheduletime ASC 
                                    LIMIT 1
                                ";
                                $next_stmt = $database->prepare($next_appointment_query);
                                $next_stmt->bind_param("i", $userid);
                                $next_stmt->execute();
                                $next_result = $next_stmt->get_result();

                                if ($next_result->num_rows > 0) {
                                    $next_appointment = $next_result->fetch_assoc();
                                    
                                    $next_appo_title = htmlspecialchars($next_appointment['title']);
                                    $next_appo_number = intval($next_appointment['apponum']);
                                    $next_appo_date = date('F d, Y', strtotime($next_appointment['scheduledate']));
                                    $next_appo_time = date('h:i A', strtotime($next_appointment['scheduletime']));
                                    $next_appo_doctor = htmlspecialchars($next_appointment['docname']);

                                    echo "<div class='next-appointment-container'>
                                            <div class='next-appointment-header'>
                                                <h3 class='next-appointment-title'>Your Next Appointment</h3>
                                            </div>
                                            <div class='next-appointment-body'>
                                                <div class='next-appointment-details'>
                                                    <div class='appointment-number'>
                                                        <span class='badge'>#{$next_appo_number}</span>
                                                    </div>
                                                    <div class='appointment-info'>
                                                        <h4>{$next_appo_title}</h4>
                                                        <p>with Dr. {$next_appo_doctor}</p>
                                                        <small>{$next_appo_date} at {$next_appo_time}</small>
                                                    </div>
                                                </div>
                                              </div>
                                          </div>";
                                } else {
                                    echo "<div class='next-appointment-container no-appointments'>
                                            <div class='next-appointment-header'>
                                                <h3 class='next-appointment-title'>Your Next Appointment</h3>
                                            </div>
                                            <div class='next-appointment-body'>
                                                <div class='next-appointment-details'>
                                                    <p>No confirmed upcoming appointments.</p>
                                                </div>
                                            </div>
                                          </div>";
                                }
                                ?>
                    
                    </table>
                    </center>
                    
                </td>
                </tr>
                <tr>

                                    </center)







                                </td>
                                
                            </tr>
                        </table>
                    </td>
                    <td>


                            
                                    <p style="font-size: 20px;font-weight:600;padding-left: 40px;" class="anime">Available Sessions</p>
                                    <center>
                                    <tr class="date-container">
                                <td width="100%">
                                    <p style="font-size: 14px;color: rgb(119, 119, 119);padding: 0;margin: 0;">
                                    Today's Date
                                    </p>
                                    <p class="heading-sub12" style="margin: 0; text-align: center;">
                                        <?php 
                                            date_default_timezone_set('Asia/Manila');
                                            $today = date('Y-m-d');
                                            echo $today;

                                            $patientrow = $database->query("select  * from  patient;");
                                            $doctorrow = $database->query("select  * from  doctor;");
                                            $appointmentrow = $database->query("SELECT * FROM appointment WHERE appodate >= '$today' AND status = 'Approved' AND is_confirmed = 1");
                                            $schedulerow = $database->query("select  * from  schedule where scheduledate='$today';");
                                        ?>
                                    </p>
                                </td>
                            </tr>
                                        <div class="abc scroll" style="height: 500px;padding: 0;margin: 0;">
                                        <?php
                                        // Get current month and year
                                        $current_month = date('F Y');
                                        
                                        // Fetch available sessions for the current month
                                        $today = date('Y-m-d');
                                        $sqlSessions = "SELECT 
                                            schedule.scheduledate, 
                                            COUNT(schedule.scheduleid) as session_count,
                                            SUM(schedule.available_slots) as total_slots,
                                            GROUP_CONCAT(CONCAT(
                                                schedule.scheduleid, '|', 
                                                schedule.title, '|', 
                                                doctor.docname, '|', 
                                                schedule.scheduletime, '|', 
                                                schedule.available_slots, '|', 
                                                schedule.nop
                                            ) SEPARATOR ';;') as session_details
                                        FROM 
                                            schedule
                                        JOIN 
                                            doctor ON schedule.docid = doctor.docid
                                        WHERE 
                                            schedule.scheduledate >= '$today'
                                            AND schedule.available_slots > 0
                                        GROUP BY 
                                            schedule.scheduledate
                                        ORDER BY 
                                            schedule.scheduledate";
                                        
                                        $sessionsResult = $database->query($sqlSessions);
                                        $availableSessions = [];
                                        
                                        while ($row = $sessionsResult->fetch_assoc()) {
                                            $sessionDetails = [];
                                            if (!empty($row['session_details'])) {
                                                $detailsArr = explode(';;', $row['session_details']);
                                                foreach ($detailsArr as $sessionStr) {
                                                    list($scheduleid, $title, $docname, $scheduletime, $available_slots, $total_slots) = explode('|', $sessionStr);
                                                    $sessionDetails[] = [
                                                        'scheduleid' => $scheduleid,
                                                        'title' => $title,
                                                        'docname' => $docname,
                                                        'time' => $scheduletime,
                                                        'available_slots' => (int)$available_slots,
                                                        'total_slots' => (int)$total_slots
                                                    ];
                                                }
                                            }
                                            $availableSessions[$row['scheduledate']] = $sessionDetails;
                                        }
                                        ?>
                                        
                                        <div class="calendar-container" style="max-width: 1500px; margin: 0 auto; padding: 20px;">
                                            <div class="calendar-header" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
                                                <button id="prev-month-btn" style="background: none; border: none; font-size: 24px; cursor: pointer;">&larr;</button>
                                                <h2 id="calendar-month-title" style="margin: 0; color: #fff;"><?php echo $current_month; ?></h2>
                                                <button id="next-month-btn" style="background: none; border: none; font-size: 24px; cursor: pointer;">&rarr;</button>
                                            </div>
                                            
                                            <div id="calendar-grid"></div>
                                        </div>
                                        

                                        <script>
                                        // --- Month navigation and dynamic calendar rendering ---
                                        const calendarMonthTitle = document.getElementById('calendar-month-title');
                                        const calendarGrid = document.getElementById('calendar-grid');
                                        const prevMonthBtn = document.getElementById('prev-month-btn');
                                        const nextMonthBtn = document.getElementById('next-month-btn');

                                        // Get today's date from PHP
                                        const today = new Date('<?php echo date('Y-m-d'); ?>');
                                        today.setHours(0,0,0,0);

                                        let currentYear = today.getFullYear();
                                        let currentMonth = today.getMonth(); // 0-indexed

                                        function pad(num) { return num.toString().padStart(2, '0'); }

                                        function renderCalendar(year, month, availableSessions) {
                                            // month: 0-indexed
                                            const firstDayOfMonth = new Date(year, month, 1);
                                            const daysInMonth = new Date(year, month + 1, 0).getDate();
                                            const firstDay = firstDayOfMonth.getDay(); // 0 (Sun) - 6 (Sat)
                                            const currentMonthStr = firstDayOfMonth.toLocaleString('default', { month: 'long', year: 'numeric' });
                                            calendarMonthTitle.textContent = currentMonthStr;

                                            let html = '';
                                            const days = ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'];
                                            for (const day of days) {
                                                html += `<div style='font-weight: bold; color: #2d6a4f; background: white;'>${day}</div>`;
                                            }
                                            for (let i = 0; i < firstDay; i++) {
                                                html += '<div></div>';
                                            }
                                            // Render days
                                            for (let day = 1; day <= daysInMonth; day++) {
                                                const dateStr = `${year}-${pad(month+1)}-${pad(day)}`;
                                                const dateObj = new Date(dateStr);
                                                const dayOfWeek = dateObj.getDay();
                                                const isWeekday = (dayOfWeek >= 1 && dayOfWeek <= 5); // Mon-Fri
                                                const isPastDate = (dateObj < today);
                                                const hasSession = availableSessions[dateStr] && availableSessions[dateStr].length > 0;
                                                const isBookable = isWeekday && !isPastDate && hasSession;
                                                let style = "padding: 10px; border: 1px solid #e0e0e0; border-radius: 5px;";
                                                let tooltip = '';
                                                let sessionDetails = '';
                                                if (isBookable) {
                                                    style += " background-color: #e6f3e6; cursor: pointer; color: #2d6a4f; font-weight: bold;";
                                                    const sessionCount = availableSessions[dateStr].length;
                                                    let totalSlots = 0;
                                                    for (const s of availableSessions[dateStr]) totalSlots += s.available_slots;
                                                    tooltip = `Sessions: ${sessionCount}, Available Slots: ${totalSlots}`;
                                                    sessionDetails = availableSessions[dateStr].map(s => `${s.scheduleid}|${s.title}|${s.docname}|${s.time}|${s.available_slots}|${s.total_slots}`).join(';;');
                                                } else {
                                                    style += " color: #cccccc; cursor: default;";
                                                    tooltip = isPastDate ? "Past date" : (!isWeekday ? "Bookings only on weekdays" : "No available sessions");
                                                }
                                                html += `<div style='${style}' data-date='${dateStr}' data-sessions='${isBookable ? sessionDetails.replace(/'/g, "&#39;") : ''}' title='${tooltip.replace(/'/g, "&#39;")}' >${day}</div>`;
                                            }
                                            calendarGrid.innerHTML = html;
                                            calendarGrid.onclick = function(e) {
                                                const target = e.target.closest('div[data-date]');
                                                if (!target) return;
                                                // Only allow click if the cell is bookable (has data-sessions and not empty)
                                                if (target.getAttribute('data-sessions')) {
                                                    showSessionDetails(target);
                                                }
                                            };
                                        }

                                        function fetchAndRenderCalendar(year, month) {
                                            // month: 0-indexed, but PHP expects 1-indexed
                                            fetch(`../get_monthly_sessions.php?year=${year}&month=${month+1}`)
                                                .then(res => res.json())
                                                .then(data => {
                                                    // Convert the structure to match what renderCalendar expects
                                                    // data[date] = { session_count, total_slots, session_details }
                                                    // We want: data[date] = [ {scheduleid, title, docname, time, available_slots, total_slots}, ... ]
                                                    const sessionsByDate = {};
                                                    for (const date in data) {
                                                        const details = data[date].session_details;
                                                        if (details) {
                                                            sessionsByDate[date] = details.split(';;').map(sessionStr => {
                                                                const [scheduleid, title, docname, time, available_slots, total_slots] = sessionStr.split('|');
                                                                return {
                                                                    scheduleid,
                                                                    title,
                                                                    docname,
                                                                    time,
                                                                    available_slots: parseInt(available_slots),
                                                                    total_slots: parseInt(total_slots)
                                                                };
                                                            });
                                                        } else {
                                                            sessionsByDate[date] = [];
                                                        }
                                                    }
                                                    renderCalendar(year, month, sessionsByDate);
                                                })
                                                .catch(() => {
                                                    calendarGrid.innerHTML = '<div style="color:red; text-align:center;">Failed to load sessions.</div>';
                                                });
                                        }

                                        prevMonthBtn.addEventListener('click', function() {
                                            if (currentMonth === 0) {
                                                currentMonth = 11;
                                                currentYear--;
                                            } else {
                                                currentMonth--;
                                            }
                                            fetchAndRenderCalendar(currentYear, currentMonth);
                                        });
                                        nextMonthBtn.addEventListener('click', function() {
                                            if (currentMonth === 11) {
                                                currentMonth = 0;
                                                currentYear++;
                                            } else {
                                                currentMonth++;
                                            }
                                            fetchAndRenderCalendar(currentYear, currentMonth);
                                        });

                                        // Initial render
                                        fetchAndRenderCalendar(currentYear, currentMonth);

                                        // showSessionDetails function remains unchanged (already present below)
                                        </script>
                                        
                                        <!-- Remove the previous modal HTML -->
                                        </div>
                                    </center>

                                    <h1 style="color:#2d6a4f; font-size: 2rem; margin-bottom: 10px; text-align:left; letter-spacing:2px; margin-left: 100px;">Legend</h1>
                                        <div class="calendar-legend">
                                            <span style="background:#e3f2fd;"></span> General Check Up
                                            <span style="background:#bbdefb;"></span> TB Dots
                                            <span style="background:#fce4ec;"></span> Family Planning
                                            <span style="background:#fff9c4;"></span> Wednesday
                                            <span style="background:#c8e6c9;"></span> Immunization
                                            <span style="background:#ffe0b2;"></span> Prenatal
                                        </div>
                                        <div class="available-legend" style="margin-bottom:20px; padding:10px;text-align:center;">
                                            <span style="background:#2d6a4f; color: #fff; border: 1px solid #2d6a4f; padding: 0 10px; border-radius: 4px; display: inline-block; margin-left:18px; font-weight: 500;">Available</span>
                                            <span style="background:#cccccc; color: #222; border: 1px solid #cccccc; padding: 0 10px; border-radius: 4px; display: inline-block; font-weight: 500; margin-left:10px;">Not available</span>
                                        </div>
                                    <p style="font-size: 20px;font-weight:600;padding-left: 40px;" class="anime">Your Upcoming Booking</p>
                                    <center>
                                        <div class="abc scroll" style="height: 1000px;padding: 0;margin: 0;">
                                        <div style="display: flex; flex-wrap: wrap; justify-content: center; gap: 20px; padding: 20px;">
                                            <?php
                                            $nextweek = date("Y-m-d", strtotime("+1 week"));
                                            $sqlmain = "SELECT *, IF(is_self = 0, 'For Myself', other_patient_name) AS patient_display FROM schedule 
                                                        INNER JOIN appointment ON schedule.scheduleid = appointment.scheduleid 
                                                        INNER JOIN patient ON patient.pid = appointment.pid 
                                                        INNER JOIN doctor ON schedule.docid = doctor.docid
                                                        WHERE patient.pid = $userid 
                                                        AND schedule.scheduledate >= '$today' 
                                                        AND appointment.is_confirmed = 1
                                                        ORDER BY schedule.scheduledate ASC";
                                            $result = $database->query($sqlmain);

                                            if ($result->num_rows == 0) {
                                                echo '<div style="width: 100%; text-align: center; padding: 40px;">
                                                    <img src="../img/notfound.svg" width="25%">
                                                    <br>
                                                    <p class="heading-main12">Nothing to show here!</p>
                                                    <a href="schedule.php" class="login-btn btn-primary-soft btn">
                                                        Find a Schedule
                                                    </a>
                                                </div>';
                                            } else {
                                                while ($row = $result->fetch_assoc()) {
                                                    $scheduleid = $row["scheduleid"];
                                                    $title = $row["title"];
                                                    $apponum = $row["apponum"];
                                                    $docname = $row["docname"];
                                                    $scheduledate = $row["scheduledate"];
                                                    $scheduletime = $row["scheduletime"];
                                                    $patient_display = $row["patient_display"];
                                                    $appoid = $row["appoid"];

                                                    // Calculate time until appointment
                                                    $appointmentDateTime = new DateTime("$scheduledate $scheduletime");
                                                    $currentDateTime = new DateTime();
                                                    $timeUntilAppointment = $currentDateTime->diff($appointmentDateTime);
                                                    
                                                    // Format the time difference
                                                    $timeDisplay = '';
                                                    if ($timeUntilAppointment->d > 0) {
                                                        $timeDisplay .= $timeUntilAppointment->d . ' day' . ($timeUntilAppointment->d > 1 ? 's' : '') . ' ';
                                                    }
                                                    if ($timeUntilAppointment->h > 0) {
                                                        $timeDisplay .= $timeUntilAppointment->h . ' hour' . ($timeUntilAppointment->h > 1 ? 's' : '') . ' ';
                                                    }
                                                    if ($timeUntilAppointment->i > 0) {
                                                        $timeDisplay .= $timeUntilAppointment->i . ' minute' . ($timeUntilAppointment->i > 1 ? 's' : '');
                                                    }

                                                    echo '<div class="booking-card" style="background:rgb(241, 241, 241); border-radius: 10px; box-shadow: 0 4px 8px rgba(0, 0, 0, 0.44); padding: 20px; width: 300px; margin: 10px;">
                                                        <div style="text-align: center; margin-bottom: 15px;">
                                                            <h3 style="color: #2d6a4f; margin: 0;">' . substr($title, 0, 30) . '</h3>
                                                            <div style="font-size: 24px; color: #2d6a4f; font-weight: bold; margin: 10px 0;">
                                                                Appointment #' . $apponum . '
                                                            </div>
                                                        </div>
                                                        
                                                        <div style="margin: 15px 0;">
                                                            <p style="margin: 5px 0;"><strong>Doctor:</strong> ' . substr($docname, 0, 20) . '</p>
                                                            <p style="margin: 5px 0;"><strong>Date:</strong> ' . date('F j, Y', strtotime($scheduledate)) . '</p>
                                                            <p style="margin: 5px 0;"><strong>Time:</strong> ' . date('h:i A', strtotime($scheduletime)) . '</p>
                                                            <p style="margin: 5px 0;"><strong>Booked For:</strong> ' . $patient_display . '</p>
                                                        </div>
                                                        
                                                        <div style="background-color: #e8f5e9; padding: 10px; border-radius: 5px; margin: 10px 0;">
                                                            <p style="margin: 0; color: #2d6a4f;">
                                                                <i class="fas fa-clock"></i> ' . $timeDisplay . ' until appointment
                                                            </p>
                                                        </div>
                                                        
                                                        <div style="margin: 15px 0; display: flex; justify-content: center;">
                                                            <button onclick="cancelAppointment(' . $appoid . ', \'' . addslashes($title) . '\', \'' . addslashes($docname) . '\')" class="btn-primary-soft btn cancel-button" style="width: auto; background-color: #dc3545; color: white;">
                                                                Cancel
                                                            </button>
                                                        </div>
                                                    </div>';
                                                }
                                            }
                                            ?>
                                        </div>
                                        </div>
                                        </center>

                                        <script>
                                        function viewAppointmentDetails(appoid) {
                                            Swal.fire({
                                                title: 'Appointment Details',
                                                html: 'Loading appointment details...',
                                                allowOutsideClick: false,
                                                didOpen: () => {
                                                    Swal.showLoading();
                                                    // You can add an AJAX call here to fetch more details
                                                    setTimeout(() => {
                                                        Swal.close();
                                                        window.location.href = 'appointment.php?action=view&id=' + appoid;
                                                    }, 500);
                                                }
                                            });
                                        }

                                        function cancelAppointment(appoid, title, docname) {
                                            Swal.fire({
                                                title: 'Cancel Appointment?',
                                                html: `
                                                    Are you sure you want to cancel this appointment?<br><br>
                                                    <strong>Session:</strong> ${title}<br>
                                                    <strong>Doctor:</strong> ${docname}
                                                `,
                                                icon: 'warning',
                                                showCancelButton: true,
                                                confirmButtonColor: '#dc3545',
                                                cancelButtonColor: '#6c757d',
                                                confirmButtonText: 'Yes, cancel it',
                                                cancelButtonText: 'No, keep it'
                                            }).then((result) => {
                                                if (result.isConfirmed) {
                                                    window.location.href = 'delete-appointment.php?id=' + appoid;
                                                }
                                            });
                                        }
                                        </script>

                                        <style>
                                        @media (max-width: 768px) {
                                            .booking-card {
                                                width: 100% !important;
                                                margin: 10px 0 !important;
                                            }
                                            .booking-card {
                                                background: rgb(241, 241, 241);
                                                border-radius: 10px;
                                                box-shadow: 0 4px 8px rgba(0, 0, 0, 0.44);
                                                padding: 20px;
                                                width: 300px;
                                                margin: 10px;
                                                transition: transform 0.2s ease; /* Smooth transition */
                                            }

                                            .booking-card:hover {
                                                transform: translateY(-3px); /* Move up slightly on hover */
                                            }
                                            .booking-card {
                                                transition: transform 0.2s ease; /* Smooth transition */
                                            }


                                            

                                        }
                                        </style>







                                </td>
                <tr>
            </table>
        </div>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        var calendarEl = document.getElementById('calendar');
        var sessionsModal = document.getElementById('sessions-modal');
        var modalClose = document.querySelector('.sessions-modal-close');
        var modalBody = document.getElementById('sessions-modal-body');
        var modalDateTitle = document.getElementById('modal-date-title');
        var currentMonth = new Date().getMonth();
        var currentYear = new Date().getFullYear();
        var isLoading = false;

        // Close modal when clicking on close button
        modalClose.onclick = function() {
            sessionsModal.style.display = "none";
        }

        // Close modal when clicking outside
        window.onclick = function(event) {
            if (event.target == sessionsModal) {
                sessionsModal.style.display = "none";
            }
        }

        // Function to update month/year display
        function updateMonthYearDisplay(date) {
            const monthYearDisplay = document.getElementById('current-month-year');
            if (monthYearDisplay) {
                const options = { year: 'numeric', month: 'long' };
                monthYearDisplay.textContent = date.toLocaleDateString('en-US', options);
            }
        }

        // Initialize the calendar
        var calendar = new FullCalendar.Calendar(calendarEl, {
            initialView: 'dayGridMonth',
            headerToolbar: {
                left: 'prev,next today',
                center: 'title',
                right: 'dayGridMonth,timeGridWeek,timeGridDay'
            },
            datesSet: function(dateInfo) {
                currentMonth = dateInfo.view.currentStart.getMonth();
                currentYear = dateInfo.view.currentStart.getFullYear();
                updateMonthYearDisplay(dateInfo.view.currentStart);
                loadSessionsForMonth(currentYear, currentMonth + 1);
            },
            dateClick: function(info) {
                // Fetch sessions for the clicked date
                fetchSessionsForDate(info.dateStr);
            },
            events: function(fetchInfo, successCallback, failureCallback) {
                if (isLoading) return;
                
                isLoading = true;
                document.getElementById('calendar-loading').style.display = 'block';
                
                // Fetch events (sessions) for the calendar
                fetch(`get_sessions.php?start=${fetchInfo.startStr}&end=${fetchInfo.endStr}`)
                    .then(response => response.json())
                    .then(events => {
                        successCallback(events);
                        document.getElementById('calendar-loading').style.display = 'none';
                        isLoading = false;
                    })
                    .catch(error => {
                        console.error('Error fetching events:', error);
                        document.getElementById('calendar-loading').style.display = 'none';
                        failureCallback(error);
                        isLoading = false;
                    });
            }
        });
        
        // Load sessions for the current month
        function loadSessionsForMonth(year, month) {
            if (isLoading) return;
            
            isLoading = true;
            const loadingElement = document.getElementById('calendar-loading');
            if (loadingElement) loadingElement.style.display = 'block';
            
            // You can add additional loading logic here if needed
            // For example, you might want to show a loading indicator
            
            // The actual calendar will handle the loading of events through the events callback
            isLoading = false;
            if (loadingElement) loadingElement.style.display = 'none';
        }
        
        // Initial render
        calendar.render();
        updateMonthYearDisplay(new Date());

        function fetchSessionsForDate(date) {
            // Get current date from PHP to ensure accurate validation
            const currentDate = new Date('<?php echo date('Y-m-d'); ?>');
            const selectedDate = new Date(date);
            
            // Normalize dates to remove time components for accurate comparison
            currentDate.setHours(0, 0, 0, 0);
            selectedDate.setHours(0, 0, 0, 0);
            
            // Check if the selected date is in the past
            if (selectedDate < currentDate) {
                Swal.fire({
                    title: 'Cannot Interact with Past Date',
                    text: 'You cannot view or book sessions for a date that has already passed.',
                    icon: 'warning',
                    confirmButtonColor: '#2d6a4f'
                });
                return;
            }
            
            // Show loading state
            modalBody.innerHTML = `
                <div class="text-center py-4">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                    <p class="mt-2">Loading sessions...</p>
                </div>`;
            sessionsModal.style.display = "block";

            fetch(`get_sessions.php?date=${date}`)
                .then(response => response.json())
                .then(sessions => {
                    // Clear previous sessions
                    modalBody.innerHTML = '';
                    
                    // Update modal title
                    modalDateTitle.textContent = `Sessions on ${new Date(date).toLocaleDateString('en-US', { 
                        weekday: 'long', 
                        year: 'numeric', 
                        month: 'long', 
                        day: 'numeric' 
                    })}`;

                    if (sessions.length === 0) {
                        // No sessions available
                        modalBody.innerHTML = `
                            <div class="no-sessions-message">
                                <p>No available sessions on this date.</p>
                            </div>
                        `;
                    } else {
                        // Create session list
                        let sessionHTML = `
                            <div class="table-responsive custom-session-table">
                                <table class="table" style="width:100%; border-collapse: collapse;">
                                    <thead style="background-color: #2d6a4f; color: white;">
                                        <tr>
                                            <th style="padding: 10px; text-align: center;">Session Title</th>
                                            <th style="padding: 10px; text-align: center;">Doctor</th>
                                            <th style="padding: 10px; text-align: center;">Time</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        ${sessions.map((session, idx) => {
                                            // Format time from 24-hour to 12-hour format
                                            let time = session.scheduletime;
                                            let formattedTime = '';
                                            if (time) {
                                                const [hour, minute] = time.split(":");
                                                let h = parseInt(hour);
                                                let ampm = h >= 12 ? 'pm' : 'am';
                                                h = h % 12;
                                                if (h === 0) h = 12;
                                                formattedTime = `${h}:${minute} ${ampm}`;
                                            }
                                            return `
                                                <tr style="border-bottom: 1px solid #e0e0e0;">
                                                    <td style="padding: 10px;">${session.title}</td>
                                                    <td style="padding: 10px;">Dr. ${session.docname}</td>
                                                    <td style="padding: 10px;">${formattedTime}</td>
                                                </tr>
                                            `;
                                        }).join('')}
                                    </tbody>
                                </table>
                                <div class="slots-badges-row" style="display: flex; flex-wrap: wrap; gap: 10px; justify-content: center; margin-top: 18px;">
                                    ${sessions.map((session, idx) => {
                                        // Format time from 24-hour to 12-hour format
                                        let time = session.scheduletime;
                                        let formattedTime = '';
                                        if (time) {
                                            const [hour, minute] = time.split(":");
                                            let h = parseInt(hour);
                                            let ampm = h >= 12 ? 'pm' : 'am';
                                            h = h % 12;
                                            if (h === 0) h = 12;
                                            formattedTime = `${h}:${minute} ${ampm}`;
                                        }
                                        return `
                                            <span class="slot-badge" id="slot-badge-${idx}" style="padding: 7px 18px; border-radius: 15px; background-color: ${session.available_slots > 0 ? '#28a745' : '#dc3545'}; color: white; font-weight: bold; font-size: 1rem; display:inline-block; min-width:120px; text-align:center;">
                                                Available Slots — ${session.available_slots}/${session.total_slots} slots<br>
                                                Time: ${formattedTime}
                                            </span>
                                        `;
                                    }).join('')}
                                </div>
                                <div class="book-now-row" style="display: flex; flex-wrap: wrap; gap: 10px; justify-content: center; margin-top: 18px;">
                                    ${sessions.map((session, idx) => `
                                        <a href="booking.php?id=${session.scheduleid}" class="login-btn btn-primary-soft btn ${session.available_slots > 0 ? 'book-now' : 'session-full'}" style="min-width: 120px; padding: 10px 0; border-radius: 7px; text-align: center;${session.available_slots <= 0 ? 'opacity: 0.6; cursor: not-allowed;' : ''}" ${session.available_slots <= 0 ? 'disabled' : ''}>
                                            ${session.available_slots > 0 ? 'Book Now' : 'Session Full'}
                                        </a>
                                    `).join('')}
                                </div>
                            </div>
                        `;

                        modalBody.innerHTML = sessionHTML;
                    }

                    // Show the modal
                    sessionsModal.style.display = "block";
                })
                .catch(error => {
                    console.error('Error fetching sessions:', error);
                    modalBody.innerHTML = `
                        <div class="no-sessions-message">
                            <p>Error loading sessions. Please try again.</p>
                        </div>
                    `;
                    sessionsModal.style.display = "block";
                });
        }
    });
    </script>
    
    <script>
    function showSessionDetails(dateElement) {
        const date = dateElement.getAttribute('data-date');
        const sessionsData = dateElement.getAttribute('data-sessions');
        // Get current date from PHP to ensure accurate validation
        const currentDate = new Date('<?php echo date('Y-m-d'); ?>');
        const selectedDate = new Date(date);
        currentDate.setHours(0, 0, 0, 0);
        selectedDate.setHours(0, 0, 0, 0);
        if (selectedDate < currentDate) {
            Swal.fire({
                title: 'Cannot Interact with Past Date',
                text: 'You cannot view or book sessions for a date that has already passed.',
                icon: 'warning',
                confirmButtonColor: '#2d6a4f'
            });
            return;
        }
        const formattedDate = new Date(date).toLocaleDateString('en-US', {
            weekday: 'long',
            year: 'numeric',
            month: 'long',
            day: 'numeric'
        });
        if (!sessionsData) {
            Swal.fire({
                title: formattedDate,
                html: `<div style="text-align:center; color:#6c757d;"><img src="../img/notfound.svg" style="max-width:200px; margin-bottom:15px;"><p>No sessions available on this date.</p></div>`,
                icon: 'info',
                confirmButtonColor: '#2d6a4f'
            });
            return;
        }
        const sessions = sessionsData.split(';;').map(sessionStr => {
            const [scheduleid, title, docname, scheduletime, available_slots, total_slots] = sessionStr.split('|');
            return { scheduleid, title, docname, scheduletime, available_slots, total_slots };
        });
        Swal.fire({
            title: formattedDate,
            html: `
                <div class="table-responsive custom-session-table">
                    <table class="table" style="width:100%; border-collapse: collapse;">
                        <thead style="background-color: #2d6a4f; color: white;">
                            <tr>
                                <th style="padding: 10px; text-align: center;">Session Title</th>
                                <th style="padding: 10px; text-align: center;">Doctor</th>
                                <th style="padding: 10px; text-align: center;">Time</th>
                            </tr>
                        </thead>
                        <tbody>
                            ${sessions.map((session, idx) => {
                                // Format time from 24-hour to 12-hour format
                                let time = session.scheduletime;
                                let formattedTime = '';
                                if (time) {
                                    const [hour, minute] = time.split(":");
                                    let h = parseInt(hour);
                                    let ampm = h >= 12 ? 'pm' : 'am';
                                    h = h % 12;
                                    if (h === 0) h = 12;
                                    formattedTime = `${h}:${minute} ${ampm}`;
                                }
                                return `
                                    <tr style="border-bottom: 1px solid #e0e0e0;">
                                        <td style="padding: 10px;">${session.title}</td>
                                        <td style="padding: 10px;">Dr. ${session.docname}</td>
                                        <td style="padding: 10px;">${formattedTime}</td>
                                    </tr>
                                `;
                            }).join('')}
                        </tbody>
                    </table>
                    <div class="slots-badges-row" style="display: flex; flex-wrap: wrap; gap: 10px; justify-content: center; margin-top: 18px;">
                        ${sessions.map((session, idx) => {
                            // Format time from 24-hour to 12-hour format
                            let time = session.scheduletime;
                            let formattedTime = '';
                            if (time) {
                                const [hour, minute] = time.split(":");
                                let h = parseInt(hour);
                                let ampm = h >= 12 ? 'pm' : 'am';
                                h = h % 12;
                                if (h === 0) h = 12;
                                formattedTime = `${h}:${minute} ${ampm}`;
                            }
                            return `
                                <span class="slot-badge" id="slot-badge-${idx}" style="padding: 7px 18px; border-radius: 15px; background-color: ${session.available_slots > 0 ? '#28a745' : '#dc3545'}; color: white; font-weight: bold; font-size: 1rem; display:inline-block; min-width:120px; text-align:center;">
                                    Available Slots — ${session.available_slots}/${session.total_slots} slots<br>
                                </span>
                            `;
                        }).join('')}
                    </div>
                    <div class="book-now-row" style="display: flex; flex-wrap: wrap; gap: 10px; justify-content: center; margin-top: 18px;">
                        ${sessions.map((session, idx) => `
                            <a href="booking.php?id=${session.scheduleid}" class="login-btn btn-primary-soft btn ${session.available_slots > 0 ? 'book-now' : 'session-full'}" style="min-width: 120px; padding: 10px 0; border-radius: 7px; text-align: center;${session.available_slots <= 0 ? 'opacity: 0.6; cursor: not-allowed;' : ''}" ${session.available_slots <= 0 ? 'disabled' : ''}>
                                ${session.available_slots > 0 ? 'Book Now' : 'Session Full'}
                            </a>
                        `).join('')}
                    </div>
                </div>
            `,
            width: '700px',
            showCloseButton: true,
            showConfirmButton: false,
            customClass: {
                popup: 'custom-swal-popup',
                title: 'custom-swal-title'
            },
            didRender: () => {
                const style = document.createElement('style');
                style.textContent = `
                    .custom-swal-popup { border-radius: 10px !important; box-shadow: 0 4px 6px rgba(0,0,0,0.1) !important; }
                    .custom-swal-title { color: #2d6a4f !important; font-weight: 600 !important; }
                    .slot-badge { margin: 0 5px 8px 5px; }
                    .book-now-row { margin-top: 18px; }
                    .slots-badges-row { margin-top: 18px; }
                    @media (max-width: 600px) {
                        .swal2-popup.custom-swal-popup { width: 99vw !important; min-width: 0 !important; padding: 0 !important; }
                        .custom-session-table table { font-size: 0.95rem !important; }
                        .slots-badges-row { flex-direction: column; gap: 8px; align-items: stretch; }
                        .slot-badge { font-size: 0.95rem; display: block; margin-bottom: 8px; min-width: 0; }
                        .book-now-row { flex-direction: column; gap: 8px; align-items: stretch; }
                        .login-btn { font-size: 0.98rem !important; min-width: 0 !important; width: 100% !important; }
                    }
                `;
                document.head.appendChild(style);
            }
        });
    }
    </script>
    
    <!-- <script>
    // Add calendar header click for month/year navigation with restrictions
    (function() {
        const calendarHeader = document.querySelector('.calendar-header');
        if (calendarHeader) {
            calendarHeader.style.cursor = 'pointer';
            calendarHeader.title = 'Click to jump to a specific month/year';
            calendarHeader.addEventListener('click', function() {
                const now = new Date();
                const thisYear = now.getFullYear();
                const thisMonth = now.getMonth();
                const maxYear = 2025;
                let maxMonth = thisMonth + 2; // 2 months ahead (0-indexed)
                let yearOptions = '';
                for (let y = thisYear; y <= maxYear; y++) {
                    yearOptions += `<option value="${y}" ${y === currentYear ? 'selected' : ''}>${y}</option>`;
                }
                const months = [
                    'January', 'February', 'March', 'April', 'May', 'June',
                    'July', 'August', 'September', 'October', 'November', 'December'
                ];
                function getMonthOptions(selectedYear) {
                    let lastMonth = 11;
                    if (selectedYear == thisYear && maxYear == thisYear) {
                        lastMonth = Math.min(thisMonth + 2, 11);
                    } else if (selectedYear == thisYear) {
                        lastMonth = Math.min(thisMonth + 2, 11);
                    } else if (selectedYear == maxYear) {
                        lastMonth = 1; // Only Jan and Feb for 2025
                    }
                    let opts = '';
                    for (let m = 0; m <= lastMonth; m++) {
                        opts += `<option value="${m}" ${m === currentMonth && selectedYear === currentYear ? 'selected' : ''}>${months[m]}</option>`;
                    }
                    return opts;
                }
                let initialMonthOptions = getMonthOptions(currentYear);
                Swal.fire({
                    title: 'Jump to Month/Year',
                    html: `
                        <div class="calendar-jump-popup" style="display:flex; flex-direction:column; gap:10px; align-items:center;">
                            <img src="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/icons/calendar2-week.svg" alt="Calendar" style="width:48px; height:48px; margin-bottom:6px; display:block;" />
                            <div style="font-size:1em; color:#2d6a4f; margin-bottom:8px; text-align:center; font-weight:500;">Select a month and year to quickly jump in the calendar</div>
                            <select id='swal-month' class='swal2-input' style='width: 200px;'>${initialMonthOptions}</select>
                            <select id='swal-year' class='swal2-input' style='width: 200px;'>${yearOptions}</select>
                        </div>
                    `,
                    showCancelButton: true,
                    confirmButtonText: 'Go',
                    cancelButtonText: 'Cancel',
                    didOpen: () => {
                        const yearSelect = document.getElementById('swal-year');
                        const monthSelect = document.getElementById('swal-month');
                        yearSelect.addEventListener('change', function() {
                            const selectedYear = parseInt(this.value);
                            monthSelect.innerHTML = getMonthOptions(selectedYear);
                        });
                    },
                    preConfirm: () => {
                        const month = parseInt(document.getElementById('swal-month').value);
                        const year = parseInt(document.getElementById('swal-year').value);
                        // Restrict again in case of manual tampering
                        if (year > maxYear) return false;
                        if (year === thisYear && month > Math.min(thisMonth + 2, 11)) return false;
                        if (year === maxYear && month > 1) return false;
                        return { month, year };
                    },
                    customClass: { popup: 'custom-swal-popup' },
                    didRender: () => {
                        const style = document.createElement('style');
                        style.textContent = `
                            .calendar-jump-popup img { max-width: 60px; height: auto; margin-bottom: 8px; }
                            .calendar-jump-popup { gap: 10px; }
                            @media (max-width: 600px) {
                                .swal2-popup.custom-swal-popup { width: 99vw !important; min-width: 0 !important; padding: 0 !important; }
                                .calendar-jump-popup img { max-width: 40px; }
                                .calendar-jump-popup { gap: 6px; }
                                .calendar-jump-popup div { font-size: 0.98em; }
                                .swal2-input { width: 90vw !important; min-width: 0 !important; }
                            }
                        `;
                        document.head.appendChild(style);
                    }
                }).then(result => {
                    if (result.isConfirmed && result.value) {
                        currentMonth = result.value.month;
                        currentYear = result.value.year;
                        fetchAndRenderCalendar(currentYear, currentMonth);
                        // Close the popup manually if needed
                        Swal.close();
                    }
                });
            });
        }
    })();
    </script> -->
    
</body>
</html>

<!-- Session Details Modal (for calendar session details) -->
<div id="sessions-modal" style="display:none; position: fixed; z-index: 1000; left: 0; top: 0; width: 100%; height: 100%; overflow: auto; background-color: rgba(0,0,0,0.4);">
  <div class="sessions-modal-content" style="background-color: #fefefe; margin: 15% auto; padding: 20px; border: 1px solid #888; width: 80%; max-width: 600px; border-radius: 10px; box-shadow: 0 4px 8px rgba(0,0,0,0.2);">
    <div class="sessions-modal-header" style="display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid #e0e0e0; padding-bottom: 10px;">
      <span id="modal-date-title"></span>
      <span class="sessions-modal-close" style="color: #aaa; font-size: 28px; font-weight: bold; cursor: pointer;">&times;</span>
    </div>
    <div class="sessions-modal-body" id="sessions-modal-body"></div>
  </div>
</div>
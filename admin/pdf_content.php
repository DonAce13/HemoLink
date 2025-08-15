<?php
// PDF Content Template for Admin Dashboard

// Dashboard stats
$totalPatients = $database->query("SELECT COUNT(*) as total FROM patient")->fetch_assoc()['total'];
$totalAppointments = $database->query("SELECT COUNT(*) as total FROM appointment WHERE status = 'Approved'")->fetch_assoc()['total'];
$totalDoctors = $database->query("SELECT COUNT(*) as total FROM doctor")->fetch_assoc()['total'];
$totalSchedules = $database->query("SELECT COUNT(*) as total FROM schedule WHERE deleted_at IS NULL")->fetch_assoc()['total'];

// Patient categories
$pwdCount = $database->query("SELECT COUNT(*) as count FROM patient WHERE patient_category LIKE '%PWD%'")->fetch_assoc()['count'];
$seniorCount = $database->query("SELECT COUNT(*) as count FROM patient WHERE patient_category LIKE '%SENIOR CITIZEN%'")->fetch_assoc()['count'];
$ipCount = $database->query("SELECT COUNT(*) as count FROM patient WHERE patient_category LIKE '%IP%'")->fetch_assoc()['count'];

// Appointment status counts
$statusTableQuery = "SELECT status, COUNT(*) as count FROM appointment WHERE (status = 'scheduled' OR status = 'done') GROUP BY status";
$statusTableResult = $database->query($statusTableQuery);
$statusCounts = ['scheduled' => 0, 'done' => 0];
while($row = $statusTableResult->fetch_assoc()) {
    $statusCounts[$row['status']] = $row['count'];
}

// Confirmation status counts
$confirmationQuery = "SELECT is_confirmed, COUNT(*) as count FROM appointment WHERE 1=1 GROUP BY is_confirmed ORDER BY is_confirmed DESC";
$confirmationResult = $database->query($confirmationQuery);
$confirmationCounts = ['approved' => 0, 'pending' => 0, 'declined' => 0];
while($row = $confirmationResult->fetch_assoc()) {
    if ($row['is_confirmed'] == 1) $confirmationCounts['approved'] = $row['count'];
    elseif ($row['is_confirmed'] == 0) $confirmationCounts['pending'] = $row['count'];
    elseif ($row['is_confirmed'] == -1) $confirmationCounts['declined'] = $row['count'];
}
?>

<style>
    .stat-grid {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 15px;
        margin-bottom: 20px;
    }
    .stat-card {
        border: 1px solid #ddd;
        border-radius: 5px;
        padding: 15px;
        background: #f9f9f9;
    }
    .stat-title {
        font-weight: bold;
        color: #2d6a4f;
        margin-bottom: 5px;
    }
    .stat-value {
        font-size: 18px;
        font-weight: bold;
    }
    table {
        width: 100%;
        border-collapse: collapse;
        margin-bottom: 20px;
    }
    th, td {
        border: 1px solid #ddd;
        padding: 8px;
        text-align: left;
    }
    th {
        background-color: #f2f2f2;
    }
</style>

<div class="stat-grid">
    <div class="stat-card">
        <div class="stat-title">Total Patients</div>
        <div class="stat-value"><?php echo number_format($totalPatients); ?></div>
    </div>
    <div class="stat-card">
        <div class="stat-title">Total Appointments</div>
        <div class="stat-value"><?php echo number_format($totalAppointments); ?></div>
    </div>
    <div class="stat-card">
        <div class="stat-title">Total Doctors</div>
        <div class="stat-value"><?php echo number_format($totalDoctors); ?></div>
    </div>
    <div class="stat-card">
        <div class="stat-title">Active Schedules</div>
        <div class="stat-value"><?php echo number_format($totalSchedules); ?></div>
    </div>
    <div class="stat-card">
        <div class="stat-title">PWD Patients</div>
        <div class="stat-value"><?php echo number_format($pwdCount); ?></div>
    </div>
    <div class="stat-card">
        <div class="stat-title">Senior Citizens</div>
        <div class="stat-value"><?php echo number_format($seniorCount); ?></div>
    </div>
    <div class="stat-card">
        <div class="stat-title">Indigenous People</div>
        <div class="stat-value"><?php echo number_format($ipCount); ?></div>
    </div>
</div>

<h3>Appointment Status</h3>
<table>
    <tr>
        <th>Status</th>
        <th>Count</th>
    </tr>
    <tr>
        <td>Scheduled</td>
        <td><?php echo number_format($statusCounts['scheduled']); ?></td>
    </tr>
    <tr>
        <td>Done</td>
        <td><?php echo number_format($statusCounts['done']); ?></td>
    </tr>
</table>

<h3>Appointment Confirmation</h3>
<table>
    <tr>
        <th>Status</th>
        <th>Count</th>
    </tr>
    <tr>
        <td>Approved</td>
        <td><?php echo number_format($confirmationCounts['approved']); ?></td>
    </tr>
    <tr>
        <td>Pending</td>
        <td><?php echo number_format($confirmationCounts['pending']); ?></td>
    </tr>
    <tr>
        <td>Declined</td>
        <td><?php echo number_format($confirmationCounts['declined']); ?></td>
    </tr>
</table>

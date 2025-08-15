<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
require_once 'connection.php'; // Your database connection file

function getNextScheduleId() {
    global $database;
    $result = $database->query("SELECT MAX(scheduleid) as max_id FROM schedule");
    $row = $result->fetch_assoc();
    return ($row['max_id'] ?? 0) + 1;
}

function generateSchedule($startDate, $weeks = 4) {
    $schedules = [];
    $scheduleId = getNextScheduleId(); // Get the next available ID
    
    // Define the weekly schedule pattern (Monday to Friday)
    $weeklyPattern = [
        // Monday
        1 => [
            [
                'title' => 'General Check-up',
                'time' => '15:00:00',
                'duration' => 120, // 2 hours (3 PM - 5 PM)
                'slots' => 20
            ]
        ],
        // Tuesday
        2 => [
            [
                'title' => 'TB DOTS',
                'time' => '15:00:00',
                'duration' => 120,
                'slots' => 15
            ]
        ],
        // Wednesday
        3 => [
            [
                'title' => 'Family Planning',
                'time' => '15:00:00',
                'duration' => 120,
                'slots' => 15
            ]
        ],
        // Thursday
        4 => [
            [
                'title' => 'Immunization',
                'time' => '15:00:00',
                'duration' => 120,
                'slots' => 20
            ]
        ],
        // Friday
        5 => [
            [
                'title' => 'Prenatal',
                'time' => '15:00:00',
                'duration' => 120,
                'slots' => 15
            ]
        ]
    ];

    $currentDate = new DateTime($startDate);
    $endDate = clone $currentDate;
    $endDate->modify("+$weeks weeks");
    
    // Find the next Monday if start date is not Monday
    if ($currentDate->format('N') != 1) {
        $currentDate->modify('next monday');
    }

    while ($currentDate < $endDate) {
        $dayOfWeek = $currentDate->format('N'); // 1 (Mon) to 7 (Sun)
        
        // Only process weekdays (Mon-Fri)
        if ($dayOfWeek <= 5) {
            foreach ($weeklyPattern[$dayOfWeek] as $session) {
                $schedules[] = [
                    'title' => $session['title'],
                    'date' => $currentDate->format('Y-m-d'),
                    'time' => $session['time'],
                    'duration' => $session['duration'],
                    'slots' => $session['slots']
                ];
            }
        }
        
        $currentDate->modify('+1 day');
    }

    return $schedules;
}

// Find the latest scheduledate in the database
$result = $database->query("SELECT MAX(scheduledate) as last_date FROM schedule");
$row = $result->fetch_assoc();
$lastDate = $row['last_date'] ?? null;

if ($lastDate) {
    $startDate = new DateTime($lastDate);
    $startDate->modify('next monday');
} else {
    // If no schedules exist, start from next Monday after today
    $startDate = new DateTime();
    if ($startDate->format('N') == 1) {
        $startDate->modify('next monday');
    } else {
        $startDate->modify('monday next week');
    }
}

// Generate schedule for 4 weeks
$schedules = generateSchedule($startDate->format('Y-m-d'), 4);

// Output the SQL for reference
function generateScheduleSQL($schedules) {
    $sql = "INSERT INTO schedule (docid, title, scheduledate, scheduletime, session_duration, end_time, nop, total_slots, available_slots, approved_bookings, max_approved_bookings, deleted_at) VALUES\n";
    $values = [];
    foreach ($schedules as $sched) {
        $endTime = new DateTime($sched['date'] . ' ' . $sched['time']);
        $endTime->modify('+' . $sched['duration'] . ' minutes');
        $values[] = sprintf(
            "('1', '%s', '%s', '%s', %d, '%s', %d, %d, %d, 0, 5, NULL)",
            addslashes($sched['title']),
            $sched['date'],
            $sched['time'],
            $sched['duration'],
            $endTime->format('H:i:s'),
            $sched['slots'],
            $sched['slots'],
            $sched['slots']
        );
    }
    $sql .= implode(",\n", $values) . ";\n";
    return $sql;
}

$sql = generateScheduleSQL($schedules);
echo "<pre>\n";
echo "-- Generated on: " . date('Y-m-d H:i:s') . "\n";
echo $sql;
echo "</pre>";

try {
    $successCount = 0;
    foreach ($schedules as $sched) {
        $endTime = (new DateTime($sched['date'] . ' ' . $sched['time']))->modify('+' . $sched['duration'] . ' minutes')->format('H:i:s');
        $stmt = $database->prepare("INSERT INTO schedule (docid, title, scheduledate, scheduletime, session_duration, end_time, nop, total_slots, available_slots, approved_bookings, max_approved_bookings, deleted_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, 0, 5, NULL)");
        $stmt->bind_param(
            "ssssssiii",
            $docid, // set this to your doctor id, e.g. '1'
            $sched['title'],
            $sched['date'],
            $sched['time'],
            $sched['duration'],
            $endTime,
            $sched['slots'],
            $sched['slots'],
            $sched['slots']
        );
        $docid = '1'; // or whatever doctor id you want
        if ($stmt->execute()) {
            $successCount++;
    } else {
            echo "<div class='error'>Error: " . htmlspecialchars($stmt->error) . "</div>";
        }
        $stmt->close();
    }
    echo "<div class='success'>Schedule generated successfully! $successCount sessions created.</div>";
} catch (Exception $e) {
    echo "<div class='error'>Error: " . htmlspecialchars($e->getMessage()) . "</div>";
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Generate Schedule</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        pre { background: #f4f4f4; padding: 15px; border-radius: 5px; }
        .success { color: green; margin: 10px 0; }
        .error { color: red; margin: 10px 0; }
    </style>
</head>
<body>
    <h1>Generate Weekly Schedule</h1>
    <p>This will generate a schedule starting from next Monday for 4 weeks.</p>
    <p><strong>Schedule Pattern:</strong></p>
    <ul>
        <li>Monday: General Check-up (20 slots)</li>
        <li>Tuesday: TB DOTS (15 slots)</li>
        <li>Wednesday: Family Planning (15 slots)</li>
        <li>Thursday: Immunization (20 slots)</li>
        <li>Friday: Prenatal (15 slots)</li>
    </ul>
    <p>All sessions run from 3:00 PM to 5:00 PM.</p>
    
    <h2>Generated SQL:</h2>
    <?php 
    // SQL is already generated and displayed above
    ?>
    
    <p><em>Note: To execute this SQL directly, uncomment the execution code in the PHP file.</em></p>
</body>
</html>

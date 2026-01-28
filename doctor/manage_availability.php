<?php
// Define center working hours for each day
$working_hours = [
    'Monday' => ['start' => '08:00', 'end' => '17:00'],
    'Tuesday' => ['start' => '08:00', 'end' => '17:00'],
    'Wednesday' => ['start' => '08:00', 'end' => '17:00'],
    'Thursday' => ['start' => '09:00', 'end' => '15:00'],
    'Friday' => ['start' => '09:00', 'end' => '15:00'],
    'Saturday' => ['start' => '10:00', 'end' => '13:00'],
    'Sunday' => ['start' => '10:00', 'end' => '13:00']
];

// Function to convert time to minutes
function timeToMinutes($time) {
    $time_parts = explode(":", $time);
    return $time_parts[0] * 60 + $time_parts[1];
}

// Start the session to access session variables
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

include 'config.php';


$alertMessage = ''; 
$messageType = 'alert-error'; 
$allValid = true; 
$someProcessingDone = false; 

$conn = mysqli_connect(db_SERVER, db_USER, db_PASSWORD, db_DNAME);

if (!$conn) {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') { // Only show connection error if post
        $alertMessage = 'Demo Mode: Could not connect to the database. ';
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $someProcessingDone = true;
    $days = $_POST['days'] ?? [];
    $from = $_POST['from'] ?? '';
    $to = $_POST['to'] ?? '';
    $duration = $_POST['duration'] ?? '';
    $doctor_id = $_SESSION['doctor_id'] ?? 1; 

    if (!$conn && !empty($alertMessage)) { 
         $alertMessage .= 'Cannot save data without a database connection.';
    } elseif (empty($doctor_id)) {
        $alertMessage = 'Please log in.';
        $allValid = false;
    } elseif (empty($days) || empty($from) || empty($to) || empty($duration)) {
        $alertMessage = 'All fields are required.';
        $allValid = false;
    } else {
        $successfulInserts = 0;
        foreach ($days as $day) {
            $center_start = $working_hours[$day]['start'];
            $center_end = $working_hours[$day]['end'];

            $from_minutes = timeToMinutes($from);
            $to_minutes = timeToMinutes($to);
            $center_start_minutes = timeToMinutes($center_start);
            $center_end_minutes = timeToMinutes($center_end);

            if ($from_minutes < $center_start_minutes || $to_minutes > $center_end_minutes) {
                $alertMessage .= "Invalid time selection for $day. Center hours: $center_start - $center_end.";
                $allValid = false;
                continue;
            }
            
            if ($to_minutes <= $from_minutes) {
                $alertMessage .= "Invalid time range for $day: 'To Time' must be after 'From Time'.\\n";
                $allValid = false;
                continue;
            }

            $total_minutes = ($to_minutes - $from_minutes);
            $patient_limit = floor($total_minutes / (int)$duration);

            if ($patient_limit <= 0) {
                $alertMessage .= "Invalid time range or duration for $day, results in no available slots. Skipped.\\n";
                $allValid = false;
                continue;
            }

            if ($conn) { // Only proceed with DB if connection is valid
                $sql = "INSERT INTO doctor_availability (doctor_id, from_time, to_time,day, patient_limit,appointment_duration_minutes) VALUES (?, ?, ?, ?, ?,?)";
                $stmt = mysqli_prepare($conn, $sql);
                if ($stmt) {
                    mysqli_stmt_bind_param($stmt, "isssii", $doctor_id, $from, $to, $day, $patient_limit,$duration);
                    if (mysqli_stmt_execute($stmt)) {
                        $successfulInserts++;
                    } else {
                        $alertMessage .= "Error saving availability for $day: " . mysqli_stmt_error($stmt) . "\\n";
                        $allValid = false; // Mark as not all valid if any insert fails
                    }
                    mysqli_stmt_close($stmt);
                } else {
                    $alertMessage .= "Error preparing statement for $day: " . mysqli_error($conn) . "\\n";
                    $allValid = false;
                }
            } else {
                 // $alertMessage .= "Skipped saving $day due to DB connection issue.\n"; // Redundant if global DB error shown
            }
        }

        if ($conn && $successfulInserts > 0 && $allValid) {
            $alertMessage = 'Availability saved successfully for ' . $successfulInserts . ' day(s)!';
            $messageType = 'alert-success';
        } elseif ($conn && $successfulInserts > 0 && !$allValid) {
            $alertMessage .= "Some entries saved successfully, but other issues occurred.";
            $messageType = 'alert-error'; // Still an error overall
        } elseif (!$allValid && empty(trim($alertMessage))) { // If allValid is false but no specific message set
            $alertMessage = "Please review your selections. Some entries were invalid.";
        } elseif (empty(trim($alertMessage)) && $someProcessingDone && count($days) > 0) {
             // If no days processed successfully and no other errors, but form was submitted
            $alertMessage = "No availability slots were saved. Please check your inputs and center hours.";
        }
         if (empty(trim($alertMessage)) && $someProcessingDone && empty($days)){
            $alertMessage = "No days selected.";
        }
    }
}

if ($conn) {
    mysqli_close($conn);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Availability</title>
    <link rel="stylesheet" href="styles.css">
    </head>
<body>

    <div class="main-content">
        <div class="card">
            <h2>Set Your Availability</h2>
            <p>Please enter the days and time range you will be available.</p>

            <?php if (!empty(trim($alertMessage))): ?>
                <div id="alertMessageContainer" class="alert-message <?php echo $allValid && $successfulInserts > 0 ? 'alert-success' : 'alert-error'; ?>">
                    <p><?php echo nl2br(htmlspecialchars(trim($alertMessage))); ?></p>
                </div>
            <?php endif; ?>

            <form method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
                <div class="form-group">
                    <label>Days Available:</label>
                    <div class="checkbox-group"> <label><input type="checkbox" name="days[]" value="Monday"> Monday</label>
                        <label><input type="checkbox" name="days[]" value="Tuesday"> Tuesday</label>
                        <label><input type="checkbox" name="days[]" value="Wednesday"> Wednesday</label>
                        <label><input type="checkbox" name="days[]" value="Thursday"> Thursday</label>
                        <label><input type="checkbox" name="days[]" value="Friday"> Friday</label>
                        <label><input type="checkbox" name="days[]" value="Saturday"> Saturday</label>
                        <label><input type="checkbox" name="days[]" value="Sunday"> Sunday</label>
                    </div>
                </div>

               <div class="form-group">
                <label for="from">From Time:</label>
                <input type="time" id="from" name="from" required>
                  </div>

                <div class="form-group">
                 <label for="to">To Time:</label>
                 <input type="time" id="to" name="to" required>
                  </div>

                <div class="form-group">
                  <label for="duration">Appointment Duration (minutes):</label>
                  <input type="number" id="duration" name="duration" min="1" required>
                    </div>
                <input type="submit" value="Save Availability" class="btn">
            </form>
        </div>
    </div>

    <a href="doctor_dashboard.php" class="btn"> Back to Dashboard</a>

    <script>
        const alertContainer = document.getElementById("alertMessageContainer");
        if (alertContainer) {
            setTimeout(function() {
                alertContainer.style.opacity = '0';
                setTimeout(function() { // Wait for fade out before hiding
                    alertContainer.style.display = 'none';
                }, 500); // This duration should match CSS transition if added
            }, 4500); // Increased to 4.5 seconds before starting fade
        }
    </script>

</body>
</html>
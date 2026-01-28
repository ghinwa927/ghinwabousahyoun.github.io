<?php
session_start();
include "config.php";
include "../email.php";
$doctorId = $_GET['id'] ?? null;
if (!$doctorId) die("Doctor ID is required.");

$patientId = 1; // Replace with $_SESSION['user_id'] in real usage

$message = "";
$month = $_GET['month'] ?? date('m');
$year = $_GET['year'] ?? date('Y');
$selectedDate = $_GET['date'] ?? null;

$daysInMonth = cal_days_in_month(CAL_GREGORIAN, $month, $year);
$firstDay = date('w', strtotime("$year-$month-01")); // 0 (Sun) to 6 (Sat)

// Step 1: Fetch doctor weekday availability
$weekAvailability = [];
$stmt = $conn->prepare("SELECT * FROM doctor_availability WHERE doctor_id = ?");
$stmt->bind_param("i", $doctorId);
$stmt->execute();
$res = $stmt->get_result();
while ($row = $res->fetch_assoc()) {
    $weekAvailability[$row['day']] = $row;
}
$stmt->close();

// Fetch doctor information
$query = "SELECT * FROM doctors WHERE doctor_id = ?";
$stmt = $conn->prepare($query);
if ($stmt === false) {
    die("Prepare failed: " . $conn->error);
}
$stmt->bind_param("i", $doctorId);
$stmt->execute();
$result = $stmt->get_result();
$doctor = $result->fetch_assoc();

if (!$doctor) {
    die("Doctor not found.");
}
$stmt->close(); 

// Step 2: Handle booking (POST)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['slot'])) {
    $slot = $_POST['slot'];
    $date = $_POST['date'];
    $duration = isset($_POST['duration']) ? $_POST['duration'] : 0;


    $stmt = $conn->prepare("SELECT * FROM appointment WHERE doctor_id = ? AND date = ? AND start_time = ?");
    $stmt->bind_param("iss", $doctorId, $date, $slot);
    $stmt->execute();
    $exists = $stmt->get_result()->num_rows > 0;
    $stmt->close();

    if ($exists) {
        $message = "Slot already booked.";
    } else {
        $end = date("H:i:s", strtotime($slot) + $duration * 60);
        $stmt = $conn->prepare("INSERT INTO appointment (patient_id, doctor_id, date, start_time, end_time, status, created_at) VALUES (?, ?, ?, ?, ?, 'scheduled', NOW())");
        $stmt->bind_param("iisss", $patientId, $doctorId, $date, $slot, $end);
        if ($stmt->execute()) {
            $username= $_SESSION['username'] ;
            $subject="Doctor Appointment";
            $body="Dear {$_SESSION['patien_id']},<br>
            We are pleased to confirm your appointment with Dr. {$doctor['fullname']} on $date at $slot.<br><br>
            If you need to reschedule or cancel your appointment, please contact or reply to this email.<br>
            We look forward to seening you and helping you with your health needs.<br><br>
            Best regards,
            ";


            sendEmailToUser($username,$subject,$body);
            $message = "Appointment booked for $date at $slot.";
        } else {
            $message = "Error booking appointment.";
        }
        $stmt->close();
    }
}

// Step 3: Generate slots for selected date
$slots = [];
$slotDuration = 0;
if ($selectedDate) {
    $dayName = date('l', strtotime($selectedDate));
    if (isset($weekAvailability[$dayName])) {
        $avail = $weekAvailability[$dayName];
        $from = new DateTime($avail['from_time']);
        $to = new DateTime($avail['to_time']);
        $slotDuration = (int)$avail['appointment_duration_minutes'];

        // Get booked slots
        $stmt = $conn->prepare("SELECT start_time FROM appointment WHERE doctor_id = ? AND date = ?");
        $stmt->bind_param("is", $doctorId, $selectedDate);
        $stmt->execute();
        $booked = $stmt->get_result();
        $bookedSlots = [];
        while ($b = $booked->fetch_assoc()) {
            $bookedSlots[] = $b['start_time'];
        }
        $stmt->close();

        while ($from < $to) {
            $slot = $from->format("H:i:s");
            if (!in_array($slot, $bookedSlots)) {
                $slots[] = $slot;
            }
            $from->modify("+$slotDuration minutes");
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Doctor Calendar Booking</title>
    <link rel="stylesheet" href="doctor_profile.css">
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            background: #f4f6f9;
            margin: 0;
            padding: 0;
        }
        h1, h2, h3 {
            text-align: center;
        }
        .calendar {
            max-width: 800px;
            margin: 30px auto;
            background: white;
            border-radius: 12px;
            padding: 20px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        }
        table {
            width: 100%;
            border-collapse: collapse;
            text-align: center;
        }
        th {
            background: #333;
            color: white;
            padding: 10px;
        }
        td {
            padding: 15px;
            border: 1px solid #ddd;
        }
        .available {
            background: #28a745;
            color: white;
            cursor: pointer;
        }
        .available:hover {
            background: #218838;
        }
        .not-available {
            background: #f8d7da;
            color: #721c24;
        }
        .today {
            border: 2px solid #007bff;
        }
        .slots {
            max-width: 500px;
            margin: 20px auto;
            background: #fff;
            border: 1px solid #ccc;
            padding: 20px;
            border-radius: 10px;
        }
        .slot-button {
            display: inline-block;
            margin: 5px;
            padding: 8px 12px;
            background: #007bff;
            color: white;
            border: none;
            border-radius: 6px;
            cursor: pointer;
        }
        .slot-button:hover {
            background: #0056b3;
        }
        .msg {
            text-align: center;
            color: green;
        }
    </style>
</head>
<body>
    

<header>
    <h1>Dr. <?php echo htmlspecialchars($doctor['fullname']); ?></h1>
    <p>Specialty: <?php echo htmlspecialchars($doctor['specialty']); ?></p>
</header>

<div class="profile-container">
    <img src="../images/<?php echo htmlspecialchars($doctor['image']); ?>" alt="Dr. <?php echo htmlspecialchars($doctor['fullname']); ?>">
</div>

<h3>Availability</h3>
<?php
$availability = []; // Ensure it's an array

// Fetch doctor availability from the database
$stmt = $conn->prepare("SELECT day, from_time AS 'from', to_time AS 'to', patient_limit AS 'limit', appointment_duration_minutes AS 'duration' FROM doctor_availability WHERE doctor_id = ?");
$stmt->bind_param("i", $doctorId);
$stmt->execute();
$res = $stmt->get_result();

while ($row = $res->fetch_assoc()) {
    $availability[$row['day']] = $row;
}

$stmt->close();
?>

<table>
    <tr>
        <th>Day</th>
        <th>From</th>
        <th>To</th>
        <th>Max Patients</th>
        <th>Appointment Duration (mins)</th>
    </tr>
    <?php foreach ($availability as $day => $info): ?>
        <tr>
            <td><?php echo htmlspecialchars($day); ?></td>
            <td><?php echo htmlspecialchars($info['from']); ?></td>
            <td><?php echo htmlspecialchars($info['to']); ?></td>
            <td><?php echo htmlspecialchars($info['limit']); ?></td>
            <td><?php echo htmlspecialchars($info['duration']); ?></td>
        </tr>
    <?php endforeach; ?>
</table>
</table>

<div class="calendar">
    <table>
        <tr>
            <th>Sun</th><th>Mon</th><th>Tue</th><th>Wed</th>
            <th>Thu</th><th>Fri</th><th>Sat</th>
        </tr>
        <tr>
        <?php
        $day = 1;
        $cell = 0;

        // Empty cells before the first day
        for ($i = 0; $i < $firstDay; $i++) {
            echo "<td></td>";
            $cell++;
        }

        while ($day <= $daysInMonth) {
            $dateStr = "$year-$month-" . str_pad($day, 2, '0', STR_PAD_LEFT);
            $weekday = date('l', strtotime($dateStr));
            $isAvailable = isset($weekAvailability[$weekday]);
            $isToday = $dateStr == date('Y-m-d');

            $class = $isAvailable ? 'available' : 'not-available';
            if ($isToday) $class .= ' today';

            echo "<td class='$class'>";
            if ($isAvailable) {
                echo "<a style='color:white;text-decoration:none;' href='?id=$doctorId&date=$dateStr&month=$month&year=$year'>$day</a>";
            } else {
                echo $day;
            }
            echo "</td>";

            $day++;
            $cell++;
            if ($cell % 7 == 0) echo "</tr><tr>";
        }

        // Empty cells after last day
        while ($cell % 7 != 0) {
            echo "<td></td>";
            $cell++;
        }
        ?>
        </tr>
    </table>
</div>

<?php if ($selectedDate && count($slots) > 0): ?>
    <div class="slots">
        <h3>Available slots for <?php echo htmlspecialchars($selectedDate); ?></h3>
        <form method="post">
            <input type="hidden" name="date" value="<?php echo htmlspecialchars($selectedDate); ?>">
            <input type="hidden" name="duration" value="<?php echo htmlspecialchars($slotDuration); ?>">
            <?php foreach ($slots as $slot): ?>
                <label>
                    <input type="radio" name="slot" value="<?php echo $slot; ?>" required>
                    <?php echo date("g:i A", strtotime($slot)); ?>
                </label><br>
            <?php endforeach; ?>
            <br><button class="slot-button" type="submit">Book Slot</button>
        </form>
    </div>
<?php elseif ($selectedDate): ?>
    <div class="slots">
        <p>No available slots for this date.</p>
    </div>
<?php endif; ?>

<?php if ($message): ?>
    <p class="msg"><?php echo htmlspecialchars($message); ?></p>
<?php endif; ?>

</body>
</html>

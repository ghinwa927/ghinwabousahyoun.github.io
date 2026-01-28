<?php
include 'config.php';

header('Content-Type: application/json');

// Debugging: Log the raw POST data
$rawInput = file_get_contents('php://input');
error_log("Raw Input: " . $rawInput);

// Decode the JSON input
$data = json_decode($rawInput, true);

// Debugging: Log the decoded input
error_log("Decoded Input: " . print_r($data, true));

// Validate the input
if (!isset($data['selectedDate']) || !isset($data['doctorId'])) {
    echo json_encode(['available' => false, 'message' => 'Invalid input data.']);
    exit;
}

$selectedDate = $data['selectedDate'];
$doctorId = $data['doctorId'];
error_log("Selected Date: $selectedDate, Doctor ID: $doctorId");

// Decode the JSON input
$data = json_decode(file_get_contents('php://input'), true);

// Validate that data exists
if (!isset($data['selectedDate']) || !isset($data['doctorId'])) {
    echo json_encode(['available' => false, 'message' => 'Invalid input data.']);
    exit;
}

// Extract and validate the selected date
$selectedDate = $data['selectedDate'];
$doctorId = $data['doctorId'];

echo $selectedDate;
echo $doctorId;

// Convert the selected date to a day of the week
$dayOfWeek = date('l', strtotime($selectedDate));


if (!$stmt = $conn->prepare($sql)) {
    echo json_encode(['available' => false, 'message' => 'SQL preparation failed.']);
    exit;
}


// Query the database for doctor availability
$sql = "SELECT weekday, 
        (SELECT COUNT(*) FROM appointments WHERE doctor_id = ? AND appointment_date = ?) AS booked_patients
        FROM doctors WHERE doctor_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("isi", $doctorId, $selectedDate, $doctorId);
$stmt->execute();
$result = $stmt->get_result();

$response = [];

if ($row = $result->fetch_assoc()) {
    $availableDays = explode(',', $row['weekday']); // "Monday,Tuesday"
    $bookedPatients = $row['booked_patients'];

    if (in_array($dayOfWeek, $availableDays)) {
        if ($bookedPatients < 15) {
            $response['available'] = true;
            $response['message'] = "Doctor is available!";
        } else {
            $response['available'] = false;
            $response['message'] = "Doctor is fully booked on this day.";
        }
    } else {
        $response['available'] = false;
        $response['message'] = "Doctor is unavailable on this day.";
    }
} else {
    $response['available'] = false;
    $response['message'] = "Doctor not found.";
}

// Output JSON response
echo json_encode($response);
?>

<?php
header('Content-Type: application/json');

$conn = new mysqli("localhost", "root", "", "senior");
if ($conn->connect_error) {
    http_response_code(500);
    echo json_encode(["error" => "Database connection failed"]);
    exit;
}

$sql = "
    SELECT 
        a.id,
        a.date,
        a.start_time,
        a.end_time,
        a.status,
        u.name AS patient_name,
        d.fullname AS doctor_name
    FROM appointment a
    JOIN users u ON a.patient_id = u.patient_id
    JOIN doctors d ON a.doctor_id = d.doctor_id
";

$result = $conn->query($sql);
$events = [];

while ($row = $result->fetch_assoc()) {
    $events[] = [
        'id' => $row['id'],
        'title' => 'Dr. ' . $row['doctor_name'] . ' with ' . $row['patient_name'],
        'start' => $row['date'] . 'T' . $row['start_time']
        'allDay' => false,
        'extendedProps' => [
            'status' => $row['status'],
            'doctor_name' => $row['doctor_name'],
            'patient_name' => $row['patient_name'],
            'time' => $row['start_time'],
            'date' => $row['date']
        ]
    ];
}

echo json_encode($events);
?>
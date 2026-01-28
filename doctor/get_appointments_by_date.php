<?php
include 'config.php'; // Adjust based on your setup
session_start();
$doctor_id = $_SESSION['doctor_id'];

if (isset($_GET['date'])) {
    $date = $_GET['date'];

    $stmt = $conn->prepare("SELECT
        a.id AS appointment_id,
        a.date,
        a.start_time,
        a.end_time,
        u.patient_id AS patient_id,
        u.name AS patient_name
        FROM appointment a
        JOIN users u ON a.patient_id = u.patient_id
        WHERE a.status = 'scheduled'
        AND a.date = ?
        AND a.doctor_id = ?
        ORDER BY a.start_time");
    $stmt->bind_param("si", $date, $doctor_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        echo "<table>
                <tr>
                    <th>Patient Name</th>
                    <th>Date</th>
                    <th>Start Time</th>
                    <th>End Time</th>
                    <th>View</th>
                    <th>Prescription</th>
                </tr>";
        while ($row = $result->fetch_assoc()) {
            echo "<tr>";
            echo "<td>" . htmlspecialchars($row['patient_name']) . "</td>";
            echo "<td>" . htmlspecialchars($row['date']) . "</td>";
            echo "<td>" . htmlspecialchars($row['start_time']) . "</td>";
            echo "<td>" . htmlspecialchars($row['end_time']) . "</td>";
            echo "<td><a href='patient_details.php?patient_id=" . urlencode($row['patient_id']) . "' class='btn'>Details</a></td>";
            echo "<td>
                    <form action='add_diagnosis.php' method='POST'>
                        <input type='hidden' name='appointment_id' value='" . htmlspecialchars($row['appointment_id']) . "'>
                        <input type='hidden' name='patient_id' value='" . htmlspecialchars($row['patient_id']) . "'>
                        <button type='submit' class='btn'>Prescription</button>
                    </form>
                  </td>";
            echo "</tr>";
        }
        echo "</table>";
    } else {
        echo "<p>No appointments found for this date.</p>";
    }

    $stmt->close();
    $conn->close();
}
?>

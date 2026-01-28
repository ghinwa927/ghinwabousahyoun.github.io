<?php
session_start();
include 'config.php';

$patient_id = $_GET['patient_id'] ?? null;
if (!$patient_id) {
    echo "<p style='color: red; text-align: center; padding: 20px;'>Invalid patient ID. Please provide a valid patient ID in the URL (e.g., patient_details.php?patient_id=123).</p>";
    exit;
}

// Fetch patient info
$stmt = $conn->prepare("SELECT name, blood_type, allergies, notes FROM users WHERE patient_id = ?");
if (!$stmt) {
    echo "Database prepare error (patient info): " . $conn->error;
    exit;
}
$stmt->bind_param("i", $patient_id);
if (!$stmt->execute()) {
    echo "Database execute error (patient info): " . $stmt->error;
    exit;
}
$result_patient = $stmt->get_result();
$patient = $result_patient->fetch_assoc();

if (!$patient) {
    echo "<p style='color: red; text-align: center; padding: 20px;'>No patient found with ID: " . htmlspecialchars($patient_id) . "</p>";
    $patient = ['name' => 'N/A', 'blood_type' => 'N/A', 'allergies' => 'N/A', 'notes' => 'N/A'];
}

// Fetch previous diagnoses
$stmt_diag = $conn->prepare("
    SELECT p.prescription_id, p.created_at, p.diagnosis, doc.fullname AS doctor_name
    FROM prescriptions p
    JOIN doctors doc ON p.doctor_id = doc.doctor_id
    WHERE p.patient_id = ?
    ORDER BY p.created_at DESC
");
if (!$stmt_diag) {
    echo "Database prepare error (diagnoses): " . $conn->error;
    exit;
}
$stmt_diag->bind_param("i", $patient_id);
if (!$stmt_diag->execute()) {
    echo "Database execute error (diagnoses): " . $stmt_diag->error;
    exit;
}
$diagnoses = $stmt_diag->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Patient Details - <?= htmlspecialchars($patient['name'] ?? 'N/A') ?></title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f4f7f6;
            padding: 20px;
        }
        .container {
            max-width: 900px;
            margin: auto;
            background-color: #fff;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        }
        h2, h3 {
            color: #0056b3;
            border-bottom: 2px solid #e0e0e0;
            padding-bottom: 10px;
        }
        .patient-info p strong {
            display: inline-block;
            width: 120px;
            color: #555;
        }
        .notes-content {
            background-color: #eef2f7;
            padding: 10px;
            border-left: 3px solid #0056b3;
            border-radius: 4px;
            white-space: pre-wrap;
        }
        .diagnosis-card {
            background-color: #f9f9f9;
            border: 1px solid #ddd;
            border-radius: 6px;
            margin-top: 20px;
            padding: 20px;
        }
        .label {
            font-weight: bold;
            color: #333;
        }
        .content {
            margin-left: 10px;
            padding-left: 10px;
            border-left: 2px solid #0056b3;
        }
        ul.medicine-list {
            margin-top: 10px;
            padding-left: 20px;
        }
        .medicine-list li {
            margin-bottom: 5px;
        }
        .no-diagnoses {
            color: #777;
            font-style: italic;
            padding: 15px;
            background-color: #f0f0f0;
            border-left: 4px solid #0056b3;
            border-radius: 4px;
        }
        .btn {
            display: inline-block;
            padding: 10px 15px;
            background-color:#0056b3;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            margin-top: 10px;
        }

        .btn:hover {
            background-color:  #004494;
        }

    </style>
</head>
<body>
    <div class="container">
        <h2>Patient Medical Information</h2>
        <div class="patient-info">
            <p><strong>Name:</strong> <?= htmlspecialchars($patient['name'] ?? 'N/A') ?></p>
            <p><strong>Blood Type:</strong> <?= htmlspecialchars($patient['blood_type'] ?? 'N/A') ?></p>
            <p><strong>Allergies:</strong> <?= htmlspecialchars($patient['allergies'] ?? 'N/A') ?></p>
            <p><strong>Notes:</strong></p>
            <div class="notes-content"><?= nl2br(htmlspecialchars($patient['notes'] ?? 'No additional notes.')) ?></div>
        </div>

        <h3>Previous Diagnoses</h3>
        <?php if ($diagnoses && $diagnoses->num_rows > 0): ?>
            <?php while ($row = $diagnoses->fetch_assoc()): ?>
                <div class="diagnosis-card">
                    <p><span class="label">Date:</span> <?= htmlspecialchars(date("F j, Y, g:i a", strtotime($row['created_at']))) ?></p>
                    <p><span class="label">Doctor:</span> <?= htmlspecialchars($row['doctor_name']) ?></p>
                    <div>
                        <p><span class="label">Diagnosis:</span></p>
                        <div class="content"><?= nl2br(htmlspecialchars($row['diagnosis'])) ?></div>
                    </div>
                    <div>
                        <p><span class="label">Medicines:</span></p>
                        <ul class="medicine-list">
                            <?php
                            // Fetch medicines for this prescription
                            $stmt_meds = $conn->prepare("
                                SELECT medicine_name, dosage_instruction
                                FROM prescription_medicines
                                WHERE prescription_id = ?
                            ");
                            if ($stmt_meds) {
                                $stmt_meds->bind_param("i", $row['prescription_id']);
                                $stmt_meds->execute();
                                $result_meds = $stmt_meds->get_result();
                                if ($result_meds->num_rows > 0) {
                                    while ($med = $result_meds->fetch_assoc()) {
                                        echo "<li><strong>" . htmlspecialchars($med['medicine_name']) . ":</strong> " . htmlspecialchars($med['dosage_instruction']) . "</li>";
                                    }
                                } else {
                                    echo "<li>No medicines listed.</li>";
                                }
                                $stmt_meds->close();
                            } else {
                                echo "<li>Error loading medicines.</li>";
                            }
                            ?>
                        </ul>
                    </div>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <p class="no-diagnoses">No previous diagnoses recorded for this patient.</p>
        <?php endif; ?>

             <a href="doctor_dashboard.php" class="btn">Back to dashboard</a>
    </div>
</body>
</html>
<?php
if (isset($stmt)) $stmt->close();
if (isset($stmt_diag)) $stmt_diag->close();
if (isset($conn)) $conn->close();
?>

<?php
session_start();
include 'config.php';

if (!isset($_SESSION['user_id'])) {
    echo "You must be logged in to view your medical record.";
    exit;
}

$patient_id = $_SESSION['user_id'];

$sql = "SELECT 
    a.id AS appointment_id,
    a.date, 
    a.time,
    d.fullname AS doctor_name,
    p.prescription_id, 
    p.diagnosis,
    GROUP_CONCAT(CONCAT(pm.medicine_name, ' - ', pm.dosage_instruction) SEPARATOR '\n') AS prescription_details
FROM appointment a
JOIN prescriptions p ON a.id = p.appointment_id
JOIN doctors d ON a.doctor_id = d.doctor_id
JOIN prescription_medicines pm ON p.prescription_id = pm.prescription_id
WHERE a.patient_id = ?
GROUP BY p.prescription_id
ORDER BY a.date DESC;";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $patient_id);
$stmt->execute();
$result = $stmt->get_result();
?>


<!DOCTYPE html>
<html>
<head>
  <title>Medical Record</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
  <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
  <link rel="stylesheet" href="history.css">
</head>
<body>
<div class="container">
  <header class="main-header">
        <h2 class="text-center mb-4">Medical Record</h2>
        <a href="user-dashboard.php" id="homeIcon" class="home-icon" title="Go to Dashboard"><i class="fas fa-home"></i></a>
    </header>

  <?php 
  $i = 0;
  if ($result->num_rows > 0):
    while ($row = $result->fetch_assoc()):
      $i++;
  ?>
  <div class="card mb-3">
    <div class="card-body">
      <h5 class="card-title">Appointment on <?= htmlspecialchars($row['date']) ?> at <?= htmlspecialchars($row['time']) ?></h5>
      <p class="card-text"><strong>Doctor:</strong> <?= htmlspecialchars($row['doctor_name']) ?></p>
      <p class="card-text"><strong>Diagnosis:</strong> <?= htmlspecialchars($row['diagnosis']) ?></p>
      <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#prescriptionModal<?= $i ?>">View Prescription</button>
    </div>
  </div>

  <!-- Bootstrap Modal -->
  <div class="modal fade" id="prescriptionModal<?= $i ?>" tabindex="-1">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Prescription</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <div class="prescription-style" id="prescriptionContent<?= $i ?>">
            <h3 class="text-center"><?php echo htmlspecialchars($_SESSION['username'])?> Prescription</h3>
            <hr>
           <p><strong>Date:</strong> <?= htmlspecialchars($row['date']) ?></p>
           <p><strong>Doctor:</strong> <?= htmlspecialchars($row['doctor_name']) ?></p>
           <p><strong>Diagnosis:</strong> <?= nl2br(htmlspecialchars($row['diagnosis'])) ?></p>
           <p><strong>Prescription:</strong><br><?= nl2br(htmlspecialchars($row['prescription_details'])) ?></p>

          </div>
        </div>
        <div class="modal-footer">
          <button class="btn btn-success" onclick="downloadPDF('prescriptionContent<?= $i ?>')">Download PDF</button>
        </div>
      </div>
    </div>
  </div>
  <?php endwhile; else: ?>
    <p class="text-center">No past appointments or prescriptions found.</p>
  <?php endif; ?>
</div>

<!--for downloading prescription properly-->
<script>
function downloadPDF(containerId) {
  const doc = new window.jspdf.jsPDF('p', 'pt', 'a4');  // A4 page
  const content = document.getElementById(containerId);

  doc.html(content, {
    callback: function (doc) {
      doc.save("prescription.pdf");
      alert("Prescription PDF saved to your device.");
    },
    x: 10,
    y: 10,
    width: 600,        // fits well on A4 width
    windowWidth: 1200  // virtual viewport width for HTML
  });
}

</script>

</body>
</html>

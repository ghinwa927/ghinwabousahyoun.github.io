<?php
session_start();
include 'config.php';

$isLoggedIn = isset($_SESSION['user_id']); // Check if user is logged in
$specialty = $_GET['specialty'] ?? 'all';

$sql = ($specialty !== "all") ? "SELECT * FROM doctors WHERE TRIM(specialty) = TRIM('$specialty')" : "SELECT * FROM doctors";

$result = $conn->query($sql);

echo "<div class='doctor-grid'>";
while ($row = $result->fetch_assoc()) {
    echo "<div class='doctor-card'>";
    
   
        echo "<a href='doctor_profile.php?id={$row['doctor_id']}' class='doctor-link'>
                <img src='../images/{$row['image']}' alt='Dr. {$row['fullname']}' class='doctor-image'>
              </a>";

         echo "<h2>{$row['fullname']}</h2>";
         echo "<p>Specialty: {$row['specialty']}</p>";
         echo "</div>";
}
echo "</div>";
?>

<script>
document.addEventListener("DOMContentLoaded", function() {
    document.querySelectorAll("[data-disabled='true']").forEach(img => {
        img.addEventListener("click", function(event) {
            event.preventDefault(); // Prevent default click behavior
        });
    });
});
</script>
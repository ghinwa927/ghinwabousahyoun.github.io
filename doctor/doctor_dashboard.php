<?php
include 'config.php';
session_start();

if (!isset($_SESSION['doctor_id'])) {
    header("Location: index.php");
 exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Doctor Dashboard</title>
    <link rel="stylesheet" href="doctor_dashboard.css">
    
</head>
<body>

<header>
    <h1>Welcome, Dr <?php echo $_SESSION['doctor_name']; ?></h1>
</header>

<div class="sidebar">
    <a href="manage_availability.php">Manage Availability</a>
    <a href="edit_profile.php">Edit Profile</a>
    <a href="change_password.php">Change password</a>
    <a href="index.php">Logout</a>
</div>

<div class="main-content">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>

<div class="card">
    <h2>Upcoming Appointments</h2>
    
    <input type="text" id="appointmentCalendar" placeholder="Select a date" class="calendar-input">
    <div id="appointmentsList" class="appointment-list">
        <p>Please select a date to view upcoming appointments.</p>
    </div>
</div>

<script>
flatpickr("#appointmentCalendar", {
    dateFormat: "Y-m-d",
    defaultDate: new Date(), // Sets today as default
    onChange: function(selectedDates, dateStr) {
        fetchAppointments(dateStr);
    }
});

// Automatically fetch today's appointments when page loads
document.addEventListener("DOMContentLoaded", function () {
    const today = new Date().toISOString().split('T')[0]; // Format as YYYY-MM-DD
    document.getElementById("appointmentCalendar").value = today;
    fetchAppointments(today);
});

function fetchAppointments(date) {
    const xhr = new XMLHttpRequest();
    xhr.open("GET", "get_appointments_by_date.php?date=" + date, true);
    xhr.onload = function () {
        if (this.status === 200) {
            document.getElementById("appointmentsList").innerHTML = this.responseText;
        }
    };
    xhr.send();
}
</script>


    <div class="card">
        <h2>Manage Availability</h2>
        <a href="manage_availability.php" class="btn">Set Available Times</a>
    </div>

    <div class="card">
        <h2>Edit Profile</h2>
        <a href="edit_profile.php" class="btn">Edit Profile</a>
        <a href="change_password.php" class="btn" style="background-color: #555;">Change Password</a>
    </div>

   <!-- <div class="card">
        <h2>Add Diagnosis & Prescriptions</h2>
        <a href="add_diagnosis.php" class="btn">Add Now</a>
    </div>-->
</div>

</body>
</html>

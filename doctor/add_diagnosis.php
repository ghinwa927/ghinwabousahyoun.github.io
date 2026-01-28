<?php
session_start();
include 'config.php';

// Get doctor_id from session
$doctor_id = $_SESSION['doctor_id'] ?? null;

$message = '';
$message_type = '';

// get data from form
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // form data
    $patient_id = $_POST['patient_id'] ?? null;
    $appointment_id = $_POST['appointment_id'] ?? null;
    $diagnosis = trim($_POST['diagnosis'] ?? '');
    
    // Collect medicine names and dosage instructions
    $medicine_names = $_POST['medicine_name'] ?? [];
    $dosage_instructions = $_POST['dosage_instruction'] ?? [];

    // Validate fields
    if (!$doctor_id) {
        $message = "Error: Doctor not identified. Please log in.";
        $message_type = "error";
    } elseif (!$patient_id || !$appointment_id || empty($diagnosis) || empty($medicine_names) || empty($dosage_instructions)) {
        $message = "Please fill in all fields.";
        $message_type = "error";
    } else {
        $conn->begin_transaction();

        try {
            $sql = "INSERT INTO prescriptions (patient_id, doctor_id, appointment_id, diagnosis, created_at) 
                    VALUES (?, ?, ?, ?, NOW())";

            $stmt = $conn->prepare($sql);
            $stmt->bind_param("iiis", $patient_id, $doctor_id, $appointment_id, $diagnosis);
            $stmt->execute();
            $prescription_id = $stmt->insert_id; // Get the inserted prescription ID
            $stmt->close();

            foreach ($medicine_names as $index => $medicine_name) {
                $dosage_instruction = $dosage_instructions[$index] ?? '';

                if ($medicine_name && $dosage_instruction) {
                    $sql = "INSERT INTO prescription_medicines (prescription_id, medicine_name, dosage_instruction) 
                            VALUES (?, ?, ?)";

                    $stmt = $conn->prepare($sql);
                    $stmt->bind_param("iss", $prescription_id, $medicine_name, $dosage_instruction);
                    $stmt->execute();
                    $stmt->close();
                }
            }

            $conn->commit();

            $message = "Prescription saved successfully!";
            $message_type = "success";

        } catch (Exception $e) {
            $conn->rollback();
            $message = "Error saving prescription: " . $e->getMessage();
            $message_type = "error";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Add Prescription</title>
    <style>
       /* General Body Styles (if not already defined elsewhere) */
body {
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    background-color: #f4f7f6;
    color: #333;
    margin: 0;
    padding: 20px;
    display: flex;
    justify-content: center; /* Center the container */
    align-items: flex-start; /* Align to top if content is short */
    min-height: 100vh;
    box-sizing: border-box;
}

/* Container (acting as a card) */
.container {
    background-color: #ffffff;
    padding: 25px 30px;
    border-radius: 8px;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
    width: 100%;
    max-width: 700px; /* Adjust as needed */
    margin-top: 20px;
    margin-bottom: 20px;
}

.container h2 {
    color: #2c3e50;
    text-align: center;
    margin-top: 0;
    margin-bottom: 25px;
    font-size: 1.8em;
}

/* Message/Alert Styling */
.message {
    padding: 12px 15px;
    margin-bottom: 20px;
    border-radius: 5px;
    font-size: 0.95em;
    text-align: center;
}

.message.success { /* Example: if $message_type is 'success' */
    background-color: #d4edda;
    color: #155724;
    border: 1px solid #c3e6cb;
}

.message.error { /* Example: if $message_type is 'error' */
    background-color: #f8d7da;
    color: #721c24;
    border: 1px solid #f5c6cb;
}

/* Form Group Styling */
.form-group {
    margin-bottom: 20px;
}

.form-group label {
    display: block;
    font-weight: 600;
    margin-bottom: 8px;
    color: #34495e;
}

/* Textarea Styling */
.form-group textarea {
    width: 100%;
    padding: 10px 12px;
    border: 1px solid #ddd;
    border-radius: 5px;
    box-sizing: border-box;
    font-size: 1em;
    line-height: 1.5;
    transition: border-color 0.3s ease;
    min-height: 100px; /* Ensure decent height */
}

.form-group textarea:focus {
    border-color: #3498db;
    outline: none;
    box-shadow: 0 0 0 2px rgba(52, 152, 219, 0.2);
}

/* Medicine List Styling */
#medicine-list {
    margin-bottom: 20px;
}

.medicine-row {
    display: flex;
    gap: 10px; /* Space between inputs and remove button */
    margin-bottom: 10px;
    align-items: center; /* Vertically align items if heights differ */
}

.medicine-row input[type="text"] {
    flex-grow: 1; /* Allow inputs to take available space */
    padding: 10px 12px;
    border: 1px solid #ddd;
    border-radius: 5px;
    box-sizing: border-box;
    font-size: 1em;
    transition: border-color 0.3s ease;
}
.medicine-row input[type="text"]::placeholder {
    color: #aaa;
}

.medicine-row input[type="text"]:focus {
    border-color: #3498db;
    outline: none;
    box-shadow: 0 0 0 2px rgba(52, 152, 219, 0.2);
}

/* Style for a hypothetical remove button for medicine rows */
.medicine-row .remove-medicine-btn {
    background-color: #e74c3c;
    color: white;
    border: none;
    border-radius: 5px;
    padding: 8px 12px; /* Slightly smaller than main buttons */
    cursor: pointer;
    font-size: 0.9em;
    transition: background-color 0.3s ease;
    flex-shrink: 0; /* Prevent button from shrinking */
}

.medicine-row .remove-medicine-btn:hover {
    background-color: #c0392b;
}


/* Button Styling */
/* Primary button (e.g., Save Prescription) */
button[type="submit"], button#add-medicine-btn {
    background-color:rgb(14, 75, 155);
    color: white;
    padding: 10px 20px;
    border: none;
    border-radius: 5px;
    cursor: pointer;
    font-size: 1em;
    font-weight: 500;
    text-align: center;
    text-decoration: none;
    transition: background-color 0.3s ease, transform 0.1s ease;
    margin-right: 10px; /* Space between buttons if they are inline */
}

button[type="submit"]:hover, button#add-medicine-btn:hover {
    background-color:rgb(10, 82, 131);
    transform: translateY(-1px);
}

button[type="submit"]:active, button#add-medicine-btn:active {
    transform: translateY(0);
}

/* Secondary/Utility button (e.g., Add Another Medicine) */
button#add-medicine-btn {
    background-color:rgb(14, 75, 155); /* Green for "add" actions */
    margin-bottom: 20px; /* Give it some space before the submit button area */
}
button#add-medicine-btn:hover {
    background-color:rgb(36, 63, 197);
}


/* Link styled as a button (e.g., Back to Dashboard) */
a.btn {
    display: inline-block; /* To allow padding and margin */
    background-color: #7f8c8d;
    color: white !important; /* Important to override default link color */
    padding: 10px 20px;
    border-radius: 5px;
    text-decoration: none;
    font-size: 1em;
    font-weight: 500;
    transition: background-color 0.3s ease, transform 0.1s ease;
    vertical-align: middle; /* Align with buttons if on the same line */
}

a.btn:hover {
    background-color: #6c7a7b;
    transform: translateY(-1px);
    text-decoration: none; /* Ensure no underline on hover */
}
a.btn:active {
    transform: translateY(0);
}

/* Optional: Add a wrapper for the final action buttons for better layout */
.form-actions {
    margin-top: 20px;
    display: flex;
    justify-content: flex-start; /* Or center, space-between */
    align-items: center;
}

    </style>
</head>
<body>
    <div class="container">
        <h2>Add Prescription</h2>

        <?php if ($message): ?>
            <div class="message <?= htmlspecialchars($message_type) ?>">
                <?= htmlspecialchars($message) ?>
            </div>
        <?php endif; ?>

        <form method="POST" action="">
            <!-- Hidden inputs for patient_id, appointment_id, doctor_id -->
            <input type="hidden" name="patient_id" value="<?= htmlspecialchars($patient_id) ?>">
            <input type="hidden" name="appointment_id" value="<?= htmlspecialchars($appointment_id) ?>">
            <input type="hidden" name="doctor_id" value="<?= htmlspecialchars($doctor_id) ?>">

            <!-- Diagnosis field -->
            <div class="form-group">
                <label for="diagnosis">Diagnosis:</label>
                <textarea id="diagnosis" name="diagnosis" rows="5" required><?= htmlspecialchars($_POST['diagnosis'] ?? '') ?></textarea>
            </div>

            <!-- Medicines List -->
            <div id="medicine-list">
                <div class="medicine-row">
                    <input type="text" name="medicine_name[]" placeholder="Medicine Name" required>
                    <input type="text" name="dosage_instruction[]" placeholder="Dosage Instruction" required>
                </div>
            </div>

            <!-- Add More Medicines Button -->
            <button type="button" id="add-medicine-btn">Add Another Medicine</button>

            <br><br>

            <!-- Submit Button -->
            <button type="submit">Save Prescription</button>
            <a href="doctor_dashboard.php" class="btn">Back to dashboard</a>
        </form>
    </div>

    <script>
        // Add new medicine row when the 'Add Another Medicine' button is clicked
        document.getElementById('add-medicine-btn').addEventListener('click', function() {
            const medicineList = document.getElementById('medicine-list');
            const newRow = document.createElement('div');
            newRow.classList.add('medicine-row');
            newRow.innerHTML = `
                <input type="text" name="medicine_name[]" placeholder="Medicine Name" required>
                <input type="text" name="dosage_instruction[]" placeholder="Dosage Instruction (e.g., once a day)" required>
            `;
            medicineList.appendChild(newRow);
        });
    </script>
</body>
</html>

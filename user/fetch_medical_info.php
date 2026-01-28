<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Medical Info</title>
    <link rel="stylesheet" href="fetchmedicalinfo.css">
    <script>
        function addAllergyField() {
            const container = document.getElementById("allergy-container");
            const newInput = document.createElement("input");
            newInput.type = "text";
            newInput.name = "allergies[]";
            newInput.placeholder = "Enter another allergy";
            container.appendChild(newInput);
        }
    </script>
</head>
<body>

<h2>Medical Information</h2>

<form action="submit_medical_info.php" method="POST">
    <div class="form-group">
        <label for="allergies">Allergies:</label>
        <div class="allergy-container" id="allergy-container">
            <input type="text" name="allergies[]" placeholder="Enter your known allergies">
        </div>
        <button type="button" onclick="addAllergyField()">Add Allergy</button>
    </div>

    <div class="form-group">
        <label for="blood_type">Blood Type:</label>
        <select id="blood_type" name="blood_type">
            <option value="" disabled selected>Select your blood type</option>
            <option value="A+">A+</option>
            <option value="A-">A-</option>
            <option value="B+">B+</option>
            <option value="B-">B-</option>
            <option value="AB+">AB+</option>
            <option value="AB-">AB-</option>
            <option value="O+">O+</option>
            <option value="O-">O-</option>
        </select>
    </div>

    <div class="form-group">
        <label for="notes">Additional Notes (Optional):</label>
        <textarea id="notes" name="notes" rows="4" placeholder="Any other important medical information?"></textarea>
    </div>

    <button type="submit">Submit</button>
</form>

</body>
</html>
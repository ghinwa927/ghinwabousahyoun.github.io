<?php
$conn = new mysqli("localhost", "root", "", "senior");
if ($conn->connect_error) {
    http_response_code(500);
    echo "Connection failed";
    exit;
}

$id = $_POST['id'] ?? null;
$status = $_POST['status'] ?? null;

if ($id && $status) {
    $stmt = $conn->prepare("UPDATE appointment SET status = ? WHERE id = ?");
    $stmt->bind_param("si", $status, $id);
    if ($stmt->execute()) {
        echo "Status updated";
    } else {
        http_response_code(500);
        echo "Update failed";
    }
} else {
    http_response_code(400);
    echo "Missing data";
}
?>
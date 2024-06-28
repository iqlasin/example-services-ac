<?php
session_start();
if (!isset($_SESSION['username']) || $_SESSION['role'] != 'teknisi') {
    header('Location: ../auth/login.php');
    exit();
}
require '../config/config.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id = $_POST['id'];
    $status = strtolower($_POST['status']);

    // Convert visual "proses" to database "dikerjakan"
    if ($status == 'proses') {
        $status = 'dikerjakan';
    }

    $stmt = $con->prepare("UPDATE service SET status=? WHERE id=?");
    if ($stmt) {
        $stmt->bind_param("si", $status, $id);
        if ($stmt->execute()) {
            $message = 'Status updated successfully';
        } else {
            $error = 'Error updating status: ' . $stmt->error;
        }
        $stmt->close();
    } else {
        $error = 'Prepare failed: ' . $con->error;
    }
    header('Location: manage_jobs.php');
}
?>

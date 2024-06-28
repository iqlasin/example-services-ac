<?php
require '../config/config.php';

$response = array('success' => false);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id = $_POST['id'];

    // Option 1: Check if there are dependent records in 'service' table
    $stmt_check = $con->prepare("SELECT COUNT(*) FROM service WHERE user_id = ?");
    $stmt_check->bind_param("i", $id);
    $stmt_check->execute();
    $stmt_check->bind_result($count);
    $stmt_check->fetch();
    $stmt_check->close();

    if ($count > 0) {
        // Option 1: Delete dependent records in 'service' table first
        $stmt_delete_service = $con->prepare("DELETE FROM service WHERE user_id = ?");
        $stmt_delete_service->bind_param("i", $id);
        $stmt_delete_service->execute();
        $stmt_delete_service->close();
    }

    // Now attempt to delete from 'users' table
    $stmt = $con->prepare("DELETE FROM users WHERE id = ?");
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        $response['success'] = true;
    }

    $stmt->close();
}

echo json_encode($response);
?>

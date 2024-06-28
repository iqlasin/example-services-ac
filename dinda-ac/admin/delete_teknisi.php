<?php
require '../config/config.php';

$response = array('success' => false);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id = $_POST['id'];

    $stmt = $con->prepare("DELETE FROM teknisi WHERE id=?");
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        $response['success'] = true;
    }

    $stmt->close();
}

echo json_encode($response);
?>

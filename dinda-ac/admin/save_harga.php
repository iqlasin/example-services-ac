<?php
require '../config/config.php';

$response = array('success' => false);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id = isset($_POST['id']) ? $_POST['id'] : '';
    $service_ac = $_POST['service_ac'];

    if ($id) {
        // Update existing harga
        $stmt = $con->prepare("UPDATE harga SET service_ac=? WHERE id=?");
        $stmt->bind_param("di", $service_ac, $id);
    } else {
        // Insert new harga
        $stmt = $con->prepare("INSERT INTO harga (service_ac) VALUES (?)");
        $stmt->bind_param("d", $service_ac);
    }

    if ($stmt->execute()) {
        $response['success'] = true;
    }

    $stmt->close();
}

echo json_encode($response);
?>

<?php
require '../config/config.php';

$response = array('success' => false);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id = $_POST['id'];
    $nama_service = $_POST['nama_service'];
    $deskripsi = $_POST['deskripsi'];
    $harga = $_POST['harga'];

    if ($id) {
        // Update existing service
        $stmt = $con->prepare("UPDATE services SET nama_service=?, deskripsi=?, harga=? WHERE id=?");
        $stmt->bind_param("ssdi", $nama_service, $deskripsi, $harga, $id);
    } else {
        // Insert new service
        $stmt = $con->prepare("INSERT INTO services (nama_service, deskripsi, harga) VALUES (?, ?, ?)");
        $stmt->bind_param("ssd", $nama_service, $deskripsi, $harga);
    }

    if ($stmt->execute()) {
        $response['success'] = true;
    }

    $stmt->close();
}

echo json_encode($response);
?>

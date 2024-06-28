<?php
require '../config/config.php';

$response = array('success' => false);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id = $_POST['id'];
    $username = $_POST['username'];
    $role = $_POST['role'];
    $alamat = $_POST['alamat'];
    $nomor_hp = $_POST['nomor_hp'];
    $password = $_POST['password'];

    if ($id) {
        // Update existing user
        if (!empty($password)) {
            $password_hashed = password_hash($password, PASSWORD_BCRYPT);
            $stmt = $con->prepare("UPDATE users SET username=?, role=?, alamat=?, nomor_hp=?, password=? WHERE id=?");
            $stmt->bind_param("sssssi", $username, $role, $alamat, $nomor_hp, $password_hashed, $id);
        } else {
            $stmt = $con->prepare("UPDATE users SET username=?, role=?, alamat=?, nomor_hp=? WHERE id=?");
            $stmt->bind_param("ssssi", $username, $role, $alamat, $nomor_hp, $id);
        }
    } else {
        // Insert new user
        $password_hashed = password_hash($password, PASSWORD_BCRYPT);
        $stmt = $con->prepare("INSERT INTO users (username, password, role, alamat, nomor_hp) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("sssss", $username, $password_hashed, $role, $alamat, $nomor_hp);
    }

    if ($stmt->execute()) {
        $response['success'] = true;
    }

    $stmt->close();
}

echo json_encode($response);
?>

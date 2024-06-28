<?php
require '../config/config.php';

// Data admin
$username = 'admin';
$password = password_hash('admin', PASSWORD_DEFAULT); // Hash password 'admin'
$role = 'admin';
$alamat = 'admin';
$nomor_hp = 'admin';

$stmt = $con->prepare("INSERT INTO users (username, password, role, alamat, nomor_hp) VALUES (?, ?, ?, ?, ?)");
$stmt->bind_param("sssss", $username, $password, $role, $alamat, $nomor_hp);

if ($stmt->execute()) {
    echo "Admin user inserted successfully.";
} else {
    echo "Error inserting admin user: " . $stmt->error;
}

$stmt->close();
$con->close();
?>

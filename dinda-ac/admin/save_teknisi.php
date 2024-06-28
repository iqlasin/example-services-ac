<?php
require '../config/config.php';

$response = array('success' => false);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id = $_POST['id'];
    $nama = $_POST['nama'];
    $alamat = $_POST['alamat'];
    $nomor_hp = $_POST['nomor_hp'];
    $spesialisasi = $_POST['spesialisasi'];
    $password = password_hash($_POST['password'], PASSWORD_BCRYPT);

    if ($id) {
        // Update existing technician
        $stmt = $con->prepare("UPDATE teknisi SET nama=?, alamat=?, nomor_hp=?, spesialisasi=? WHERE id=?");
        $stmt->bind_param("ssssi", $nama, $alamat, $nomor_hp, $spesialisasi, $id);

        // Update existing user
        $stmt2 = $con->prepare("UPDATE users SET username=?, password=?, alamat=?, nomor_hp=?, role='teknisi' WHERE id=?");
        $stmt2->bind_param("ssssi", $nama, $password, $alamat, $nomor_hp, $id);
    } else {
        // Insert new technician
        $stmt = $con->prepare("INSERT INTO teknisi (nama, alamat, nomor_hp, spesialisasi) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ssss", $nama, $alamat, $nomor_hp, $spesialisasi);

        // Insert new user
        $stmt2 = $con->prepare("INSERT INTO users (username, password, alamat, nomor_hp, role) VALUES (?, ?, ?, ?, 'teknisi')");
        $stmt2->bind_param("ssss", $nama, $password, $alamat, $nomor_hp);
    }

    if ($stmt->execute() && $stmt2->execute()) {
        $response['success'] = true;
    }

    $stmt->close();
    $stmt2->close();
}

echo json_encode($response);
?>

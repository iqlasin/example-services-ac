<?php
require '../config/config.php';

$query_ac = "SELECT COUNT(*) AS acCount FROM service WHERE service_ac = 1";
$stmt_ac = $con->prepare($query_ac);
$stmt_ac->execute();
$result_ac = $stmt_ac->get_result();
$acCount = $result_ac->fetch_assoc()['acCount'];
$stmt_ac->close();

$otherCount = 0; // Ganti dengan query yang sesuai untuk layanan lainnya

$output = [
    'acCount' => $acCount,
    'otherCount' => $otherCount
];

header('Content-Type: application/json');
echo json_encode($output);
?>

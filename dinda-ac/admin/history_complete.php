<?php
session_start();
include '../config/config.php';
include '../includes/header.php';

if (!isset($_SESSION['username']) || $_SESSION['role'] != 'admin') {
    http_response_code(403);
    exit("Unauthorized");
}

$query = "
    SELECT s.id, u.username AS user_name, t.nama AS teknisi_name, s.tanggal_servis, s.location, s.harga, s.deskripsi
    FROM service s
    INNER JOIN users u ON s.user_id = u.id
    INNER JOIN teknisi t ON s.teknisi_id = t.id
    WHERE s.status = 'selesai'
    ORDER BY s.tanggal_servis DESC
";

$stmt = $con->prepare($query);
$stmt->execute();
$result = $stmt->get_result();

$services = [];
while ($row = $result->fetch_assoc()) {
    $services[] = $row;
}

$stmt->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Completed Services History</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.11.4/css/jquery.dataTables.css">
    <style>
        .container {
            margin-top: 50px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="row">
            <div class="col-12">
                <h2 class="text-center">Completed Services History</h2>
                <div class="mb-3">
                    <button class="btn btn-primary" onclick="window.print()">Print</button>
                </div>
            </div>
        </div>
        
        <div class="row">
            <div class="col-12">
                <table id="completedServicesTable" class="table table-striped table-bordered">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>User Name</th>
                            <th>Technician Name</th>
                            <th>Date</th>
                            <th>Location</th>
                            <th>Price</th>
                            <th>Description</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($services as $service): ?>
                            <tr>
                                <td><?php echo $service['id']; ?></td>
                                <td><?php echo $service['user_name']; ?></td>
                                <td><?php echo $service['teknisi_name']; ?></td>
                                <td><?php echo $service['tanggal_servis']; ?></td>
                                <td><?php echo $service['location']; ?></td>
                                <td><?php echo $service['harga']; ?></td>
                                <td><?php echo $service['deskripsi']; ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/jquery@3.6.0/dist/jquery.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.4/js/jquery.dataTables.js"></script>
    <script>
        $(document).ready(function() {
            $('#completedServicesTable').DataTable();
        });
    </script>

</body>
</html>

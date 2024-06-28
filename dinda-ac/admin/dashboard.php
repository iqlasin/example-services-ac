<?php
session_start();
if (!isset($_SESSION['username']) || $_SESSION['role'] != 'admin') {
    header('Location: ../auth/login.php');
    exit();
}
include '../includes/header.php';
require '../config/config.php';

// Ambil data jumlah pesanan per bulan dari database
$query_orders = "
    SELECT MONTH(tanggal_servis) AS month, COUNT(*) AS order_count
    FROM service
    GROUP BY MONTH(tanggal_servis)
    ORDER BY MONTH(tanggal_servis)
";
$stmt_orders = $con->prepare($query_orders);
$stmt_orders->execute();
$result_orders = $stmt_orders->get_result();

$order_data = [];
while ($row = $result_orders->fetch_assoc()) {
    $order_data[] = $row;
}

$stmt_orders->close();

// Query untuk menghitung total pendapatan berdasarkan layanan AC yang sudah selesai
$query_total_pendapatan = "
    SELECT SUM(s.harga) AS total_pendapatan
    FROM service s
    WHERE s.status = 'selesai'
";

$stmt_total_pendapatan = $con->prepare($query_total_pendapatan);
if ($stmt_total_pendapatan === false) {
    // Handle error if preparation fails
    die('Error preparing statement for total pendapatan: ' . $con->error);
}

$result_total_pendapatan = $stmt_total_pendapatan->execute();
if ($result_total_pendapatan === false) {
    // Handle error if execution fails
    die('Error executing statement for total pendapatan: ' . $stmt_total_pendapatan->error);
}

$result_total_pendapatan = $stmt_total_pendapatan->get_result();
$total_pendapatan = $result_total_pendapatan->fetch_assoc()['total_pendapatan'] ?? 0;

$stmt_total_pendapatan->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        .card {
            height: 100%;
        }
    </style>
</head>
<body>
    <div class="container mt-5">
        <div class="row">
            <div class="col-12">
                <h2 class="text-center mb-4">Admin Dashboard</h2>
                <p class="text-center fs-5">Selamat datang, <?php echo $_SESSION['username']; ?>!</p>
            </div>
        </div>
        <div class="row">
            <div class="col-md-9">
                <div class="card mb-4 shadow">
                    <div class="card-body">
                        <h5 class="card-title mb-4">Monthly Orders & Total Pendapatan</h5>
                        <div class="row">
                            <div class="col-md-6">
                                <canvas id="ordersChart" style="height: 300px;"></canvas>
                            </div>
                            <div class="col-md-6">
                                <div class="card mb-4 shadow">
                                    <div class="card-body">
                                        <h5 class="card-title mb-4">Total Pendapatan</h5>
                                        <p class="fs-4">Rp <?php echo number_format($total_pendapatan, 2, ',', '.'); ?></p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card mb-4 shadow">
                    <div class="card-body">
                        <h5 class="card-title mb-3">Menu</h5>
                        <ul class="list-group list-group-flush">
                            <li class="list-group-item"><a href="manage_users.php"><i class="fas fa-users me-2"></i>Manage Users</a></li>
                            <li class="list-group-item"><a href="manage_teknisi.php"><i class="fas fa-wrench me-2"></i>Manage Technicians</a></li>
                            <li class="list-group-item"><a href="manage_service.php"><i class="fas fa-toolbox me-2"></i>Manage Services</a></li>
                            <li class="list-group-item"><a href="manage_harga.php"><i class="fas fa-money-bill-wave me-2"></i>Manage Harga</a></li>
                            <li class="list-group-item"><a href="history_complete.php"><i class="fas fa-history me-2"></i>Completed Services History</a></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            // Data untuk grafik jumlah pesanan per bulan
            const orderData = <?php echo json_encode($order_data); ?>;

            const orderLabels = [];
            const orderCounts = [];

            orderData.forEach(order => {
                orderLabels.push(new Date(2020, order.month - 1).toLocaleString('default', { month: 'long' }));
                orderCounts.push(order.order_count);
            });

            // Grafik jumlah pesanan per bulan
            const ctxOrders = document.getElementById('ordersChart').getContext('2d');
            const ordersChart = new Chart(ctxOrders, {
                type: 'bar',
                data: {
                    labels: orderLabels,
                    datasets: [{
                        label: 'Total Orders',
                        data: orderCounts,
                        backgroundColor: 'rgba(54, 162, 235, 0.2)',
                        borderColor: 'rgba(54, 162, 235, 1)',
                        borderWidth: 1
                    }]
                },
                options: {
                    scales: {
                        y: {
                            beginAtZero: true,
                            stepSize: 1
                        }
                    }
                }
            });
        });
    </script>
</body>
</html>

<?php include '../includes/footer.php'; ?>

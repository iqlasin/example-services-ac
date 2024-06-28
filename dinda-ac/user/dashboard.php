<?php
session_start();
if (!isset($_SESSION['username']) || $_SESSION['role'] != 'user') {
    header('Location: ../auth/login.php');
    exit();
}
include '../includes/header.php';
require '../config/config.php';

// Ambil data jumlah pesanan per bulan dari database
$user_id = $_SESSION['username'];
$user_query = $con->prepare("SELECT id FROM users WHERE username=?");
$user_query->bind_param("s", $user_id);
$user_query->execute();
$user_result = $user_query->get_result();
$user_data = $user_result->fetch_assoc();
$user_id = $user_data['id'];

$query = "
    SELECT MONTH(tanggal_servis) AS month, COUNT(*) AS order_count
    FROM service
    WHERE user_id = ?
    GROUP BY MONTH(tanggal_servis)
    ORDER BY MONTH(tanggal_servis)
";
$stmt = $con->prepare($query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

$order_data = [];
while ($row = $result->fetch_assoc()) {
    $order_data[] = $row;
}

$stmt->close();
$user_query->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>
    <div class="container mt-5">
        <div class="row">
            <div class="col-12">
                <h2 class="text-center">User Dashboard</h2>
                <p class="text-center">Welcome, <?php echo $_SESSION['username']; ?>!</p>
            </div>
        </div>
        <div class="row">
            <div class="col-md-6">
                <div class="card mb-4">
                    <div class="card-body">
                        <h5 class="card-title">Services</h5>
                        <ul class="list-group list-group-flush">
                            <li class="list-group-item"><a href="order_service.php">Order Service</a></li>
                            <li class="list-group-item"><a href="view_orders.php">View Your Orders</a></li>
                            <li class="list-group-item"><a href="profile.php">Profile</a></li>
                            <!-- <li class="list-group-item"><a href="rating_user">Rate for Me</a> -->
                        </ul>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card mb-4">
                    <div class="card-body">
                        <h5 class="card-title">Order Statistics</h5>
                        <canvas id="ordersChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const orderData = <?php echo json_encode($order_data); ?>;

            const labels = [];
            const data = [];

            orderData.forEach(order => {
                labels.push(new Date(2020, order.month - 1).toLocaleString('default', { month: 'long' }));
                data.push(order.order_count);
            });

            const ctx = document.getElementById('ordersChart').getContext('2d');
            const ordersChart = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: labels,
                    datasets: [{
                        label: '# of Orders',
                        data: data,
                        backgroundColor: 'rgba(54, 162, 235, 0.2)',
                        borderColor: 'rgba(54, 162, 235, 1)',
                        borderWidth: 1
                    }]
                },
                options: {
                    scales: {
                        y: {
                            beginAtZero: true
                        }
                    }
                }
            });
        });
    </script>
</body>
</html>

<?php include '../includes/footer.php'; ?>

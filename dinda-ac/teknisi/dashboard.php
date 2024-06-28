<?php
session_start();
if (!isset($_SESSION['username']) || $_SESSION['role'] != 'teknisi') {
    header('Location: ../auth/login.php');
    exit();
}
require '../config/config.php';

$teknisi_username = $_SESSION['username'];

// Get teknisi ID based on username
$teknisi_result = $con->query("SELECT id FROM teknisi WHERE nama='$teknisi_username'");
if ($teknisi_result && $teknisi_result->num_rows > 0) {
    $teknisi_data = $teknisi_result->fetch_assoc();
    $teknisi_id = $teknisi_data['id'];
} else {
    die('Teknisi not found');
}

// Get jobs for teknisi
$jobs_result = $con->query("SELECT * FROM service WHERE teknisi_id=$teknisi_id");

// Get monthly statistics
$stats_result = $con->query("SELECT MONTH(tanggal_servis) as month, COUNT(*) as count FROM service WHERE teknisi_id=$teknisi_id GROUP BY MONTH(tanggal_servis)");
$monthly_stats = [];
while ($row = $stats_result->fetch_assoc()) {
    $monthly_stats[] = $row;
}

include '../includes/header.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Technician Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>
    <div class="container mt-5">
        <div class="row">
            <div class="col-12">
                <h2 class="text-center">Dashboard Teknisi</h2>
                <p class="text-center">Selamat Datang, <?php echo $_SESSION['username']; ?>!</p>
            </div>
        </div>
        <div class="row">
            <div class="col-md-6">
                <div class="card mb-4">
                    <div class="card-body">
                        <h5 class="card-title">Pekerjaan yang diberikan</h5>
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Tanggal Servis</th>
                                    <th>Deskripsi</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while ($row = $jobs_result->fetch_assoc()): ?>
                                <tr>
                                    <td><?php echo $row['tanggal_servis']; ?></td>
                                    <td><?php echo $row['deskripsi']; ?></td>
                                    <!-- <td><?php echo $row['status']; ?></td> -->
                                    <td><?php echo $row['status'] == 'dikerjakan' ? 'proses' : $row['status']; ?></td>

                                </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card mb-4">
                    <div class="card-body">
                        <h5 class="card-title">Statistik pekerjaan</h5>
                        <canvas id="ordersChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-12 text-center">
                <a href="manage_jobs.php" class="btn btn-primary">Manage Jobs</a>
            </div>
        </div>
    </div>
    <script>
        // Prepare data for the chart
        const monthlyStats = <?php echo json_encode($monthly_stats); ?>;
        const months = ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'];
        const labels = months.slice(0, monthlyStats.length);
        const data = monthlyStats.map(stat => stat.count);

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
    </script>
</body>
</html>

<?php include '../includes/footer.php'; ?>

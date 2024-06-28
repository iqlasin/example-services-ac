<?php
session_start();
if (!isset($_SESSION['username']) || $_SESSION['role'] != 'user') {
    header('Location: ../auth/login.php');
    exit();
}
require '../config/config.php';

$user_id = $_SESSION['username'];
$user_result = $con->query("SELECT id FROM users WHERE username='$user_id'");
if ($user_result && $user_result->num_rows > 0) {
    $user_data = $user_result->fetch_assoc();
    $user_id = $user_data['id'];
} else {
    die('User not found');
}

$orders_result = $con->query("SELECT * FROM service WHERE user_id='$user_id'");
if (!$orders_result) {
    die('Error fetching orders: ' . $con->error);
}
?>

<?php include '../includes/header.php'; ?>
<div class="row justify-content-center">
    <div class="col-md-8">
        <h2>Your Orders</h2>
        <table class="table">
            <thead>
                <tr>
                    <th>Tanggal Service</th>
                    <th>Deskripsi</th>
                    <th>Teknisi</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $orders_result->fetch_assoc()): ?>
                <tr>
                    <td><?php echo $row['tanggal_servis']; ?></td>
                    <td><?php echo $row['deskripsi']; ?></td>
                    <td>
                        <?php
                        $teknisi_id = $row['teknisi_id'];
                        $teknisi_result = $con->query("SELECT nama FROM teknisi WHERE id='$teknisi_id'");
                        if ($teknisi_result && $teknisi_result->num_rows > 0) {
                            $teknisi_data = $teknisi_result->fetch_assoc();
                            echo $teknisi_data['nama'];
                        } else {
                            echo 'Teknisi not found';
                        }
                        ?>
                    </td>
                    <td>
                        <?php
                        if ($row['status'] == 'dikerjakan') {
                            echo 'proses';
                        } else {
                            echo $row['status'];
                        }
                        ?>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</div>
<?php include '../includes/footer.php'; ?>

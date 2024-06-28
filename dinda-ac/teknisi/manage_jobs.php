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

// Handle status update
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['id']) && isset($_POST['status'])) {
    $job_id = $_POST['id'];
    $status = $_POST['status'];
    $update_stmt = $con->prepare("UPDATE service SET status = ? WHERE id = ? AND teknisi_id = ?");
    $update_stmt->bind_param('sii', $status, $job_id, $teknisi_id);
    if ($update_stmt->execute()) {
        $message = 'Status updated successfully';
    } else {
        $error = 'Failed to update status: ' . $update_stmt->error;
    }
    $update_stmt->close();
}

// Get jobs for teknisi
$jobs_result = $con->query("SELECT * FROM service WHERE teknisi_id=$teknisi_id");
if (!$jobs_result) {
    die('Error fetching jobs: ' . $con->error);
}

include '../includes/header.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Jobs</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <h2 class="text-center">Manage Jobs</h2>
                <?php if (isset($message)) echo "<p class='text-success'>$message</p>"; ?>
                <?php if (isset($error)) echo "<p class='text-danger'>$error</p>"; ?>
                <table class="table">
                    <thead>
                        <tr>
                            <th>Tanggal Servis</th>
                            <th>Deskripsi</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($row = $jobs_result->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo $row['tanggal_servis']; ?></td>
                            <td><?php echo $row['deskripsi']; ?></td>
                            <td><?php echo $row['status']; ?></td>
                            <td>
                                <form method="post" action="manage_jobs.php">
                                    <input type="hidden" name="id" value="<?php echo $row['id']; ?>">
                                    <select name="status" class="form-control" required>
                                        <option value="menunggu" <?php if ($row['status'] == 'menunggu') echo 'selected'; ?>>Menunggu</option>
                                        <option value="dikerjakan" <?php if ($row['status'] == 'dikerjakan') echo 'selected'; ?>>Proses</option>
                                        <option value="selesai" <?php if ($row['status'] == 'selesai') echo 'selected'; ?>>Selesai</option>
                                    </select>
                                    <button type="submit" class="btn btn-primary mt-2">Update Status</button>
                                </form>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</body>
</html>

<?php include '../includes/footer.php'; ?>

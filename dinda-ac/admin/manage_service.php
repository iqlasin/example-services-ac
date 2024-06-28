<?php
session_start();
if (!isset($_SESSION['username']) || $_SESSION['role'] != 'admin') {
    header('Location: ../auth/login.php');
    exit();
}
require '../config/config.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $service_id = $_POST['service_id'];
    $teknisi_id = $_POST['teknisi_id'];

    $stmt = $con->prepare("UPDATE service SET teknisi_id = ? WHERE id = ?");
    $stmt->bind_param("ii", $teknisi_id, $service_id);

    if ($stmt->execute()) {
        $message = 'Technician assigned successfully';
    } else {
        $error = 'Error assigning technician: ' . $stmt->error;
    }
    $stmt->close();
}

$services_query = "SELECT service.*, users.username AS user_name FROM service JOIN users ON service.user_id = users.id WHERE service.teknisi_id IS NULL";
$services_result = $con->query($services_query);
if (!$services_result) {
    die("Query error: " . $con->error);
}

$teknisi_result = $con->query("SELECT * FROM teknisi");
if (!$teknisi_result) {
    die("Query error: " . $con->error);
}
?>

<?php include '../includes/header.php'; ?>
<div class="row justify-content-center">
    <div class="col-md-10">
        <h2>Manage Services</h2>
        <table id="servicesTable" class="table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Customer</th>
                    <th>Tanggal Servis</th>
                    <th>Harga</th>
                    <th>Deskripsi</th>
                    <th>Location</th>
                    <th>Status</th>
                    <th>Assign Teknisi</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $services_result->fetch_assoc()): ?>
                <tr>
                    <td><?php echo $row['id']; ?></td>
                    <td><?php echo $row['user_name']; ?></td>
                    <td><?php echo $row['tanggal_servis']; ?></td>
                    <td><?php echo $row['harga']; ?></td>
                    <td><?php echo $row['deskripsi']; ?></td>
                    <td><?php echo $row['location']; ?></td>
                    <td><?php echo $row['status']; ?></td>
                    <td>
                        <form method="post" action="manage_service.php">
                            <input type="hidden" name="service_id" value="<?php echo $row['id']; ?>">
                            <select name="teknisi_id" required>
                                <?php while ($teknisi = $teknisi_result->fetch_assoc()): ?>
                                    <option value="<?php echo $teknisi['id']; ?>"><?php echo $teknisi['nama']; ?></option>
                                <?php endwhile; ?>
                            </select>
                            <button type="submit" class="btn btn-primary">Assign</button>
                        </form>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</div>
<?php include '../includes/footer.php'; ?>

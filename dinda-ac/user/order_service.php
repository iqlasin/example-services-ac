<?php
session_start();
if (!isset($_SESSION['username']) || $_SESSION['role'] != 'user') {
    header('Location: ../auth/login.php');
    exit();
}
require '../config/config.php';

// Fetch harga for the service before form display
$harga_query = $con->query("SELECT service_ac FROM harga LIMIT 1");
if (!$harga_query) {
    die("Query error: " . $con->error);
}
$harga = $harga_query->fetch_assoc()['service_ac'];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_SESSION['username'];
    $tanggal_servis = $_POST['tanggal_servis'];
    $deskripsi = $_POST['deskripsi'];
    $location = $_POST['location'];

    // Fetch user_id from users table based on username
    $user_query = $con->prepare("SELECT id FROM users WHERE username=?");
    if (!$user_query) {
        die("Prepare error: " . $con->error);
    }
    $user_query->bind_param("s", $username);
    $user_query->execute();
    $user_result = $user_query->get_result();
    if ($user_result->num_rows > 0) {
        $user_id = $user_result->fetch_assoc()['id'];

        // Insert the new service order into the service table
        $stmt = $con->prepare("INSERT INTO service (user_id, teknisi_id, tanggal_servis, harga, status, deskripsi, location) VALUES (?, NULL, ?, ?, 'menunggu', ?, ?)");
        if (!$stmt) {
            die("Prepare error: " . $con->error);
        }
        $stmt->bind_param("issss", $user_id, $tanggal_servis, $harga, $deskripsi, $location);

        if ($stmt->execute()) {
            $message = 'Service ordered successfully';
        } else {
            $error = 'Error ordering service: ' . $stmt->error;
        }
        $stmt->close();
    } else {
        $error = 'User not found';
    }
    $user_query->close();
}
?>

<?php include '../includes/header.php'; ?>
<div class="row justify-content-center">
    <div class="col-md-6">
        <h2>Order Service</h2>
        <form method="post" action="order_service.php">
            <div class="form-group">
                <label for="tanggal_servis">Tanggal Service:</label>
                <input type="datetime-local" class="form-control" id="tanggal_servis" name="tanggal_servis" required>
            </div>
            <div class="form-group">
                <label for="deskripsi">Deskripsi:</label>
                <input type="text" class="form-control" id="deskripsi" name="deskripsi" required>
            </div>
            <div class="form-group">
                <label for="location">Location:</label>
                <input type="text" class="form-control" id="location" name="location" required>
            </div>
            <div class="form-group">
                <label for="harga">Harga:</label>
                <input type="text" class="form-control" id="harga" name="harga" value="<?php echo $harga; ?>" readonly>
            </div>
            <button type="submit" class="btn btn-primary">Order Service</button>
        </form>
        <?php if (isset($message)) echo "<p class='text-success'>$message</p>"; ?>
        <?php if (isset($error)) echo "<p class='text-danger'>$error</p>"; ?>
    </div>
</div>
<?php include '../includes/footer.php'; ?>

<?php
session_start();
if (!isset($_SESSION['username']) || $_SESSION['role'] != 'admin') {
    header('Location: ../auth/login.php');
    exit();
}
require '../config/config.php';

// Get the current harga
$harga_result = $con->query("SELECT * FROM harga LIMIT 1");

if (!$harga_result) {
    die("Query error: " . $con->error);
}

$harga = $harga_result->fetch_assoc();
?>

<?php include '../includes/header.php'; ?>
<div class="row justify-content-center">
    <div class="col-md-8">
        <h2>Manage Harga</h2>
        <form id="hargaForm">
            <input type="hidden" id="hargaId" value="<?php echo isset($harga['id']) ? $harga['id'] : ''; ?>">
            <div class="mb-3">
                <label for="service_ac" class="form-label">Harga Service AC</label>
                <input type="number" class="form-control" id="service_ac" step="0.01" value="<?php echo isset($harga['service_ac']) ? $harga['service_ac'] : ''; ?>" required>
            </div>
            <button type="submit" class="btn btn-primary">Save changes</button>
        </form>
    </div>
</div>

<?php include '../includes/footer.php'; ?>

<script>
$(document).ready(function() {
    $('#hargaForm').on('submit', function(e) {
        e.preventDefault();
        const id = $('#hargaId').val();
        const service_ac = $('#service_ac').val();

        $.post('save_harga.php', { id, service_ac }, function(response) {
            if (response.success) {
                alert('Harga berhasil disimpan!');
                location.reload();
            } else {
                alert('Error menyimpan harga');
            }
        }, 'json');
    });
});
</script>


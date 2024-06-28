<?php
session_start();
if (!isset($_SESSION['username']) || $_SESSION['role'] != 'admin') {
    header('Location: ../auth/login.php');
    exit();
}
require '../config/config.php';

$teknisi_result = $con->query("SELECT * FROM teknisi");
?>

<?php include '../includes/header.php'; ?>
<div class="row justify-content-center">
    <div class="col-md-8">
        <h2>Manage Technicians</h2>
        <button id="addTechnicianBtn" class="btn btn-primary mb-3">Add Technician</button>
        <table id="teknisiTable" class="table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nama</th>
                    <th>Alamat</th>
                    <th>Nomor HP</th>
                    <th>Spesialisasi</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $teknisi_result->fetch_assoc()): ?>
                <tr>
                    <td class="id"><?php echo $row['id']; ?></td>
                    <td class="nama"><?php echo $row['nama']; ?></td>
                    <td class="alamat"><?php echo $row['alamat']; ?></td>
                    <td class="nomor_hp"><?php echo $row['nomor_hp']; ?></td>
                    <td class="spesialisasi"><?php echo $row['spesialisasi']; ?></td>
                    <td>
                        <button class="btn btn-warning editBtn">Edit</button>
                        <button class="btn btn-danger deleteBtn">Delete</button>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Add/Edit Technician Modal -->
<div class="modal fade" id="teknisiModal" tabindex="-1" aria-labelledby="teknisiModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="teknisiModalLabel">Add/Edit Technician</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <form id="teknisiForm">
          <input type="hidden" id="teknisiId">
          <div class="mb-3">
            <label for="nama" class="form-label">Nama</label>
            <input type="text" class="form-control" id="nama" required>
          </div>
          <div class="mb-3">
            <label for="password" class="form-label">Password</label>
            <input type="password" class="form-control" id="password" required>
          </div>
          <div class="mb-3">
            <label for="alamat" class="form-label">Alamat</label>
            <input type="text" class="form-control" id="alamat" required>
          </div>
          <div class="mb-3">
            <label for="nomor_hp" class="form-label">Nomor HP</label>
            <input type="text" class="form-control" id="nomor_hp" required>
          </div>
          <div class="mb-3">
            <label for="spesialisasi" class="form-label">Spesialisasi</label>
            <input type="text" class="form-control" id="spesialisasi" required>
          </div>
          
          <button type="submit" class="btn btn-primary">Save changes</button>
        </form>
      </div>
    </div>
  </div>
</div>


<?php include '../includes/footer.php'; ?>

<script>
$(document).ready(function() {
    $('#teknisiTable').DataTable();

    $('#addTechnicianBtn').on('click', function() {
        $('#teknisiId').val('');
        $('#teknisiForm')[0].reset();
        $('#teknisiModal').modal('show');
    });

    $('.editBtn').on('click', function() {
        const row = $(this).closest('tr');
        $('#teknisiId').val(row.find('.id').text());
        $('#nama').val(row.find('.nama').text());
        $('#alamat').val(row.find('.alamat').text());
        $('#nomor_hp').val(row.find('.nomor_hp').text());
        $('#spesialisasi').val(row.find('.spesialisasi').text());
        $('#password').val(''); // Clear password field on edit
        $('#teknisiModal').modal('show');
    });

    $('#teknisiForm').on('submit', function(e) {
        e.preventDefault();
        const id = $('#teknisiId').val();
        const nama = $('#nama').val();
        const alamat = $('#alamat').val();
        const nomor_hp = $('#nomor_hp').val();
        const spesialisasi = $('#spesialisasi').val();
        const password = $('#password').val();

        $.post('save_teknisi.php', { id, nama, alamat, nomor_hp, spesialisasi, password }, function(response) {
            if (response.success) {
                location.reload();
            } else {
                alert('Error saving technician');
            }
        }, 'json');
    });

    $('.deleteBtn').on('click', function() {
        if (confirm('Are you sure you want to delete this technician?')) {
            const id = $(this).closest('tr').find('.id').text();
            $.post('delete_teknisi.php', { id }, function(response) {
                if (response.success) {
                    location.reload();
                } else {
                    alert('Error deleting technician');
                }
            }, 'json');
        }
    });
});

</script>

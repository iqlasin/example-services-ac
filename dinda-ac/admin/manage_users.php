<?php
session_start();
if (!isset($_SESSION['username']) || $_SESSION['role'] != 'admin') {
    header('Location: ../auth/login.php');
    exit();
}
require '../config/config.php';

$users_result = $con->query("SELECT * FROM users");
?>

<?php include '../includes/header.php'; ?>
<div class="row justify-content-center">
    <div class="col-md-8">
        <h2>Manage Users</h2>
        <button id="addUserBtn" class="btn btn-primary mb-3">Add User</button>
        <table id="usersTable" class="table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Username</th>
                    <th>Role</th>
                    <th>Alamat</th>
                    <th>Nomor HP</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $users_result->fetch_assoc()): ?>
                <tr>
                    <td class="id"><?php echo $row['id']; ?></td>
                    <td class="username"><?php echo $row['username']; ?></td>
                    <td class="role"><?php echo $row['role']; ?></td>
                    <td class="alamat"><?php echo $row['alamat']; ?></td>
                    <td class="nomor_hp"><?php echo $row['nomor_hp']; ?></td>
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

<!-- Add/Edit User Modal -->
<div class="modal fade" id="userModal" tabindex="-1" aria-labelledby="userModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="userModalLabel">Add/Edit User</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <form id="userForm">
          <input type="hidden" id="userId">
          <div class="mb-3">
            <label for="username" class="form-label">Username</label>
            <input type="text" class="form-control" id="username" required>
          </div>
          <div class="mb-3">
            <label for="password" class="form-label">Password</label>
            <input type="password" class="form-control" id="password">
          </div>
          <div class="mb-3">
            <label for="role" class="form-label">Role</label>
            <input type="text" class="form-control" id="role" required>
          </div>
          <div class="mb-3">
            <label for="alamat" class="form-label">Alamat</label>
            <input type="text" class="form-control" id="alamat" required>
          </div>
          <div class="mb-3">
            <label for="nomor_hp" class="form-label">Nomor HP</label>
            <input type="text" class="form-control" id="nomor_hp" required>
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
    $('#usersTable').DataTable();

    $('#addUserBtn').on('click', function() {
        $('#userId').val('');
        $('#userForm')[0].reset();
        $('#username').prop('disabled', false); // Enable username field
        $('#role').val('user').prop('disabled', true); // Set role to "user" and disable it
        $('#userModal').modal('show');
    });

    $('.editBtn').on('click', function() {
        const row = $(this).closest('tr');
        $('#userId').val(row.find('.id').text());
        $('#username').val(row.find('.username').text());
        $('#role').val(row.find('.role').text()).prop('disabled', true); // Disable role field when editing
        $('#alamat').val(row.find('.alamat').text());
        $('#nomor_hp').val(row.find('.nomor_hp').text());
        $('#password').val(''); // Clear the password field
        $('#userModal').modal('show');
    });

    $('#userForm').on('submit', function(e) {
        e.preventDefault();
        const id = $('#userId').val();
        const username = $('#username').val();
        const role = $('#role').val();
        const alamat = $('#alamat').val();
        const nomor_hp = $('#nomor_hp').val();
        const password = $('#password').val();

        $.post('save_user.php', { id, username, role, alamat, nomor_hp, password }, function(response) {
            if (response.success) {
                location.reload();
            } else {
                alert('Error saving user');
            }
        }, 'json');
    });

    $('.deleteBtn').on('click', function() {
        if (confirm('Are you sure you want to delete this user?')) {
            const id = $(this).closest('tr').find('.id').text();
            $.post('delete_user.php', { id }, function(response) {
                if (response.success) {
                    location.reload();
                } else {
                    alert('Error deleting user');
                }
            }, 'json');
        }
    });
});
</script>

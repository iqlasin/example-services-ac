<?php
session_start();
require '../config/config.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $alamat = $_POST['alamat'];
    $nomor_hp = $_POST['nomor_hp'];
    
    $stmt = $con->prepare("INSERT INTO users (username, password, role, alamat, nomor_hp) VALUES (?, ?, 'user', ?, ?)");
    $stmt->bind_param("ssss", $username, $password, $alamat, $nomor_hp);

    if ($stmt->execute()) {
        $_SESSION['username'] = $username;
        $_SESSION['role'] = 'user';
        header('Location: ../user/dashboard.php');
    } else {
        $error = 'Error registering user';
    }
    $stmt->close();
}
?>

<?php include '../includes/header.php'; ?>
<div class="row justify-content-center">
    <div class="col-md-6">
        <h2>Register</h2>
        <form method="post" action="register.php">
            <div class="form-group">
                <label for="username">Username:</label>
                <input type="text" class="form-control" id="username" name="username" required>
            </div>
            <div class="form-group">
                <label for="password">Password:</label>
                <input type="password" class="form-control" id="password" name="password" required>
            </div>
            <div class="form-group">
                <label for="alamat">Alamat:</label>
                <textarea class="form-control" id="alamat" name="alamat" required></textarea>
            </div>
            <div class="form-group">
                <label for="nomor_hp">Nomor HP:</label>
                <input type="text" class="form-control" id="nomor_hp" name="nomor_hp" required>
            </div>
            <button type="submit" class="btn btn-primary">Register</button>
        </form>
        <?php if (isset($error)) echo "<p class='text-danger'>$error</p>"; ?>
    </div>
</div>
<?php include '../includes/footer.php'; ?>

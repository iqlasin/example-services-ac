<?php
session_start();
if (!isset($_SESSION['username']) || $_SESSION['role'] != 'user') {
    header('Location: ../auth/login.php');
    exit();
}
include '../includes/header.php';
require '../config/config.php';

$user_id = $_SESSION['username'];
$user_query = $con->prepare("SELECT * FROM users WHERE username=?");
$user_query->bind_param("s", $user_id);
$user_query->execute();
$user_result = $user_query->get_result();
$user_data = $user_result->fetch_assoc();

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_info'])) {
    $alamat = $_POST['alamat'];
    $nomor_hp = $_POST['nomor_hp'];

    $update_info_query = $con->prepare("UPDATE users SET alamat=?, nomor_hp=? WHERE username=?");
    $update_info_query->bind_param("sss", $alamat, $nomor_hp, $user_id);
    if ($update_info_query->execute()) {
        $message_info = "Information updated successfully.";
        $user_data['alamat'] = $alamat;
        $user_data['nomor_hp'] = $nomor_hp;
    } else {
        $message_info = "Failed to update information.";
    }
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['change_password'])) {
    $old_password = $_POST['old_password'];
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];

    if (password_verify($old_password, $user_data['password'])) {
        if ($new_password === $confirm_password) {
            $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
            $update_password_query = $con->prepare("UPDATE users SET password=? WHERE username=?");
            $update_password_query->bind_param("ss", $hashed_password, $user_id);
            if ($update_password_query->execute()) {
                $message_password = "Password updated successfully.";
            } else {
                $message_password = "Failed to update password.";
            }
        } else {
            $message_password = "New passwords do not match.";
        }
    } else {
        $message_password = "Old password is incorrect.";
    }
}

$user_query->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <div class="row">
            <div class="col-12">
                <h2 class="text-center">Profile</h2>
                <?php if (isset($message_info)) { echo "<p class='text-center'>$message_info</p>"; } ?>
                <?php if (isset($message_password)) { echo "<p class='text-center'>$message_password</p>"; } ?>
            </div>
        </div>
        <div class="row">
            <div class="col-md-6 offset-md-3">
                <form method="POST">
                    <div class="mb-3">
                        <label for="username" class="form-label">Username</label>
                        <input type="text" class="form-control" id="username" value="<?php echo $user_data['username']; ?>" disabled>
                    </div>
                    <div class="mb-3">
                        <label for="alamat" class="form-label">Address</label>
                        <input type="text" class="form-control" id="alamat" name="alamat" value="<?php echo $user_data['alamat']; ?>" required>
                    </div>
                    <div class="mb-3">
                        <label for="nomor_hp" class="form-label">Phone Number</label>
                        <input type="text" class="form-control" id="nomor_hp" name="nomor_hp" value="<?php echo $user_data['nomor_hp']; ?>" required>
                    </div>
                    <button type="submit" class="btn btn-primary" name="update_info">Update Information</button>
                </form>
                <hr>
                <h3 class="text-center">Change Password</h3>
                <form method="POST">
                    <div class="mb-3">
                        <label for="old_password" class="form-label">Old Password</label>
                        <input type="password" class="form-control" id="old_password" name="old_password" required>
                    </div>
                    <div class="mb-3">
                        <label for="new_password" class="form-label">New Password</label>
                        <input type="password" class="form-control" id="new_password" name="new_password" required>
                    </div>
                    <div class="mb-3">
                        <label for="confirm_password" class="form-label">Confirm New Password</label>
                        <input type="password" class="form-control" id="confirm_password" name="confirm_password" required>
                    </div>
                    <button type="submit" class="btn btn-primary" name="change_password">Change Password</button>
                </form>
            </div>
        </div>
    </div>
</body>
</html>

<?php include '../includes/footer.php'; ?>

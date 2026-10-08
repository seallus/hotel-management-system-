<?php
require_once 'includes/db.php';
require_once 'includes/functions.php';
require_login();

$page_title = "My Profile";
$base_url = '';
$user_id = $_SESSION['user_id'];

$result = mysqli_query($conn, "SELECT * FROM users WHERE user_id = $user_id");
$user = mysqli_fetch_assoc($result);

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $full_name = clean($_POST['full_name']);
    $phone     = clean($_POST['phone']);
    $new_password = $_POST['new_password'];

    if (empty($full_name) || empty($phone)) {
        $errors[] = "Name and phone number are required.";
    }

    if (empty($errors)) {
        $sql = "UPDATE users SET full_name = '$full_name', phone = '$phone' WHERE user_id = $user_id";

        if (!empty($new_password)) {
            if (strlen($new_password) < 6) {
                $errors[] = "New password must be at least 6 characters.";
            } else {
                $hashed = password_hash($new_password, PASSWORD_DEFAULT);
                $sql = "UPDATE users SET full_name = '$full_name', phone = '$phone', password = '$hashed' WHERE user_id = $user_id";
            }
        }

        if (empty($errors) && mysqli_query($conn, $sql)) {
            $_SESSION['full_name'] = $full_name;
            set_flash('success', 'Profile updated successfully.');
            redirect('profile.php');
        }
    }
}

require_once 'includes/header.php';
?>

<div class="auth-box">
    <h3 class="text-center mb-4"><i class="fa-solid fa-user"></i> My Profile</h3>

    <?php foreach ($errors as $error): ?>
        <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
    <?php endforeach; ?>

    <form method="POST">
        <div class="mb-3">
            <label class="form-label">Full Name</label>
            <input type="text" name="full_name" class="form-control" value="<?php echo htmlspecialchars($user['full_name']); ?>" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Email Address</label>
            <input type="email" class="form-control" value="<?php echo htmlspecialchars($user['email']); ?>" disabled>
            <div class="form-text">Email cannot be changed.</div>
        </div>
        <div class="mb-3">
            <label class="form-label">Phone Number</label>
            <input type="text" name="phone" class="form-control" value="<?php echo htmlspecialchars($user['phone']); ?>" required>
        </div>
        <div class="mb-3">
            <label class="form-label">New Password (leave blank to keep current)</label>
            <input type="password" name="new_password" class="form-control">
        </div>
        <button type="submit" class="btn btn-primary w-100">Update Profile</button>
    </form>
</div>

<?php require_once 'includes/footer.php'; ?>

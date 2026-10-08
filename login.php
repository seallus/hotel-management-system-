<?php
require_once 'includes/db.php';
require_once 'includes/functions.php';

$page_title = "Login";
$base_url = '';

if (is_logged_in()) {
    redirect(is_admin() ? 'admin/dashboard.php' : 'index.php');
}

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email    = clean($_POST['email']);
    $password = $_POST['password'];

    if (empty($email) || empty($password)) {
        $errors[] = "Please enter both email and password.";
    } else {
        $result = mysqli_query($conn, "SELECT * FROM users WHERE email = '$email'");
        $user = mysqli_fetch_assoc($result);

        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['user_id']   = $user['user_id'];
            $_SESSION['full_name'] = $user['full_name'];
            $_SESSION['role']      = $user['role'];

            set_flash('success', 'Welcome back, ' . $user['full_name'] . '!');

            if ($user['role'] === 'admin') {
                redirect('admin/dashboard.php');
            } else {
                redirect('index.php');
            }
        } else {
            $errors[] = "Invalid email or password.";
        }
    }
}

require_once 'includes/header.php';
?>

<div class="auth-box">
    <h3 class="text-center mb-4"><i class="fa-solid fa-right-to-bracket"></i> Login</h3>

    <?php foreach ($errors as $error): ?>
        <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
    <?php endforeach; ?>

    <form method="POST">
        <div class="mb-3">
            <label class="form-label">Email Address</label>
            <input type="email" name="email" class="form-control" required autofocus>
        </div>
        <div class="mb-3">
            <label class="form-label">Password</label>
            <input type="password" name="password" class="form-control" required>
        </div>
        <button type="submit" class="btn btn-primary w-100">Login</button>
    </form>
    <p class="text-center mt-3 small">Don't have an account? <a href="register.php">Register here</a></p>
    <p class="text-center small text-muted">Admin demo login: admin@hotel.com / admin123</p>
</div>

<?php require_once 'includes/footer.php'; ?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($page_title) ? $page_title . ' - NyatpoleHotel' : 'NyatpoleHotel'; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet">
    <link href="<?php echo isset($base_url) ? $base_url : ''; ?>assets/css/style.css" rel="stylesheet">
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-dark">
    <div class="container">
        <a class="navbar-brand" href="<?php echo isset($base_url) ? $base_url : ''; ?>index.php">
            <i class="fa-solid fa-hotel"></i> NYatpoles Hotel
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navMenu">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navMenu">
            <ul class="navbar-nav ms-auto align-items-lg-center">
                <li class="nav-item"><a class="nav-link" href="<?php echo isset($base_url) ? $base_url : ''; ?>index.php">Home</a></li>
                <li class="nav-item"><a class="nav-link" href="<?php echo isset($base_url) ? $base_url : ''; ?>rooms.php">Rooms</a></li>
                <?php if (is_logged_in() && !is_admin()): ?>
                    <li class="nav-item"><a class="nav-link" href="<?php echo isset($base_url) ? $base_url : ''; ?>my_bookings.php">My Bookings</a></li>
                    <li class="nav-item"><a class="nav-link" href="<?php echo isset($base_url) ? $base_url : ''; ?>profile.php">Profile</a></li>
                    <li class="nav-item"><span class="nav-link text-light-emphasis">Hi, <?php echo htmlspecialchars(explode(' ', $_SESSION['full_name'])[0]); ?></span></li>
                    <li class="nav-item"><a class="btn btn-outline-light btn-sm ms-lg-2" href="<?php echo isset($base_url) ? $base_url : ''; ?>logout.php">Logout</a></li>
                <?php elseif (is_logged_in() && is_admin()): ?>
                    <li class="nav-item"><a class="btn btn-outline-light btn-sm ms-lg-2" href="<?php echo isset($base_url) ? $base_url : ''; ?>admin/dashboard.php">Admin Panel</a></li>
                    <li class="nav-item"><a class="btn btn-outline-light btn-sm ms-lg-2" href="<?php echo isset($base_url) ? $base_url : ''; ?>logout.php">Logout</a></li>
                <?php else: ?>
                    <li class="nav-item"><a class="nav-link" href="<?php echo isset($base_url) ? $base_url : ''; ?>login.php">Login</a></li>
                    <li class="nav-item"><a class="btn btn-light btn-sm ms-lg-2" href="<?php echo isset($base_url) ? $base_url : ''; ?>register.php">Register</a></li>
                <?php endif; ?>
            </ul>
        </div>
    </div>
</nav>

<div class="container my-4">
    <?php show_flash(); ?>

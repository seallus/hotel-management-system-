<?php
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/functions.php';
require_admin();
$base_url = '../';
$current = basename($_SERVER['PHP_SELF']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($page_title) ? $page_title . ' - Admin Panel' : 'Admin Panel'; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet">
    <link href="../assets/css/style.css" rel="stylesheet">
</head>
<body>

<nav class="navbar navbar-dark" style="background-color:#16543f;">
    <div class="container-fluid">
        <span class="navbar-brand"><i class="fa-solid fa-hotel"></i> NyatpoleHotel — Admin Panel</span>
        <div>
            <a href="../index.php" class="btn btn-outline-light btn-sm me-2" target="_blank">View Site</a>
            <a href="../logout.php" class="btn btn-light btn-sm">Logout</a>
        </div>
    </div>
</nav>

<div class="d-flex">
    <div class="sidebar" style="width: 230px;">
        <a href="dashboard.php" class="<?php echo $current === 'dashboard.php' ? 'active' : ''; ?>"><i class="fa-solid fa-gauge"></i> Dashboard</a>
        <a href="room_types.php" class="<?php echo $current === 'room_types.php' ? 'active' : ''; ?>"><i class="fa-solid fa-tags"></i> Room Types</a>
        <a href="rooms.php" class="<?php echo $current === 'rooms.php' ? 'active' : ''; ?>"><i class="fa-solid fa-bed"></i> Manage Rooms</a>
        <a href="bookings.php" class="<?php echo $current === 'bookings.php' ? 'active' : ''; ?>"><i class="fa-solid fa-calendar-check"></i> Bookings</a>
        <a href="customers.php" class="<?php echo $current === 'customers.php' ? 'active' : ''; ?>"><i class="fa-solid fa-users"></i> Customers</a>
    </div>
    <div class="flex-grow-1 p-4">
        <?php show_flash(); ?>

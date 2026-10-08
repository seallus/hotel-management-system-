<?php
$page_title = "Dashboard";
require_once 'includes_header.php';

// Stats
$total_rooms   = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) c FROM rooms"))['c'];
$total_bookings = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) c FROM bookings"))['c'];
$pending_bookings = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) c FROM bookings WHERE status='pending'"))['c'];
$total_customers = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) c FROM users WHERE role='customer'"))['c'];
$revenue = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COALESCE(SUM(total_price),0) r FROM bookings WHERE payment_status='paid'"))['r'];

// Recent bookings
$recent = mysqli_query($conn, "SELECT b.*, u.full_name, r.room_number, rt.type_name
                                FROM bookings b
                                JOIN users u ON b.user_id = u.user_id
                                JOIN rooms r ON b.room_id = r.room_id
                                JOIN room_types rt ON r.room_type_id = rt.room_type_id
                                ORDER BY b.created_at DESC LIMIT 8");
?>

<h2 class="mb-4"><i class="fa-solid fa-gauge"></i> Dashboard</h2>

<div class="row g-3 mb-4">
    <div class="col-md-3 col-sm-6">
        <div class="stat-card stat-1">
            <i class="fa-solid fa-bed fa-lg"></i>
            <h3><?php echo $total_rooms; ?></h3>
            <p class="mb-0">Total Rooms</p>
        </div>
    </div>
    <div class="col-md-3 col-sm-6">
        <div class="stat-card stat-2">
            <i class="fa-solid fa-calendar-check fa-lg"></i>
            <h3><?php echo $total_bookings; ?></h3>
            <p class="mb-0">Total Bookings</p>
        </div>
    </div>
    <div class="col-md-3 col-sm-6">
        <div class="stat-card stat-3">
            <i class="fa-solid fa-users fa-lg"></i>
            <h3><?php echo $total_customers; ?></h3>
            <p class="mb-0">Customers</p>
        </div>
    </div>
    <div class="col-md-3 col-sm-6">
        <div class="stat-card stat-4">
            <i class="fa-solid fa-hourglass-half fa-lg"></i>
            <h3><?php echo $pending_bookings; ?></h3>
            <p class="mb-0">Pending Bookings</p>
        </div>
    </div>
</div>

<div class="card p-3 mb-4">
    <h5>Total Revenue (Paid Bookings)</h5>
    <p class="price-tag fs-3"><?php echo format_price($revenue); ?></p>
</div>

<div class="card p-3">
    <h5 class="mb-3">Recent Bookings</h5>
    <div class="table-responsive">
        <table class="table table-sm align-middle">
            <thead class="table-light">
                <tr>
                    <th>Customer</th>
                    <th>Room</th>
                    <th>Check-in</th>
                    <th>Check-out</th>
                    <th>Status</th>
                    <th>Amount</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($b = mysqli_fetch_assoc($recent)): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($b['full_name']); ?></td>
                        <td><?php echo htmlspecialchars($b['type_name']); ?> (<?php echo htmlspecialchars($b['room_number']); ?>)</td>
                        <td><?php echo htmlspecialchars($b['check_in']); ?></td>
                        <td><?php echo htmlspecialchars($b['check_out']); ?></td>
                        <td>
                            <?php
                            $badge = match($b['status']) {
                                'pending' => 'warning text-dark',
                                'confirmed' => 'success',
                                'cancelled' => 'secondary',
                                'completed' => 'primary',
                                default => 'secondary'
                            };
                            ?>
                            <span class="badge bg-<?php echo $badge; ?>"><?php echo ucfirst($b['status']); ?></span>
                        </td>
                        <td><?php echo format_price($b['total_price']); ?></td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require_once 'includes_footer.php'; ?>

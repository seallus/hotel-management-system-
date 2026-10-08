<?php
$page_title = "Bookings";
require_once 'includes_header.php';

// Handle status updates
if (isset($_GET['action']) && isset($_GET['id'])) {
    $id = (int) $_GET['id'];
    $action = clean($_GET['action']);

    $valid_actions = [
        'confirm'   => "UPDATE bookings SET status='confirmed' WHERE booking_id=$id",
        'cancel'    => "UPDATE bookings SET status='cancelled' WHERE booking_id=$id",
        'complete'  => "UPDATE bookings SET status='completed' WHERE booking_id=$id",
        'mark_paid' => "UPDATE bookings SET payment_status='paid' WHERE booking_id=$id",
    ];

    if (array_key_exists($action, $valid_actions)) {
        mysqli_query($conn, $valid_actions[$action]);
        set_flash('success', 'Booking updated successfully.');
    }
    redirect('bookings.php');
}

$status_filter = isset($_GET['status']) ? clean($_GET['status']) : '';

$sql = "SELECT b.*, u.full_name, u.email, r.room_number, rt.type_name
        FROM bookings b
        JOIN users u ON b.user_id = u.user_id
        JOIN rooms r ON b.room_id = r.room_id
        JOIN room_types rt ON r.room_type_id = rt.room_type_id";

if ($status_filter) {
    $sql .= " WHERE b.status = '$status_filter'";
}
$sql .= " ORDER BY b.created_at DESC";

$bookings = mysqli_query($conn, $sql);
?>

<h2 class="mb-4"><i class="fa-solid fa-calendar-check"></i> Bookings</h2>

<div class="mb-3">
    <a href="bookings.php" class="btn btn-sm btn-outline-secondary <?php echo !$status_filter ? 'active' : ''; ?>">All</a>
    <a href="bookings.php?status=pending" class="btn btn-sm btn-outline-warning <?php echo $status_filter === 'pending' ? 'active' : ''; ?>">Pending</a>
    <a href="bookings.php?status=confirmed" class="btn btn-sm btn-outline-success <?php echo $status_filter === 'confirmed' ? 'active' : ''; ?>">Confirmed</a>
    <a href="bookings.php?status=completed" class="btn btn-sm btn-outline-primary <?php echo $status_filter === 'completed' ? 'active' : ''; ?>">Completed</a>
    <a href="bookings.php?status=cancelled" class="btn btn-sm btn-outline-dark <?php echo $status_filter === 'cancelled' ? 'active' : ''; ?>">Cancelled</a>
</div>

<div class="card p-3">
    <div class="table-responsive">
        <table class="table table-bordered align-middle">
            <thead class="table-light">
                <tr>
                    <th>Customer</th>
                    <th>Room</th>
                    <th>Check-in</th>
                    <th>Check-out</th>
                    <th>Total</th>
                    <th>Status</th>
                    <th>Payment</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($bookings && mysqli_num_rows($bookings) > 0): ?>
                    <?php while ($b = mysqli_fetch_assoc($bookings)): ?>
                        <tr>
                            <td>
                                <?php echo htmlspecialchars($b['full_name']); ?><br>
                                <span class="small text-muted"><?php echo htmlspecialchars($b['email']); ?></span>
                            </td>
                            <td><?php echo htmlspecialchars($b['type_name']); ?> (<?php echo htmlspecialchars($b['room_number']); ?>)</td>
                            <td><?php echo htmlspecialchars($b['check_in']); ?></td>
                            <td><?php echo htmlspecialchars($b['check_out']); ?></td>
                            <td><?php echo format_price($b['total_price']); ?></td>
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
                            <td>
                                <span class="badge bg-<?php echo $b['payment_status'] === 'paid' ? 'success' : 'secondary'; ?>">
                                    <?php echo ucfirst($b['payment_status']); ?>
                                </span>
                            </td>
                            <td class="text-nowrap">
                                <?php if ($b['status'] === 'pending'): ?>
                                    <a href="bookings.php?action=confirm&id=<?php echo $b['booking_id']; ?>" class="btn btn-sm btn-success mb-1">Confirm</a>
                                    <a href="bookings.php?action=cancel&id=<?php echo $b['booking_id']; ?>" class="btn btn-sm btn-outline-danger mb-1">Cancel</a>
                                <?php elseif ($b['status'] === 'confirmed'): ?>
                                    <a href="bookings.php?action=complete&id=<?php echo $b['booking_id']; ?>" class="btn btn-sm btn-primary mb-1">Mark Completed</a>
                                    <a href="bookings.php?action=cancel&id=<?php echo $b['booking_id']; ?>" class="btn btn-sm btn-outline-danger mb-1">Cancel</a>
                                <?php endif; ?>
                                <?php if ($b['payment_status'] === 'unpaid'): ?>
                                    <a href="bookings.php?action=mark_paid&id=<?php echo $b['booking_id']; ?>" class="btn btn-sm btn-outline-success mb-1">Mark Paid</a>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr><td colspan="8" class="text-center text-muted py-4">No bookings found.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require_once 'includes_footer.php'; ?>

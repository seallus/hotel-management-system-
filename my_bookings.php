<?php
require_once 'includes/db.php';
require_once 'includes/functions.php';
require_login();

$page_title = "My Bookings";
$base_url = '';
$user_id = $_SESSION['user_id'];

// Handle cancellation
if (isset($_GET['cancel'])) {
    $booking_id = (int) $_GET['cancel'];
    // Only allow cancelling your own booking
    $check = mysqli_query($conn, "SELECT * FROM bookings WHERE booking_id = $booking_id AND user_id = $user_id");
    if (mysqli_num_rows($check) > 0) {
        mysqli_query($conn, "UPDATE bookings SET status = 'cancelled' WHERE booking_id = $booking_id");
        set_flash('success', 'Booking cancelled successfully.');
    } else {
        set_flash('error', 'Booking not found.');
    }
    redirect('my_bookings.php');
}

$sql = "SELECT b.*, r.room_number, rt.type_name, rt.price_per_night
        FROM bookings b
        JOIN rooms r ON b.room_id = r.room_id
        JOIN room_types rt ON r.room_type_id = rt.room_type_id
        WHERE b.user_id = $user_id
        ORDER BY b.created_at DESC";
$bookings = mysqli_query($conn, $sql);

require_once 'includes/header.php';
?>

<h2 class="mb-4"><i class="fa-solid fa-clipboard-list"></i> My Bookings</h2>

<?php if ($bookings && mysqli_num_rows($bookings) > 0): ?>
    <div class="table-responsive">
        <table class="table table-bordered bg-white align-middle">
            <thead class="table-light">
                <tr>
                    <th>Room</th>
                    <th>Check-in</th>
                    <th>Check-out</th>
                    <th>Guests</th>
                    <th>Total Price</th>
                    <th>Status</th>
                    <th>Payment</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($b = mysqli_fetch_assoc($bookings)): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($b['type_name']); ?> (Room <?php echo htmlspecialchars($b['room_number']); ?>)</td>
                        <td><?php echo htmlspecialchars($b['check_in']); ?></td>
                        <td><?php echo htmlspecialchars($b['check_out']); ?></td>
                        <td><?php echo (int)$b['num_guests']; ?></td>
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
                        <td>
                            <?php if ($b['status'] === 'pending' || $b['status'] === 'confirmed'): ?>
                                <a href="my_bookings.php?cancel=<?php echo $b['booking_id']; ?>"
                                   class="btn btn-sm btn-outline-danger"
                                   onclick="return confirm('Are you sure you want to cancel this booking?');">
                                    Cancel
                                </a>
                            <?php else: ?>
                                <span class="text-muted small">—</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
<?php else: ?>
    <div class="text-center py-5">
        <i class="fa-solid fa-calendar-xmark fa-3x text-muted mb-3"></i>
        <p class="text-muted">You have no bookings yet.</p>
        <a href="rooms.php" class="btn btn-primary">Browse Rooms</a>
    </div>
<?php endif; ?>

<?php require_once 'includes/footer.php'; ?>

<?php
$page_title = "Customers";
require_once 'includes_header.php';

$sql = "SELECT u.*,
        (SELECT COUNT(*) FROM bookings b WHERE b.user_id = u.user_id) AS total_bookings
        FROM users u
        WHERE u.role = 'customer'
        ORDER BY u.created_at DESC";
$customers = mysqli_query($conn, $sql);
?>

<h2 class="mb-4"><i class="fa-solid fa-users"></i> Customers</h2>

<div class="card p-3">
    <div class="table-responsive">
        <table class="table table-bordered align-middle">
            <thead class="table-light">
                <tr>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Phone</th>
                    <th>Total Bookings</th>
                    <th>Registered On</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($customers && mysqli_num_rows($customers) > 0): ?>
                    <?php while ($c = mysqli_fetch_assoc($customers)): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($c['full_name']); ?></td>
                            <td><?php echo htmlspecialchars($c['email']); ?></td>
                            <td><?php echo htmlspecialchars($c['phone']); ?></td>
                            <td><span class="badge bg-primary"><?php echo (int)$c['total_bookings']; ?></span></td>
                            <td><?php echo htmlspecialchars($c['created_at']); ?></td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr><td colspan="5" class="text-center text-muted py-4">No customers registered yet.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require_once 'includes_footer.php'; ?>

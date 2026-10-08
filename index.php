<?php
require_once 'includes/db.php';
require_once 'includes/functions.php';

$page_title = "Home";
$base_url = '';

// Fetch a few featured rooms
$sql = "SELECT r.room_id, r.room_number, rt.type_name, rt.description, rt.price_per_night, rt.capacity
        FROM rooms r
        JOIN room_types rt ON r.room_type_id = rt.room_type_id
        WHERE r.status = 'available'
        GROUP BY rt.room_type_id
        LIMIT 4";
$result = mysqli_query($conn, $sql);

require_once 'includes/header.php';
?>

<div class="hero">
    <h1 class="display-5">Welcome to NyatpoleHotel</h1>
    <p class="lead">Comfort, convenience, and hospitality — book your perfect stay in just a few clicks.</p>
    <a href="rooms.php" class="btn btn-light btn-lg mt-2"><i class="fa-solid fa-bed"></i> Browse Rooms</a>
</div>

<h2 class="mb-4">Our Room Categories</h2>
<div class="row g-4">
    <?php if ($result && mysqli_num_rows($result) > 0): ?>
        <?php while ($room = mysqli_fetch_assoc($result)): ?>
            <div class="col-md-3 col-sm-6">
                <div class="card h-100">
                    <img src="https://images.unsplash.com/photo-1611892440504-42a792e24d32?auto=format&fit=crop&w=500&q=60" class="room-img" alt="<?php echo htmlspecialchars($room['type_name']); ?>">
                    <div class="card-body">
                        <h5 class="card-title"><?php echo htmlspecialchars($room['type_name']); ?></h5>
                        <p class="card-text small text-muted"><?php echo htmlspecialchars($room['description']); ?></p>
                        <p class="price-tag"><?php echo format_price($room['price_per_night']); ?> <span class="fs-6 text-muted fw-normal">/ night</span></p>
                        <p class="small text-muted"><i class="fa-solid fa-user-group"></i> Up to <?php echo (int)$room['capacity']; ?> guests</p>
                        <a href="rooms.php" class="btn btn-primary btn-sm w-100">View & Book</a>
                    </div>
                </div>
            </div>
        <?php endwhile; ?>
    <?php else: ?>
        <p class="text-muted">No rooms available at the moment. Please check back later.</p>
    <?php endif; ?>
</div>

<div class="row mt-5 g-4 text-center">
    <div class="col-md-4">
        <i class="fa-solid fa-calendar-check fa-2x mb-2" style="color: var(--primary);"></i>
        <h5>Easy Booking</h5>
        <p class="text-muted small">Check availability and book your room in minutes, online, anytime.</p>
    </div>
    <div class="col-md-4">
        <i class="fa-solid fa-shield-halved fa-2x mb-2" style="color: var(--primary);"></i>
        <h5>Secure & Reliable</h5>
        <p class="text-muted small">Your booking details and information are kept safe and private.</p>
    </div>
    <div class="col-md-4">
        <i class="fa-solid fa-headset fa-2x mb-2" style="color: var(--primary);"></i>
        <h5>24/7 Support</h5>
        <p class="text-muted small">Our team is here to help you before, during, and after your stay.</p>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>

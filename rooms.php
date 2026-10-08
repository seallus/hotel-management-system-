<?php
require_once 'includes/db.php';
require_once 'includes/functions.php';

$page_title = "Rooms";
$base_url = '';

$check_in  = isset($_GET['check_in']) ? clean($_GET['check_in']) : '';
$check_out = isset($_GET['check_out']) ? clean($_GET['check_out']) : '';
$type_filter = isset($_GET['room_type']) ? (int) $_GET['room_type'] : 0;

// Fetch room types for the filter dropdown
$types_result = mysqli_query($conn, "SELECT * FROM room_types ORDER BY price_per_night");

// Base query: all active rooms with their type info
$sql = "SELECT r.room_id, r.room_number, r.status, r.room_type_id,
               rt.type_name, rt.description, rt.price_per_night, rt.capacity
        FROM rooms r
        JOIN room_types rt ON r.room_type_id = rt.room_type_id
        WHERE r.status = 'available'";

if ($type_filter > 0) {
    $sql .= " AND r.room_type_id = " . $type_filter;
}
$sql .= " ORDER BY rt.price_per_night ASC";

$rooms_result = mysqli_query($conn, $sql);

require_once 'includes/header.php';
?>

<h2 class="mb-4"><i class="fa-solid fa-bed"></i> Available Rooms</h2>

<!-- Search / filter form -->
<form method="GET" class="row g-3 align-items-end mb-4 bg-white p-3 rounded shadow-sm">
    <div class="col-md-3">
        <label class="form-label">Check-in</label>
        <input type="date" name="check_in" class="form-control" value="<?php echo htmlspecialchars($check_in); ?>" min="<?php echo date('Y-m-d'); ?>">
    </div>
    <div class="col-md-3">
        <label class="form-label">Check-out</label>
        <input type="date" name="check_out" class="form-control" value="<?php echo htmlspecialchars($check_out); ?>" min="<?php echo date('Y-m-d'); ?>">
    </div>
    <div class="col-md-3">
        <label class="form-label">Room Type</label>
        <select name="room_type" class="form-select">
            <option value="0">All Types</option>
            <?php mysqli_data_seek($types_result, 0); while ($t = mysqli_fetch_assoc($types_result)): ?>
                <option value="<?php echo $t['room_type_id']; ?>" <?php echo $type_filter == $t['room_type_id'] ? 'selected' : ''; ?>>
                    <?php echo htmlspecialchars($t['type_name']); ?>
                </option>
            <?php endwhile; ?>
        </select>
    </div>
    <div class="col-md-3">
        <button type="submit" class="btn btn-primary w-100"><i class="fa-solid fa-magnifying-glass"></i> Search</button>
    </div>
</form>

<?php if ($check_in && $check_out && $check_in >= $check_out): ?>
    <div class="alert alert-warning">Check-out date must be after check-in date.</div>
<?php endif; ?>

<div class="row g-4">
    <?php if ($rooms_result && mysqli_num_rows($rooms_result) > 0): ?>
        <?php while ($room = mysqli_fetch_assoc($rooms_result)):
            $available = true;
            if ($check_in && $check_out && $check_in < $check_out) {
                $available = is_room_available($conn, $room['room_id'], $check_in, $check_out);
            }
        ?>
            <div class="col-md-4 col-sm-6">
                <div class="card h-100">
                    <img src="https://images.unsplash.com/photo-1611892440504-42a792e24d32?auto=format&fit=crop&w=500&q=60" class="room-img" alt="<?php echo htmlspecialchars($room['type_name']); ?>">
                    <div class="card-body d-flex flex-column">
                        <div class="d-flex justify-content-between align-items-start">
                            <h5 class="card-title"><?php echo htmlspecialchars($room['type_name']); ?> — Room <?php echo htmlspecialchars($room['room_number']); ?></h5>
                        </div>
                        <p class="card-text small text-muted"><?php echo htmlspecialchars($room['description']); ?></p>
                        <p class="price-tag"><?php echo format_price($room['price_per_night']); ?> <span class="fs-6 text-muted fw-normal">/ night</span></p>
                        <p class="small text-muted mb-3"><i class="fa-solid fa-user-group"></i> Up to <?php echo (int)$room['capacity']; ?> guests</p>

                        <?php if ($check_in && $check_out && $check_in < $check_out): ?>
                            <?php if ($available): ?>
                                <span class="badge badge-available text-white mb-2 align-self-start">Available for selected dates</span>
                                <a href="book.php?room_id=<?php echo $room['room_id']; ?>&check_in=<?php echo urlencode($check_in); ?>&check_out=<?php echo urlencode($check_out); ?>" class="btn btn-primary mt-auto">
                                    <i class="fa-solid fa-calendar-plus"></i> Book Now
                                </a>
                            <?php else: ?>
                                <span class="badge bg-secondary mb-2 align-self-start">Booked for selected dates</span>
                                <button class="btn btn-secondary mt-auto" disabled>Not Available</button>
                            <?php endif; ?>
                        <?php else: ?>
                            <a href="book.php?room_id=<?php echo $room['room_id']; ?>" class="btn btn-primary mt-auto">
                                <i class="fa-solid fa-calendar-plus"></i> Select Dates & Book
                            </a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        <?php endwhile; ?>
    <?php else: ?>
        <p class="text-muted">No rooms match your search criteria.</p>
    <?php endif; ?>
</div>

<?php require_once 'includes/footer.php'; ?>

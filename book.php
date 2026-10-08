<?php
require_once 'includes/db.php';
require_once 'includes/functions.php';
require_login();

$page_title = "Book Room";
$base_url = '';

$room_id = isset($_GET['room_id']) ? (int) $_GET['room_id'] : 0;

// Fetch room details
$room_sql = "SELECT r.room_id, r.room_number, rt.type_name, rt.description, rt.price_per_night, rt.capacity
             FROM rooms r JOIN room_types rt ON r.room_type_id = rt.room_type_id
             WHERE r.room_id = $room_id AND r.status = 'available'";
$room_result = mysqli_query($conn, $room_sql);

if (!$room_result || mysqli_num_rows($room_result) === 0) {
    set_flash('error', 'Room not found or is no longer available.');
    redirect('rooms.php');
}
$room = mysqli_fetch_assoc($room_result);

$check_in  = isset($_GET['check_in']) ? clean($_GET['check_in']) : '';
$check_out = isset($_GET['check_out']) ? clean($_GET['check_out']) : '';

$errors = [];
$success = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $check_in    = clean($_POST['check_in']);
    $check_out   = clean($_POST['check_out']);
    $num_guests  = (int) $_POST['num_guests'];
    $special_req = clean($_POST['special_request']);

    if (empty($check_in) || empty($check_out)) {
        $errors[] = "Please select check-in and check-out dates.";
    } elseif ($check_in < date('Y-m-d')) {
        $errors[] = "Check-in date cannot be in the past.";
    } elseif ($check_out <= $check_in) {
        $errors[] = "Check-out date must be after check-in date.";
    } elseif ($num_guests < 1 || $num_guests > $room['capacity']) {
        $errors[] = "Number of guests must be between 1 and " . $room['capacity'] . " for this room.";
    } elseif (!is_room_available($conn, $room_id, $check_in, $check_out)) {
        $errors[] = "Sorry, this room is already booked for the selected dates. Please choose different dates.";
    }

    if (empty($errors)) {
        $nights = nights_between($check_in, $check_out);
        $total_price = $nights * $room['price_per_night'];
        $user_id = $_SESSION['user_id'];

        $sql = "INSERT INTO bookings (user_id, room_id, check_in, check_out, num_guests, total_price, status, payment_status, special_request)
                VALUES ($user_id, $room_id, '$check_in', '$check_out', $num_guests, $total_price, 'pending', 'unpaid', '$special_req')";

        if (mysqli_query($conn, $sql)) {
            $success = true;
        } else {
            $errors[] = "Booking failed. Please try again.";
        }
    }
}

require_once 'includes/header.php';
?>

<div class="row justify-content-center">
    <div class="col-lg-7">
        <?php if ($success): ?>
            <div class="card p-4 text-center">
                <i class="fa-solid fa-circle-check fa-3x text-success mb-3"></i>
                <h4>Booking Request Submitted!</h4>
                <p class="text-muted">Your booking for Room <?php echo htmlspecialchars($room['room_number']); ?> (<?php echo htmlspecialchars($room['type_name']); ?>) has been received and is pending confirmation.</p>
                <a href="my_bookings.php" class="btn btn-primary mt-2">View My Bookings</a>
            </div>
        <?php else: ?>
            <div class="card p-4">
                <h4 class="mb-3"><i class="fa-solid fa-calendar-plus"></i> Book: <?php echo htmlspecialchars($room['type_name']); ?> — Room <?php echo htmlspecialchars($room['room_number']); ?></h4>
                <p class="text-muted"><?php echo htmlspecialchars($room['description']); ?></p>
                <p class="price-tag mb-3"><?php echo format_price($room['price_per_night']); ?> / night</p>

                <?php foreach ($errors as $error): ?>
                    <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
                <?php endforeach; ?>

                <form method="POST">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Check-in Date</label>
                            <input type="date" name="check_in" id="check_in" class="form-control" value="<?php echo htmlspecialchars($check_in); ?>" min="<?php echo date('Y-m-d'); ?>" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Check-out Date</label>
                            <input type="date" name="check_out" id="check_out" class="form-control" value="<?php echo htmlspecialchars($check_out); ?>" min="<?php echo date('Y-m-d'); ?>" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Number of Guests</label>
                            <input type="number" name="num_guests" class="form-control" min="1" max="<?php echo (int)$room['capacity']; ?>" value="1" required>
                            <div class="form-text">Max capacity: <?php echo (int)$room['capacity']; ?> guests</div>
                        </div>
                        <div class="col-12">
                            <label class="form-label">Special Requests (optional)</label>
                            <textarea name="special_request" class="form-control" rows="3" placeholder="e.g. late check-in, extra pillows, airport pickup..."></textarea>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-primary w-100 mt-4">Confirm Booking</button>
                </form>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>

<?php
$page_title = "Manage Rooms";
require_once 'includes_header.php';

// Handle add / edit
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $room_number = clean($_POST['room_number']);
    $room_type_id = (int) $_POST['room_type_id'];
    $floor = (int) $_POST['floor_number'];
    $status = clean($_POST['status']);

    if (isset($_POST['room_id']) && $_POST['room_id'] !== '') {
        $room_id = (int) $_POST['room_id'];
        $sql = "UPDATE rooms SET room_number='$room_number', room_type_id=$room_type_id, floor_number=$floor, status='$status' WHERE room_id=$room_id";
        $msg = "Room updated successfully.";
    } else {
        $sql = "INSERT INTO rooms (room_number, room_type_id, floor_number, status) VALUES ('$room_number',$room_type_id,$floor,'$status')";
        $msg = "Room added successfully.";
    }

    if (mysqli_query($conn, $sql)) {
        set_flash('success', $msg);
    } else {
        set_flash('error', 'Operation failed. Room number may already exist.');
    }
    redirect('rooms.php');
}

// Handle delete
if (isset($_GET['delete'])) {
    $id = (int) $_GET['delete'];
    if (mysqli_query($conn, "DELETE FROM rooms WHERE room_id = $id")) {
        set_flash('success', 'Room deleted.');
    } else {
        set_flash('error', 'Cannot delete: this room has existing bookings.');
    }
    redirect('rooms.php');
}

$edit_room = null;
if (isset($_GET['edit'])) {
    $id = (int) $_GET['edit'];
    $edit_room = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM rooms WHERE room_id = $id"));
}

$room_types = mysqli_query($conn, "SELECT * FROM room_types ORDER BY type_name");

$rooms = mysqli_query($conn, "SELECT r.*, rt.type_name FROM rooms r JOIN room_types rt ON r.room_type_id = rt.room_type_id ORDER BY r.room_number");
?>

<h2 class="mb-4"><i class="fa-solid fa-bed"></i> Manage Rooms</h2>

<div class="row g-4">
    <div class="col-lg-4">
        <div class="card p-3">
            <h5><?php echo $edit_room ? 'Edit Room' : 'Add New Room'; ?></h5>
            <form method="POST">
                <?php if ($edit_room): ?>
                    <input type="hidden" name="room_id" value="<?php echo $edit_room['room_id']; ?>">
                <?php endif; ?>
                <div class="mb-3">
                    <label class="form-label">Room Number</label>
                    <input type="text" name="room_number" class="form-control" required
                           value="<?php echo $edit_room ? htmlspecialchars($edit_room['room_number']) : ''; ?>">
                </div>
                <div class="mb-3">
                    <label class="form-label">Room Type</label>
                    <select name="room_type_id" class="form-select" required>
                        <?php mysqli_data_seek($room_types, 0); while ($t = mysqli_fetch_assoc($room_types)): ?>
                            <option value="<?php echo $t['room_type_id']; ?>"
                                <?php echo ($edit_room && $edit_room['room_type_id'] == $t['room_type_id']) ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($t['type_name']); ?>
                            </option>
                        <?php endwhile; ?>
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label">Floor Number</label>
                    <input type="number" name="floor_number" class="form-control" required
                           value="<?php echo $edit_room ? $edit_room['floor_number'] : '1'; ?>">
                </div>
                <div class="mb-3">
                    <label class="form-label">Status</label>
                    <select name="status" class="form-select">
                        <option value="available" <?php echo ($edit_room && $edit_room['status'] === 'available') ? 'selected' : ''; ?>>Available</option>
                        <option value="maintenance" <?php echo ($edit_room && $edit_room['status'] === 'maintenance') ? 'selected' : ''; ?>>Under Maintenance</option>
                    </select>
                </div>
                <button type="submit" class="btn btn-primary w-100"><?php echo $edit_room ? 'Update' : 'Add'; ?> Room</button>
                <?php if ($edit_room): ?>
                    <a href="rooms.php" class="btn btn-outline-secondary w-100 mt-2">Cancel</a>
                <?php endif; ?>
            </form>
        </div>
    </div>

    <div class="col-lg-8">
        <div class="card p-3">
            <h5 class="mb-3">All Rooms</h5>
            <div class="table-responsive">
                <table class="table table-bordered align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>Room No.</th>
                            <th>Type</th>
                            <th>Floor</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($r = mysqli_fetch_assoc($rooms)): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($r['room_number']); ?></td>
                                <td><?php echo htmlspecialchars($r['type_name']); ?></td>
                                <td><?php echo (int)$r['floor_number']; ?></td>
                                <td>
                                    <span class="badge bg-<?php echo $r['status'] === 'available' ? 'success' : 'secondary'; ?>">
                                        <?php echo ucfirst($r['status']); ?>
                                    </span>
                                </td>
                                <td>
                                    <a href="rooms.php?edit=<?php echo $r['room_id']; ?>" class="btn btn-sm btn-outline-primary">Edit</a>
                                    <a href="rooms.php?delete=<?php echo $r['room_id']; ?>" class="btn btn-sm btn-outline-danger"
                                       onclick="return confirm('Delete this room?');">Delete</a>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php require_once 'includes_footer.php'; ?>

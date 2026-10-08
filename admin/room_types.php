<?php
$page_title = "Room Types";
require_once 'includes_header.php';

// Handle add / edit
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $type_name = clean($_POST['type_name']);
    $description = clean($_POST['description']);
    $price = (float) $_POST['price_per_night'];
    $capacity = (int) $_POST['capacity'];

    if (isset($_POST['type_id']) && $_POST['type_id'] !== '') {
        $type_id = (int) $_POST['type_id'];
        $sql = "UPDATE room_types SET type_name='$type_name', description='$description', price_per_night=$price, capacity=$capacity WHERE room_type_id=$type_id";
        $msg = "Room type updated successfully.";
    } else {
        $sql = "INSERT INTO room_types (type_name, description, price_per_night, capacity) VALUES ('$type_name','$description',$price,$capacity)";
        $msg = "Room type added successfully.";
    }

    if (mysqli_query($conn, $sql)) {
        set_flash('success', $msg);
    } else {
        set_flash('error', 'Operation failed.');
    }
    redirect('room_types.php');
}

// Handle delete
if (isset($_GET['delete'])) {
    $id = (int) $_GET['delete'];
    if (mysqli_query($conn, "DELETE FROM room_types WHERE room_type_id = $id")) {
        set_flash('success', 'Room type deleted.');
    } else {
        set_flash('error', 'Cannot delete: rooms of this type still exist.');
    }
    redirect('room_types.php');
}

// If editing, fetch that record
$edit_type = null;
if (isset($_GET['edit'])) {
    $id = (int) $_GET['edit'];
    $edit_type = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM room_types WHERE room_type_id = $id"));
}

$types = mysqli_query($conn, "SELECT * FROM room_types ORDER BY price_per_night");
?>

<h2 class="mb-4"><i class="fa-solid fa-tags"></i> Room Types</h2>

<div class="row g-4">
    <div class="col-lg-4">
        <div class="card p-3">
            <h5><?php echo $edit_type ? 'Edit Room Type' : 'Add New Room Type'; ?></h5>
            <form method="POST">
                <?php if ($edit_type): ?>
                    <input type="hidden" name="type_id" value="<?php echo $edit_type['room_type_id']; ?>">
                <?php endif; ?>
                <div class="mb-3">
                    <label class="form-label">Type Name</label>
                    <input type="text" name="type_name" class="form-control" required
                           value="<?php echo $edit_type ? htmlspecialchars($edit_type['type_name']) : ''; ?>">
                </div>
                <div class="mb-3">
                    <label class="form-label">Description</label>
                    <textarea name="description" class="form-control" rows="2"><?php echo $edit_type ? htmlspecialchars($edit_type['description']) : ''; ?></textarea>
                </div>
                <div class="mb-3">
                    <label class="form-label">Price per Night (Rs.)</label>
                    <input type="number" step="0.01" name="price_per_night" class="form-control" required
                           value="<?php echo $edit_type ? $edit_type['price_per_night'] : ''; ?>">
                </div>
                <div class="mb-3">
                    <label class="form-label">Capacity (guests)</label>
                    <input type="number" name="capacity" class="form-control" required
                           value="<?php echo $edit_type ? $edit_type['capacity'] : '2'; ?>">
                </div>
                <button type="submit" class="btn btn-primary w-100"><?php echo $edit_type ? 'Update' : 'Add'; ?> Room Type</button>
                <?php if ($edit_type): ?>
                    <a href="room_types.php" class="btn btn-outline-secondary w-100 mt-2">Cancel</a>
                <?php endif; ?>
            </form>
        </div>
    </div>

    <div class="col-lg-8">
        <div class="card p-3">
            <h5 class="mb-3">Existing Room Types</h5>
            <div class="table-responsive">
                <table class="table table-bordered align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>Name</th>
                            <th>Description</th>
                            <th>Price/Night</th>
                            <th>Capacity</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($t = mysqli_fetch_assoc($types)): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($t['type_name']); ?></td>
                                <td class="small text-muted"><?php echo htmlspecialchars($t['description']); ?></td>
                                <td><?php echo format_price($t['price_per_night']); ?></td>
                                <td><?php echo (int)$t['capacity']; ?></td>
                                <td>
                                    <a href="room_types.php?edit=<?php echo $t['room_type_id']; ?>" class="btn btn-sm btn-outline-primary">Edit</a>
                                    <a href="room_types.php?delete=<?php echo $t['room_type_id']; ?>" class="btn btn-sm btn-outline-danger"
                                       onclick="return confirm('Delete this room type? Rooms using it must be removed first.');">Delete</a>
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

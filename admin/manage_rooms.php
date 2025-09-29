<?php
$page_title = "Manage Rooms";
include '../config/database.php';
include '../includes/session.php';
include '../classes/Room.php';
include '../includes/admin_header.php';

$database = new Database();
$db = $database->getConnection();
$room = new Room($db);

// Handle delete room
if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
    $delete_id = (int)$_GET['delete'];
    $room->deleteRoom($delete_id);
    header('Location: manage_rooms.php');
    exit();
}

// Handle edit room
$edit_room = null;
if (isset($_GET['edit']) && is_numeric($_GET['edit'])) {
    $edit_room = $room->getRoomById((int)$_GET['edit']);
}
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $room->room_number = $_POST['room_number'];
    $room->room_type = $_POST['room_type'];
    $room->capacity = $_POST['capacity'];
    $room->price_per_night = $_POST['price_per_night'];
    $room->description = $_POST['description'];
    $room->amenities = json_encode($_POST['amenities'] ?? []);
    $room->image_url = $_POST['image_url'];
    $room->is_available = isset($_POST['is_available']) ? 1 : 0;
    if (isset($_POST['room_id']) && is_numeric($_POST['room_id'])) {
        // Edit room
        $stmt = $db->prepare("UPDATE rooms SET room_number=?, room_type=?, capacity=?, price_per_night=?, description=?, amenities=?, image_url=?, is_available=? WHERE id=?");
        $stmt->execute([
            $room->room_number, $room->room_type, $room->capacity, $room->price_per_night, $room->description, $room->amenities, $room->image_url, $room->is_available, (int)$_POST['room_id']
        ]);
    }
    header('Location: manage_rooms.php');
    exit();
}

$rooms = $room->getAllRooms();
?>
<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="font-serif">Manage Rooms</h2>
        <a href="add_room.php" class="btn btn-primary"><i class="fas fa-plus me-2"></i>Add Room</a>
    </div>

    <?php if ($edit_room): ?>
    <div class="card mb-4">
        <div class="card-header">
            <h5 class="mb-0">Edit Room</h5>
        </div>
        <div class="card-body">
            <form method="POST" action="">
                <input type="hidden" name="room_id" value="<?php echo $edit_room['id']; ?>">
                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Room Number</label>
                        <input type="text" name="room_number" class="form-control" value="<?php echo htmlspecialchars($edit_room['room_number']); ?>" required>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Room Type</label>
                        <select name="room_type" class="form-control" required>
                            <option value="standard" <?php if ($edit_room['room_type'] == 'standard') echo 'selected'; ?>>Standard</option>
                            <option value="deluxe" <?php if ($edit_room['room_type'] == 'deluxe') echo 'selected'; ?>>Deluxe</option>
                            <option value="suite" <?php if ($edit_room['room_type'] == 'suite') echo 'selected'; ?>>Suite</option>
                            <option value="presidential" <?php if ($edit_room['room_type'] == 'presidential') echo 'selected'; ?>>Presidential</option>
                        </select>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Capacity</label>
                        <input type="number" name="capacity" class="form-control" min="1" value="<?php echo htmlspecialchars($edit_room['capacity']); ?>" required>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Price per Night</label>
                        <input type="number" name="price_per_night" class="form-control" min="0" step="0.01" value="<?php echo htmlspecialchars($edit_room['price_per_night']); ?>" required>
                    </div>
                    <div class="col-md-8 mb-3">
                        <label class="form-label">Image URL (or filename)</label>
                        <input type="text" name="image_url" class="form-control" value="<?php echo htmlspecialchars($edit_room['image_url']); ?>">
                    </div>
                </div>
                <div class="mb-3">
                    <label class="form-label">Description</label>
                    <textarea name="description" class="form-control" rows="2" required><?php echo htmlspecialchars($edit_room['description']); ?></textarea>
                </div>
                <div class="mb-3">
                    <label class="form-label">Amenities</label><br>
                    <?php $edit_amenities = json_decode($edit_room['amenities'], true) ?: []; ?>
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="checkbox" name="amenities[]" value="wifi" id="edit_amenity_wifi" <?php if (in_array('wifi', $edit_amenities)) echo 'checked'; ?>>
                        <label class="form-check-label" for="edit_amenity_wifi">WiFi</label>
                    </div>
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="checkbox" name="amenities[]" value="parking" id="edit_amenity_parking" <?php if (in_array('parking', $edit_amenities)) echo 'checked'; ?>>
                        <label class="form-check-label" for="edit_amenity_parking">Parking</label>
                    </div>
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="checkbox" name="amenities[]" value="breakfast" id="edit_amenity_breakfast" <?php if (in_array('breakfast', $edit_amenities)) echo 'checked'; ?>>
                        <label class="form-check-label" for="edit_amenity_breakfast">Breakfast</label>
                    </div>
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="checkbox" name="amenities[]" value="cityview" id="edit_amenity_cityview" <?php if (in_array('cityview', $edit_amenities)) echo 'checked'; ?>>
                        <label class="form-check-label" for="edit_amenity_cityview">City View</label>
                    </div>
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="checkbox" name="amenities[]" value="spa" id="edit_amenity_spa" <?php if (in_array('spa', $edit_amenities)) echo 'checked'; ?>>
                        <label class="form-check-label" for="edit_amenity_spa">Spa</label>
                    </div>
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="checkbox" name="amenities[]" value="butler" id="edit_amenity_butler" <?php if (in_array('butler', $edit_amenities)) echo 'checked'; ?>>
                        <label class="form-check-label" for="edit_amenity_butler">Butler</label>
                    </div>
                </div>
                <div class="form-check mb-3">
                    <input class="form-check-input" type="checkbox" name="is_available" id="edit_is_available" value="1" <?php if ($edit_room['is_available']) echo 'checked'; ?>>
                    <label class="form-check-label" for="edit_is_available">Available</label>
                </div>
                <button type="submit" class="btn btn-success">Save</button>
                <a href="manage_rooms.php" class="btn btn-secondary ms-2">Cancel</a>
            </form>
        </div>
    </div>
    <?php endif; ?>

    <div class="card">
        <div class="card-header">
            <h5 class="mb-0">Room List</h5>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>ID</th>
                            <th>Room #</th>
                            <th>Type</th>
                            <th>Capacity</th>
                            <th>Price/Night</th>
                            <th>Amenities</th>
                            <th>Available</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($row = $rooms->fetch(PDO::FETCH_ASSOC)): ?>
                        <tr>
                            <td><?php echo $row['id']; ?></td>
                            <td><?php echo htmlspecialchars($row['room_number']); ?></td>
                            <td><?php echo ucfirst($row['room_type']); ?></td>
                            <td><?php echo $row['capacity']; ?></td>
                            <td>$<?php echo number_format($row['price_per_night'], 2); ?></td>
                            <td>
                                <?php $am = json_decode($row['amenities'], true) ?: []; ?>
                                <?php foreach ($am as $a): ?>
                                    <span class="badge bg-light text-dark"><?php echo ucfirst($a); ?></span>
                                <?php endforeach; ?>
                            </td>
                            <td><?php echo $row['is_available'] ? '<span class="badge bg-success">Yes</span>' : '<span class="badge bg-secondary">No</span>'; ?></td>
                            <td>
                                <a href="manage_rooms.php?edit=<?php echo $row['id']; ?>" class="btn btn-sm btn-outline-primary">Edit</a>
                                <a href="manage_rooms.php?delete=<?php echo $row['id']; ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Delete this room?');">Delete</a>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>

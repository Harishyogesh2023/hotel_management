
<?php
$page_title = "Add New Room";
include '../config/database.php';
include '../includes/session.php';
include '../classes/Room.php';
include '../includes/admin_header.php';

$database = new Database();
$db = $database->getConnection();
$room = new Room($db);

$success_message = '';
$error_message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
	$room->room_number = $_POST['room_number'];
	$room->room_type = $_POST['room_type'];
	$room->capacity = $_POST['capacity'];
	$room->price_per_night = $_POST['price_per_night'];
	$room->description = $_POST['description'];
	$room->amenities = json_encode($_POST['amenities'] ?? []);
	$room->image_url = $_POST['image_url'];
	$room->is_available = isset($_POST['is_available']) ? 1 : 0;
	if ($room->addRoom()) {
		$success_message = 'Room added successfully!';
	} else {
		$error_message = 'Failed to add room. Please check your input.';
	}
}

?>
<div class="container mt-4">
	<div class="mb-4">
		<h2 class="font-serif">Add New Room</h2>
	</div>
	<?php if ($success_message): ?>
		<div class="alert alert-success"><?php echo $success_message; ?></div>
	<?php endif; ?>
	<?php if ($error_message): ?>
		<div class="alert alert-danger"><?php echo $error_message; ?></div>
	<?php endif; ?>
	<div class="card">
		<div class="card-body">
			<form method="POST" action="">
				<div class="row">
					<div class="col-md-4 mb-3">
						<label class="form-label">Room Number</label>
						<input type="text" name="room_number" class="form-control" required>
					</div>
					<div class="col-md-4 mb-3">
						<label class="form-label">Room Type</label>
						<select name="room_type" class="form-control" required>
							<option value="standard">Standard</option>
							<option value="deluxe">Deluxe</option>
							<option value="suite">Suite</option>
							<option value="presidential">Presidential</option>
						</select>
					</div>
					<div class="col-md-4 mb-3">
						<label class="form-label">Capacity</label>
						<input type="number" name="capacity" class="form-control" min="1" required>
					</div>
				</div>
				<div class="row">
					<div class="col-md-4 mb-3">
						<label class="form-label">Price per Night</label>
						<input type="number" name="price_per_night" class="form-control" min="0" step="0.01" required>
					</div>
					<div class="col-md-8 mb-3">
						<label class="form-label">Image URL (or filename)</label>
						<input type="text" name="image_url" class="form-control" placeholder="e.g. image2.png or full URL">
					</div>
				</div>
				<div class="mb-3">
					<label class="form-label">Description</label>
					<textarea name="description" class="form-control" rows="2" required></textarea>
				</div>
				<div class="mb-3">
					<label class="form-label">Amenities</label><br>
					<div class="form-check form-check-inline">
						<input class="form-check-input" type="checkbox" name="amenities[]" value="wifi" id="amenity_wifi">
						<label class="form-check-label" for="amenity_wifi">WiFi</label>
					</div>
					<div class="form-check form-check-inline">
						<input class="form-check-input" type="checkbox" name="amenities[]" value="parking" id="amenity_parking">
						<label class="form-check-label" for="amenity_parking">Parking</label>
					</div>
					<div class="form-check form-check-inline">
						<input class="form-check-input" type="checkbox" name="amenities[]" value="breakfast" id="amenity_breakfast">
						<label class="form-check-label" for="amenity_breakfast">Breakfast</label>
					</div>
					<div class="form-check form-check-inline">
						<input class="form-check-input" type="checkbox" name="amenities[]" value="cityview" id="amenity_cityview">
						<label class="form-check-label" for="amenity_cityview">City View</label>
					</div>
					<div class="form-check form-check-inline">
						<input class="form-check-input" type="checkbox" name="amenities[]" value="spa" id="amenity_spa">
						<label class="form-check-label" for="amenity_spa">Spa</label>
					</div>
					<div class="form-check form-check-inline">
						<input class="form-check-input" type="checkbox" name="amenities[]" value="butler" id="amenity_butler">
						<label class="form-check-label" for="amenity_butler">Butler</label>
					</div>
				</div>
				<div class="form-check mb-3">
					<input class="form-check-input" type="checkbox" name="is_available" id="is_available" value="1" checked>
					<label class="form-check-label" for="is_available">Available</label>
				</div>
				<button type="submit" class="btn btn-success">Add Room</button>
			</form>
		</div>
	</div>
</div>

<?php include '../includes/footer.php'; ?>

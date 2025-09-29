<?php
include 'config/database.php';
include 'includes/session.php';
include 'classes/Room.php';
include 'includes/header.php';

$database = new Database();
$db = $database->getConnection();
$room = new Room($db);

$room_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$room_data = $room->getRoomById($room_id);

if (!$room_data) {
    echo "<div class='container mt-5'><div class='alert alert-danger'>Room not found.</div></div>";
    include 'includes/footer.php';
    exit;
}

$amenities = json_decode($room_data['amenities'], true) ?: [];
?>
<div class="container mt-5">
    <div class="row">
        <div class="col-md-6">
            <img src="<?php echo $room_data['image_url'] ?: 'https://images.unsplash.com/photo-1611892440504-42a792e24d32?ixlib=rb-4.0.3&w=600'; ?>" class="img-fluid rounded mb-3" alt="Room Image">
        </div>
        <div class="col-md-6">
            <h2 class="mb-3"><?php echo ucfirst($room_data['room_type']); ?> Room #<?php echo $room_data['room_number']; ?></h2>
            <p class="text-muted mb-2">Capacity: <?php echo $room_data['capacity']; ?> <?php echo $room_data['capacity'] == 1 ? 'Guest' : 'Guests'; ?></p>
            <p class="mb-3"><?php echo $room_data['description']; ?></p>
            <h4 class="mb-3">$<?php echo number_format($room_data['price_per_night'], 2); ?> <small class="text-muted">/night</small></h4>
            <?php if (!empty($amenities)): ?>
            <div class="mb-3">
                <strong>Amenities:</strong>
                <ul class="list-inline">
                    <?php foreach ($amenities as $amenity): ?>
                        <li class="list-inline-item badge bg-light text-dark mb-1"><?php echo ucfirst($amenity); ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
            <?php endif; ?>
            <?php if ($room_data['is_available']): ?>
                <a href="booking.php?room_id=<?php echo $room_data['id']; ?>" class="btn btn-primary">Book Now</a>
            <?php else: ?>
                <button class="btn btn-secondary" disabled>Occupied</button>
            <?php endif; ?>
        </div>
    </div>
</div>
<?php include 'includes/footer.php'; ?>

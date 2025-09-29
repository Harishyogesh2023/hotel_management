<?php
$page_title = "Rooms & Suites";
include 'config/database.php';
include 'includes/session.php';
include 'classes/Room.php';

$database = new Database();
$db = $database->getConnection();
$room = new Room($db);


$check_in = $_GET['check_in'] ?? '';
$check_out = $_GET['check_out'] ?? '';
$guests = $_GET['guests'] ?? '';
$room_type = $_GET['room_type'] ?? '';
$min_price = $_GET['min_price'] ?? 0;
$max_price = $_GET['max_price'] ?? 30000;

if ($check_in && $check_out) {
    $available_rooms = $room->getAvailableRooms($check_in, $check_out);
} else {
    $available_rooms = $room->getAllRooms();
}


if (!$available_rooms) {
    $errorInfo = $db->errorInfo();
    echo "<div style='color:red;'>Query failed: " . htmlspecialchars(implode(' | ', $errorInfo)) . "</div>";
} else {
    $rowCount = $available_rooms->rowCount();
    echo "<div style='color:blue;'>Debug: Rooms found = $rowCount</div>";
}

include 'includes/header.php';
?>



<div class="container mt-5">
    
    <div class="text-center mb-5" data-aos="fade-up">
        <h1 class="font-serif display-4 mb-3">Our Rooms & Suites</h1>
        <p class="lead text-muted">Discover the perfect accommodation for your stay</p>
    </div>
    
    <div class="row">
      
        <div class="col-lg-3 mb-4">
            <div class="card shadow-sm sticky-top" style="top: 100px;">
                <div class="card-body">
                    <h5 class="card-title mb-4">
                        <i class="fas fa-filter me-2"></i>Filter Rooms
                    </h5>
                    
                    <form method="GET" action="">
                        
                        <div class="mb-4">
                            <h6>Stay Dates</h6>
                            <div class="mb-3">
                                <label for="check_in" class="form-label">Check-in</label>
                                <input type="date" class="form-control" id="check_in" name="check_in" 
                                       value="<?php echo $check_in; ?>">
                            </div>
                            <div class="mb-3">
                                <label for="check_out" class="form-label">Check-out</label>
                                <input type="date" class="form-control" id="check_out" name="check_out" 
                                       value="<?php echo $check_out; ?>">
                            </div>
                        </div>
                        
                       
                        <div class="mb-4">
                            <label for="guests" class="form-label">Guests</label>
                            <select class="form-control" id="guests" name="guests">
                                <option value="">Any</option>
                                <option value="1" <?php echo $guests == '1' ? 'selected' : ''; ?>>1 Guest</option>
                                <option value="2" <?php echo $guests == '2' ? 'selected' : ''; ?>>2 Guests</option>
                                <option value="3" <?php echo $guests == '3' ? 'selected' : ''; ?>>3 Guests</option>
                                <option value="4" <?php echo $guests == '4' ? 'selected' : ''; ?>>4 Guests</option>
                                <option value="5" <?php echo $guests == '5' ? 'selected' : ''; ?>>5+ Guests</option>
                            </select>
                        </div>
                        
                       
                        <div class="mb-4">
                            <label for="room_type" class="form-label">Room Type</label>
                            <select class="form-control" id="room_type" name="room_type">
                                <option value="">All Types</option>
                                <option value="standard" <?php echo $room_type == 'standard' ? 'selected' : ''; ?>>Standard</option>
                                <option value="deluxe" <?php echo $room_type == 'deluxe' ? 'selected' : ''; ?>>Deluxe</option>
                                <option value="suite" <?php echo $room_type == 'suite' ? 'selected' : ''; ?>>Suite</option>
                                <option value="presidential" <?php echo $room_type == 'presidential' ? 'selected' : ''; ?>>Presidential</option>
                            </select>
                        </div>
                        
                        <!-- Price Range -->
                        <div class="mb-4">
                            <label class="form-label">Price Range (per night)</label>
                            <div class="row g-2">
                                <div class="col-6">
                                    <input type="number" class="form-control" name="min_price" placeholder="Min" 
                                           value="<?php echo $min_price; ?>" min="0">
                                </div>
                                <div class="col-6">
                                    <input type="number" class="form-control" name="max_price" placeholder="Max" 
                                           value="<?php echo $max_price; ?>" min="0">
                                </div>
                            </div>
                        </div>
                        
                        <button type="submit" class="btn btn-primary w-100 mb-2">Apply Filters</button>
                        <a href="rooms.php" class="btn btn-outline-secondary w-100">Clear All</a>
                    </form>
                </div>
            </div>
        </div>
        
        
        <div class="col-lg-9">
            <?php if ($check_in && $check_out): ?>
                <div class="alert alert-info mb-4">
                    <i class="fas fa-info-circle me-2"></i>
                    Showing available rooms from <strong><?php echo date('M j, Y', strtotime($check_in)); ?></strong> 
                    to <strong><?php echo date('M j, Y', strtotime($check_out)); ?></strong>
                </div>
            <?php endif; ?>
            
            <div class="row g-4" id="roomResults">
                <?php
                $room_count = 0;
                while ($room_data = $available_rooms->fetch(PDO::FETCH_ASSOC)) {
                    // Apply additional filters
                    if ($room_type && $room_data['room_type'] != $room_type) continue;
                    if ($guests && $room_data['capacity'] < intval($guests)) continue;
                    if ($room_data['price_per_night'] < $min_price || $room_data['price_per_night'] > $max_price) continue;
                    
                    $amenities = json_decode($room_data['amenities'], true) ?: [];
                    $room_count++;
                    ?>
                    <div class="col-md-6 col-xl-4" data-aos="fade-up" data-aos-delay="<?php echo ($room_count % 3) * 100; ?>">
                        <div class="room-card card h-100">
                            <div class="position-relative">
                                <img src="<?php echo $room_data['image_url'] ?: 'https://images.unsplash.com/photo-1611892440504-42a792e24d32?ixlib=rb-4.0.3&w=400'; ?>" 
                                     class="card-img-top" alt="<?php echo ucfirst($room_data['room_type']); ?> Room" 
                                     style="height: 250px; object-fit: cover;">
                                
                                <div class="position-absolute top-0 end-0 m-3">
                                    <?php if ($room_data['is_available']): ?>
                                        <span class="badge bg-success">Available</span>
                                    <?php else: ?>
                                        <span class="badge bg-danger">Occupied</span>
                                    <?php endif; ?>
                                </div>
                            </div>
                            
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-start mb-3">
                                    <div>
                                        <h5 class="card-title room-type"><?php echo ucfirst($room_data['room_type']); ?> Room</h5>
                                        <p class="text-muted mb-0">Room #<?php echo $room_data['room_number']; ?></p>
                                    </div>
                                </div>
                                
                                <p class="card-text text-muted small"><?php echo $room_data['description']; ?></p>
                                
                                <div class="d-flex align-items-center mb-3">
                                    <i class="fas fa-users text-primary me-2"></i>
                                    <span class="small"><?php echo $room_data['capacity']; ?> <?php echo $room_data['capacity'] == 1 ? 'Guest' : 'Guests'; ?></span>
                                </div>
                                
                                <?php if (!empty($amenities)): ?>
                                <div class="amenities mb-3">
                                    <div class="d-flex flex-wrap gap-1">
                                        <?php foreach (array_slice($amenities, 0, 4) as $amenity): ?>
                                            <small class="badge bg-light text-dark">
                                                <i class="fas fa-<?php echo $amenity == 'wifi' ? 'wifi' : ($amenity == 'parking' ? 'car' : ($amenity == 'breakfast' ? 'utensils' : 'star')); ?> me-1"></i>
                                                <?php echo ucfirst($amenity); ?>
                                            </small>
                                        <?php endforeach; ?>
                                        <?php if (count($amenities) > 4): ?>
                                            <small class="text-muted">+<?php echo count($amenities) - 4; ?> more</small>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                <?php endif; ?>
                            </div>
                            
                            <div class="card-footer bg-transparent">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <span class="price">$<?php echo number_format($room_data['price_per_night'], 2); ?></span>
                                        <small class="text-muted">/night</small>
                                        
                                        <?php if ($check_in && $check_out): ?>
                                            <div class="small text-muted">
                                                <?php 
                                                $nights = (strtotime($check_out) - strtotime($check_in)) / (60 * 60 * 24);
                                                $total = $nights * $room_data['price_per_night'];
                                                echo "Total: $" . number_format($total, 2) . " ({$nights} nights)";
                                                ?>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                    
                                    <div class="d-flex flex-wrap gap-2">
                                        <a href="room-details.php?id=<?php echo $room_data['id']; ?><?php echo $check_in ? "&check_in=$check_in&check_out=$check_out" : ''; ?>" 
                                           class="btn btn-outline-primary btn-sm flex-fill">Details</a>
                                        <?php if ($room_data['is_available']): ?>
                                            <a href="booking.php?room_id=<?php echo $room_data['id']; ?><?php echo $check_in ? "&check_in=$check_in&check_out=$check_out" : ''; ?>" 
                                               class="btn btn-primary btn-sm flex-fill">Book</a>
                                        <?php else: ?>
                                            <button class="btn btn-secondary btn-sm flex-fill" disabled>Occupied</button>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php
                }
                
                if ($room_count == 0) {
                    echo '<div class="col-12">
                            <div class="card">
                                <div class="card-body text-center py-5">
                                    <i class="fas fa-search fa-3x text-muted mb-3"></i>
                                    <h5>No rooms found</h5>
                                    <p class="text-muted">Try adjusting your filters to see more results.</p>
                                    <a href="rooms.php" class="btn btn-outline-primary">Clear Filters</a>
                                </div>
                            </div>
                          </div>';
                }
                ?>
            </div>
            
            <?php if ($room_count > 0): ?>
            <div class="text-center mt-5">
                <p class="text-muted">Showing <?php echo $room_count; ?> available rooms</p>
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<script>
// Auto-submit form when dates change
document.getElementById('check_in').addEventListener('change', function() {
    const checkOut = document.getElementById('check_out');
    checkOut.min = this.value;
    
    if (checkOut.value && this.value && checkOut.value > this.value) {
        // Auto-submit if both dates are selected
        setTimeout(() => {
            document.querySelector('form').submit();
        }, 500);
    }
});

document.getElementById('check_out').addEventListener('change', function() {
    const checkIn = document.getElementById('check_in');
    
    if (checkIn.value && this.value && this.value > checkIn.value) {
        // Auto-submit if both dates are selected
        setTimeout(() => {
            document.querySelector('form').submit();
        }, 500);
    }
});
</script>

<?php include 'includes/footer.php'; ?>

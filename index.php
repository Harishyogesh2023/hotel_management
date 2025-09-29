<?php
$page_title = "Home";
include 'config/database.php';
include 'includes/session.php';
include 'classes/Room.php';

$database = new Database();
$db = $database->getConnection();
$room = new Room($db);

$featured_rooms = $room->getAvailableRooms();

include 'includes/header.php';
?>


<section class="hero">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-6" data-aos="fade-right">
                <div class="hero-content">
                    <h1 class="font-serif">Experience Luxury Beyond Compare</h1>
                    <p class="lead">Discover unparalleled comfort and elegance at Grand Hotel. Your perfect stay awaits with world-class amenities and exceptional service.</p>
                    <a href="rooms.php" class="btn btn-primary btn-lg me-3">View Rooms</a>
                    <a href="#amenities" class="btn btn-outline-light btn-lg">Explore Amenities</a>
                </div>
            </div>
            <div class="col-lg-6" data-aos="fade-left">
               
                <div class="search-form">
                    <h4 class="mb-4 text-dark">Find Your Perfect Room</h4>
                    <form id="availabilityForm" method="GET" action="rooms.php">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="check_in" class="form-label">Check-in Date</label>
                                <input type="date" class="form-control" id="check_in" name="check_in" required>
                            </div>
                            <div class="col-md-6">
                                <label for="check_out" class="form-label">Check-out Date</label>
                                <input type="date" class="form-control" id="check_out" name="check_out" required>
                            </div>
                            <div class="col-md-6">
                                <label for="guests" class="form-label">Guests</label>
                                <select class="form-control" id="guests" name="guests">
                                    <option value="1">1 Guest</option>
                                    <option value="2" selected>2 Guests</option>
                                    <option value="3">3 Guests</option>
                                    <option value="4">4 Guests</option>
                                    <option value="5">5+ Guests</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label for="room_type" class="form-label">Room Type</label>
                                <select class="form-control" id="room_type" name="room_type">
                                    <option value="">All Types</option>
                                    <option value="standard">Standard</option>
                                    <option value="deluxe">Deluxe</option>
                                    <option value="suite">Suite</option>
                                    <option value="presidential">Presidential</option>
                                </select>
                            </div>
                            <div class="col-12">
                                <button type="submit" id="searchRoomsBtn" class="btn btn-primary w-100">
                                    <i class="fas fa-search me-2"></i>Search Available Rooms
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>


<section class="py-5">
    <div class="container">
        <div class="text-center mb-5" data-aos="fade-up">
            <h2 class="font-serif display-5 mb-3">Featured Accommodations</h2>
            <p class="lead text-muted">Discover our carefully curated selection of rooms and suites, each designed to provide the ultimate in comfort and luxury.</p>
        </div>
        
        <div class="row g-4">
            <?php
            $count = 0;
            while ($room_data = $featured_rooms->fetch(PDO::FETCH_ASSOC)) {
                if ($count >= 3) break;
                $amenities = json_decode($room_data['amenities'], true) ?: [];
                ?>
                <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="<?php echo $count * 100; ?>">
                    <div class="room-card card h-100">
                        <img src="<?php echo $room_data['image_url'] ?: 'https://images.unsplash.com/photo-1611892440504-42a792e24d32?ixlib=rb-4.0.3&w=400'; ?>" 
                             class="card-img-top" alt="<?php echo ucfirst($room_data['room_type']); ?> Room">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-start mb-3">
                                <div>
                                    <h5 class="card-title room-type"><?php echo ucfirst($room_data['room_type']); ?> Room</h5>
                                    <p class="text-muted mb-0">Room #<?php echo $room_data['room_number']; ?></p>
                                </div>
                                <span class="badge bg-success">Available</span>
                            </div>
                            
                            <p class="card-text text-muted"><?php echo $room_data['description']; ?></p>
                            
                            <div class="d-flex align-items-center mb-3">
                                <i class="fas fa-users text-primary me-2"></i>
                                <span><?php echo $room_data['capacity']; ?> <?php echo $room_data['capacity'] == 1 ? 'Guest' : 'Guests'; ?></span>
                            </div>
                            
                            <?php if (!empty($amenities)): ?>
                            <div class="amenities mb-3">
                                <?php foreach (array_slice($amenities, 0, 4) as $amenity): ?>
                                    <small class="badge bg-light text-dark me-1 mb-1">
                                        <i class="fas fa-<?php echo $amenity == 'wifi' ? 'wifi' : ($amenity == 'parking' ? 'car' : ($amenity == 'breakfast' ? 'utensils' : 'star')); ?> me-1"></i>
                                        <?php echo ucfirst($amenity); ?>
                                    </small>
                                <?php endforeach; ?>
                            </div>
                            <?php endif; ?>
                        </div>
                        <div class="card-footer bg-transparent">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <span class="price">$<?php echo number_format($room_data['price_per_night'], 2); ?></span>
                                    <small class="text-muted">/night</small>
                                </div>
                                <div>
                                    <a href="room-details.php?id=<?php echo $room_data['id']; ?>" class="btn btn-outline-primary btn-sm me-2">Details</a>
                                    <a href="booking.php?room_id=<?php echo $room_data['id']; ?>" class="btn btn-primary btn-sm">Book Now</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <?php
                $count++;
            }
            ?>
        </div>
        
        <div class="text-center mt-5" data-aos="fade-up">
            <a href="rooms.php" class="btn btn-outline-primary btn-lg">View All Rooms</a>
        </div>
    </div>
</section>


<section id="amenities" class="py-5 bg-light">
    <div class="container">
        <div class="text-center mb-5" data-aos="fade-up">
            <h2 class="font-serif display-5 mb-3">World-Class Amenities</h2>
            <p class="lead text-muted">Experience exceptional facilities and services designed to enhance your stay.</p>
        </div>
        
        <div class="row g-4">
            <div class="col-lg-4 col-md-6" data-aos="fade-up">
                <div class="amenity-card">
                    <div class="amenity-icon">
                        <i class="fas fa-wifi"></i>
                    </div>
                    <h5>Free Wi-Fi</h5>
                    <p class="text-muted">High-speed internet throughout the hotel for all your connectivity needs.</p>
                </div>
            </div>
            
            <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="100">
                <div class="amenity-card">
                    <div class="amenity-icon">
                        <i class="fas fa-car"></i>
                    </div>
                    <h5>Valet Parking</h5>
                    <p class="text-muted">Complimentary valet parking service for all hotel guests.</p>
                </div>
            </div>
            
            <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="200">
                <div class="amenity-card">
                    <div class="amenity-icon">
                        <i class="fas fa-utensils"></i>
                    </div>
                    <h5>Fine Dining</h5>
                    <p class="text-muted">Award-winning restaurant and 24/7 room service for culinary excellence.</p>
                </div>
            </div>
            
            <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="300">
                <div class="amenity-card">
                    <div class="amenity-icon">
                        <i class="fas fa-swimming-pool"></i>
                    </div>
                    <h5>Swimming Pool</h5>
                    <p class="text-muted">Outdoor infinity pool with stunning city views and poolside service.</p>
                </div>
            </div>
            
            <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="400">
                <div class="amenity-card">
                    <div class="amenity-icon">
                        <i class="fas fa-dumbbell"></i>
                    </div>
                    <h5>Fitness Center</h5>
                    <p class="text-muted">24/7 state-of-the-art fitness facility with personal training available.</p>
                </div>
            </div>
            
            <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="500">
                <div class="amenity-card">
                    <div class="amenity-icon">
                        <i class="fas fa-spa"></i>
                    </div>
                    <h5>Luxury Spa</h5>
                    <p class="text-muted">Full-service spa offering rejuvenating treatments and wellness programs.</p>
                </div>
            </div>
        </div>
    </div>
</section>


<section class="py-5">
    <div class="container">
        <div class="text-center mb-5" data-aos="fade-up">
            <h2 class="font-serif display-5 mb-3">Guest Experiences</h2>
            <p class="lead text-muted">What our valued guests say about their stay</p>
        </div>
        
        <div class="row g-4">
            <div class="col-lg-4" data-aos="fade-up">
                <div class="card h-100 border-0 shadow-sm">
                    <div class="card-body">
                        <div class="mb-3">
                            <?php for ($i = 0; $i < 5; $i++): ?>
                                <i class="fas fa-star text-warning"></i>
                            <?php endfor; ?>
                        </div>
                        <p class="card-text">"Absolutely exceptional service and stunning accommodations. The attention to detail is remarkable and the staff goes above and beyond."</p>
                    </div>
                    <div class="card-footer bg-transparent border-0">
                        <div class="d-flex align-items-center">
                            <img src="https://images.unsplash.com/photo-1494790108755-2616b612b1ad?w=50&h=50&fit=crop&crop=face" 
                                 class="rounded-circle me-3" alt="Sarah Johnson" width="50" height="50">
                            <div>
                                <h6 class="mb-0">Sarah Johnson</h6>
                                <small class="text-muted">Presidential Suite • 3 nights</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-lg-4" data-aos="fade-up" data-aos-delay="100">
                <div class="card h-100 border-0 shadow-sm">
                    <div class="card-body">
                        <div class="mb-3">
                            <?php for ($i = 0; $i < 5; $i++): ?>
                                <i class="fas fa-star text-warning"></i>
                            <?php endfor; ?>
                        </div>
                        <p class="card-text">"Perfect location, amazing amenities, and staff that truly cares about guest experience. Highly recommended for business and leisure."</p>
                    </div>
                    <div class="card-footer bg-transparent border-0">
                        <div class="d-flex align-items-center">
                            <img src="https://images.unsplash.com/photo-1472099645785-5658abf4ff4e?w=50&h=50&fit=crop&crop=face" 
                                 class="rounded-circle me-3" alt="Michael Chen" width="50" height="50">
                            <div>
                                <h6 class="mb-0">Michael Chen</h6>
                                <small class="text-muted">Deluxe Room • 2 nights</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-lg-4" data-aos="fade-up" data-aos-delay="200">
                <div class="card h-100 border-0 shadow-sm">
                    <div class="card-body">
                        <div class="mb-3">
                            <?php for ($i = 0; $i < 5; $i++): ?>
                                <i class="fas fa-star text-warning"></i>
                            <?php endfor; ?>
                        </div>
                        <p class="card-text">"A truly luxurious experience from check-in to check-out. Will definitely return on our next visit to the city."</p>
                    </div>
                    <div class="card-footer bg-transparent border-0">
                        <div class="d-flex align-items-center">
                            <img src="https://images.unsplash.com/photo-1438761681033-6461ffad8d80?w=50&h=50&fit=crop&crop=face" 
                                 class="rounded-circle me-3" alt="Emma Davis" width="50" height="50">
                            <div>
                                <h6 class="mb-0">Emma Davis</h6>
                                <small class="text-muted">Standard Room • 4 nights</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>


<section class="py-5 bg-primary text-white">
    <div class="container text-center">
        <div data-aos="fade-up">
            <h2 class="font-serif display-5 mb-3">Ready to Experience Luxury?</h2>
            <p class="lead mb-4">Book your stay today and discover what makes Grand Hotel the premier destination for discerning travelers.</p>
            <a href="rooms.php" class="btn btn-light btn-lg me-3">Book Your Stay</a>
            <a href="contact.php" class="btn btn-outline-light btn-lg">Contact Us</a>
        </div>
    </div>
</section>

<?php include 'includes/footer.php'; ?>

<script>

document.addEventListener('DOMContentLoaded', function() {
    var form = document.getElementById('availabilityForm');
    var btn = document.getElementById('searchRoomsBtn');
    if (form && btn) {
        btn.addEventListener('click', function(e) {
            var checkIn = document.getElementById('check_in').value;
            var checkOut = document.getElementById('check_out').value;
            if (!checkIn || !checkOut) {
                e.preventDefault();
                alert('Please select both check-in and check-out dates.');
            } else {
                form.submit();
            }
        });
        form.addEventListener('submit', function(e) {
            var checkIn = document.getElementById('check_in').value;
            var checkOut = document.getElementById('check_out').value;
            if (!checkIn || !checkOut) {
                e.preventDefault();
                alert('Please select both check-in and check-out dates.');
            }
        });
    }
});
document.addEventListener('DOMContentLoaded', function() {
    var form = document.getElementById('availabilityForm');
    if (form) {
        form.addEventListener('submit', function(e) {
            var checkIn = document.getElementById('check_in').value;
            var checkOut = document.getElementById('check_out').value;
            if (!checkIn || !checkOut) {
                e.preventDefault();
                alert('Please select both check-in and check-out dates.');
            }
        });
    }
});
</script>
    </script>
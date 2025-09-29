<?php
$page_title = "Admin Dashboard";
include '../config/database.php';
include '../includes/session.php';
include '../classes/User.php';
include '../classes/Room.php';
include '../classes/Booking.php';

// Debug output for session before admin check
echo '<div style="color:blue; font-weight:bold;">DEBUG: Session is_admin: ' . (isset($_SESSION['is_admin']) ? htmlspecialchars($_SESSION['is_admin']) : 'not set') . ' | user_id: ' . (isset($_SESSION['user_id']) ? htmlspecialchars($_SESSION['user_id']) : 'not set') . '</div>';
// Require admin access
requireAdmin();

$database = new Database();
$db = $database->getConnection();

$user = new User($db);
$room = new Room($db);
$booking = new Booking($db);

// Get statistics
$stats = $booking->getBookingStats();

// Get recent bookings
$recent_bookings = $booking->getAllBookings();

// Get room statistics
$all_rooms = $room->getAllRooms();
$total_rooms = 0;
$available_rooms = 0;

while ($room_data = $all_rooms->fetch(PDO::FETCH_ASSOC)) {
    $total_rooms++;
    if ($room_data['is_available']) {
        $available_rooms++;
    }
}

$occupancy_rate = $total_rooms > 0 ? (($total_rooms - $available_rooms) / $total_rooms) * 100 : 0;

// Use a custom admin header to avoid user navigation links
include '../includes/admin_header.php';
?>

<div class="container-fluid mt-4">
    <div class="row">
        <!-- Sidebar -->
        <div class="col-lg-2 mb-4">
            <div class="card shadow-sm">
                <div class="card-body">
                    <h6 class="card-title">Admin Menu</h6>
                    <div class="nav flex-column nav-pills">
                        <a class="nav-link active" href="dashboard.php">
                            <i class="fas fa-tachometer-alt me-2"></i>Dashboard
                        </a>
                        <a class="nav-link" href="manage_rooms.php">
                            <i class="fas fa-bed me-2"></i>Manage Rooms
                        </a>
                        <a class="nav-link" href="manage_bookings.php">
                            <i class="fas fa-calendar-check me-2"></i>Bookings
                        </a>
                        <a class="nav-link" href="manage_users.php">
                            <i class="fas fa-users me-2"></i>Users
                        </a>
                        <!-- Reports link removed -->
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Main Content -->
        <div class="col-lg-10">
            <!-- Page Header -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1 class="font-serif">Admin Dashboard</h1>
                <div class="d-flex gap-2">
                    <button class="btn btn-outline-primary" onclick="location.reload()">
                        <i class="fas fa-refresh me-2"></i>Refresh
                    </button>
                    <a href="../index.php" class="btn btn-outline-secondary">
                        <i class="fas fa-home me-2"></i>View Site
                    </a>
                </div>
            </div>
            
            <!-- Statistics Cards -->
            <div class="row g-4 mb-5">
                <div class="col-lg-3 col-md-6">
                    <div class="dashboard-card card">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="flex-shrink-0">
                                    <i class="fas fa-bed fa-2x text-primary"></i>
                                </div>
                                <div class="flex-grow-1 ms-3">
                                    <div class="stat-number"><?php echo $total_rooms; ?></div>
                                    <div class="text-muted">Total Rooms</div>
                                    <small class="text-success">
                                        <i class="fas fa-check-circle me-1"></i>
                                        <?php echo $available_rooms; ?> Available
                                    </small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="col-lg-3 col-md-6">
                    <div class="dashboard-card card">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="flex-shrink-0">
                                    <i class="fas fa-chart-line fa-2x text-success"></i>
                                </div>
                                <div class="flex-grow-1 ms-3">
                                    <div class="stat-number"><?php echo number_format($occupancy_rate, 1); ?>%</div>
                                    <div class="text-muted">Occupancy Rate</div>
                                    <small class="<?php echo $occupancy_rate >= 70 ? 'text-success' : 'text-warning'; ?>">
                                        <i class="fas fa-trend-up me-1"></i>
                                        <?php echo $occupancy_rate >= 70 ? 'Excellent' : 'Good'; ?>
                                    </small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="col-lg-3 col-md-6">
                    <div class="dashboard-card card">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="flex-shrink-0">
                                    <i class="fas fa-calendar-check fa-2x text-info"></i>
                                </div>
                                <div class="flex-grow-1 ms-3">
                                    <div class="stat-number"><?php echo $stats['total_bookings']; ?></div>
                                    <div class="text-muted">Total Bookings</div>
                                    <small class="text-info">
                                        <i class="fas fa-clock me-1"></i>
                                        <?php echo $stats['confirmed_bookings']; ?> Confirmed
                                    </small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="col-lg-3 col-md-6">
                    <div class="dashboard-card card">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="flex-shrink-0">
                                    <i class="fas fa-dollar-sign fa-2x text-warning"></i>
                                </div>
                                <div class="flex-grow-1 ms-3">
                                    <div class="stat-number">$<?php echo number_format($stats['monthly_revenue']); ?></div>
                                    <div class="text-muted">Monthly Revenue</div>
                                    <small class="text-success">
                                        <i class="fas fa-arrow-up me-1"></i>
                                        This Month
                                    </small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Recent Activity -->
            <div class="row">
                <!-- Recent Bookings -->
                <div class="col-lg-8 mb-4">
                    <div class="card shadow-sm">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h5 class="mb-0">Recent Bookings</h5>
                            <a href="manage_bookings.php" class="btn btn-sm btn-outline-primary">View All</a>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table mb-0">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Guest</th>
                                            <th>Room</th>
                                            <th>Check-in</th>
                                            <th>Check-out</th>
                                            <th>Status</th>
                                            <th>Amount</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        $count = 0;
                                        while ($booking_data = $recent_bookings->fetch(PDO::FETCH_ASSOC)) {
                                            if ($count >= 10) break; // Show only 10 recent bookings
                                            ?>
                                            <tr>
                                                <td>
                                                    <strong><?php echo $booking_data['first_name'] . ' ' . $booking_data['last_name']; ?></strong><br>
                                                    <small class="text-muted"><?php echo $booking_data['email']; ?></small>
                                                </td>
                                                <td>
                                                    <span class="badge bg-light text-dark">
                                                        #<?php echo $booking_data['room_number']; ?>
                                                    </span><br>
                                                    <small><?php echo ucfirst($booking_data['room_type']); ?></small>
                                                </td>
                                                <td><?php echo date('M j, Y', strtotime($booking_data['check_in_date'])); ?></td>
                                                <td><?php echo date('M j, Y', strtotime($booking_data['check_out_date'])); ?></td>
                                                <td>
                                                    <span class="badge bg-<?php 
                                                        echo $booking_data['status'] == 'confirmed' ? 'success' : 
                                                            ($booking_data['status'] == 'cancelled' ? 'danger' : 'warning'); 
                                                    ?>">
                                                        <?php echo ucfirst($booking_data['status']); ?>
                                                    </span>
                                                </td>
                                                <td><strong>$<?php echo number_format($booking_data['total_price'], 2); ?></strong></td>
                                                <td>
                                                    <div class="btn-group btn-group-sm">
                                                        <button class="btn btn-outline-primary" title="View Details">
                                                            <i class="fas fa-eye"></i>
                                                        </button>
                                                        <button class="btn btn-outline-secondary" title="Edit">
                                                            <i class="fas fa-edit"></i>
                                                        </button>
                                                    </div>
                                                </td>
                                            </tr>
                                            <?php
                                            $count++;
                                        }
                                        ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Quick Actions -->
                <div class="col-lg-4 mb-4">
                    <div class="card shadow-sm">
                        <div class="card-header">
                            <h5 class="mb-0">Quick Actions</h5>
                        </div>
                        <div class="card-body">
                            <div class="d-grid gap-2">
                                <a href="add_room.php" class="btn btn-primary">
                                    <i class="fas fa-plus me-2"></i>Add New Room
                                </a>
                                <!-- Create Booking link removed -->
                                <a href="manage_users.php" class="btn btn-outline-secondary">
                                    <i class="fas fa-users me-2"></i>Manage Users
                                </a>
                                <a href="download_report.php" class="btn btn-outline-success w-100">Download Report</a>
                            </div>
                        </div>
                    </div>
                    
                    <!-- System Info -->
                    <div class="card shadow-sm mt-4">
                        <div class="card-header">
                            <h5 class="mb-0">System Information</h5>
                        </div>
                        <div class="card-body">
                            <ul class="list-unstyled mb-0">
                                <li class="d-flex justify-content-between py-1">
                                    <span>Total Revenue:</span>
                                    <strong>$<?php echo number_format($stats['total_revenue']); ?></strong>
                                </li>
                                <li class="d-flex justify-content-between py-1">
                                    <span>Active Bookings:</span>
                                    <strong><?php echo $stats['confirmed_bookings']; ?></strong>
                                </li>
                                <li class="d-flex justify-content-between py-1">
                                    <span>Available Rooms:</span>
                                    <strong><?php echo $available_rooms; ?>/<?php echo $total_rooms; ?></strong>
                                </li>
                                <li class="d-flex justify-content-between py-1">
                                    <span>Last Updated:</span>
                                    <strong><?php echo date('M j, H:i'); ?></strong>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Auto-refresh dashboard every 5 minutes
setTimeout(function() {
    location.reload();
}, 5 * 60 * 1000);

// Initialize tooltips
document.addEventListener('DOMContentLoaded', function() {
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[title]'));
    var tooltipList = tooltipTriggerList.map(function(tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });
});
</script>

<?php include '../includes/footer.php'; ?>
<?php
require_once 'includes/session.php';
requireLogin();


$host = 'localhost';
$db   = 'hotel_management';
$user = 'root';
$pass = 'root';
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
];

try {
    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (PDOException $e) {
    die('Database connection failed: ' . $e->getMessage());
}

$userId = $_SESSION['user_id'];


$stmt = $pdo->prepare("
    SELECT b.id AS booking_id, b.check_in_date, b.check_out_date, b.total_price, b.status,
           r.room_number, r.room_type, r.price_per_night
    FROM bookings b
    JOIN rooms r ON b.room_id = r.id
    WHERE b.user_id = :user_id
    ORDER BY b.created_at DESC
");
$stmt->execute(['user_id' => $userId]);
$bookings = $stmt->fetchAll();

$page_title = "My Bookings";
include 'includes/header.php';
?>

<div class="container py-5">
    <h2 class="mb-4">My Bookings</h2>

    <?php if (empty($bookings)): ?>
        <div class="alert alert-info">You have no bookings yet.</div>
    <?php else: ?>
        <div class="table-responsive">
            <table class="table table-bordered align-middle">
                <thead class="table-dark">
                    <tr>
                        <th>Booking ID</th>
                        <th>Room Number</th>
                        <th>Room Type</th>
                        <th>Check-in</th>
                        <th>Check-out</th>
                        <th>Total Price</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($bookings as $booking): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($booking['booking_id']); ?></td>
                            <td><?php echo htmlspecialchars($booking['room_number']); ?></td>
                            <td><?php echo htmlspecialchars(ucfirst($booking['room_type'])); ?></td>
                            <td><?php echo htmlspecialchars(date('d M Y', strtotime($booking['check_in_date']))); ?></td>
                            <td><?php echo htmlspecialchars(date('d M Y', strtotime($booking['check_out_date']))); ?></td>
                            <td>$<?php echo number_format($booking['total_price'], 2); ?></td>
                            <td>
                                <?php if ($booking['status'] === 'cancelled'): ?>
                                    <span class="badge bg-danger">Cancelled</span>
                                <?php else: ?>
                                    <span class="badge bg-success">Confirmed</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if ($booking['status'] !== 'cancelled'): ?>
                                    <button class="btn btn-sm btn-danger cancel-booking"
                                        data-booking-id="<?php echo $booking['booking_id']; ?>"
                                        data-room-number="<?php echo $booking['room_number']; ?>">
                                        Cancel
                                    </button>
                                <?php else: ?>
                                    <span class="text-muted">N/A</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>

<?php include 'includes/footer.php'; ?>

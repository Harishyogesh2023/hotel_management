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
    echo json_encode(['success' => false, 'message' => 'Database connection failed.']);
    exit();
}


$data = json_decode(file_get_contents('php://input'), true);
$bookingId = $data['booking_id'] ?? null;

if (!$bookingId) {
    echo json_encode(['success' => false, 'message' => 'Invalid booking ID.']);
    exit();
}


$stmt = $pdo->prepare("SELECT status FROM bookings WHERE id = :id AND user_id = :user_id");
$stmt->execute([
    'id' => $bookingId,
    'user_id' => $_SESSION['user_id']
]);
$booking = $stmt->fetch();

if (!$booking) {
    echo json_encode(['success' => false, 'message' => 'Booking not found.']);
    exit();
}


if ($booking['status'] === 'cancelled') {
    echo json_encode(['success' => false, 'message' => 'Booking is already cancelled.']);
    exit();
}


$update = $pdo->prepare("UPDATE bookings SET status = 'cancelled' WHERE id = :id");
if ($update->execute(['id' => $bookingId])) {
    echo json_encode(['success' => true, 'message' => 'Booking cancelled successfully.']);
} else {
    echo json_encode(['success' => false, 'message' => 'Failed to cancel booking.']);
}

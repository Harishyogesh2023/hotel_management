<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/classes/Booking.php';
require_once __DIR__ . '/includes/session.php';

requireLogin();


$db = new Database();
$pdo = $db->getConnection();


$room_id = isset($_GET['room_id']) ? (int)$_GET['room_id'] : 0;
$check_in = $_GET['check_in'] ?? '';
$check_out = $_GET['check_out'] ?? '';


$room_query = "SELECT * FROM rooms WHERE id = :room_id LIMIT 1";
$stmt = $pdo->prepare($room_query);
$stmt->execute([':room_id' => $room_id]);
$room_data = $stmt->fetch(PDO::FETCH_ASSOC);


if (!$room_data) {
    header("Location: rooms.php");
    exit();
}


$room_number = $room_data['room_number'] ?? 'Unknown Room';
$room_type = $room_data['room_type'] ?? '';
$price_per_night = $room_data['price_per_night'] ?? 0;


function isRoomAvailable($pdo, $room_id, $check_in, $check_out) {
    $query = "SELECT COUNT(*) FROM bookings 
              WHERE room_id = :room_id 
                AND ((check_in_date < :check_out) AND (check_out_date > :check_in))";
    $stmt = $pdo->prepare($query);
    $stmt->execute([
        ':room_id' => $room_id,
        ':check_in' => $check_in,
        ':check_out' => $check_out
    ]);
    $count = $stmt->fetchColumn();
    return $count == 0;
}


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $check_in_date = $_POST['check_in_date'];
    $check_out_date = $_POST['check_out_date'];

  
    $nights = (strtotime($check_out_date) - strtotime($check_in_date)) / (60 * 60 * 24);
    if ($nights < 1) {
        die("<h3 style='color:red;'>Error: Check-out must be after Check-in</h3>");
    }

    
    if (!isRoomAvailable($pdo, $room_id, $check_in_date, $check_out_date)) {
        die("<h3 style='color:red;'>Error: Room is already booked for selected dates. Please choose different dates.</h3>");
    }

    
    $booking = new Booking($pdo);
    $booking->user_id = $_SESSION['user_id'];
    $booking->room_id = $room_id;
    $booking->check_in_date = $check_in_date;
    $booking->check_out_date = $check_out_date;
    $booking->special_requests = $_POST['special_requests'] ?? '';
    $booking->total_price = number_format($nights * $price_per_night, 2, '.', '');

    if ($booking->createBooking()) {
        $booking_id = $pdo->lastInsertId();
        include 'includes/header.php';

        echo "<div class='container mt-5'>";
        echo "<h2 class='text-success text-center'>Booking Confirmed 🎉</h2><hr>";
        echo "<h4>Booking Invoice</h4>";
        echo "<p><strong>Booking ID:</strong> {$booking_id}</p>";
        echo "<p><strong>User ID:</strong> {$_SESSION['user_id']}</p>";
        echo "<p><strong>Room Number:</strong> {$room_number}</p>";
        echo "<p><strong>Room Type:</strong> {$room_type}</p>";
        echo "<p><strong>Check-in:</strong> {$booking->check_in_date}</p>";
        echo "<p><strong>Check-out:</strong> {$booking->check_out_date}</p>";
        echo "<p><strong>Nights:</strong> {$nights}</p>";
        echo "<p><strong>Price per Night:</strong> ₹{$price_per_night}</p>";
        echo "<h3>Total Price: ₹{$booking->total_price}</h3>";
        echo "<hr>";
        echo "<a href='my-bookings.php' class='btn btn-primary'>Go to My Bookings</a>";
        echo "</div>";

        include 'includes/footer.php';
        exit();
    } else {
        die("<h3 style='color:red;'>Booking failed. Please check your DB or data.</h3>");
    }
}
?>

<?php include 'includes/header.php'; ?>

<div class="container mt-5">
    <h2>Book Room: <?php echo htmlspecialchars($room_number . ' (' . $room_type . ')'); ?></h2>
    <p><strong>Price per night:</strong> ₹<?php echo number_format($price_per_night, 2); ?></p>

    <form action="booking.php?room_id=<?php echo $room_id; ?>" method="POST">
        <input type="hidden" name="room_id" value="<?php echo $room_id; ?>">

        <div class="form-group mb-3">
            <label>Check-in Date:</label>
            <input type="date" name="check_in_date" id="check_in_date" class="form-control" required value="<?php echo htmlspecialchars($check_in); ?>">
        </div>

        <div class="form-group mb-3">
            <label>Check-out Date:</label>
            <input type="date" name="check_out_date" id="check_out_date" class="form-control" required value="<?php echo htmlspecialchars($check_out); ?>">
        </div>

        <div class="form-group mb-3">
            <label>Total Price:</label>
            <input type="text" id="total_price_display" class="form-control" value="₹0.00" readonly>
            <input type="hidden" id="total_price" name="total_price" value="0">
        </div>

        <div class="form-group mb-3">
            <label>Special Requests:</label>
            <textarea name="special_requests" class="form-control" placeholder="Any preferences or requirements"></textarea>
        </div>

        <button type="submit" class="btn btn-success">Confirm Booking</button>
    </form>
</div>

<script>
document.addEventListener("DOMContentLoaded", function() {
    const checkInInput = document.getElementById("check_in_date");
    const checkOutInput = document.getElementById("check_out_date");
    const totalDisplay = document.getElementById("total_price_display");
    const totalHidden = document.getElementById("total_price");

    const pricePerNight = <?php echo floatval($price_per_night); ?>;

    function calculateTotal() {
        const checkIn = checkInInput.value;
        const checkOut = checkOutInput.value;

        if (checkIn && checkOut) {
            const inDate = new Date(checkIn);
            const outDate = new Date(checkOut);
            const nights = (outDate - inDate) / (1000 * 60 * 60 * 24);

            if (nights > 0) {
                const total = nights * pricePerNight;
                totalDisplay.value = "₹" + total.toFixed(2);
                totalHidden.value = total.toFixed(2);
            } else {
                totalDisplay.value = "Invalid Dates";
                totalHidden.value = 0;
            }
        } else {
            totalDisplay.value = "₹0.00";
            totalHidden.value = 0;
        }
    }

    checkInInput.addEventListener("change", calculateTotal);
    checkOutInput.addEventListener("change", calculateTotal);

    calculateTotal();
});
</script>

<?php include 'includes/footer.php'; ?>

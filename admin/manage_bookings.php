<?php
$page_title = "Manage Bookings";
include '../config/database.php';
include '../includes/session.php';
include '../classes/Booking.php';
include '../includes/admin_header.php';

$database = new Database();
$db = $database->getConnection();
$booking = new Booking($db);

// Handle delete booking
if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
    $delete_id = (int)$_GET['delete'];
    $stmt = $db->prepare("DELETE FROM bookings WHERE id = :id");
    $stmt->bindParam(":id", $delete_id);
    $stmt->execute();
    header('Location: manage_bookings.php');
    exit();
}

// Handle update status
if (isset($_GET['status']) && isset($_GET['id']) && is_numeric($_GET['id'])) {
    $id = (int)$_GET['id'];
    $status = $_GET['status'];
    $stmt = $db->prepare("UPDATE bookings SET status = :status WHERE id = :id");
    $stmt->bindParam(":status", $status);
    $stmt->bindParam(":id", $id);
    $stmt->execute();
    header('Location: manage_bookings.php');
    exit();
}

$bookings = $booking->getAllBookings();
?>
<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="font-serif">Manage Bookings</h2>
    </div>
    <div class="card">
        <div class="card-header">
            <h5 class="mb-0">Booking List</h5>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>ID</th>
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
                        <?php while ($row = $bookings->fetch(PDO::FETCH_ASSOC)): ?>
                        <tr>
                            <td><?php echo $row['id']; ?></td>
                            <td><?php echo htmlspecialchars($row['first_name'] . ' ' . $row['last_name']); ?><br><small><?php echo htmlspecialchars($row['email']); ?></small></td>
                            <td>#<?php echo htmlspecialchars($row['room_number']); ?> (<?php echo ucfirst($row['room_type']); ?>)</td>
                            <td><?php echo date('M j, Y', strtotime($row['check_in_date'])); ?></td>
                            <td><?php echo date('M j, Y', strtotime($row['check_out_date'])); ?></td>
                            <td>
                                <span class="badge bg-<?php 
                                    echo $row['status'] == 'confirmed' ? 'success' : 
                                        ($row['status'] == 'cancelled' ? 'danger' : 'warning'); 
                                ?>">
                                    <?php echo ucfirst($row['status']); ?>
                                </span>
                            </td>
                            <td>$<?php echo number_format($row['total_price'], 2); ?></td>
                            <td>
                                <a href="manage_bookings.php?status=confirmed&id=<?php echo $row['id']; ?>" class="btn btn-sm btn-outline-success">Confirm</a>
                                <a href="manage_bookings.php?status=cancelled&id=<?php echo $row['id']; ?>" class="btn btn-sm btn-outline-danger">Cancel</a>
                                <a href="manage_bookings.php?delete=<?php echo $row['id']; ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Delete this booking?');">Delete</a>
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

<?php
include 'config/database.php';
include 'includes/session.php';

$database = new Database();
$db = $database->getConnection();
include 'includes/header.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$user_id = $_SESSION['user_id'];
$stmt = $db->prepare('SELECT * FROM users WHERE id = ?');
$stmt->execute([$user_id]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user) {
    echo "<div class='container mt-5'><div class='alert alert-danger'>User not found.</div></div>";
    include 'includes/footer.php';
    exit;
}
?>
<div class="container mt-5">
    <h2>My Profile</h2>
    <table class="table table-bordered w-50">
        <tr><th>Username</th><td><?php echo htmlspecialchars($user['username']); ?></td></tr>
        <tr><th>Email</th><td><?php echo htmlspecialchars($user['email']); ?></td></tr>
        <tr><th>First Name</th><td><?php echo htmlspecialchars($user['first_name']); ?></td></tr>
        <tr><th>Last Name</th><td><?php echo htmlspecialchars($user['last_name']); ?></td></tr>
        <tr><th>Phone</th><td><?php echo htmlspecialchars($user['phone']); ?></td></tr>
    </table>
</div>
<?php include 'includes/footer.php'; ?>

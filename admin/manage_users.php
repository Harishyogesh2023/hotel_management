
<?php
$page_title = "Manage Users";
include '../config/database.php';
include '../includes/session.php';
include '../classes/User.php';
include '../includes/admin_header.php';

$database = new Database();
$db = $database->getConnection();
$user = new User($db);

// Handle delete user
if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
	$delete_id = (int)$_GET['delete'];
	$stmt = $db->prepare("DELETE FROM users WHERE id = :id");
	$stmt->bindParam(":id", $delete_id);
	$stmt->execute();
	header('Location: manage_users.php');
	exit();
}

// Handle add/edit user
$edit_user = null;
if (isset($_GET['edit']) && is_numeric($_GET['edit'])) {
	$edit_user = $user->getUserById((int)$_GET['edit']);
}
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
	$username = $_POST['username'];
	$email = $_POST['email'];
	$first_name = $_POST['first_name'];
	$last_name = $_POST['last_name'];
	$phone = $_POST['phone'];
	$is_admin = isset($_POST['is_admin']) ? 1 : 0;
	$password = $_POST['password'];
	if (isset($_POST['user_id']) && is_numeric($_POST['user_id'])) {
		// Edit user
		$user_id = (int)$_POST['user_id'];
		if ($password) {
			$password_hash = password_hash($password, PASSWORD_BCRYPT);
			$stmt = $db->prepare("UPDATE users SET username=?, email=?, first_name=?, last_name=?, phone=?, is_admin=?, password=? WHERE id=?");
			$stmt->execute([$username, $email, $first_name, $last_name, $phone, $is_admin, $password_hash, $user_id]);
		} else {
			$stmt = $db->prepare("UPDATE users SET username=?, email=?, first_name=?, last_name=?, phone=?, is_admin=? WHERE id=?");
			$stmt->execute([$username, $email, $first_name, $last_name, $phone, $is_admin, $user_id]);
		}
	} else {
		// Add user
		$password_hash = password_hash($password, PASSWORD_BCRYPT);
		$stmt = $db->prepare("INSERT INTO users (username, password, email, first_name, last_name, phone, is_admin) VALUES (?, ?, ?, ?, ?, ?, ?)");
		$stmt->execute([$username, $password_hash, $email, $first_name, $last_name, $phone, $is_admin]);
	}
	header('Location: manage_users.php');
	exit();
}

$users = $user->getAllUsers();
?>
<div class="container mt-4">
	<div class="d-flex justify-content-between align-items-center mb-4">
		<h2 class="font-serif">Manage Users</h2>
		<a href="manage_users.php?add=1" class="btn btn-primary"><i class="fas fa-user-plus me-2"></i>Add User</a>
	</div>

	<?php if (isset($_GET['add']) || $edit_user): ?>
	<div class="card mb-4">
		<div class="card-header">
			<h5 class="mb-0"><?php echo $edit_user ? 'Edit User' : 'Add User'; ?></h5>
		</div>
		<div class="card-body">
			<form method="POST" action="">
				<?php if ($edit_user): ?>
					<input type="hidden" name="user_id" value="<?php echo $edit_user['id']; ?>">
				<?php endif; ?>
				<div class="row">
					<div class="col-md-4 mb-3">
						<label class="form-label">Username</label>
						<input type="text" name="username" class="form-control" value="<?php echo $edit_user['username'] ?? ''; ?>" required>
					</div>
					<div class="col-md-4 mb-3">
						<label class="form-label">Email</label>
						<input type="email" name="email" class="form-control" value="<?php echo $edit_user['email'] ?? ''; ?>" required>
					</div>
					<div class="col-md-4 mb-3">
						<label class="form-label">Phone</label>
						<input type="text" name="phone" class="form-control" value="<?php echo $edit_user['phone'] ?? ''; ?>">
					</div>
				</div>
				<div class="row">
					<div class="col-md-4 mb-3">
						<label class="form-label">First Name</label>
						<input type="text" name="first_name" class="form-control" value="<?php echo $edit_user['first_name'] ?? ''; ?>" required>
					</div>
					<div class="col-md-4 mb-3">
						<label class="form-label">Last Name</label>
						<input type="text" name="last_name" class="form-control" value="<?php echo $edit_user['last_name'] ?? ''; ?>" required>
					</div>
					<div class="col-md-4 mb-3">
						<label class="form-label">Password <?php if ($edit_user) echo '(leave blank to keep current)'; ?></label>
						<input type="password" name="password" class="form-control" <?php if (!$edit_user) echo 'required'; ?>>
					</div>
				</div>
				<div class="form-check mb-3">
					<input class="form-check-input" type="checkbox" name="is_admin" id="is_admin" value="1" <?php if (($edit_user && $edit_user['is_admin']) || (!$edit_user && isset($_POST['is_admin']))) echo 'checked'; ?>>
					<label class="form-check-label" for="is_admin">Is Admin</label>
				</div>
				<button type="submit" class="btn btn-success">Save</button>
				<a href="manage_users.php" class="btn btn-secondary ms-2">Cancel</a>
			</form>
		</div>
	</div>
	<?php endif; ?>

	<div class="card">
		<div class="card-header">
			<h5 class="mb-0">User List</h5>
		</div>
		<div class="card-body p-0">
			<div class="table-responsive">
				<table class="table mb-0">
					<thead class="table-light">
						<tr>
							<th>ID</th>
							<th>Username</th>
							<th>Email</th>
							<th>Name</th>
							<th>Phone</th>
							<th>Admin</th>
							<th>Created</th>
							<th>Actions</th>
						</tr>
					</thead>
					<tbody>
						<?php while ($row = $users->fetch(PDO::FETCH_ASSOC)): ?>
						<tr>
							<td><?php echo $row['id']; ?></td>
							<td><?php echo htmlspecialchars($row['username']); ?></td>
							<td><?php echo htmlspecialchars($row['email']); ?></td>
							<td><?php echo htmlspecialchars($row['first_name'] . ' ' . $row['last_name']); ?></td>
							<td><?php echo htmlspecialchars($row['phone']); ?></td>
							<td><?php echo $row['is_admin'] ? '<span class="badge bg-success">Yes</span>' : '<span class="badge bg-secondary">No</span>'; ?></td>
							<td><?php echo date('M j, Y', strtotime($row['created_at'])); ?></td>
							<td>
								<a href="manage_users.php?edit=<?php echo $row['id']; ?>" class="btn btn-sm btn-outline-primary">Edit</a>
								<?php if ($row['id'] != $_SESSION['user_id']): ?>
								<a href="manage_users.php?delete=<?php echo $row['id']; ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Delete this user?');">Delete</a>
								<?php endif; ?>
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

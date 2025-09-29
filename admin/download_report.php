<?php

// Download Report: Export all bookings, users, and rooms as CSV
include '../config/database.php';
include '../classes/Booking.php';
include '../classes/User.php';
include '../classes/Room.php';

$database = new Database();
$db = $database->getConnection();
$booking = new Booking($db);
$user = new User($db);
$room = new Room($db);

header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename="hotel_report_' . date('Ymd_His') . '.csv"');
$output = fopen('php://output', 'w');

// --- USERS ---
fputcsv($output, ["USERS"]);
fputcsv($output, ["ID", "Username", "Email", "First Name", "Last Name", "Phone", "Is Admin", "Created At"]);
$users = $user->getAllUsers();
while ($row = $users->fetch(PDO::FETCH_ASSOC)) {
	fputcsv($output, [
		$row['id'],
		$row['username'],
		$row['email'],
		$row['first_name'],
		$row['last_name'],
		$row['phone'],
		$row['is_admin'] ? 'Yes' : 'No',
		$row['created_at']
	]);
}
fputcsv($output, []); // blank line

// --- ROOMS ---
fputcsv($output, ["ROOMS"]);
fputcsv($output, ["ID", "Room Number", "Type", "Capacity", "Price/Night", "Description", "Amenities", "Image URL", "Available", "Created At"]);
$rooms = $room->getAllRooms();
while ($row = $rooms->fetch(PDO::FETCH_ASSOC)) {
	fputcsv($output, [
		$row['id'],
		$row['room_number'],
		$row['room_type'],
		$row['capacity'],
		$row['price_per_night'],
		$row['description'],
		$row['amenities'],
		$row['image_url'],
		$row['is_available'] ? 'Yes' : 'No',
		$row['created_at']
	]);
}
fputcsv($output, []); // blank line

// --- BOOKINGS ---
fputcsv($output, ["BOOKINGS"]);
fputcsv($output, ["ID", "User ID", "Room ID", "Check-in", "Check-out", "Total Price", "Status", "Special Requests", "Created At"]);
$bookings = $booking->getAllBookings();
while ($row = $bookings->fetch(PDO::FETCH_ASSOC)) {
	fputcsv($output, [
		$row['id'],
		$row['user_id'],
		$row['room_id'],
		$row['check_in_date'],
		$row['check_out_date'],
		$row['total_price'],
		$row['status'],
		$row['special_requests'],
		$row['created_at']
	]);
}

fclose($output);
exit();

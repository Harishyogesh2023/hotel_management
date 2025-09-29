<?php
class Booking {
    private $conn;
    private $table_name = "bookings";

    public $id;
    public $user_id;
    public $room_id;
    public $check_in_date;
    public $check_out_date;
    public $total_price;
    public $status;
    public $special_requests;
    public $created_at;

    public function __construct($db) {
        $this->conn = $db;
    }

    // Create new booking
    public function createBooking() {
        $query = "INSERT INTO " . $this->table_name . " 
            (user_id, room_id, check_in_date, check_out_date, total_price, special_requests)
            VALUES (:user_id, :room_id, :check_in_date, :check_out_date, :total_price, :special_requests)";

        $stmt = $this->conn->prepare($query);

        // Sanitize
        $this->user_id = htmlspecialchars(strip_tags($this->user_id));
        $this->room_id = htmlspecialchars(strip_tags($this->room_id));
        $this->check_in_date = htmlspecialchars(strip_tags($this->check_in_date));
        $this->check_out_date = htmlspecialchars(strip_tags($this->check_out_date));
        $this->total_price = htmlspecialchars(strip_tags($this->total_price));
        $this->special_requests = htmlspecialchars(strip_tags($this->special_requests));

        // Bind values
        $stmt->bindParam(":user_id", $this->user_id);
        $stmt->bindParam(":room_id", $this->room_id);
        $stmt->bindParam(":check_in_date", $this->check_in_date);
        $stmt->bindParam(":check_out_date", $this->check_out_date);
        $stmt->bindParam(":total_price", $this->total_price);
        $stmt->bindParam(":special_requests", $this->special_requests);

        if($stmt->execute()) {
            return $this->conn->lastInsertId();
        } else {
            $errorInfo = $stmt->errorInfo();
            error_log('BOOKING ERROR: ' . implode(' | ', $errorInfo));
            echo "<div style='color:red; font-weight:bold;'>Booking Error: " . htmlspecialchars(implode(' | ', $errorInfo)) . "</div>";
        }
        return false;
    }

    // Get bookings by user ID
    public function getUserBookings($user_id) {
        $query = "SELECT b.*, r.room_number, r.room_type, r.image_url 
                  FROM " . $this->table_name . " b
                  JOIN rooms r ON b.room_id = r.id
                  WHERE b.user_id = :user_id 
                  ORDER BY b.created_at DESC";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":user_id", $user_id);
        $stmt->execute();
        return $stmt;
    }

    // Get all bookings (admin only)
    public function getAllBookings() {
        $query = "SELECT b.*, r.room_number, r.room_type, u.first_name, u.last_name, u.email 
                  FROM " . $this->table_name . " b
                  JOIN rooms r ON b.room_id = r.id
                  JOIN users u ON b.user_id = u.id
                  ORDER BY b.created_at DESC";
        
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }

    // Get booking by ID
    public function getBookingById($id) {
        $query = "SELECT b.*, r.room_number, r.room_type, r.image_url, u.first_name, u.last_name, u.email, u.phone 
                  FROM " . $this->table_name . " b
                  JOIN rooms r ON b.room_id = r.id
                  JOIN users u ON b.user_id = u.id
                  WHERE b.id = :id";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id", $id);
        $stmt->execute();
        
        if($stmt->rowCount() > 0) {
            return $stmt->fetch(PDO::FETCH_ASSOC);
        }
        return false;
    }

    // Cancel booking
    public function cancelBooking($id, $user_id = null) {
        $query = "UPDATE " . $this->table_name . " SET status = 'cancelled' WHERE id = :id";
        
        if($user_id) {
            $query .= " AND user_id = :user_id";
        }
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id", $id);
        
        if($user_id) {
            $stmt->bindParam(":user_id", $user_id);
        }
        
        return $stmt->execute();
    }

    // Update booking status (admin only)
    public function updateBookingStatus($id, $status) {
        $query = "UPDATE " . $this->table_name . " SET status = :status WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":status", $status);
        $stmt->bindParam(":id", $id);
        return $stmt->execute();
    }

    // Get booking statistics (admin dashboard)
    public function getBookingStats() {
        $stats = array();
        
        // Total bookings
        $query = "SELECT COUNT(*) as total_bookings FROM " . $this->table_name;
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        $stats['total_bookings'] = $stmt->fetch(PDO::FETCH_ASSOC)['total_bookings'];
        
        // Confirmed bookings
        $query = "SELECT COUNT(*) as confirmed_bookings FROM " . $this->table_name . " WHERE status = 'confirmed'";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        $stats['confirmed_bookings'] = $stmt->fetch(PDO::FETCH_ASSOC)['confirmed_bookings'];
        
        // Total revenue
        $query = "SELECT SUM(total_price) as total_revenue FROM " . $this->table_name . " WHERE status != 'cancelled'";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        $stats['total_revenue'] = $stmt->fetch(PDO::FETCH_ASSOC)['total_revenue'] ?: 0;
        
        // Monthly revenue
        $query = "SELECT SUM(total_price) as monthly_revenue FROM " . $this->table_name . " 
                  WHERE status != 'cancelled' AND MONTH(created_at) = MONTH(CURRENT_DATE()) 
                  AND YEAR(created_at) = YEAR(CURRENT_DATE())";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        $stats['monthly_revenue'] = $stmt->fetch(PDO::FETCH_ASSOC)['monthly_revenue'] ?: 0;
        
        return $stats;
    }
}
?>
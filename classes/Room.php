<?php
class Room {
    private $conn;
    private $table_name = "rooms";

    public $id;
    public $room_number;
    public $room_type;
    public $capacity;
    public $price_per_night;
    public $description;
    public $amenities;
    public $image_url;
    public $is_available;
    public $created_at;

    public function __construct($db) {
        $this->conn = $db;
    }

    // Get all rooms
    public function getAllRooms() {
        $query = "SELECT * FROM " . $this->table_name . " ORDER BY room_number ASC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }

    // Get available rooms
    public function getAvailableRooms($check_in = null, $check_out = null) {
        $query = "SELECT * FROM " . $this->table_name . " 
                  WHERE is_available = 1";
        
        if($check_in && $check_out) {
            $query .= " AND id NOT IN (
                SELECT room_id FROM bookings 
                WHERE status = 'confirmed' 
                AND (
                    (check_in_date <= :check_in AND check_out_date > :check_in) 
                    OR (check_in_date < :check_out AND check_out_date >= :check_out)
                    OR (check_in_date >= :check_in AND check_out_date <= :check_out)
                )
            )";
        }
        
        $query .= " ORDER BY room_type, price_per_night ASC";
        
        $stmt = $this->conn->prepare($query);
        
        if($check_in && $check_out) {
            $stmt->bindParam(":check_in", $check_in);
            $stmt->bindParam(":check_out", $check_out);
        }
        
        $stmt->execute();
        return $stmt;
    }

    // Get room by ID
    public function getRoomById($id) {
        $query = "SELECT * FROM " . $this->table_name . " WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id", $id);
        $stmt->execute();
        
        if($stmt->rowCount() > 0) {
            return $stmt->fetch(PDO::FETCH_ASSOC);
        }
        return false;
    }

    // Add new room (admin only)
    public function addRoom() {
        $query = "INSERT INTO " . $this->table_name . " 
                  SET room_number = :room_number, room_type = :room_type, 
                      capacity = :capacity, price_per_night = :price_per_night, 
                      description = :description, amenities = :amenities, 
                      image_url = :image_url, is_available = :is_available";

        $stmt = $this->conn->prepare($query);

        // Sanitize
        $this->room_number = htmlspecialchars(strip_tags($this->room_number));
        $this->room_type = htmlspecialchars(strip_tags($this->room_type));
        $this->capacity = htmlspecialchars(strip_tags($this->capacity));
        $this->price_per_night = htmlspecialchars(strip_tags($this->price_per_night));
        $this->description = htmlspecialchars(strip_tags($this->description));
        $this->amenities = htmlspecialchars(strip_tags($this->amenities));
        $this->image_url = htmlspecialchars(strip_tags($this->image_url));

        // Bind values
        $stmt->bindParam(":room_number", $this->room_number);
        $stmt->bindParam(":room_type", $this->room_type);
        $stmt->bindParam(":capacity", $this->capacity);
        $stmt->bindParam(":price_per_night", $this->price_per_night);
        $stmt->bindParam(":description", $this->description);
        $stmt->bindParam(":amenities", $this->amenities);
        $stmt->bindParam(":image_url", $this->image_url);
        $stmt->bindParam(":is_available", $this->is_available);

        if($stmt->execute()) {
            return true;
        }
        return false;
    }

    // Update room status
    public function updateRoomStatus($room_id, $status) {
        $query = "UPDATE " . $this->table_name . " SET is_available = :status WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":status", $status);
        $stmt->bindParam(":id", $room_id);
        return $stmt->execute();
    }

    // Delete room
    public function deleteRoom($id) {
        $query = "DELETE FROM " . $this->table_name . " WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id", $id);
        return $stmt->execute();
    }
}
?>
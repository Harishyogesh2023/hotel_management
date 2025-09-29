-- Hotel Management System Database Schema
CREATE DATABASE hotel_management;
USE hotel_management;

-- Users table
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    first_name VARCHAR(50) NOT NULL,
    last_name VARCHAR(50) NOT NULL,
    phone VARCHAR(20),
    is_admin BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Rooms table
CREATE TABLE rooms (
    id INT AUTO_INCREMENT PRIMARY KEY,
    room_number VARCHAR(10) UNIQUE NOT NULL,
    room_type ENUM('standard', 'deluxe', 'suite', 'presidential') NOT NULL,
    capacity INT NOT NULL,
    price_per_night DECIMAL(10,2) NOT NULL,
    description TEXT,
    amenities JSON,
    image_url VARCHAR(255),
    is_available BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Bookings table
CREATE TABLE bookings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    room_id INT NOT NULL,
    check_in_date DATE NOT NULL,
    check_out_date DATE NOT NULL,
    total_price DECIMAL(10,2) NOT NULL,
    status ENUM('confirmed', 'cancelled', 'completed') DEFAULT 'confirmed',
    special_requests TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (room_id) REFERENCES rooms(id) ON DELETE CASCADE
);

-- Insert sample admin user (password: admin123)
INSERT INTO users (username, password, email, first_name, last_name, is_admin) 
VALUES ('admin', 'admin123', 'admin@gmail.com', 'Admin', 'User', TRUE);

-- Insert sample rooms
INSERT INTO rooms (room_number, room_type, capacity, price_per_night, description, amenities, image_url) VALUES
('101', 'standard', 2, 2500.00, 'Comfortable standard room with modern amenities and city view.', '["wifi", "breakfast", "cityview"]', 'image2.png'),
('102', 'standard', 2, 2500.00, 'Comfortable standard room with modern amenities and garden view.', '["wifi", "breakfast", "parking"]', 'image2.png'),
('205', 'deluxe', 4, 6000.00, 'Spacious deluxe room with premium finishes and exceptional comfort.', '["wifi", "parking", "breakfast", "cityview"]', 'image3.png'),
('206', 'deluxe', 4, 6000.00, 'Elegant deluxe room with marble bathroom and luxury amenities.', '["wifi", "parking", "breakfast", "spa"]', 'image3.png'),
('312', 'suite', 6, 15000.00, 'Luxurious suite with separate living area and premium amenities.', '["wifi", "parking", "breakfast", "cityview", "spa"]', 'image4.png
'),
('401', 'presidential', 8, 25000.00, 'Ultimate luxury with panoramic views and personal butler service.', '["wifi", "parking", "breakfast", "cityview", "spa", "butler"]', 'image4.png');
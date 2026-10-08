-- =====================================================
-- Hotel Room Booking System - Database Schema
-- BCA 8th Semester Project (Project III)
-- Tribhuvan University
-- =====================================================

CREATE DATABASE IF NOT EXISTS hotel_booking_system;
USE hotel_booking_system;

-- -----------------------------------------------------
-- Table: users  (customers + admin)
-- -----------------------------------------------------
CREATE TABLE users (
    user_id INT AUTO_INCREMENT PRIMARY KEY,
    full_name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    phone VARCHAR(20) NOT NULL,
    password VARCHAR(255) NOT NULL,
    role ENUM('customer', 'admin') NOT NULL DEFAULT 'customer',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- -----------------------------------------------------
-- Table: room_types  (e.g. Single, Double, Deluxe, Suite)
-- -----------------------------------------------------
CREATE TABLE room_types (
    room_type_id INT AUTO_INCREMENT PRIMARY KEY,
    type_name VARCHAR(50) NOT NULL,
    description TEXT,
    price_per_night DECIMAL(10,2) NOT NULL,
    capacity INT NOT NULL DEFAULT 2
) ENGINE=InnoDB;

-- -----------------------------------------------------
-- Table: rooms  (individual physical rooms)
-- -----------------------------------------------------
CREATE TABLE rooms (
    room_id INT AUTO_INCREMENT PRIMARY KEY,
    room_number VARCHAR(10) NOT NULL UNIQUE,
    room_type_id INT NOT NULL,
    floor_number INT DEFAULT 1,
    status ENUM('available', 'maintenance') NOT NULL DEFAULT 'available',
    image_path VARCHAR(255) DEFAULT NULL,
    FOREIGN KEY (room_type_id) REFERENCES room_types(room_type_id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- -----------------------------------------------------
-- Table: bookings
-- -----------------------------------------------------
CREATE TABLE bookings (
    booking_id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    room_id INT NOT NULL,
    check_in DATE NOT NULL,
    check_out DATE NOT NULL,
    num_guests INT NOT NULL DEFAULT 1,
    total_price DECIMAL(10,2) NOT NULL,
    status ENUM('pending', 'confirmed', 'cancelled', 'completed') NOT NULL DEFAULT 'pending',
    payment_status ENUM('unpaid', 'paid') NOT NULL DEFAULT 'unpaid',
    special_request TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE,
    FOREIGN KEY (room_id) REFERENCES rooms(room_id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- -----------------------------------------------------
-- Seed data
-- -----------------------------------------------------

-- Default admin account (password: admin123)
INSERT INTO users (full_name, email, phone, password, role) VALUES
('System Admin', 'admin@hotel.com', '9800000000', '$2y$10$gODtA9BR9dJbz3pPe7jBnui.STsCV6GOWhXU6AADkvmj/vEx0ZDxq', 'admin');
-- Login with: admin@hotel.com / admin123

-- Room types
INSERT INTO room_types (type_name, description, price_per_night, capacity) VALUES
('Single', 'Cozy room with a single bed, ideal for solo travelers.', 1500.00, 1),
('Double', 'Comfortable room with a double bed for two guests.', 2500.00, 2),
('Deluxe', 'Spacious room with premium furnishing and city view.', 4000.00, 3),
('Suite', 'Luxury suite with living area, minibar, and balcony.', 7000.00, 4);

-- Rooms
INSERT INTO rooms (room_number, room_type_id, floor_number, status) VALUES
('101', 1, 1, 'available'),
('102', 1, 1, 'available'),
('201', 2, 2, 'available'),
('202', 2, 2, 'available'),
('203', 2, 2, 'available'),
('301', 3, 3, 'available'),
('302', 3, 3, 'available'),
('401', 4, 4, 'available');

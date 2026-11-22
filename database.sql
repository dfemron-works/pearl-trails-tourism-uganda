CREATE DATABASE IF NOT EXISTS pearl_trails_db;
USE pearl_trails_db;

-- 1. Users Table (Supports 'admin' and 'tourist' roles)
CREATE TABLE users (
    user_id INT AUTO_INCREMENT PRIMARY KEY,
    full_name VARCHAR(120) NOT NULL,
    email VARCHAR(120) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    role ENUM('admin', 'tourist') DEFAULT 'tourist',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- 2. Tour Packages Table
CREATE TABLE tour_packages (
    package_id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(150) NOT NULL,
    destination VARCHAR(100) NOT NULL,
    duration_days INT NOT NULL,
    price_usd DECIMAL(10, 2) NOT NULL,
    max_capacity INT NOT NULL,
    description TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- 3. Bookings Table (Links Users and Tour Packages)
CREATE TABLE bookings (
    booking_id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    package_id INT NOT NULL,
    travel_date DATE NOT NULL,
    num_guests INT NOT NULL,
    total_cost DECIMAL(10, 2) NOT NULL,
    status ENUM('Pending', 'Confirmed', 'Cancelled') DEFAULT 'Pending',
    booked_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE,
    FOREIGN KEY (package_id) REFERENCES tour_packages(package_id) ON DELETE CASCADE
);

-- 4. Tour Reviews Table (4th related table for tourist feedback)
CREATE TABLE reviews (
    review_id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    package_id INT NOT NULL,
    rating TINYINT NOT NULL CHECK (rating BETWEEN 1 AND 5),
    comment TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE,
    FOREIGN KEY (package_id) REFERENCES tour_packages(package_id) ON DELETE CASCADE
);

-- Sample Accounts (Password for both is: password123)
INSERT INTO users (full_name, email, password_hash, role) VALUES
('System Admin', 'admin@pearltrails.ug', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin'),
('Sarah Namukasa', 'sarah@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'tourist');

-- Sample Tour Packages
INSERT INTO tour_packages (title, destination, duration_days, price_usd, max_capacity, description) VALUES
('Bwindi Gorilla Trekking Adventure', 'Bwindi Impenetrable Forest', 3, 850.00, 8, 'Guided mountain gorilla tracking experience with eco-lodge accommodation.'),
('Murchison Falls Wildlife & Nile Cruise', 'Murchison Falls National Park', 4, 620.00, 12, 'Big Five game drives and boat safari to the base of Murchison Falls.'),
('Jinja Source of the Nile White-Water Rafting', 'Jinja', 2, 240.00, 15, 'Full-day Grade 5 rafting, quad biking, and sunset boat cruise.');

-- Sample Booking & Review
INSERT INTO bookings (user_id, package_id, travel_date, num_guests, total_cost, status) VALUES
(2, 1, '2026-12-15', 2, 1700.00, 'Confirmed');

INSERT INTO reviews (user_id, package_id, rating, comment) VALUES
(2, 1, 5, 'Unforgettable experience seeing the mountain gorillas up close!');

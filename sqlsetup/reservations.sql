CREATE DATABASE IF NOT EXISTS cafe;
USE cafe;

-- Create locations table first
CREATE TABLE restaurant_locations (
    location_id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,        
    address TEXT NOT NULL,
    description TEXT NOT NULL,
    city VARCHAR(50) NOT NULL,
    phone VARCHAR(20),
    opening_time TIME,
    closing_time TIME,
    is_active BOOLEAN DEFAULT TRUE
);

-- Create tables with proper foreign key
CREATE TABLE restaurant_tables (
    location_id INT NOT NULL,
    table_id INT AUTO_INCREMENT PRIMARY KEY,
    table_number VARCHAR(10) NOT NULL,
    capacity INT NOT NULL,
    description VARCHAR(255),
    type ENUM('standard', 'round', 'booth', 'private', 'bar', 'outdoor') NOT NULL,
    is_active BOOLEAN DEFAULT TRUE,
    x_position INT,
    y_position INT,
    FOREIGN KEY (location_id) REFERENCES restaurant_locations(location_id)
);

-- Create reservations table
CREATE TABLE reservations (
    reservation_id INT AUTO_INCREMENT PRIMARY KEY,
    table_id VARCHAR(10) NOT NULL,
    customer_name VARCHAR(100) NOT NULL,
    phone VARCHAR(20) NOT NULL,
    email VARCHAR(100),
    reservation_date DATE NOT NULL,
    start_time TIME NOT NULL,
    end_time TIME NOT NULL,
    guests INT NOT NULL,
    special_requests TEXT,
    status ENUM('confirmed', 'pending', 'cancelled', 'completed', 'no-show') DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (table_id) REFERENCES restaurant_tables(table_id)
    confirmation_number VARCHAR(20)
);

-- Insert locations first (required for the foreign key)
INSERT INTO restaurant_locations 
(name, address, city, phone, opening_time, closing_time) 
VALUES
('Downtown Café', '123 Main St', 'New York', '+1 555-1234', '08:00:00', '22:00:00'),
('Mall Branch', '456 Shopping Ave', 'Chicago', '+1 555-5678', '10:00:00', '21:00:00'),
('Waterfront Café', '789 Harbor Rd', 'Seattle', '+1 555-9012', '07:00:00', '20:00:00');

-- Now insert tables with valid location_id references
INSERT INTO restaurant_tables 
(table_id, name, capacity, location_id, description, type) 
VALUES
('W1', 'Window Table W1', 2, 1, 'Intimate table with beautiful street view, perfect for couples.', 'standard'),
('W2', 'Window Table W2', 2, 1, 'Cozy window seat with natural lighting throughout the day.', 'standard'),
('W3', 'Window Table W3', 4, 1, 'Spacious window table ideal for small groups with scenic views.', 'standard'),
('C1', 'Center Table C1', 4, 2, 'Central location with easy access and lively atmosphere.', 'standard'),
('C2', 'Center Table C2', 6, 2, 'Large round table perfect for family gatherings and celebrations.', 'round'),
('C3', 'Center Table C3', 4, 2, 'Well-positioned table in the heart of the restaurant.', 'standard'),
('B1', 'Booth B1', 4, 3, 'Private booth seating with comfortable cushions and intimate setting.', 'booth'),
('B2', 'Booth B2', 4, 3, 'Cozy booth perfect for private conversations and romantic dinners.', 'booth'),
('B3', 'Booth B3', 4, 3, 'Comfortable booth with excellent service access.', 'booth'),
('P1', 'Private Table P1', 8, 1, 'Exclusive table for large groups with enhanced privacy and service.', 'private'),
('P2', 'Private Table P2', 8, 1, 'Premium table for special occasions and business meetings.', 'private');
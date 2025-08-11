
-- Restaurant locations
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

-- Restaurant tables
CREATE TABLE restaurant_tables (
    location_id INT NOT NULL,
    table_id VARCHAR(10) PRIMARY KEY,
    table_number VARCHAR(10) NOT NULL,
    capacity INT NOT NULL,
    description VARCHAR(255),
    type ENUM('standard', 'round', 'booth', 'private', 'bar', 'outdoor') NOT NULL,
    is_active BOOLEAN DEFAULT TRUE,
    x_position INT,
    y_position INT,
    FOREIGN KEY (location_id) REFERENCES restaurant_locations(location_id)
);

-- Orders
CREATE TABLE orders (
    order_id INT AUTO_INCREMENT PRIMARY KEY,
    customer_name VARCHAR(100) NOT NULL,
    phone VARCHAR(20) NOT NULL,
    email VARCHAR(100),
    order_type ENUM('dine_in', 'takeaway', 'delivery') NOT NULL,
    table_number INT,
    pickup_location_id INT,
    delivery_address TEXT,
    delivery_time VARCHAR(50),
    payment_method VARCHAR(50) NOT NULL,
    total_amount DECIMAL(10,2) NOT NULL,
    special_instructions TEXT,
    status ENUM('pending', 'preparing', 'ready', 'completed', 'cancelled') DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (pickup_location_id) REFERENCES restaurant_locations(location_id)
);

-- Order items
CREATE TABLE order_items (
    order_item_id INT AUTO_INCREMENT PRIMARY KEY,
    order_id INT NOT NULL,
    product_id INT NOT NULL,
    quantity INT NOT NULL,
    price DECIMAL(10,2) NOT NULL,
    special_instructions TEXT,
    FOREIGN KEY (order_id) REFERENCES orders(order_id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(product_id)
);

-- Insert sample restaurant locations
INSERT INTO restaurant_locations (name, address, description, city, phone, opening_time, closing_time, is_active)
VALUES 
('Main Street Bistro', '123 Main St', 'Our flagship location with a cozy atmosphere', 'Springfield', '555-0101', '08:00:00', '22:00:00', TRUE),
('Riverside Cafe', '456 River Rd', 'Beautiful views by the river', 'Springfield', '555-0202', '07:00:00', '21:00:00', TRUE),
('Downtown Express', '789 Center Ave', 'Quick service for busy professionals', 'Springfield', '555-0303', '06:30:00', '20:00:00', TRUE);

-- Insert sample restaurant tables
INSERT INTO restaurant_tables (location_id, table_number, capacity, description, type, is_active, x_position, y_position)
VALUES 
(1, 'T1', 4, 'Window table', 'standard', TRUE, 10, 15),
(1, 'T2', 6, 'Center table', 'round', TRUE, 20, 25),
(1, 'T3', 2, 'Corner booth', 'booth', TRUE, 30, 35),
(2, 'A1', 4, 'Patio table', 'outdoor', TRUE, 40, 45),
(2, 'A2', 8, 'Private dining', 'private', TRUE, 50, 55),
(3, 'B1', 2, 'Bar seating', 'bar', TRUE, 60, 65);

-- Insert sample orders
INSERT INTO orders (customer_name, phone, email, order_type, table_number, pickup_location_id, delivery_address, delivery_time, payment_method, total_amount, special_instructions, status)
VALUES 
('John Smith', '555-1001', 'john@example.com', 'dine_in', 1, NULL, NULL, NULL, 'credit_card', 45.50, 'No onions please', 'completed'),
('Sarah Johnson', '555-1002', 'sarah@example.com', 'takeaway', NULL, 1, NULL, 'ASAP', 'cash', 32.75, 'Extra napkins', 'ready'),
('Mike Brown', '555-1003', 'mike@example.com', 'delivery', NULL, NULL, '123 Oak St, Springfield', '18:30', 'credit_card', 68.90, 'Ring doorbell twice', 'preparing'),
('Emily Davis', '555-1004', NULL, 'dine_in', 3, NULL, NULL, NULL, 'debit_card', 28.25, 'Allergies: peanuts', 'pending');

-- Insert sample order items
INSERT INTO order_items (order_id, product_id, quantity, price, special_instructions)
VALUES 
(1, 101, 2, 12.50, 'Medium rare'),
(1, 102, 1, 8.00, 'No mayo'),
(1, 103, 1, 15.00, 'Extra cheese'),
(2, 104, 3, 10.25, 'Gluten-free bun'),
(2, 105, 1, 12.00, NULL),
(3, 106, 2, 18.50, 'Spicy'),
(3, 107, 1, 15.90, NULL),
(3, 108, 1, 16.50, 'Dressing on side'),
(4, 109, 1, 10.25, NULL),
(4, 110, 1, 18.00, 'Well done');
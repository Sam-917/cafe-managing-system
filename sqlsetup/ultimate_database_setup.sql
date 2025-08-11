CREATE DATABASE IF NOT EXISTS cafe;
USE cafe;

-- Users table for customers and admin 
CREATE TABLE users (
    user_id INT AUTO_INCREMENT PRIMARY KEY,
    first_name VARCHAR(50) NOT NULL,
    last_name VARCHAR(50) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    phone VARCHAR(20),
    username VARCHAR(50) UNIQUE NOT NULL,
    password_hash VARCHAR(255) NOT NULL,
    role ENUM('customer', 'admin') DEFAULT 'customer',
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    last_login DATETIME,
    INDEX idx_email (email),
    INDEX idx_username (username)
) ENGINE=InnoDB;
 
-- User sessions table
CREATE TABLE user_sessions (
    session_id VARCHAR(128) PRIMARY KEY,
    user_id INT NULL, 
    ip_address VARCHAR(45),
    user_agent TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    expires_at DATETIME NOT NULL,
    is_active BOOLEAN DEFAULT TRUE,
    FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE, 
    INDEX idx_user_sessions (user_id)
) ENGINE=InnoDB;


CREATE TABLE categories (
    category_id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(50) NOT NULL,
    description TEXT,
    display_order INT,
    is_active BOOLEAN DEFAULT TRUE
);

CREATE TABLE products (
    product_id INT AUTO_INCREMENT PRIMARY KEY,
    category_id INT,
    name VARCHAR(100) NOT NULL,
    description TEXT,
    base_price DECIMAL(10,2) NOT NULL,
    calories INT,
    image_url VARCHAR(255),
    is_featured BOOLEAN DEFAULT FALSE,
    is_active BOOLEAN DEFAULT TRUE,
    display_order INT,
    FOREIGN KEY (category_id) REFERENCES categories(category_id)
);

CREATE TABLE product_options (
    option_id INT AUTO_INCREMENT PRIMARY KEY,
    product_id INT,
    option_type ENUM('size', 'milk', 'syrup', 'topping', 'temperature') NOT NULL,
    name VARCHAR(50) NOT NULL,
    additional_price DECIMAL(10,2) DEFAULT 0.00,
    calories_addition INT,
    FOREIGN KEY (product_id) REFERENCES products(product_id)
);

CREATE TABLE nutritional_info (
    nutrition_id INT AUTO_INCREMENT PRIMARY KEY,
    product_id INT,
    serving_size VARCHAR(50),
    caffeine_mg INT,
    sugar_g INT,
    fat_g INT,
    protein_g INT,
    allergens VARCHAR(255),
    FOREIGN KEY (product_id) REFERENCES products(product_id)
);

CREATE TABLE seasonal_items (
    special_id INT AUTO_INCREMENT PRIMARY KEY,
    product_id INT,
    start_date DATE,
    end_date DATE,
    is_limited_edition BOOLEAN,
    FOREIGN KEY (product_id) REFERENCES products(product_id)
);

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

INSERT INTO categories (name, description, display_order) VALUES
('Coffee', 'Our handcrafted coffee selections', 1),
('Tea', 'Premium loose-leaf teas', 2),
('Breakfast', 'Morning sandwiches & pastries', 3),
('Lunch', 'Paninis and salads', 4),
('Bakery', 'Freshly baked goods', 5),
('Seasonal', 'Limited-time offerings', 6);

INSERT INTO products (category_id, name, description, base_price, calories, image_url, is_featured) VALUES
-- Coffee
(1, 'Espresso', 'Rich single-origin espresso shot', 2.50, 5, 'espresso.jpg', TRUE),
(1, 'Caffè Latte', 'Espresso with steamed milk', 4.25, 190, 'latte.jpg', TRUE),
(1, 'Caramel Macchiato', 'Vanilla syrup with espresso and caramel drizzle', 5.25, 250, 'macchiato.jpg', FALSE),

-- Tea
(2, 'Matcha Latte', 'Ceremonial-grade matcha with milk', 4.75, 180, 'matcha.jpg', TRUE),
(2, 'Chai Latte', 'Spiced black tea with steamed milk', 4.50, 240, 'chai.jpg', FALSE),

-- Breakfast
(3, 'Avocado Toast', 'Sourdough with avocado, chili flakes', 7.50, 320, 'avocado_toast.jpg', TRUE),
(3, 'Breakfast Sandwich', 'Egg, cheddar & bacon on croissant', 6.75, 450, 'breakfast_sandwich.jpg', FALSE),

-- Lunch
(4, 'Caprese Panini', 'Mozzarella, tomato & pesto', 8.25, 520, 'caprese.jpg', TRUE),
(4, 'Chicken Caesar Wrap', 'Grilled chicken with romaine', 8.75, 580, 'caesar_wrap.jpg', FALSE),

-- Bakery
(5, 'Croissant', 'Buttery French-style', 3.25, 310, 'croissant.jpg', TRUE),
(5, 'Blueberry Muffin', 'Fresh blueberries in muffin', 3.75, 420, 'blueberry_muffin.jpg', FALSE),

-- Seasonal Products (added here first)
(6, 'Pumpkin Spice Latte', 'Signature fall drink with spices', 5.95, 380, 'pumpkin_spice.jpg', TRUE),
(6, 'Peppermint Mocha', 'Holiday chocolate-mint espresso', 5.75, 420, 'peppermint_mocha.jpg', TRUE),
(6, 'Iced Lavender Latte', 'Floral summer cold brew', 5.25, 210, 'lavender_latte.jpg', FALSE),
(6, 'Watermelon Mint Tea', 'Refreshing summer cooler', 4.95, 90, 'watermelon_tea.jpg', FALSE),
(6, 'Gingerbread Cookie', 'Festive molasses spice cookie', 3.50, 280, 'gingerbread.jpg', TRUE);

-- Now insert product options
INSERT INTO product_options (product_id, option_type, name, additional_price, calories_addition) VALUES
-- Espresso sizes
(1, 'size', 'Single', 0.00, 5),
(1, 'size', 'Double', 1.00, 10),

-- Latte options
(2, 'size', '12oz', 0.00, 190),
(2, 'size', '16oz', 0.75, 240),
(2, 'milk', 'Almond', 0.50, 30),
(2, 'milk', 'Oat', 0.50, 50),
(2, 'syrup', 'Vanilla', 0.75, 80),

-- Matcha latte customizations
(4, 'size', '12oz', 0.00, 180),
(4, 'size', '16oz', 0.75, 220),
(4, 'milk', 'Coconut', 0.75, 40),

-- Sandwich modifications
(7, 'topping', 'Extra Bacon', 1.50, 120),
(7, 'topping', 'Avocado', 1.25, 90);

-- Insert nutritional info
INSERT INTO nutritional_info (product_id, serving_size, caffeine_mg, sugar_g, fat_g, protein_g, allergens) VALUES
-- Coffee
(1, '1 shot', 75, 0, 0, 0, 'None'),
(2, '12oz', 150, 12, 8, 10, 'Dairy'),
(3, '12oz', 175, 28, 6, 8, 'Dairy'),

-- Tea
(4, '12oz', 80, 10, 5, 6, 'Dairy'),
(5, '12oz', 50, 22, 5, 7, 'Dairy, Tree Nuts'),

-- Food
(6, '1 serving', 2, 5, 22, 8, 'Gluten'),
(7, '1 sandwich', 45, 3, 28, 22, 'Gluten, Dairy, Egg'),
(8, '1 panini', 5, 4, 18, 26, 'Gluten, Dairy'),
(10, '1 muffin', 5, 32, 16, 5, 'Gluten, Dairy, Egg'),

-- Seasonal items
(11, '12oz', 150, 32, 7, 6, 'Dairy'),
(12, '12oz', 125, 38, 8, 7, 'Dairy'),
(13, '16oz', 120, 18, 4, 3, 'Dairy'),
(15, '1 cookie', 0, 22, 8, 2, 'Gluten, Dairy, Molasses');

-- Finally, insert seasonal items (now the products exist)
INSERT INTO seasonal_items (product_id, start_date, end_date, is_limited_edition) VALUES
-- Winter specials
(11, '2023-11-01', '2024-01-31', TRUE),
(12, '2023-11-01', '2024-01-31', TRUE),

-- Summer drinks
(13, '2024-05-15', '2024-08-31', FALSE),
(14, '2024-05-15', '2024-08-31', FALSE),

-- Holiday special
(15, '2023-12-01', '2023-12-31', TRUE);



-- Insert sample restaurant locations
INSERT INTO restaurant_locations (name, address, description, city, phone, opening_time, closing_time, is_active)
VALUES 
('Main Street Bistro', '123 Main St', 'Our flagship location with a cozy atmosphere', 'Springfield', '555-0101', '08:00:00', '22:00:00', TRUE),
('Riverside Cafe', '456 River Rd', 'Beautiful views by the river', 'Springfield', '555-0202', '07:00:00', '21:00:00', TRUE),
('Downtown Express', '789 Center Ave', 'Quick service for busy professionals', 'Springfield', '555-0303', '06:30:00', '20:00:00', TRUE);

 

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

-- Insert locations first (required for the foreign key)
INSERT INTO restaurant_locations 
(name, address, city, phone, opening_time, closing_time) 
VALUES
('Downtown Café', '123 Main St', 'New York', '+1 555-1234', '08:00:00', '22:00:00'),
('Mall Branch', '456 Shopping Ave', 'Chicago', '+1 555-5678', '10:00:00', '21:00:00'),
('Waterfront Café', '789 Harbor Rd', 'Seattle', '+1 555-9012', '07:00:00', '20:00:00');

 
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
    table_id VARCHAR(10) AUTO_INCREMENT PRIMARY KEY,
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
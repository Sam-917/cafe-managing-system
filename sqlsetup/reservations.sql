 
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
    confirmation_number VARCHAR(20),
    FOREIGN KEY (table_id) REFERENCES restaurant_tables(table_id)
);
 

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

INSERT INTO restaurant_tables 
(location_id, table_id, table_number, capacity, description, type, x_position, y_position) 
VALUES
-- Window tables (left side)
(1, 'W1', 'W1', 2, 'Intimate table with beautiful street view, perfect for couples.', 'standard', 50, 100),
(1, 'W2', 'W2', 2, 'Cozy window seat with natural lighting throughout the day.', 'standard', 50, 180),
(1, 'W3', 'W3', 4, 'Spacious window table ideal for small groups with scenic views.', 'standard', 50, 260),

-- Private tables (back right)
(1, 'P1', 'P1', 8, 'Exclusive table for large groups with enhanced privacy and service.', 'private', 350, 100),
(1, 'P2', 'P2', 8, 'Premium table for special occasions and business meetings.', 'private', 350, 220),

-- Mall Branch (location_id = 2) tables)
(2, 'C1', 'C1', 4, 'Central location with easy access and lively atmosphere.', 'standard', 200, 150),
(2, 'C2', 'C2', 6, 'Large round table perfect for family gatherings and celebrations.', 'round', 200, 250),
(2, 'C3', 'C3', 4, 'Well-positioned table in the heart of the restaurant.', 'standard', 300, 200),

-- Waterfront Café (location_id = 3) booths)
(3, 'B1', 'B1', 4, 'Private booth seating with comfortable cushions and intimate setting.', 'booth', 100, 150),
(3, 'B2', 'B2', 4, 'Cozy booth perfect for private conversations and romantic dinners.', 'booth', 100, 250),
(3, 'B3', 'B3', 4, 'Comfortable booth with excellent service access.', 'booth', 250, 200);
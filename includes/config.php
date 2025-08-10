<?php
// Database Configuration
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'cafe');

// Site Configuration
define('SITE_NAME', 'Restaurant Reservation System');
define('BASE_URL', 'http://localhost/cafe');

// Timezone Configuration
date_default_timezone_set('America/New_York');

// Error Reporting (disable in production)
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Database Connection
try {
    $conn = new PDO("mysql:host=".DB_HOST.";dbname=".DB_NAME, DB_USER, DB_PASS);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $conn->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
} catch(PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}

// Helper Functions
function getAvailableTimeSlots($date, $partySize) {
    global $conn;
    
    // This is a simplified version - you'll need to adjust based on your business logic
    $times = [];
    
    // Example: Generate time slots from opening to closing time
    $openingTime = strtotime('11:00');
    $closingTime = strtotime('21:00');
    
    for ($time = $openingTime; $time <= $closingTime; $time += 1800) { // 30-minute intervals
        $times[] = date('H:i', $time);
    }
    
    return $times;
}

function getTableAvailability($date, $time) {
    global $conn;
    
    try {
        // Calculate end time (1.5 hours after start time)
        $endTime = date('H:i:s', strtotime($time) + 5400); // 5400 seconds = 1.5 hours
        
        $stmt = $conn->prepare("
            SELECT table_id FROM reservations 
            WHERE reservation_date = :date 
            AND (
                (start_time < :end_time AND end_time > :time)
                AND status IN ('confirmed', 'pending')
            )
        ");
        
        $stmt->execute([
            ':date' => $date,
            ':time' => $time,
            ':end_time' => $endTime
        ]);
        
        return $stmt->fetchAll(PDO::FETCH_COLUMN, 0);
        
    } catch (PDOException $e) {
        error_log("Database error in getTableAvailability: " . $e->getMessage());
        return []; // Return empty array if there's an error
    }
}

// Sanitize Input
function sanitize($data) {
    return htmlspecialchars(strip_tags(trim($data)));
}

// Generate random confirmation number
function generateConfirmationNumber() {
    return 'RES-' . strtoupper(substr(md5(uniqid(mt_rand(), true)), 0, 8));
}
?>
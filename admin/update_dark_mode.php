<?php
session_start();
require '../includes/config.php';

// Check if user is admin
if (!isset($_SESSION['logged_in'])) {
    header("HTTP/1.1 401 Unauthorized");
    exit();
}

if ($_SESSION['role'] !== 'admin') {
    header("HTTP/1.1 403 Forbidden");
    exit();
}

// Get JSON input
$data = json_decode(file_get_contents('php://input'), true);

if (isset($data['dark_mode'])) {
    try {
        $dark_mode = $data['dark_mode'] ? 1 : 0;
        
        // Update dark mode setting in database
        $stmt = $conn->prepare("UPDATE site_settings SET dark_mode = ? WHERE id = 1");
        $stmt->execute([$dark_mode]);
        
        echo json_encode(['success' => true]);
    } catch (PDOException $e) {
        header("HTTP/1.1 500 Internal Server Error");
        echo json_encode(['error' => $e->getMessage()]);
    }
} else {
    header("HTTP/1.1 400 Bad Request");
    echo json_encode(['error' => 'Missing dark_mode parameter']);
}
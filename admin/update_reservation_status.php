<?php
session_start();
require '../includes/config.php';

// Check if user is admin
if (!isset($_SESSION['logged_in']) || $_SESSION['role'] !== 'admin') {
    header('HTTP/1.1 403 Forbidden');
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit();
}

// Get JSON input
$input = json_decode(file_get_contents('php://input'), true);

// Validate input
if (!isset($input['reservation_id']) || !isset($input['status'])) {
    header('HTTP/1.1 400 Bad Request');
    echo json_encode(['success' => false, 'message' => 'Invalid input']);
    exit();
}

$reservation_id = (int)$input['reservation_id'];
$status = $input['status'];

// Validate status
$valid_statuses = ['confirmed', 'pending', 'cancelled', 'completed', 'no-show'];
if (!in_array($status, $valid_statuses)) {
    header('HTTP/1.1 400 Bad Request');
    echo json_encode(['success' => false, 'message' => 'Invalid status']);
    exit();
}

try {
    // Update the reservation status
    $stmt = $conn->prepare("UPDATE reservations SET status = :status WHERE reservation_id = :reservation_id");
    $stmt->bindParam(':status', $status);
    $stmt->bindParam(':reservation_id', $reservation_id);
    $stmt->execute();
    
    echo json_encode(['success' => true]);
} catch (PDOException $e) {
    header('HTTP/1.1 500 Internal Server Error');
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
}
<?php
session_start();
require '../includes/config.php';

// Check if user is admin
if (!isset($_SESSION['logged_in']) || $_SESSION['role'] !== 'admin') {
    header('HTTP/1.1 403 Forbidden');
    echo json_encode(['success' => false, 'message' => 'Access denied']);
    exit();
}

// Get JSON input
$json = file_get_contents('php://input');
$data = json_decode($json, true);

// Validate input
if (!isset($data['product_id']) || !is_numeric($data['product_id']) || !isset($data['new_status'])) {
    header('HTTP/1.1 400 Bad Request');
    echo json_encode(['success' => false, 'message' => 'Invalid input']);
    exit();
}

$product_id = (int)$data['product_id'];
$new_status = (bool)$data['new_status'];

try {
    // Update product status
    $stmt = $conn->prepare("UPDATE products SET is_active = :status WHERE product_id = :product_id");
    $stmt->bindParam(':status', $new_status, PDO::PARAM_BOOL);
    $stmt->bindParam(':product_id', $product_id);
    $stmt->execute();

    echo json_encode(['success' => true]);
    
} catch (PDOException $e) {
    header('HTTP/1.1 500 Internal Server Error');
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
}
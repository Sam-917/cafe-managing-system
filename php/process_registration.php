<?php
session_start();
require_once '../includes/config.php';

// Set JSON content type
header('Content-Type: application/json');

try {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new Exception('Invalid request method');
    }

    // Get all form data
    $firstName = trim($_POST['first_name'] ?? '');
    $lastName = trim($_POST['last_name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirmPassword = $_POST['confirmPassword'] ?? '';

    // Validate inputs
    if (empty($firstName)) throw new Exception('First name is required');
    if (empty($lastName)) throw new Exception('Last name is required');
    if (empty($email)) throw new Exception('Email is required');
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) throw new Exception('Invalid email format');
    if (empty($username)) throw new Exception('Username is required');
    if (strlen($username) < 4) throw new Exception('Username must be at least 4 characters');
    if (!preg_match('/^[a-zA-Z0-9_]+$/', $username)) throw new Exception('Username can only contain letters, numbers, and underscores');
    if (empty($password)) throw new Exception('Password is required');
    if (strlen($password) < 8) throw new Exception('Password must be at least 8 characters');
    if ($password !== $confirmPassword) throw new Exception('Passwords do not match');

    // Check if email or username already exists
    $checkStmt = $conn->prepare("SELECT user_id FROM users WHERE email = ? OR username = ?");
    $checkStmt->execute([$email, $username]);
    if ($checkStmt->fetch()) {
        throw new Exception('Email or username already registered');
    }

    // Hash the password
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    // Insert new user with default 'customer' role
    $stmt = $conn->prepare("INSERT INTO users 
        (first_name, last_name, email, phone, username, password_hash, role) 
        VALUES (?, ?, ?, ?, ?, ?, 'customer')");
    
    $stmt->execute([
        $firstName,
        $lastName,
        $email,
        $phone,
        $username,
        $hashedPassword
    ]);

    // Return success response
    echo json_encode([
        'success' => true,
        'message' => 'Registration successful!',
        'redirect' => '../login.php?registration=success'
    ]);

} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
?>
<?php
session_start();
require_once '../includes/config.php';

// Clear any previous output
ob_clean();

// Set proper headers before any output
header('Content-Type: application/json');

try {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new Exception('Invalid request method');
    }

    $inputUsername = trim($_POST['username'] ?? '');
    $inputPassword = $_POST['password'] ?? '';

    if (empty($inputUsername) || empty($inputPassword)) {
        throw new Exception('Username and password are required');
    }

    // Check users table (now includes all roles)
    $userStmt = $conn->prepare("SELECT * FROM users WHERE email = ? OR username = ?");
    $userStmt->execute([$inputUsername, $inputUsername]);
    $user = $userStmt->fetch(PDO::FETCH_ASSOC);

    if ($user && password_verify($inputPassword, $user['password_hash'])) {
        // Login successful - set session variables
        $_SESSION['user_id'] = $user['user_id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['email'] = $user['email'];
        $_SESSION['first_name'] = $user['first_name'];
        $_SESSION['last_name'] = $user['last_name'];
        $_SESSION['role'] = $user['role'];
        $_SESSION['logged_in'] = true;
        $_SESSION['login_time'] = time();
        
        // Update last login
        $updateStmt = $conn->prepare("UPDATE users SET last_login = NOW() WHERE user_id = ?");
        $updateStmt->execute([$user['user_id']]);
        
        // Return response with role information
        echo json_encode([
            'success' => true, 
            'role' => $user['role'],
            'message' => 'Login successful'
        ]);
        exit();
    }

    throw new Exception('Invalid username or password');

} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
?>
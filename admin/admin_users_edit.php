<?php
session_start();
require '../includes/config.php';

// Check if user is admin
if (!isset($_SESSION['logged_in']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit();
}

// Get user ID from URL
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: admin_users.php");
    exit();
}

$user_id = (int)$_GET['id'];

// Fetch user details
try {
    $user_stmt = $conn->prepare("SELECT * FROM users WHERE user_id = :user_id");
    $user_stmt->bindParam(':user_id', $user_id);
    $user_stmt->execute();
    $user = $user_stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user) {
        header("Location: admin_users.php");
        exit();
    }

} catch (PDOException $e) {
    $error = "Failed to fetch user details: " . $e->getMessage();
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_user'])) {
    // Validate and sanitize input
    $first_name = trim($_POST['first_name']);
    $last_name = trim($_POST['last_name']);
    $email = trim($_POST['email']);
    $phone = trim($_POST['phone']);
    $username = trim($_POST['username']);
    $role = $_POST['role'];
    $is_active = isset($_POST['is_active']) ? 1 : 0;

    // Check if email is already taken by another user
    try {
        $email_check = $conn->prepare("SELECT user_id FROM users WHERE email = :email AND user_id != :user_id");
        $email_check->bindParam(':email', $email);
        $email_check->bindParam(':user_id', $user_id);
        $email_check->execute();
        
        if ($email_check->rowCount() > 0) {
            $error = "Email address is already in use by another account.";
        }
    } catch (PDOException $e) {
        $error = "Error checking email: " . $e->getMessage();
    }

    // Check if username is already taken by another user
    try {
        $username_check = $conn->prepare("SELECT user_id FROM users WHERE username = :username AND user_id != :user_id");
        $username_check->bindParam(':username', $username);
        $username_check->bindParam(':user_id', $user_id);
        $username_check->execute();
        
        if ($username_check->rowCount() > 0) {
            $error = "Username is already in use by another account.";
        }
    } catch (PDOException $e) {
        $error = "Error checking username: " . $e->getMessage();
    }

    // Only proceed if no errors
    if (!isset($error)) {
        try {
            // Update user
            $update_stmt = $conn->prepare("
                UPDATE users 
                SET 
                    first_name = :first_name,
                    last_name = :last_name,
                    email = :email,
                    phone = :phone,
                    username = :username,
                    role = :role,
                    is_active = :is_active
                WHERE user_id = :user_id
            ");
            
            $update_stmt->bindParam(':first_name', $first_name);
            $update_stmt->bindParam(':last_name', $last_name);
            $update_stmt->bindParam(':email', $email);
            $update_stmt->bindParam(':phone', $phone);
            $update_stmt->bindParam(':username', $username);
            $update_stmt->bindParam(':role', $role);
            $update_stmt->bindParam(':is_active', $is_active);
            $update_stmt->bindParam(':user_id', $user_id);
            $update_stmt->execute();

            $success = "User updated successfully!";
            
            // Refresh user data
            $user_stmt->execute();
            $user = $user_stmt->fetch(PDO::FETCH_ASSOC);
            
        } catch (PDOException $e) {
            $error = "Failed to update user: " . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit User | Cafe & Netic</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="font-inter bg-gray-50">
    <!-- Admin Layout -->
    <div class="flex h-screen overflow-hidden"> 
        <?php include '../includes/admin_sidebar.php'; ?>
        
        <!-- Main Content -->
        <div class="flex-1 overflow-auto">
            <?php 
                $page_title = "Edit User";
                include '../includes/admin_topnav.php'; 
            ?>
            
            <!-- User Edit Content -->
            <main class="p-6">
                <div class="flex justify-between items-center mb-6">
                    <div>
                        <h1 class="text-2xl font-bold text-gray-800">Edit User</h1>
                        <nav class="flex" aria-label="Breadcrumb">
                            <ol class="inline-flex items-center space-x-1 md:space-x-2">
                                <li class="inline-flex items-center">
                                    <a href="admin_users.php" class="inline-flex items-center text-sm font-medium text-gray-700 hover:text-amber-600">
                                        Users
                                    </a>
                                </li>
                                <li aria-current="page">
                                    <div class="flex items-center">
                                        <svg class="w-6 h-6 text-gray-400" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path></svg>
                                        <span class="ml-1 text-sm font-medium text-gray-500 md:ml-2"><?= htmlspecialchars($user['first_name'] . ' ' . htmlspecialchars($user['last_name'])) ?></span>
                                    </div>
                                </li>
                            </ol>
                        </nav>
                    </div>
                    <a href="admin_users.php" class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md shadow-sm text-white bg-amber-600 hover:bg-amber-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-amber-500">
                        Back to Users
                    </a>
                </div>

                <?php if (isset($error)): ?>
                    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-6">
                        <?= htmlspecialchars($error) ?>
                    </div>
                <?php endif; ?>

                <?php if (isset($success)): ?>
                    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-6">
                        <?= htmlspecialchars($success) ?>
                    </div>
                <?php endif; ?>

                <div class="bg-white shadow rounded-lg overflow-hidden">
                    <form method="POST">
                        <div class="px-6 py-4 border-b border-gray-200">
                            <h2 class="text-lg font-medium text-gray-900">User Information</h2>
                        </div>
                        <div class="px-6 py-4 grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Basic Info -->
                            <div class="space-y-4">
                                <div>
                                    <label for="first_name" class="block text-sm font-medium text-gray-700">First Name *</label>
                                    <input type="text" id="first_name" name="first_name" required 
                                           value="<?= htmlspecialchars($user['first_name']) ?>" 
                                           class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-amber-500 focus:border-amber-500 sm:text-sm">
                                </div>

                                <div>
                                    <label for="last_name" class="block text-sm font-medium text-gray-700">Last Name *</label>
                                    <input type="text" id="last_name" name="last_name" required 
                                           value="<?= htmlspecialchars($user['last_name']) ?>" 
                                           class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-amber-500 focus:border-amber-500 sm:text-sm">
                                </div>

                                <div>
                                    <label for="email" class="block text-sm font-medium text-gray-700">Email *</label>
                                    <input type="email" id="email" name="email" required 
                                           value="<?= htmlspecialchars($user['email']) ?>" 
                                           class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-amber-500 focus:border-amber-500 sm:text-sm">
                                </div>

                                <div>
                                    <label for="phone" class="block text-sm font-medium text-gray-700">Phone</label>
                                    <input type="tel" id="phone" name="phone" 
                                           value="<?= htmlspecialchars($user['phone'] ?? '') ?>" 
                                           class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-amber-500 focus:border-amber-500 sm:text-sm">
                                </div>
                            </div>

                            <!-- Account Info -->
                            <div class="space-y-4">
                                <div>
                                    <label for="username" class="block text-sm font-medium text-gray-700">Username *</label>
                                    <input type="text" id="username" name="username" required 
                                           value="<?= htmlspecialchars($user['username']) ?>" 
                                           class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-amber-500 focus:border-amber-500 sm:text-sm">
                                </div>

                                <div>
                                    <label for="role" class="block text-sm font-medium text-gray-700">Role *</label>
                                    <select id="role" name="role" required 
                                            class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-amber-500 focus:border-amber-500 sm:text-sm">
                                        <option value="customer" <?= $user['role'] === 'customer' ? 'selected' : '' ?>>Customer</option>
                                        <option value="admin" <?= $user['role'] === 'admin' ? 'selected' : '' ?>>Admin</option>
                                    </select>
                                </div>

                                <div class="flex items-center">
                                    <input id="is_active" name="is_active" type="checkbox" 
                                           <?= $user['is_active'] ? 'checked' : '' ?> 
                                           class="h-4 w-4 text-amber-600 focus:ring-amber-500 border-gray-300 rounded">
                                    <label for="is_active" class="ml-2 block text-sm text-gray-700">Account Active</label>
                                </div>

                                <div class="pt-4">
                                    <h3 class="text-sm font-medium text-gray-700">Account Created</h3>
                                    <p class="mt-1 text-sm text-gray-500"><?= date('M j, Y g:i A', strtotime($user['created_at'])) ?></p>
                                </div>

                                <?php if ($user['last_login']): ?>
                                    <div>
                                        <h3 class="text-sm font-medium text-gray-700">Last Login</h3>
                                        <p class="mt-1 text-sm text-gray-500"><?= date('M j, Y g:i A', strtotime($user['last_login'])) ?></p>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>

                        <!-- Form Actions -->
                        <div class="px-6 py-4 border-t border-gray-200 bg-gray-50 text-right">
                            <button type="submit" name="update_user" class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-amber-600 hover:bg-amber-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-amber-500">
                                Save Changes
                            </button>
                        </div>
                    </form>
                </div>
            </main>
        </div>
    </div>
</body>
</html>
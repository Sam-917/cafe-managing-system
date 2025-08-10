<?php
session_start();

// Redirect if not logged in
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header("Location: login.php");
    exit();
}

// Fetch complete user data from database
require_once 'includes/config.php';

try {
    $stmt = $conn->prepare("SELECT * FROM users WHERE user_id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user) {
        throw new Exception("User not found");
    }

    // Update session with complete user data
    $_SESSION = array_merge($_SESSION, $user);

} catch (PDOException $e) {
    die("Database error: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Profile | Cafe & Netic</title>
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- Custom CSS -->
    <link href="assets/css/profile.css" rel="stylesheet">

 
</head>
<body class="font-inter bg-gradient-to-br from-amber-50 to-orange-100 min-h-screen">
    <!-- Navigation -->
    <nav class="bg-white/90 backdrop-blur-md shadow-lg sticky top-0 z-50 transition-all duration-300">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-16">
                <!-- Logo -->
                <div class="flex items-center space-x-2">
                    <div class="coffee-steam text-2xl">☕</div>
                    <a href="index.php" class="font-playfair text-2xl font-bold text-amber-800">Cafe & Netic</a>
                </div>
                
                <!-- Navigation Links -->
                <div class="hidden md:flex space-x-8 items-center">
                    <a href="about_us.php" class="nav-item text-gray-700 hover:text-amber-700 font-medium px-3 py-2">About Us</a>
                    <a href="menu.php" class="nav-item text-gray-700 hover:text-amber-700 font-medium px-3 py-2">Menu</a>
                    <a href="reservation.php" class="nav-item text-gray-700 hover:text-amber-700 font-medium px-3 py-2">Reservation</a>
                    
                    <!-- User Profile Dropdown -->
                    <div class="relative group">
                        <button class="flex items-center space-x-2 focus:outline-none">
                            <span class="font-medium text-gray-700"><?= htmlspecialchars($_SESSION['first_name'] ?? 'User') ?></span>
                            <div class="w-8 h-8 rounded-full bg-amber-600 flex items-center justify-center text-white font-semibold">
                                <?= strtoupper(substr($_SESSION['first_name'] ?? 'U', 0, 1)) ?>
                            </div>
                            <svg class="w-4 h-4 text-gray-600 transition-transform group-hover:rotate-180" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                            </svg>
                        </button>
                        
                        <!-- Dropdown Menu -->
                        <div class="absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg py-1 z-50 hidden group-hover:block">
                            <a href="profile.php" class="block px-4 py-2 text-sm text-gray-700 hover:bg-amber-50">Profile</a>
                            <a href="logout.php" class="block px-4 py-2 text-sm text-gray-700 hover:bg-amber-50">Sign Out</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <div class="max-w-5xl mx-auto py-12 px-4 sm:px-6 lg:px-8">
        <!-- Success Message -->
        <?php if (isset($_SESSION['success_message'])): ?>
            <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-8 rounded" role="alert">
                <p><?= htmlspecialchars($_SESSION['success_message']) ?></p>
                <?php unset($_SESSION['success_message']); ?>
            </div>
        <?php endif; ?>
        
        <div class="bg-white rounded-xl shadow-xl overflow-hidden">
            <!-- Profile Header -->
            <div class="bg-gradient-to-r from-amber-600 to-orange-600 p-6 text-white">
                <div class="flex flex-col md:flex-row md:items-center md:justify-between">
                    <div>
                        <h1 class="font-playfair text-3xl font-bold">My Profile</h1>
                        <p class="text-amber-100">Manage your account information</p>
                    </div>
                    <div class="mt-4 md:mt-0">
                        <a href="php/edit_profile.php" class="inline-flex items-center px-4 py-2 bg-white/20 rounded-lg hover:bg-white/30 transition-all">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                            </svg>
                            Edit Profile
                        </a>
                    </div>
                </div>
            </div>
            
            <!-- Profile Content -->
            <div class="grid md:grid-cols-4 gap-8 p-6">
                <!-- Profile Summary -->
                <div class="md:col-span-1 flex flex-col items-center">
                    <div class="w-40 h-40 rounded-full bg-amber-100 flex items-center justify-center text-amber-800 text-6xl font-bold mb-6 profile-avatar">
                        <?= strtoupper(substr($_SESSION['first_name'] ?? 'U', 0, 1)) ?>
                    </div>
                    
                    <h2 class="text-2xl font-semibold text-center text-gray-800">
                        <?= htmlspecialchars($_SESSION['first_name'] . ' ' . htmlspecialchars($_SESSION['last_name'])) ?>
                    </h2>
                    <p class="text-gray-600 text-center"><?= htmlspecialchars($_SESSION['email']) ?></p>
                    
                    <div class="mt-4 text-center">
                        <span class="inline-block px-3 py-1 text-xs font-semibold text-amber-800 bg-amber-100 rounded-full">
                            <?= ucfirst(htmlspecialchars($_SESSION['role'] ?? 'customer')) ?>
                        </span>
                    </div>
                    
                    <p class="text-gray-500 text-sm mt-4 text-center">
                        Member since <?= date('F Y', strtotime($_SESSION['created_at'] ?? 'now')) ?>
                    </p>
                </div>
                
                <!-- Profile Details -->
                <div class="md:col-span-3 space-y-6">
                    <!-- Personal Information -->
                    <div class="bg-white p-6 rounded-lg shadow-sm info-card">
                        <h3 class="font-playfair text-2xl font-semibold text-amber-800 mb-4">Personal Information</h3>
                        
                        <div class="grid md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-gray-500 text-sm mb-1">First Name</label>
                                <p class="font-medium text-gray-800 text-lg"><?= htmlspecialchars($_SESSION['first_name'] ?? '') ?></p>
                            </div>
                            
                            <div>
                                <label class="block text-gray-500 text-sm mb-1">Last Name</label>
                                <p class="font-medium text-gray-800 text-lg"><?= htmlspecialchars($_SESSION['last_name'] ?? '') ?></p>
                            </div>
                            
                            <div class="md:col-span-2">
                                <label class="block text-gray-500 text-sm mb-1">Email</label>
                                <p class="font-medium text-gray-800 text-lg"><?= htmlspecialchars($_SESSION['email'] ?? '') ?></p>
                            </div>
                            
                            <div>
                                <label class="block text-gray-500 text-sm mb-1">Phone</label>
                                <p class="font-medium text-gray-800 text-lg">
                                    <?= !empty($_SESSION['phone']) ? htmlspecialchars($_SESSION['phone']) : '<span class="text-gray-400">Not provided</span>' ?>
                                </p>
                            </div>
                            
                            <div>
                                <label class="block text-gray-500 text-sm mb-1">Username</label>
                                <p class="font-medium text-gray-800 text-lg"><?= htmlspecialchars($_SESSION['username'] ?? '') ?></p>
                            </div>
                        </div>
                    </div>
                    
                    <hr class="divider my-6">
                    
                    <!-- Account Actions -->
                    <div class="bg-white p-6 rounded-lg shadow-sm">
                        <h3 class="font-playfair text-2xl font-semibold text-amber-800 mb-4">Account Actions</h3>
                        
                        <div class="flex flex-wrap gap-4">
                            <a href="php/edit_profile.php" class="action-btn flex items-center px-6 py-3 bg-amber-100 text-amber-800 rounded-lg hover:bg-amber-200">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                </svg>
                                Edit Profile
                            </a>
                            
                            <a href="change_password.php" class="action-btn flex items-center px-6 py-3 bg-blue-100 text-blue-800 rounded-lg hover:bg-blue-200">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                                </svg>
                                Change Password
                            </a>
                            
                            <a href="logout.php" class="action-btn flex items-center px-6 py-3 bg-red-100 text-red-800 rounded-lg hover:bg-red-200">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                                </svg>
                                Sign Out
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
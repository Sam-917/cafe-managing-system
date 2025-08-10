<?php
session_start();
require_once '../includes/config.php';

// Redirect if not logged in
if (!isset($_SESSION['logged_in'])) {
    header("Location: ../login.php");
    exit();
}

// Initialize variables
$errors = [];
$success = false;

// Fetch current user data
try {
    $stmt = $conn->prepare("SELECT * FROM users WHERE user_id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user) {
        throw new Exception("User not found");
    }

    // Set default values
    $firstName = $user['first_name'];
    $lastName = $user['last_name'];
    $email = $user['email'];
    $phone = $user['phone'];
    $username = $user['username'];

} catch (PDOException $e) {
    $errors[] = "Database error: " . $e->getMessage();
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Sanitize inputs
    $firstName = trim($_POST['first_name'] ?? '');
    $lastName = trim($_POST['last_name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $username = trim($_POST['username'] ?? '');

    // Validate inputs
    if (empty($firstName)) {
        $errors[] = "First name is required";
    }
    if (empty($lastName)) {
        $errors[] = "Last name is required";
    }
    if (empty($email)) {
        $errors[] = "Email is required";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Invalid email format";
    }
    if (empty($username)) {
        $errors[] = "Username is required";
    } elseif (strlen($username) < 4) {
        $errors[] = "Username must be at least 4 characters";
    } elseif (!preg_match('/^[a-zA-Z0-9_]+$/', $username)) {
        $errors[] = "Username can only contain letters, numbers, and underscores";
    }

    // Check if email or username already exists (excluding current user)
    if (empty($errors)) {
        try {
            $checkStmt = $conn->prepare("SELECT user_id FROM users WHERE (email = ? OR username = ?) AND user_id != ?");
            $checkStmt->execute([$email, $username, $_SESSION['user_id']]);
            if ($checkStmt->fetch()) {
                $errors[] = "Email or username already in use by another account";
            }
        } catch (PDOException $e) {
            $errors[] = "Database error: " . $e->getMessage();
        }
    }

    // Update if no errors
    if (empty($errors)) {
        try {
            $updateStmt = $conn->prepare("UPDATE users SET 
                first_name = ?, 
                last_name = ?, 
                email = ?, 
                phone = ?, 
                username = ?,
                updated_at = NOW()
                WHERE user_id = ?");
            
            $updateStmt->execute([
                $firstName,
                $lastName,
                $email,
                $phone,
                $username,
                $_SESSION['user_id']
            ]);

            // Update session variables
            $_SESSION['first_name'] = $firstName;
            $_SESSION['last_name'] = $lastName;
            $_SESSION['email'] = $email;
            $_SESSION['phone'] = $phone;
            $_SESSION['username'] = $username;

            $success = true;
            $_SESSION['success_message'] = "Profile updated successfully!";
            header("Location: ../profile.php");
            exit();

        } catch (PDOException $e) {
            $errors[] = "Database error: " . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Profile | Cafe & Netic</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;700&family=Inter:wght@300;400;500;600&display=swap');
        
        .font-playfair { font-family: 'Playfair Display', serif; }
        .font-inter { font-family: 'Inter', sans-serif; }
        
        .nav-item {
            position: relative;
            transition: all 0.3s ease;
        }
        
        .nav-item::after {
            content: '';
            position: absolute;
            width: 0;
            height: 2px;
            bottom: -5px;
            left: 50%;
            background: linear-gradient(90deg, #8B4513, #D2691E);
            transition: all 0.3s ease;
            transform: translateX(-50%);
        }
        
        .nav-item:hover::after {
            width: 100%;
        }
        
        .profile-picture {
            transition: all 0.3s ease;
        }
        
        .profile-picture:hover {
            transform: scale(1.05);
            box-shadow: 0 10px 20px rgba(0,0,0,0.1);
        }
    </style>
</head>
<body class="font-inter bg-gradient-to-br from-amber-50 to-orange-100 min-h-screen">
    <!-- Navigation (same as index.php) -->
    <nav class="bg-white/90 backdrop-blur-md shadow-lg sticky top-0 z-50 transition-all duration-300">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-16">
                <!-- Logo -->
                <div class="flex items-center space-x-2">
                    <div class="coffee-steam text-2xl">☕</div>
                    <a href="../index.php" class="font-playfair text-2xl font-bold text-amber-800">Cafe & Netic</a>
                </div>
                
                <!-- Navigation Links -->
                <div class="hidden md:flex space-x-8 items-center">
                    <a href="../about_us.php" class="nav-item text-gray-700 hover:text-amber-700 font-medium px-3 py-2">About Us</a>
                    <a href="../menu.php" class="nav-item text-gray-700 hover:text-amber-700 font-medium px-3 py-2">Menu</a>
                    <a href="../reservation.php" class="nav-item text-gray-700 hover:text-amber-700 font-medium px-3 py-2">Reservation</a>
                    
                    <?php if (isset($_SESSION['logged_in']) && $_SESSION['logged_in']): ?>
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
                                <a href="../profile.php" class="block px-4 py-2 text-sm text-gray-700 hover:bg-amber-50">Profile</a>
                                <?php if ($_SESSION['role'] === 'admin' || $_SESSION['role'] === 'staff'): ?>
                                    <a href="../admin/dashboard.php" class="block px-4 py-2 text-sm text-gray-700 hover:bg-amber-50">Dashboard</a>
                                <?php endif; ?>
                                <a href="../logout.php" class="block px-4 py-2 text-sm text-gray-700 hover:bg-amber-50">Sign Out</a>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
                
                <!-- Mobile menu button -->
                <div class="md:hidden">
                    <button id="mobile-menu-btn" class="text-gray-700 hover:text-amber-700 transition-colors">
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        </svg>
                    </button>
                </div>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <div class="max-w-4xl mx-auto py-12 px-4">
        <div class="bg-white rounded-xl shadow-md overflow-hidden p-6">
            <!-- Back button -->
            <a href="../profile.php" class="inline-flex items-center text-amber-600 hover:text-amber-800 mb-6">
                <svg class="w-5 h-5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                Back to Profile
            </a>
            
            <h1 class="font-playfair text-3xl font-bold text-amber-900 mb-6">Edit Profile</h1>
            
            <!-- Error Messages -->
            <?php if (!empty($errors)): ?>
                <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6" role="alert">
                    <p class="font-bold">Please fix the following errors:</p>
                    <ul class="list-disc pl-5">
                        <?php foreach ($errors as $error): ?>
                            <li><?= htmlspecialchars($error) ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>
            
            <!-- Profile Picture (optional) -->
            <div class="flex flex-col items-center mb-8">
                <div class="w-32 h-32 rounded-full bg-amber-100 flex items-center justify-center text-amber-800 text-5xl font-bold mb-4 profile-picture">
                    <?= strtoupper(substr($firstName ?? 'U', 0, 1)) ?>
                </div>
                <button class="text-sm text-amber-600 hover:text-amber-800">Change Profile Picture</button>
            </div>
            
            <!-- Edit Form -->
            <form method="POST" class="space-y-6">
                <div class="grid md:grid-cols-2 gap-6">
                    <div>
                        <label for="first_name" class="block text-gray-700 font-medium mb-2">First Name</label>
                        <input type="text" id="first_name" name="first_name" value="<?= htmlspecialchars($firstName ?? '') ?>" 
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-amber-500 focus:border-amber-500">
                    </div>
                    
                    <div>
                        <label for="last_name" class="block text-gray-700 font-medium mb-2">Last Name</label>
                        <input type="text" id="last_name" name="last_name" value="<?= htmlspecialchars($lastName ?? '') ?>" 
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-amber-500 focus:border-amber-500">
                    </div>
                    
                    <div>
                        <label for="email" class="block text-gray-700 font-medium mb-2">Email</label>
                        <input type="email" id="email" name="email" value="<?= htmlspecialchars($email ?? '') ?>" 
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-amber-500 focus:border-amber-500">
                    </div>
                    
                    <div>
                        <label for="phone" class="block text-gray-700 font-medium mb-2">Phone Number</label>
                        <input type="tel" id="phone" name="phone" value="<?= htmlspecialchars($phone ?? '') ?>" 
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-amber-500 focus:border-amber-500">
                    </div>
                    
                    <div class="md:col-span-2">
                        <label for="username" class="block text-gray-700 font-medium mb-2">Username</label>
                        <input type="text" id="username" name="username" value="<?= htmlspecialchars($username ?? '') ?>" 
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-amber-500 focus:border-amber-500">
                    </div>
                </div>
                
                <div class="flex justify-end space-x-4 pt-6 border-t">
                    <a href="../profile.php" class="px-6 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors">
                        Cancel
                    </a>
                    <button type="submit" class="px-6 py-2 bg-amber-600 text-white rounded-lg hover:bg-amber-700 transition-colors">
                        Save Changes
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        // Mobile menu toggle
        const mobileMenuBtn = document.getElementById('mobile-menu-btn');
        const mobileMenu = document.getElementById('mobile-menu');
        
        if (mobileMenuBtn && mobileMenu) {
            mobileMenuBtn.addEventListener('click', () => {
                mobileMenu.classList.toggle('hidden');
            });
        }

        // Profile picture upload preview (optional)
        document.getElementById('profile_picture')?.addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(event) {
                    document.getElementById('profile_preview').src = event.target.result;
                };
                reader.readAsDataURL(file);
            }
        });
    </script>
</body>
</html>
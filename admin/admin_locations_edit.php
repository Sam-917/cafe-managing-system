<?php
session_start();
require '../includes/config.php';

// Check if user is admin
if (!isset($_SESSION['logged_in']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit();
}

// Get location ID from URL
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: admin_locations.php");
    exit();
}

$location_id = (int)$_GET['id'];

// Fetch location details
try {
    $location_stmt = $conn->prepare("SELECT * FROM restaurant_locations WHERE location_id = :location_id");
    $location_stmt->bindParam(':location_id', $location_id);
    $location_stmt->execute();
    $location = $location_stmt->fetch(PDO::FETCH_ASSOC);

    if (!$location) {
        header("Location: admin_locations.php");
        exit();
    }

} catch (PDOException $e) {
    $error = "Failed to fetch location details: " . $e->getMessage();
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_location'])) {
    // Validate and sanitize input
    $name = trim($_POST['name']);
    $address = trim($_POST['address']);
    $phone = trim($_POST['phone']);
    $email = trim($_POST['email']);
    $description = trim($_POST['description']);
    $opening_time = trim($_POST['opening_time']);
    $closing_time = trim($_POST['closing_time']);
    $is_active = isset($_POST['is_active']) ? 1 : 0;

    try {
        // Update location
        $update_stmt = $conn->prepare("
            UPDATE restaurant_locations 
            SET 
                name = :name,
                address = :address,
                phone = :phone,
                email = :email,
                description = :description,
                opening_time = :opening_time,
                closing_time = :closing_time,
                is_active = :is_active
            WHERE location_id = :location_id
        ");
        
        $update_stmt->bindParam(':name', $name);
        $update_stmt->bindParam(':address', $address);
        $update_stmt->bindParam(':phone', $phone);
        $update_stmt->bindParam(':email', $email);
        $update_stmt->bindParam(':description', $description);  
        $update_stmt->bindParam(':opening_time', $opening_time);
        $update_stmt->bindParam(':closing_time', $closing_time);
        $update_stmt->bindParam(':is_active', $is_active);
        $update_stmt->bindParam(':location_id', $location_id);

        // Execute the update
        if ($update_stmt->execute()) {
            $success = "Location updated successfully!";
            
            // Refresh location data
            $location_stmt->execute();
            $location = $location_stmt->fetch(PDO::FETCH_ASSOC);
        } else {
            $error = "Failed to update location";
        }
        
    } catch (PDOException $e) {
        $error = "Failed to update location: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Location | Cafe & Netic</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="font-inter bg-gray-50">
    <!-- Admin Layout -->
    <div class="flex h-screen overflow-hidden"> 
        <?php include '../includes/admin_sidebar.php'; ?>
        
        <!-- Main Content -->
        <div class="flex-1 overflow-auto">
            <?php 
                $page_title = "Edit Location";
                include '../includes/admin_topnav.php'; 
            ?>
            
            <!-- Location Edit Content -->
            <main class="p-6">
                <div class="flex justify-between items-center mb-6">
                    <div>
                        <h1 class="text-2xl font-bold text-gray-800">Edit Location</h1>
                        <nav class="flex" aria-label="Breadcrumb">
                            <ol class="inline-flex items-center space-x-1 md:space-x-2">
                                <li class="inline-flex items-center">
                                    <a href="admin_locations.php" class="inline-flex items-center text-sm font-medium text-gray-700 hover:text-amber-600">
                                        Locations
                                    </a>
                                </li>
                                <li aria-current="page">
                                    <div class="flex items-center">
                                        <svg class="w-6 h-6 text-gray-400" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path></svg>
                                        <span class="ml-1 text-sm font-medium text-gray-500 md:ml-2"><?= htmlspecialchars($location['name']) ?></span>
                                    </div>
                                </li>
                            </ol>
                        </nav>
                    </div>
                    <a href="admin_locations.php" class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md shadow-sm text-white bg-amber-600 hover:bg-amber-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-amber-500">
                        Back to Locations
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
                            <h2 class="text-lg font-medium text-gray-900">Location Information</h2>
                        </div>
                        <div class="px-6 py-4 grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Basic Info -->
                            <div class="space-y-4">
                                <div>
                                    <label for="name" class="block text-sm font-medium text-gray-700">Location Name *</label>
                                    <input type="text" id="name" name="name" required 
                                           value="<?= htmlspecialchars($location['name']) ?>" 
                                           class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-amber-500 focus:border-amber-500 sm:text-sm">
                                </div>

                                <div>
                                    <label for="address" class="block text-sm font-medium text-gray-700">Address *</label>
                                    <input type="text" id="address" name="address" required 
                                           value="<?= htmlspecialchars($location['address']) ?>" 
                                           class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-amber-500 focus:border-amber-500 sm:text-sm">
                                </div>

                                <div>
                                    <label for="phone" class="block text-sm font-medium text-gray-700">Phone *</label>
                                    <input type="tel" id="phone" name="phone" required 
                                           value="<?= htmlspecialchars($location['phone']) ?>" 
                                           class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-amber-500 focus:border-amber-500 sm:text-sm">
                                </div>
                            </div>

                            <!-- Additional Info -->
                            <div class="space-y-4">
                                <div>
                                    <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
                                    <input type="email" id="email" name="email" 
                                           value="<?= htmlspecialchars($location['email'] ?? '') ?>" 
                                           class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-amber-500 focus:border-amber-500 sm:text-sm">
                                </div>

                                <div>
                                    <label for="opening_time" class="block text-sm font-medium text-gray-700">Opening Time</label>
                                    <input type="text" id="opening_time" name="opening_time" 
                                           value="<?= htmlspecialchars($location['opening_time'] ?? '') ?>" 
                                           class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-amber-500 focus:border-amber-500 sm:text-sm">
                                </div>

                                <div>
                                    <label for="closing_time" class="block text-sm font-medium text-gray-700">Closing Time</label>
                                    <input type="text" id="closing_time" name="closing_time" 
                                           value="<?= htmlspecialchars($location['closing_time'] ?? '') ?>" 
                                           class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-amber-500 focus:border-amber-500 sm:text-sm">
                                </div>

                                <div class="flex items-center">
                                    <input id="is_active" name="is_active" type="checkbox" 
                                           <?= $location['is_active'] ? 'checked' : '' ?> 
                                           class="h-4 w-4 text-amber-600 focus:ring-amber-500 border-gray-300 rounded">
                                    <label for="is_active" class="ml-2 block text-sm text-gray-700">Location Active</label>
                                </div>
                            </div>

                            <!-- Description -->
                            <div class="md:col-span-2">
                                <label for="description" class="block text-sm font-medium text-gray-700">Description</label>
                                <textarea id="description" name="description" rows="3" 
                                          class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-amber-500 focus:border-amber-500 sm:text-sm"><?= htmlspecialchars($location['description']) ?></textarea>
                            </div>

                        </div>

                        <!-- Form Actions -->
                        <div class="px-6 py-4 border-t border-gray-200 bg-gray-50 text-right">
                            <button type="submit" name="update_location" class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-amber-600 hover:bg-amber-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-amber-500">
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
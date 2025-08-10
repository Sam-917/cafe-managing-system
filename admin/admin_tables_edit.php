<?php
session_start();
require '../includes/config.php';

// Check if user is admin
if (!isset($_SESSION['logged_in']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit();
}

// Get table ID from URL
if (!isset($_GET['id'])) {
    header("Location: admin_tables.php");
    exit();
}

$table_id = $_GET['id'];

// Fetch table details
try {
    $table_stmt = $conn->prepare("
        SELECT t.*, l.name as location_name 
        FROM restaurant_tables t
        JOIN restaurant_locations l ON t.location_id = l.location_id
        WHERE t.table_id = :table_id
    ");
    $table_stmt->bindParam(':table_id', $table_id);
    $table_stmt->execute();
    $table = $table_stmt->fetch(PDO::FETCH_ASSOC);

    if (!$table) {
        header("Location: admin_tables.php");
        exit();
    }

    // Fetch all locations for dropdown
    $locations_stmt = $conn->query("SELECT * FROM restaurant_locations ORDER BY name");
    $locations = $locations_stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    $error = "Failed to fetch table details: " . $e->getMessage();
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_table'])) {
    // Validate and sanitize input
    $name = trim($_POST['name']);
    $location_id = (int)$_POST['location_id'];
    $capacity = (int)$_POST['capacity'];
    $type = $_POST['type'];
    $description = trim($_POST['description']);
    $is_active = isset($_POST['is_active']) ? 1 : 0;

    try {
        // Update table
        $update_stmt = $conn->prepare("
            UPDATE restaurant_tables 
            SET 
                name = :name,
                location_id = :location_id,
                capacity = :capacity,
                type = :type,
                description = :description,
                is_active = :is_active
            WHERE table_id = :table_id
        ");
        
        $update_stmt->bindParam(':name', $name);
        $update_stmt->bindParam(':location_id', $location_id);
        $update_stmt->bindParam(':capacity', $capacity);
        $update_stmt->bindParam(':type', $type);
        $update_stmt->bindParam(':description', $description);
        $update_stmt->bindParam(':is_active', $is_active);
        $update_stmt->bindParam(':table_id', $table_id);
        $update_stmt->execute();

        $success = "Table updated successfully!";
        
        // Refresh table data
        $table_stmt->execute();
        $table = $table_stmt->fetch(PDO::FETCH_ASSOC);
        
    } catch (PDOException $e) {
        $error = "Failed to update table: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Table | Cafe & Netic</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="font-inter bg-gray-50">
    <!-- Admin Layout -->
    <div class="flex h-screen overflow-hidden"> 
        <?php include '../includes/admin_sidebar.php'; ?>
        
        <!-- Main Content -->
        <div class="flex-1 overflow-auto">
            <?php 
                $page_title = "Edit Table";
                include '../includes/admin_topnav.php'; 
            ?>
            
            <!-- Table Edit Content -->
            <main class="p-6">
                <div class="flex justify-between items-center mb-6">
                    <div>
                        <h1 class="text-2xl font-bold text-gray-800">Edit Table</h1>
                        <nav class="flex" aria-label="Breadcrumb">
                            <ol class="inline-flex items-center space-x-1 md:space-x-2">
                                <li class="inline-flex items-center">
                                    <a href="admin_tables.php" class="inline-flex items-center text-sm font-medium text-gray-700 hover:text-amber-600">
                                        Tables
                                    </a>
                                </li>
                                <li aria-current="page">
                                    <div class="flex items-center">
                                        <svg class="w-6 h-6 text-gray-400" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path></svg>
                                        <span class="ml-1 text-sm font-medium text-gray-500 md:ml-2"><?= htmlspecialchars($table['name']) ?></span>
                                    </div>
                                </li>
                            </ol>
                        </nav>
                    </div>
                    <a href="admin_tables.php" class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md shadow-sm text-white bg-amber-600 hover:bg-amber-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-amber-500">
                        Back to Tables
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
                            <h2 class="text-lg font-medium text-gray-900">Table Information</h2>
                        </div>
                        <div class="px-6 py-4 grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Basic Info -->
                            <div class="space-y-4">
                                <div>
                                    <label for="table_id" class="block text-sm font-medium text-gray-700">Table ID</label>
                                    <input type="text" id="table_id" name="table_id" readonly
                                           value="<?= htmlspecialchars($table['table_id']) ?>" 
                                           class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 bg-gray-100 focus:outline-none focus:ring-amber-500 focus:border-amber-500 sm:text-sm">
                                </div>

                                <div>
                                    <label for="name" class="block text-sm font-medium text-gray-700">Table Name *</label>
                                    <input type="text" id="name" name="name" required 
                                           value="<?= htmlspecialchars($table['name']) ?>" 
                                           class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-amber-500 focus:border-amber-500 sm:text-sm">
                                </div>

                                <div>
                                    <label for="location_id" class="block text-sm font-medium text-gray-700">Location *</label>
                                    <select id="location_id" name="location_id" required 
                                            class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-amber-500 focus:border-amber-500 sm:text-sm">
                                        <?php foreach ($locations as $loc): ?>
                                            <option value="<?= $loc['location_id'] ?>" <?= $loc['location_id'] == $table['location_id'] ? 'selected' : '' ?>>
                                                <?= htmlspecialchars($loc['name']) ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>

                            <!-- Additional Info -->
                            <div class="space-y-4">
                                <div>
                                    <label for="capacity" class="block text-sm font-medium text-gray-700">Capacity *</label>
                                    <input type="number" id="capacity" name="capacity" min="1" required 
                                           value="<?= $table['capacity'] ?>" 
                                           class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-amber-500 focus:border-amber-500 sm:text-sm">
                                </div>

                                <div>
                                    <label for="type" class="block text-sm font-medium text-gray-700">Table Type *</label>
                                    <select id="type" name="type" required 
                                            class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-amber-500 focus:border-amber-500 sm:text-sm">
                                        <option value="standard" <?= $table['type'] === 'standard' ? 'selected' : '' ?>>Standard</option>
                                        <option value="round" <?= $table['type'] === 'round' ? 'selected' : '' ?>>Round</option>
                                        <option value="booth" <?= $table['type'] === 'booth' ? 'selected' : '' ?>>Booth</option>
                                        <option value="private" <?= $table['type'] === 'private' ? 'selected' : '' ?>>Private</option>
                                        <option value="bar" <?= $table['type'] === 'bar' ? 'selected' : '' ?>>Bar</option>
                                        <option value="outdoor" <?= $table['type'] === 'outdoor' ? 'selected' : '' ?>>Outdoor</option>
                                    </select>
                                </div>

                                <div class="flex items-center">
                                    <input id="is_active" name="is_active" type="checkbox" 
                                           <?= $table['is_active'] ? 'checked' : '' ?> 
                                           class="h-4 w-4 text-amber-600 focus:ring-amber-500 border-gray-300 rounded">
                                    <label for="is_active" class="ml-2 block text-sm text-gray-700">Table Active</label>
                                </div>
                            </div>

                            <!-- Description -->
                            <div class="md:col-span-2">
                                <label for="description" class="block text-sm font-medium text-gray-700">Description</label>
                                <textarea id="description" name="description" rows="3" 
                                          class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-amber-500 focus:border-amber-500 sm:text-sm"><?= htmlspecialchars($table['description']) ?></textarea>
                            </div>
                        </div>

                        <!-- Form Actions -->
                        <div class="px-6 py-4 border-t border-gray-200 bg-gray-50 text-right">
                            <button type="submit" name="update_table" class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-amber-600 hover:bg-amber-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-amber-500">
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
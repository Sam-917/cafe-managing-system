<?php
session_start();
require '../includes/config.php';

// Check if user is admin
if (!isset($_SESSION['logged_in']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit();
}

// Get all locations
try {
    $sql = "SELECT * FROM restaurant_locations ORDER BY name";
    $stmt = $conn->query($sql);
    $locations = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $error = "Failed to fetch locations: " . $e->getMessage();
}

// Count tables per location
$tables_count = [];
try {
    $count_sql = "SELECT location_id, COUNT(*) as table_count FROM restaurant_tables GROUP BY location_id";
    $count_stmt = $conn->query($count_sql);
    $counts = $count_stmt->fetchAll(PDO::FETCH_ASSOC);
    
    foreach ($counts as $count) {
        $tables_count[$count['location_id']] = $count['table_count'];
    }
} catch (PDOException $e) {
    $error = "Failed to fetch table counts: " . $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Locations | Cafe & Netic</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <style>
        .active-badge {
            background-color: #a7f3d0;
            color: #065f46;
        }
        .inactive-badge {
            background-color: #fecaca;
            color: #991b1b;
        }
    </style>
</head>
<body class="font-inter bg-gray-50">
    <!-- Admin Layout -->
    <div class="flex h-screen overflow-hidden"> 
        <?php include '../includes/admin_sidebar.php'; ?>
        
        <!-- Main Content -->
        <div class="flex-1 overflow-auto">
            <?php 
                // Update the page title in topnav
                $page_title = "Manage Locations";
                include '../includes/admin_topnav.php'; 
            ?>
            
            <!-- Locations Content -->
            <main class="p-6">
                <div class="flex justify-between items-center mb-6">
                    <h1 class="text-2xl font-bold text-gray-800">Restaurant Locations</h1>
                    <a href="admin_locations_add.php" class="bg-amber-600 hover:bg-amber-700 text-white px-4 py-2 rounded-md text-sm font-medium">
                        Add New Location
                    </a>
                </div>

                <?php if (isset($error)): ?>
                    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-6">
                        <?= htmlspecialchars($error) ?>
                    </div>
                <?php endif; ?>

                <!-- Locations Table -->
                <div class="bg-white shadow rounded-lg overflow-hidden">
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Location</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Address</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tables</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                    <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                <?php if (empty($locations)): ?>
                                    <tr>
                                        <td colspan="5" class="px-6 py-4 text-center text-sm text-gray-500">No locations found</td>
                                    </tr>
                                <?php else: ?>
                                    <?php foreach ($locations as $location): ?>
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="flex items-center">
                                                <div class="ml-4">
                                                    <div class="text-sm font-medium text-gray-900"><?= htmlspecialchars($location['name']) ?></div>
                                                    <div class="text-sm text-gray-500"><?= htmlspecialchars($location['phone']) ?></div> 
                                                    <div class="text-xs text-gray-500 mt-1"><?= htmlspecialchars(substr($location['description'], 0, 50)) ?>...</div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            <?= htmlspecialchars($location['address']) ?>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            <?= $tables_count[$location['location_id']] ?? 0 ?>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="px-2 py-1 text-xs rounded-full <?= $location['is_active'] ? 'active-badge' : 'inactive-badge' ?>">
                                                <?= $location['is_active'] ? 'Active' : 'Inactive' ?>
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                            <a href="admin_locations_edit.php?id=<?= $location['location_id'] ?>" class="text-amber-600 hover:text-amber-900 mr-3">Edit</a>
                                            <a href="admin_tables.php?location_id=<?= $location['location_id'] ?>" class="text-blue-600 hover:text-blue-900 mr-3">View Tables</a>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </main>
        </div>
    </div>
</body>
</html>
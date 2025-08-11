<?php
session_start();
require '../includes/config.php';

// Check if user is admin
if (!isset($_SESSION['logged_in']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit();
}

// Get location filter if specified
$location_id = isset($_GET['location_id']) ? (int)$_GET['location_id'] : null;

// Build the SQL query
$sql = "SELECT t.*, l.name as location_name 
        FROM restaurant_tables t
        JOIN restaurant_locations l ON t.location_id = l.location_id";

if ($location_id) {
    $sql .= " WHERE t.location_id = :location_id";
}

$sql .= " ORDER BY l.name, t.table_number";

try {
    $stmt = $conn->prepare($sql);
    
    if ($location_id) {
        $stmt->bindParam(':location_id', $location_id, PDO::PARAM_INT);
    }
    
    $stmt->execute();
    $tables = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $error = "Failed to fetch tables: " . $e->getMessage();
}

// Get all locations for the filter dropdown
try {
    $locations_sql = "SELECT * FROM restaurant_locations ORDER BY name";
    $locations_stmt = $conn->query($locations_sql);
    $locations = $locations_stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $error = "Failed to fetch locations: " . $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Tables | Cafe & Netic</title>
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
        .table-type-standard { background-color: #dbeafe; color: #1e40af; }
        .table-type-round { background-color: #f0fdf4; color: #166534; }
        .table-type-booth { background-color: #f5f3ff; color: #5b21b6; }
        .table-type-private { background-color: #ffedd5; color: #9a3412; }
        .table-type-bar { background-color: #fce7f3; color: #9d174d; }
        .table-type-outdoor { background-color: #ecfdf5; color: #047857; }
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
                $page_title = "Manage Tables";
                include '../includes/admin_topnav.php'; 
            ?>
            
            <!-- Tables Content -->
            <main class="p-6">
                <div class="flex justify-between items-center mb-6">
                    <h1 class="text-2xl font-bold text-gray-800">Restaurant Tables</h1>
                    <a href="admin_tables_add.php" class="bg-amber-600 hover:bg-amber-700 text-white px-4 py-2 rounded-md text-sm font-medium">
                        Add New Table
                    </a>
                </div>

                <?php if (isset($error)): ?>
                    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-6">
                        <?= htmlspecialchars($error) ?>
                    </div>
                <?php endif; ?>

                <!-- Location Filter -->
                <div class="mb-6">
                    <label for="location_filter" class="block text-sm font-medium text-gray-700 mb-1">Filter by Location</label>
                    <select id="location_filter" onchange="location.href='admin_tables.php'+(this.value ? '?location_id='+this.value : '')" 
                            class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-amber-500 focus:border-amber-500 sm:text-sm rounded-md">
                        <option value="">All Locations</option>
                        <?php foreach ($locations as $loc): ?>
                            <option value="<?= $loc['location_id'] ?>" <?= $location_id == $loc['location_id'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($loc['name']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <!-- Tables Table -->
                <div class="bg-white shadow rounded-lg overflow-hidden">
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Capacity</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Location</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                    <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                <?php if (empty($tables)): ?>
                                    <tr>
                                        <td colspan="7" class="px-6 py-4 text-center text-sm text-gray-500">No tables found</td>
                                    </tr>
                                <?php else: ?>
                                    <?php foreach ($tables as $table): ?>
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                            <?= htmlspecialchars($table['table_id']) ?>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            <?= htmlspecialchars($table['table_number']) ?>
                                            <?php if (!empty($table['description'])): ?>
                                                <div class="text-xs text-gray-500 mt-1"><?= htmlspecialchars(substr($table['description'], 0, 50)) ?>...</div>
                                            <?php endif; ?>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            <?= $table['capacity'] ?> seats
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="px-2 py-1 text-xs rounded-full table-type-<?= $table['type'] ?>">
                                                <?= ucfirst($table['type']) ?>
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            <?= htmlspecialchars($table['location_name']) ?>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="px-2 py-1 text-xs rounded-full <?= $table['is_active'] ? 'active-badge' : 'inactive-badge' ?>">
                                                <?= $table['is_active'] ? 'Active' : 'Inactive' ?>
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                            <a href="admin_tables_edit.php?id=<?= $table['table_id'] ?>" class="text-amber-600 hover:text-amber-900 mr-3">Edit</a>
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
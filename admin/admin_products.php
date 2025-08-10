<?php
session_start();
require '../includes/config.php';

// Check if user is admin
if (!isset($_SESSION['logged_in']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit();
}

// Get filter from URL (active/inactive/all)
$filter = isset($_GET['filter']) ? $_GET['filter'] : 'active';
$valid_filters = ['all', 'active', 'inactive'];

// Validate filter
if (!in_array($filter, $valid_filters)) {
    $filter = 'active';
}

// Build the SQL query based on filter
$sql = "SELECT p.*, c.name as category_name 
        FROM products p
        JOIN categories c ON p.category_id = c.category_id";

if ($filter === 'active') {
    $sql .= " WHERE p.is_active = 1";
} elseif ($filter === 'inactive') {
    $sql .= " WHERE p.is_active = 0";
}

$sql .= " ORDER BY c.display_order, p.display_order";

try {
    $stmt = $conn->query($sql);
    $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $error = "Failed to fetch products: " . $e->getMessage();
}

// Count products by status for the filter tabs
try {
    $counts_stmt = $conn->query("
        SELECT 
            SUM(is_active = 1) as active_count,
            SUM(is_active = 0) as inactive_count,
            COUNT(*) as total_count
        FROM products
    ");
    $counts = $counts_stmt->fetch(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $error = "Failed to fetch product counts: " . $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Products | Cafe & Netic</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <style>
        .featured-badge {
            background-color: #fef3c7;
            color: #92400e;
        }
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
                $page_title = "Manage Products";
                include '../includes/admin_topnav.php'; 
            ?>
            
            <!-- Products Content -->
            <main class="p-6">
                <div class="flex justify-between items-center mb-6">
                    <h1 class="text-2xl font-bold text-gray-800">Menu Items</h1>
                    <a href="admin_products_add.php" class="bg-amber-600 hover:bg-amber-700 text-white px-4 py-2 rounded-md text-sm font-medium">
                        Add New Product
                    </a>
                </div>

                <?php if (isset($error)): ?>
                    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-6">
                        <?= htmlspecialchars($error) ?>
                    </div>
                <?php endif; ?>

                <!-- Filter Tabs -->
                <div class="mb-6 border-b border-gray-200">
                    <nav class="-mb-px flex space-x-8">
                        <a href="admin_products.php?filter=active" 
                           class="<?= $filter === 'active' ? 'border-amber-500 text-amber-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' ?> whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
                            Active
                            <span class="bg-gray-100 text-gray-600 ml-1 py-0.5 px-2 rounded-full text-xs">
                                <?= $counts['active_count'] ?? 0 ?>
                            </span>
                        </a>
                        
                        <a href="admin_products.php?filter=inactive" 
                           class="<?= $filter === 'inactive' ? 'border-amber-500 text-amber-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' ?> whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
                            Inactive
                            <span class="bg-gray-100 text-gray-600 ml-1 py-0.5 px-2 rounded-full text-xs">
                                <?= $counts['inactive_count'] ?? 0 ?>
                            </span>
                        </a>
                        
                        <a href="admin_products.php?filter=all" 
                           class="<?= $filter === 'all' ? 'border-amber-500 text-amber-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' ?> whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
                            All
                            <span class="bg-gray-100 text-gray-600 ml-1 py-0.5 px-2 rounded-full text-xs">
                                <?= $counts['total_count'] ?? 0 ?>
                            </span>
                        </a>
                    </nav>
                </div>

                <!-- Products Table -->
                <div class="bg-white shadow rounded-lg overflow-hidden">
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Product</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Category</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Price</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Calories</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                    <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                <?php if (empty($products)): ?>
                                    <tr>
                                        <td colspan="6" class="px-6 py-4 text-center text-sm text-gray-500">No products found</td>
                                    </tr>
                                <?php else: ?>
                                    <?php foreach ($products as $product): ?>
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="flex items-center">
                                                <?php if ($product['image_url']): ?>
                                                <div class="flex-shrink-0 h-10 w-10">
                                                    <img class="h-10 w-10 rounded-full object-cover" src="../assets/img/product/removebg/<?= htmlspecialchars($product['image_url']) ?>" alt="<?= htmlspecialchars($product['name']) ?>">
                                                </div>
                                                <?php endif; ?>
                                                <div class="ml-4">
                                                    <div class="text-sm font-medium text-gray-900"><?= htmlspecialchars($product['name']) ?></div>
                                                    <div class="text-sm text-gray-500"><?= htmlspecialchars(substr($product['description'], 0, 50)) ?>...</div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            <?= htmlspecialchars($product['category_name']) ?>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            $<?= number_format($product['base_price'], 2) ?>
                                            <?php if ($product['is_featured']): ?>
                                                <span class="ml-2 featured-badge px-2 py-1 text-xs rounded-full">Featured</span>
                                            <?php endif; ?>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            <?= $product['calories'] ?? 'N/A' ?>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="px-2 py-1 text-xs rounded-full <?= $product['is_active'] ? 'active-badge' : 'inactive-badge' ?>">
                                                <?= $product['is_active'] ? 'Active' : 'Inactive' ?>
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                            <a href="admin_products_edit.php?id=<?= $product['product_id'] ?>" class="text-amber-600 hover:text-amber-900 mr-3">Edit</a>
                                            <a href="#" class="text-blue-600 hover:text-blue-900" 
                                               @click.prevent="confirmToggleStatus(<?= $product['product_id'] ?>, <?= $product['is_active'] ?>)">
                                                <?= $product['is_active'] ? 'Deactivate' : 'Activate' ?>
                                            </a>
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

    <!-- Status Toggle Modal -->
    <div x-data="{ 
        showModal: false, 
        productId: null, 
        currentStatus: false,
        openToggleModal(id, status) {
            this.productId = id;
            this.currentStatus = status;
            this.showModal = true;
        },
        async toggleStatus() {
            if (!this.productId) return;
            
            try {
                const response = await fetch('toggle_product_status.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({
                        product_id: this.productId,
                        new_status: !this.currentStatus
                    })
                });
                
                const result = await response.json();
                
                if (result.success) {
                    window.location.reload();
                } else {
                    alert(result.message || 'Failed to update status');
                }
            } catch (error) {
                console.error('Error:', error);
                alert('An error occurred while updating status');
            }
        }
    }" x-show="showModal" class="fixed z-10 inset-0 overflow-y-auto">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 transition-opacity" aria-hidden="true" @click="showModal = false">
                <div class="absolute inset-0 bg-gray-500 opacity-75"></div>
            </div>

            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

            <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full" @click.away="showModal = false">
                <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                    <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">
                        <?= $product['is_active'] ? 'Deactivate Product' : 'Activate Product' ?>
                    </h3>
                    
                    <div class="mb-4">
                        <p class="text-sm text-gray-500">
                            Are you sure you want to <?= $product['is_active'] ? 'deactivate' : 'activate' ?> this product?
                            <?= $product['is_active'] ? 'It will no longer be visible to customers.' : 'It will become available to customers.' ?>
                        </p>
                    </div>
                </div>
                <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                    <button type="button" @click="toggleStatus()" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-amber-600 text-base font-medium text-white hover:bg-amber-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-amber-500 sm:ml-3 sm:w-auto sm:text-sm">
                        Confirm
                    </button>
                    <button type="button" @click="showModal = false" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-amber-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                        Cancel
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Function to confirm status toggle
        function confirmToggleStatus(id, currentStatus) {
            const modal = document.querySelector('[x-data]').__x.$data;
            modal.openToggleModal(id, currentStatus);
        }
    </script>
</body>
</html>
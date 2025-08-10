<?php
session_start();
require '../includes/config.php';

// Check if user is admin
if (!isset($_SESSION['logged_in']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit();
}

// Get status filter from URL
$status_filter = isset($_GET['status']) ? $_GET['status'] : 'all';
$valid_statuses = ['all', 'pending', 'preparing', 'ready', 'completed', 'cancelled'];

// Validate status filter
if (!in_array($status_filter, $valid_statuses)) {
    $status_filter = 'all';
}

// Build the SQL query based on filter
$sql = "SELECT o.*, l.name as location_name 
        FROM orders o 
        LEFT JOIN restaurant_locations l ON o.pickup_location_id = l.location_id";

if ($status_filter !== 'all') {
    $sql .= " WHERE o.status = :status";
}

$sql .= " ORDER BY o.created_at DESC";

try {
    $stmt = $conn->prepare($sql);
    
    if ($status_filter !== 'all') {
        $stmt->bindParam(':status', $status_filter);
    }
    
    $stmt->execute();
    $orders = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $error = "Failed to fetch orders: " . $e->getMessage();
}

// Count orders by status for the status filter tabs
try {
    $status_counts_stmt = $conn->query("
        SELECT 
            status, 
            COUNT(*) as count 
        FROM orders 
        GROUP BY status
    ");
    $status_counts = $status_counts_stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Initialize counts
    $counts = [
        'all' => 0,
        'pending' => 0,
        'preparing' => 0,
        'ready' => 0,
        'completed' => 0,
        'cancelled' => 0
    ];
    
    foreach ($status_counts as $sc) {
        $counts[$sc['status']] = $sc['count'];
        $counts['all'] += $sc['count'];
    }
    
} catch (PDOException $e) {
    $error = "Failed to fetch order counts: " . $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Orders | Cafe & Netic</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <style>
        .order-status-pending {
            background-color: #fef3c7;
            color: #92400e;
        }
        
        .order-status-preparing {
            background-color: #bfdbfe;
            color: #1e40af;
        }
        
        .order-status-ready {
            background-color: #a7f3d0;
            color: #065f46;
        }
        
        .order-status-completed {
            background-color: #ddd6fe;
            color: #5b21b6;
        }
        
        .order-status-cancelled {
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
            <?php include '../includes/admin_topnav.php'; ?>
            
            <!-- Orders Content -->
            <main class="p-6">
                <div class="flex justify-between items-center mb-6">
                    <h1 class="text-2xl font-bold text-gray-800">Manage Orders</h1>
                </div>

                <?php if (isset($error)): ?>
                    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-6">
                        <?= htmlspecialchars($error) ?>
                    </div>
                <?php endif; ?>

                <!-- Status Filter Tabs -->
                <div class="mb-6 border-b border-gray-200">
                    <nav class="-mb-px flex space-x-8">
                        <a href="admin_orders.php" 
                           class="<?= $status_filter === 'all' ? 'border-amber-500 text-amber-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' ?> whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
                            All
                            <span class="bg-gray-100 text-gray-600 ml-1 py-0.5 px-2 rounded-full text-xs">
                                <?= $counts['all'] ?>
                            </span>
                        </a>
                        
                        <a href="admin_orders.php?status=pending" 
                           class="<?= $status_filter === 'pending' ? 'border-amber-500 text-amber-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' ?> whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
                            Pending
                            <span class="bg-gray-100 text-gray-600 ml-1 py-0.5 px-2 rounded-full text-xs">
                                <?= $counts['pending'] ?>
                            </span>
                        </a>
                        
                        <a href="admin_orders.php?status=preparing" 
                           class="<?= $status_filter === 'preparing' ? 'border-amber-500 text-amber-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' ?> whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
                            Preparing
                            <span class="bg-gray-100 text-gray-600 ml-1 py-0.5 px-2 rounded-full text-xs">
                                <?= $counts['preparing'] ?>
                            </span>
                        </a>
                        
                        <a href="admin_orders.php?status=ready" 
                           class="<?= $status_filter === 'ready' ? 'border-amber-500 text-amber-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' ?> whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
                            Ready
                            <span class="bg-gray-100 text-gray-600 ml-1 py-0.5 px-2 rounded-full text-xs">
                                <?= $counts['ready'] ?>
                            </span>
                        </a>
                        
                        <a href="admin_orders.php?status=completed" 
                           class="<?= $status_filter === 'completed' ? 'border-amber-500 text-amber-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' ?> whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
                            Completed
                            <span class="bg-gray-100 text-gray-600 ml-1 py-0.5 px-2 rounded-full text-xs">
                                <?= $counts['completed'] ?>
                            </span>
                        </a>
                        
                        <a href="admin_orders.php?status=cancelled" 
                           class="<?= $status_filter === 'cancelled' ? 'border-amber-500 text-amber-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' ?> whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
                            Cancelled
                            <span class="bg-gray-100 text-gray-600 ml-1 py-0.5 px-2 rounded-full text-xs">
                                <?= $counts['cancelled'] ?>
                            </span>
                        </a>
                    </nav>
                </div>

                <!-- Orders Table -->
                <div class="bg-white shadow rounded-lg overflow-hidden">
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Order ID</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Customer</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Amount</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                                    <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                <?php if (empty($orders)): ?>
                                    <tr>
                                        <td colspan="7" class="px-6 py-4 text-center text-sm text-gray-500">No orders found</td>
                                    </tr>
                                <?php else: ?>
                                    <?php foreach ($orders as $order): ?>
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">#<?= $order['order_id'] ?></td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm text-gray-900"><?= htmlspecialchars($order['customer_name']) ?></div>
                                            <div class="text-sm text-gray-500"><?= htmlspecialchars($order['phone']) ?></div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            <?= ucfirst(str_replace('_', ' ', $order['order_type'])) ?>
                                            <?php if ($order['order_type'] === 'dine_in' && !empty($order['table_number'])): ?>
                                                <div class="text-xs text-gray-400">Table <?= $order['table_number'] ?></div>
                                            <?php elseif ($order['order_type'] === 'takeaway' && !empty($order['location_name'])): ?>
                                                <div class="text-xs text-gray-400"><?= $order['location_name'] ?></div>
                                            <?php endif; ?>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            $<?= number_format($order['total_amount'], 2) ?>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="px-2 py-1 text-xs rounded-full order-status-<?= $order['status'] ?>">
                                                <?= ucfirst($order['status']) ?>
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            <?= date('M j, Y g:i A', strtotime($order['created_at'])) ?>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                            <a href="admin_order_details.php?id=<?= $order['order_id'] ?>" class="text-amber-600 hover:text-amber-900 mr-3">View</a>
                                            <?php if ($order['status'] !== 'completed' && $order['status'] !== 'cancelled'): ?> 
                                            <?php endif; ?>
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

    <!-- Status Update Modal -->
    <div x-data="{ 
        showModal: false, 
        orderId: null, 
        currentStatus: '',
        newStatus: '',
        openStatusModal(id, status) {
            this.orderId = id;
            this.currentStatus = status;
            this.newStatus = status;
            this.showModal = true;
        },
        async updateStatus() {
            if (!this.orderId || !this.newStatus) return;
            
            try {
                const response = await fetch('update_order_status.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({
                        order_id: this.orderId,
                        status: this.newStatus
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
                    <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">Update Order Status</h3>
                    
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Current Status</label>
                        <div class="px-2 py-1 inline-block rounded-full order-status-<%= currentStatus %>">
                            <%= currentStatus.charAt(0).toUpperCase() + currentStatus.slice(1) %>
                        </div>
                    </div>
                    
                    <div class="mb-4">
                        <label for="newStatus" class="block text-sm font-medium text-gray-700 mb-1">New Status</label>
                        <select id="newStatus" x-model="newStatus" class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-amber-500 focus:border-amber-500 sm:text-sm rounded-md">
                            <option value="pending">Pending</option>
                            <option value="preparing">Preparing</option>
                            <option value="ready">Ready</option>
                            <option value="completed">Completed</option>
                            <option value="cancelled">Cancelled</option>
                        </select>
                    </div>
                </div>
                <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                    <button type="button" @click="updateStatus()" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-amber-600 text-base font-medium text-white hover:bg-amber-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-amber-500 sm:ml-3 sm:w-auto sm:text-sm">
                        Update Status
                    </button>
                    <button type="button" @click="showModal = false" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-amber-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                        Cancel
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script>
        // This script is included for the Alpine.js modal functionality
    </script>
</body>
</html>
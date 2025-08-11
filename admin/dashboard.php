<?php
session_start();
require '../includes/config.php';

// Check if user is admin
if (!isset($_SESSION['logged_in'])) {
    header("Location: ../login.php");
    exit();
}

if ($_SESSION['role'] !== 'admin') {
    header("Location: ../index.php");
    exit();
}

// Get statistics data
$stats = [];
try {
    // Total users
    $stmt = $conn->query("SELECT COUNT(*) as total_users FROM users");
    $stats['total_users'] = $stmt->fetch(PDO::FETCH_ASSOC)['total_users'];
    
    // New users this month
    $stmt = $conn->query("SELECT COUNT(*) as new_users FROM users 
                         WHERE created_at >= DATE_SUB(NOW(), INTERVAL 1 MONTH)");
    $stats['new_users'] = $stmt->fetch(PDO::FETCH_ASSOC)['new_users'];
    
    // Users by role
    $stmt = $conn->query("SELECT role, COUNT(*) as count FROM users GROUP BY role");
    $stats['users_by_role'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Active vs inactive users
    $stmt = $conn->query("SELECT is_active, COUNT(*) as count FROM users GROUP BY is_active");
    $stats['users_by_status'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Recent users
    $stmt = $conn->query("SELECT user_id, first_name, last_name, email, created_at, role, is_active 
                         FROM users ORDER BY created_at DESC LIMIT 5");
    $stats['recent_users'] = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Total orders
    $stmt = $conn->query("SELECT COUNT(*) as total_orders FROM orders");
    $stats['total_orders'] = $stmt->fetch(PDO::FETCH_ASSOC)['total_orders'];
    
    // Total revenue
    $stmt = $conn->query("SELECT SUM(total_amount) as total_revenue FROM orders WHERE status != 'cancelled'");
    $stats['total_revenue'] = $stmt->fetch(PDO::FETCH_ASSOC)['total_revenue'] ?? 0;
    
    // Average order value
    $stats['avg_order_value'] = $stats['total_orders'] > 0 ? $stats['total_revenue'] / $stats['total_orders'] : 0;
    
    // Orders by status
    $stmt = $conn->query("SELECT status, COUNT(*) as count FROM orders GROUP BY status");
    $stats['orders_by_status'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Orders by type
    $stmt = $conn->query("SELECT order_type, COUNT(*) as count FROM orders GROUP BY order_type");
    $stats['orders_by_type'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Recent orders
    $stmt = $conn->query("SELECT o.*, l.name as location_name 
                         FROM orders o 
                         LEFT JOIN restaurant_locations l ON o.pickup_location_id = l.location_id 
                         ORDER BY created_at DESC LIMIT 5");
    $stats['recent_orders'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Popular products
    $stmt = $conn->query("SELECT p.name, SUM(oi.quantity) as total_quantity 
                         FROM order_items oi 
                         JOIN products p ON oi.product_id = p.product_id 
                         GROUP BY p.name 
                         ORDER BY total_quantity DESC LIMIT 5");
    $stats['popular_products'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Reservations
    $stmt = $conn->query("SELECT COUNT(*) as total_reservations FROM reservations");
    $stats['total_reservations'] = $stmt->fetch(PDO::FETCH_ASSOC)['total_reservations'];
    
    // Reservations by status
    $stmt = $conn->query("SELECT status, COUNT(*) as count FROM reservations GROUP BY status");
    $stats['reservations_by_status'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Reservations by location
    $stmt = $conn->query("SELECT l.name as location_name, COUNT(r.reservation_id) as count 
                         FROM reservations r
                         JOIN restaurant_tables t ON r.table_id = t.table_id
                         JOIN restaurant_locations l ON t.location_id = l.location_id
                         GROUP BY l.name");
    $stats['reservations_by_location'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Recent reservations
    $stmt = $conn->query("SELECT r.*, t.table_number as table_number, l.name as location_name
                         FROM reservations r
                         JOIN restaurant_tables t ON r.table_id = t.table_id
                         JOIN restaurant_locations l ON t.location_id = l.location_id
                         ORDER BY created_at DESC LIMIT 5");
    $stats['recent_reservations'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Revenue by month
    $stmt = $conn->query("SELECT 
                         DATE_FORMAT(created_at, '%Y-%m') as month, 
                         SUM(total_amount) as revenue 
                         FROM orders 
                         WHERE status != 'cancelled'
                         GROUP BY month 
                         ORDER BY month DESC 
                         LIMIT 6");
    $stats['revenue_by_month'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Product categories stats
    $stmt = $conn->query("SELECT c.name, COUNT(p.product_id) as product_count 
                         FROM categories c 
                         LEFT JOIN products p ON c.category_id = p.category_id 
                         GROUP BY c.name");
    $stats['categories'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Location stats
    $stmt = $conn->query("SELECT l.name, COUNT(o.order_id) as order_count 
                         FROM restaurant_locations l 
                         LEFT JOIN orders o ON l.location_id = o.pickup_location_id 
                         GROUP BY l.name");
    $stats['locations'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Table utilization
    $stmt = $conn->query("SELECT t.table_number, COUNT(r.reservation_id) as reservation_count 
                         FROM restaurant_tables t 
                         LEFT JOIN reservations r ON t.table_id = r.table_id 
                         WHERE r.reservation_date >= CURDATE() 
                         GROUP BY t.table_number 
                         ORDER BY reservation_count DESC 
                         LIMIT 5");
    $stats['table_utilization'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
} catch (PDOException $e) {
    error_log("Database error: " . $e->getMessage());
    $error = "Failed to load dashboard data";
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard | Cafe & Netic</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <style>
        .reservation-status-confirmed {
            background-color: #a7f3d0;
            color: #065f46;
        }
        
        .reservation-status-pending {
            background-color: #fef3c7;
            color: #92400e;
        }
        
        .reservation-status-cancelled {
            background-color: #fecaca;
            color: #991b1b;
        }
        
        .reservation-status-completed {
            background-color: #ddd6fe;
            color: #5b21b6;
        }
        
        .reservation-status-no-show {
            background-color: #d1d5db;
            color: #374151;
        }
        
        .user-role-admin {
            background-color: #ddd6fe;
            color: #5b21b6;
        }
        
        .user-role-customer {
            background-color: #bfdbfe;
            color: #1e40af;
        }
        
        .user-status-active {
            background-color: #a7f3d0;
            color: #065f46;
        }
        
        .user-status-inactive {
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
            
            <!-- Dashboard Content -->
            <main class="p-6">
                <?php if (isset($error)): ?>
                    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-6">
                        <?= htmlspecialchars($error) ?>
                    </div>
                <?php endif; ?>
                
                <!-- Stats Cards -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                    <!-- Total Users Card -->
                    <div class="stat-card bg-white rounded-xl shadow p-6">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm font-medium text-gray-500">Total Users</p>
                                <p class="text-3xl font-bold text-gray-800"><?= number_format($stats['total_users']) ?></p>
                            </div>
                            <div class="p-3 rounded-full bg-purple-100 text-purple-600">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                                </svg>
                            </div>
                        </div>
                        <div class="mt-4">
                            <p class="text-xs text-gray-500"><?= $stats['new_users'] ?> new this month</p>
                        </div>
                    </div>
                    
                    <!-- Total Orders Card -->
                    <div class="stat-card bg-white rounded-xl shadow p-6">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm font-medium text-gray-500">Total Orders</p>
                                <p class="text-3xl font-bold text-gray-800"><?= number_format($stats['total_orders']) ?></p>
                            </div>
                            <div class="p-3 rounded-full bg-green-100 text-green-600">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                                </svg>
                            </div>
                        </div>
                        <div class="mt-4">
                            <p class="text-xs text-gray-500">$<?= number_format($stats['total_revenue'], 2) ?> total revenue</p>
                        </div>
                    </div>
                    
                    <!-- Reservations Card -->
                    <div class="stat-card bg-white rounded-xl shadow p-6">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm font-medium text-gray-500">Reservations</p>
                                <p class="text-3xl font-bold text-gray-800"><?= number_format($stats['total_reservations']) ?></p>
                            </div>
                            <div class="p-3 rounded-full bg-pink-100 text-pink-600">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                </svg>
                            </div>
                        </div>
                        <div class="mt-4">
                            <p class="text-xs text-gray-500">Total bookings</p>
                        </div>
                    </div>
                    
                    <!-- Avg Order Value Card -->
                    <div class="stat-card bg-white rounded-xl shadow p-6">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm font-medium text-gray-500">Avg Order Value</p>
                                <p class="text-3xl font-bold text-gray-800">$<?= number_format($stats['avg_order_value'], 2) ?></p>
                            </div>
                            <div class="p-3 rounded-full bg-amber-100 text-amber-600">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                        </div>
                        <div class="mt-4">
                            <p class="text-xs text-gray-500">Based on <?= number_format($stats['total_orders']) ?> orders</p>
                        </div>
                    </div>
                </div>
                
                <!-- First Row of Charts -->
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
                    <!-- Revenue Chart -->
                    <div class="lg:col-span-2 bg-white rounded-xl shadow p-6">
                        <div class="flex justify-between items-center mb-6">
                            <h2 class="text-lg font-bold text-gray-800">Revenue Overview</h2>
                            <select class="text-sm border border-gray-300 rounded px-3 py-1 focus:outline-none focus:ring-2 focus:ring-amber-500">
                                <option>Last 6 Months</option>
                                <option>Last Year</option>
                                <option>All Time</option>
                            </select>
                        </div>
                        <div class="h-64">
                            <canvas id="revenueChart"></canvas>
                        </div>
                    </div>
                    
                    <!-- User Roles Chart -->
                    <div class="bg-white rounded-xl shadow p-6">
                        <h2 class="text-lg font-bold text-gray-800 mb-6">User Roles</h2>
                        <div class="h-64">
                            <canvas id="userRolesChart"></canvas>
                        </div>
                    </div>
                </div>
                
                <!-- Second Row of Charts -->
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
                    <!-- Order Status Chart -->
                    <div class="bg-white rounded-xl shadow p-6">
                        <h2 class="text-lg font-bold text-gray-800 mb-6">Order Status</h2>
                        <div class="h-64">
                            <canvas id="orderStatusChart"></canvas>
                        </div>
                    </div>
                    
                    <!-- Reservation Status Chart -->
                    <div class="bg-white rounded-xl shadow p-6">
                        <h2 class="text-lg font-bold text-gray-800 mb-6">Reservation Status</h2>
                        <div class="h-64">
                            <canvas id="reservationStatusChart"></canvas>
                        </div>
                    </div>
                    
                    <!-- Order Types Chart -->
                    <div class="bg-white rounded-xl shadow p-6">
                        <h2 class="text-lg font-bold text-gray-800 mb-6">Order Types</h2>
                        <div class="h-64">
                            <canvas id="orderTypesChart"></canvas>
                        </div>
                    </div>
                </div>
                
                <!-- Recent Activity -->
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                    <!-- Recent Users -->
                    <div class="bg-white rounded-xl shadow overflow-hidden">
                        <div class="px-6 py-4 border-b border-gray-200">
                            <h2 class="text-lg font-bold text-gray-800">Recent Users</h2>
                        </div>
                        <div class="divide-y divide-gray-200">
                            <?php foreach ($stats['recent_users'] as $user): ?>
                            <div class="p-4 hover:bg-gray-50">
                                <div class="flex justify-between items-start">
                                    <div>
                                        <p class="font-medium text-gray-800"><?= htmlspecialchars($user['first_name'] . ' ' . $user['last_name']) ?></p>
                                        <p class="text-sm text-gray-500"><?= htmlspecialchars($user['email']) ?></p>
                                    </div>
                                    <div class="flex flex-col items-end">
                                        <span class="px-2 py-1 text-xs rounded-full user-role-<?= $user['role'] ?> mb-1">
                                            <?= ucfirst($user['role']) ?>
                                        </span>
                                        <span class="px-2 py-1 text-xs rounded-full user-status-<?= $user['is_active'] ? 'active' : 'inactive' ?>">
                                            <?= $user['is_active'] ? 'Active' : 'Inactive' ?>
                                        </span>
                                    </div>
                                </div>
                                <div class="mt-2 text-sm text-gray-600">
                                    Joined <?= date('M j, Y', strtotime($user['created_at'])) ?>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        </div>
                        <div class="px-6 py-3 bg-gray-50 text-right">
                            <a href="admin_users.php" class="text-sm font-medium text-amber-600 hover:text-amber-700">View all users</a>
                        </div>
                    </div>
                    
                    <!-- Recent Orders -->
                    <div class="bg-white rounded-xl shadow overflow-hidden">
                        <div class="px-6 py-4 border-b border-gray-200">
                            <h2 class="text-lg font-bold text-gray-800">Recent Orders</h2>
                        </div>
                        <div class="divide-y divide-gray-200">
                            <?php foreach ($stats['recent_orders'] as $order): ?>
                            <div class="p-4 hover:bg-gray-50">
                                <div class="flex justify-between items-start">
                                    <div>
                                        <p class="font-medium text-gray-800">Order #<?= $order['order_id'] ?></p>
                                        <p class="text-sm text-gray-500"><?= htmlspecialchars($order['customer_name']) ?></p>
                                    </div>
                                    <div>
                                        <span class="px-2 py-1 text-xs rounded-full order-status-<?= $order['status'] ?>">
                                            <?= ucfirst($order['status']) ?>
                                        </span>
                                    </div>
                                </div>
                                <div class="mt-2 flex justify-between items-center">
                                    <p class="text-sm text-gray-600">
                                        <?= date('M j, Y g:i A', strtotime($order['created_at'])) ?>
                                    </p>
                                    <p class="font-medium text-amber-700">
                                        $<?= number_format($order['total_amount'], 2) ?>
                                    </p>
                                </div>
                                <?php if ($order['location_name']): ?>
                                <div class="mt-2 text-xs text-gray-500 flex items-center">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M5.05 4.05a7 7 0 119.9 9.9L10 18.9l-4.95-4.95a7 7 0 010-9.9zM10 11a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd" />
                                    </svg>
                                    <?= htmlspecialchars($order['location_name']) ?>
                                </div>
                                <?php endif; ?>
                            </div>
                            <?php endforeach; ?>
                        </div>
                        <div class="px-6 py-3 bg-gray-50 text-right">
                            <a href="admin_orders.php" class="text-sm font-medium text-amber-600 hover:text-amber-700">View all orders</a>
                        </div>
                    </div>
                    
                    <!-- Recent Reservations -->
                    <div class="bg-white rounded-xl shadow overflow-hidden">
                        <div class="px-6 py-4 border-b border-gray-200">
                            <h2 class="text-lg font-bold text-gray-800">Recent Reservations</h2>
                        </div>
                        <div class="divide-y divide-gray-200">
                            <?php foreach ($stats['recent_reservations'] as $reservation): ?>
                            <div class="p-4 hover:bg-gray-50">
                                <div class="flex justify-between items-start">
                                    <div>
                                        <p class="font-medium text-gray-800"><?= htmlspecialchars($reservation['customer_name']) ?></p>
                                        <p class="text-sm text-gray-500"><?= htmlspecialchars($reservation['table_number']) ?></p>
                                    </div>
                                    <div>
                                        <span class="px-2 py-1 text-xs rounded-full reservation-status-<?= $reservation['status'] ?>">
                                            <?= ucfirst($reservation['status']) ?>
                                        </span>
                                    </div>
                                </div>
                                <div class="mt-2 flex justify-between items-center">
                                    <p class="text-sm text-gray-600">
                                        <?= date('M j, Y', strtotime($reservation['reservation_date'])) ?>
                                        at <?= date('g:i A', strtotime($reservation['start_time'])) ?>
                                    </p>
                                    <p class="text-sm font-medium text-gray-800">
                                        <?= $reservation['guests'] ?> <?= $reservation['guests'] > 1 ? 'guests' : 'guest' ?>
                                    </p>
                                </div>
                                <div class="mt-2 text-xs text-gray-500 flex items-center">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M5.05 4.05a7 7 0 119.9 9.9L10 18.9l-4.95-4.95a7 7 0 010-9.9zM10 11a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd" />
                                    </svg>
                                    <?= htmlspecialchars($reservation['location_name']) ?>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        </div>
                        <div class="px-6 py-3 bg-gray-50 text-right">
                            <a href="admin_reservations.php" class="text-sm font-medium text-amber-600 hover:text-amber-700">View all reservations</a>
                        </div>
                    </div>
                </div>
                
                <!-- Popular Items and Locations -->
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mt-6">
                    <!-- Popular Products -->
                    <div class="bg-white rounded-xl shadow overflow-hidden">
                        <div class="px-6 py-4 border-b border-gray-200">
                            <h2 class="text-lg font-bold text-gray-800">Popular Products</h2>
                        </div>
                        <div class="divide-y divide-gray-200">
                            <?php foreach ($stats['popular_products'] as $product): ?>
                            <div class="p-4 hover:bg-gray-50">
                                <div class="flex justify-between items-center">
                                    <p class="font-medium text-gray-800"><?= htmlspecialchars($product['name']) ?></p>
                                    <p class="text-sm bg-amber-100 text-amber-800 px-2 py-1 rounded-full">
                                        <?= $product['total_quantity'] ?> sold
                                    </p>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        </div>
                        <div class="px-6 py-3 bg-gray-50 text-right">
                            <a href="admin_products.php" class="text-sm font-medium text-amber-600 hover:text-amber-700">View all products</a>
                        </div>
                    </div>
                    
                    <!-- Table Utilization -->
                    <div class="bg-white rounded-xl shadow overflow-hidden">
                        <div class="px-6 py-4 border-b border-gray-200">
                            <h2 class="text-lg font-bold text-gray-800">Top Booked Tables</h2>
                        </div>
                        <div class="divide-y divide-gray-200">
                            <?php foreach ($stats['table_utilization'] as $table): ?>
                            <div class="p-4 hover:bg-gray-50">
                                <div class="flex justify-between items-center">
                                    <p class="font-medium text-gray-800"><?= htmlspecialchars($table['name']) ?></p>
                                    <p class="text-sm bg-blue-100 text-blue-800 px-2 py-1 rounded-full">
                                        <?= $table['reservation_count'] ?> bookings
                                    </p>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        </div>
                        <div class="px-6 py-3 bg-gray-50 text-right">
                            <a href="admin_tables.php" class="text-sm font-medium text-amber-600 hover:text-amber-700">Manage tables</a>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>
    
    <script>
        // Revenue Chart
        const revenueCtx = document.getElementById('revenueChart').getContext('2d');
        const revenueChart = new Chart(revenueCtx, {
            type: 'line',
            data: {
                labels: <?= json_encode(array_map(function($item) { 
                    return date('M Y', strtotime($item['month'] . '-01')); 
                }, array_reverse($stats['revenue_by_month']))) ?>,
                datasets: [{
                    label: 'Revenue',
                    data: <?= json_encode(array_map(function($item) { 
                        return $item['revenue']; 
                    }, array_reverse($stats['revenue_by_month']))) ?>,
                    backgroundColor: 'rgba(251, 191, 36, 0.1)',
                    borderColor: 'rgba(251, 191, 36, 1)',
                    borderWidth: 2,
                    tension: 0.3,
                    fill: true
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: {
                            drawBorder: false
                        },
                        ticks: {
                            callback: function(value) {
                                return '$' + value.toLocaleString();
                            }
                        }
                    },
                    x: {
                        grid: {
                            display: false
                        }
                    }
                }
            }
        });
        
        // User Roles Chart
        const userRolesCtx = document.getElementById('userRolesChart').getContext('2d');
        const userRolesChart = new Chart(userRolesCtx, {
            type: 'doughnut',
            data: {
                labels: <?= json_encode(array_map(function($item) { 
                    return ucfirst($item['role']); 
                }, $stats['users_by_role'])) ?>,
                datasets: [{
                    data: <?= json_encode(array_map(function($item) { 
                        return $item['count']; 
                    }, $stats['users_by_role'])) ?>,
                    backgroundColor: [
                        'rgba(221, 214, 254, 1)',
                        'rgba(191, 219, 254, 1)'
                    ],
                    borderColor: [
                        'rgba(91, 33, 182, 1)',
                        'rgba(29, 78, 216, 1)'
                    ],
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'right'
                    }
                },
                cutout: '70%'
            }
        });
        
        // Order Status Chart
        const orderStatusCtx = document.getElementById('orderStatusChart').getContext('2d');
        const orderStatusChart = new Chart(orderStatusCtx, {
            type: 'doughnut',
            data: {
                labels: <?= json_encode(array_map(function($item) { 
                    return ucfirst($item['status']); 
                }, $stats['orders_by_status'])) ?>,
                datasets: [{
                    data: <?= json_encode(array_map(function($item) { 
                        return $item['count']; 
                    }, $stats['orders_by_status'])) ?>,
                    backgroundColor: [
                        'rgba(254, 243, 199, 1)',
                        'rgba(191, 219, 254, 1)',
                        'rgba(167, 243, 208, 1)',
                        'rgba(221, 214, 254, 1)',
                        'rgba(254, 202, 202, 1)'
                    ],
                    borderColor: [
                        'rgba(217, 119, 6, 1)',
                        'rgba(29, 78, 216, 1)',
                        'rgba(6, 95, 70, 1)',
                        'rgba(91, 33, 182, 1)',
                        'rgba(153, 27, 27, 1)'
                    ],
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'right'
                    }
                },
                cutout: '70%'
            }
        });
        
        // Reservation Status Chart
        const reservationStatusCtx = document.getElementById('reservationStatusChart').getContext('2d');
        const reservationStatusChart = new Chart(reservationStatusCtx, {
            type: 'pie',
            data: {
                labels: <?= json_encode(array_map(function($item) { 
                    return ucfirst($item['status']); 
                }, $stats['reservations_by_status'])) ?>,
                datasets: [{
                    data: <?= json_encode(array_map(function($item) { 
                        return $item['count']; 
                    }, $stats['reservations_by_status'])) ?>,
                    backgroundColor: [
                        'rgba(167, 243, 208, 1)',
                        'rgba(254, 243, 199, 1)',
                        'rgba(254, 202, 202, 1)',
                        'rgba(221, 214, 254, 1)',
                        'rgba(209, 213, 219, 1)'
                    ],
                    borderColor: [
                        'rgba(6, 95, 70, 1)',
                        'rgba(217, 119, 6, 1)',
                        'rgba(153, 27, 27, 1)',
                        'rgba(91, 33, 182, 1)',
                        'rgba(55, 65, 81, 1)'
                    ],
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'right'
                    }
                }
            }
        });
        
        // Order Types Chart
        const orderTypesCtx = document.getElementById('orderTypesChart').getContext('2d');
        const orderTypesChart = new Chart(orderTypesCtx, {
            type: 'bar',
            data: {
                labels: <?= json_encode(array_map(function($item) { 
                    return ucfirst(str_replace('_', ' ', $item['order_type'])); 
                }, $stats['orders_by_type'])) ?>,
                datasets: [{
                    label: 'Orders',
                    data: <?= json_encode(array_map(function($item) { 
                        return $item['count']; 
                    }, $stats['orders_by_type'])) ?>,
                    backgroundColor: 'rgba(139, 92, 246, 0.7)',
                    borderColor: 'rgba(139, 92, 246, 1)',
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: {
                            drawBorder: false
                        },
                        ticks: {
                            precision: 0
                        }
                    },
                    x: {
                        grid: {
                            display: false
                        }
                    }
                }
            }
        });
    </script>
</body>
</html>
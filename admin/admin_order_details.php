<?php
session_start();
require '../includes/config.php';

// Check if user is admin
if (!isset($_SESSION['logged_in']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit();
}

// Get order ID from URL
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: admin_orders.php");
    exit();
}

$order_id = (int)$_GET['id'];

// Fetch order details
try {
    $order_stmt = $conn->prepare("
        SELECT o.*, l.name as location_name 
        FROM orders o 
        LEFT JOIN restaurant_locations l ON o.pickup_location_id = l.location_id
        WHERE o.order_id = :order_id
    ");
    $order_stmt->bindParam(':order_id', $order_id);
    $order_stmt->execute();
    $order = $order_stmt->fetch(PDO::FETCH_ASSOC);

    if (!$order) {
        header("Location: admin_orders.php");
        exit();
    }

    // Fetch order items
    $items_stmt = $conn->prepare("
        SELECT oi.*, p.name as product_name, p.image
        FROM order_items oi
        JOIN products p ON oi.product_id = p.product_id
        WHERE oi.order_id = :order_id
    ");
    $items_stmt->bindParam(':order_id', $order_id);
    $items_stmt->execute();
    $order_items = $items_stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    $error = "Failed to fetch order details: " . $e->getMessage();
}

// Handle status update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_status'])) {
    $new_status = $_POST['status'];
    $valid_statuses = ['pending', 'preparing', 'ready', 'completed', 'cancelled'];

    if (in_array($new_status, $valid_statuses)) {
        try {
            $update_stmt = $conn->prepare("
                UPDATE orders 
                SET status = :status 
                WHERE order_id = :order_id
            ");
            $update_stmt->bindParam(':status', $new_status);
            $update_stmt->bindParam(':order_id', $order_id);
            $update_stmt->execute();

            // Refresh order data
            $order_stmt->execute();
            $order = $order_stmt->fetch(PDO::FETCH_ASSOC);

            $success = "Order status updated successfully!";
        } catch (PDOException $e) {
            $error = "Failed to update order status: " . $e->getMessage();
        }
    } else {
        $error = "Invalid status selected";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Details | Cafe & Netic</title>
    <script src="https://cdn.tailwindcss.com"></script>
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
            
            <!-- Order Details Content -->
            <main class="p-6">
                <div class="flex justify-between items-center mb-6">
                    <div>
                        <h1 class="text-2xl font-bold text-gray-800">Order Details</h1>
                        <nav class="flex" aria-label="Breadcrumb">
                            <ol class="inline-flex items-center space-x-1 md:space-x-2">
                                <li class="inline-flex items-center">
                                    <a href="admin_orders.php" class="inline-flex items-center text-sm font-medium text-gray-700 hover:text-amber-600">
                                        Orders
                                    </a>
                                </li>
                                <li aria-current="page">
                                    <div class="flex items-center">
                                        <svg class="w-6 h-6 text-gray-400" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path></svg>
                                        <span class="ml-1 text-sm font-medium text-gray-500 md:ml-2">#<?= $order['order_id'] ?></span>
                                    </div>
                                </li>
                            </ol>
                        </nav>
                    </div>
                    <a href="admin_orders.php" class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md shadow-sm text-white bg-amber-600 hover:bg-amber-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-amber-500">
                        Back to Orders
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

                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                    <!-- Order Summary -->
                    <div class="lg:col-span-2">
                        <div class="bg-white shadow rounded-lg overflow-hidden">
                            <div class="px-6 py-4 border-b border-gray-200">
                                <h2 class="text-lg font-medium text-gray-900">Order Summary</h2>
                            </div>
                            <div class="px-6 py-4">
                                <div class="flex justify-between mb-4">
                                    <div>
                                        <h3 class="text-sm font-medium text-gray-500">Order ID</h3>
                                        <p class="mt-1 text-sm text-gray-900">#<?= $order['order_id'] ?></p>
                                    </div>
                                    <div>
                                        <h3 class="text-sm font-medium text-gray-500">Date</h3>
                                        <p class="mt-1 text-sm text-gray-900"><?= date('M j, Y g:i A', strtotime($order['created_at'])) ?></p>
                                    </div>
                                    <div>
                                        <h3 class="text-sm font-medium text-gray-500">Status</h3>
                                        <p class="mt-1">
                                            <span class="px-2 py-1 text-xs rounded-full order-status-<?= $order['status'] ?>">
                                                <?= ucfirst($order['status']) ?>
                                            </span>
                                        </p>
                                    </div>
                                </div>

                                <div class="border-t border-gray-200 pt-4">
                                    <h3 class="text-sm font-medium text-gray-500">Order Type</h3>
                                    <p class="mt-1 text-sm text-gray-900">
                                        <?= ucfirst(str_replace('_', ' ', $order['order_type'])) ?>
                                        <?php if ($order['order_type'] === 'dine_in' && !empty($order['table_number'])): ?>
                                            <span class="text-gray-500">(Table <?= $order['table_number'] ?>)</span>
                                        <?php elseif ($order['order_type'] === 'takeaway' && !empty($order['location_name'])): ?>
                                            <span class="text-gray-500">(<?= $order['location_name'] ?>)</span>
                                        <?php elseif ($order['order_type'] === 'delivery' && !empty($order['delivery_address'])): ?>
                                            <span class="text-gray-500">(Delivery)</span>
                                        <?php endif; ?>
                                    </p>
                                </div>

                                <?php if (!empty($order['special_instructions'])): ?>
                                    <div class="border-t border-gray-200 pt-4">
                                        <h3 class="text-sm font-medium text-gray-500">Special Instructions</h3>
                                        <p class="mt-1 text-sm text-gray-900"><?= nl2br(htmlspecialchars($order['special_instructions'])) ?></p>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>

                        <!-- Order Items -->
                        <div class="bg-white shadow rounded-lg overflow-hidden mt-6">
                            <div class="px-6 py-4 border-b border-gray-200">
                                <h2 class="text-lg font-medium text-gray-900">Order Items</h2>
                            </div>
                            <div class="divide-y divide-gray-200">
                                <?php if (empty($order_items)): ?>
                                    <div class="px-6 py-4 text-center text-sm text-gray-500">
                                        No items found in this order
                                    </div>
                                <?php else: ?>
                                    <?php foreach ($order_items as $item): ?>
                                    <div class="px-6 py-4">
                                        <div class="flex items-start">
                                            <?php if (!empty($item['image'])): ?>
                                                <div class="flex-shrink-0 h-16 w-16 rounded-md overflow-hidden">
                                                    <img src="../uploads/products/<?= htmlspecialchars($item['image']) ?>" alt="<?= htmlspecialchars($item['product_name']) ?>" class="h-full w-full object-cover">
                                                </div>
                                            <?php endif; ?>
                                            <div class="ml-4 flex-1">
                                                <div class="flex items-center justify-between">
                                                    <h3 class="text-sm font-medium text-gray-900">
                                                        <?= htmlspecialchars($item['product_name']) ?>
                                                    </h3>
                                                    <p class="ml-4 text-sm font-medium text-gray-900">
                                                        $<?= number_format($item['price'] * $item['quantity'], 2) ?>
                                                    </p>
                                                </div>
                                                <p class="mt-1 text-sm text-gray-500">
                                                    $<?= number_format($item['price'], 2) ?> × <?= $item['quantity'] ?>
                                                </p>
                                                <?php if (!empty($item['special_instructions'])): ?>
                                                    <p class="mt-1 text-xs text-gray-500">
                                                        <span class="font-medium">Note:</span> <?= htmlspecialchars($item['special_instructions']) ?>
                                                    </p>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </div>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </div>
                            <div class="px-6 py-4 border-t border-gray-200">
                                <div class="flex justify-between text-base font-medium text-gray-900">
                                    <p>Total</p>
                                    <p>$<?= number_format($order['total_amount'], 2) ?></p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Customer & Actions -->
                    <div>
                        <!-- Customer Info -->
                        <div class="bg-white shadow rounded-lg overflow-hidden">
                            <div class="px-6 py-4 border-b border-gray-200">
                                <h2 class="text-lg font-medium text-gray-900">Customer Information</h2>
                            </div>
                            <div class="px-6 py-4">
                                <div class="mb-4">
                                    <h3 class="text-sm font-medium text-gray-500">Name</h3>
                                    <p class="mt-1 text-sm text-gray-900"><?= htmlspecialchars($order['customer_name']) ?></p>
                                </div>
                                <div class="mb-4">
                                    <h3 class="text-sm font-medium text-gray-500">Phone</h3>
                                    <p class="mt-1 text-sm text-gray-900"><?= htmlspecialchars($order['phone']) ?></p>
                                </div>
                                <?php if (!empty($order['email'])): ?>
                                    <div class="mb-4">
                                        <h3 class="text-sm font-medium text-gray-500">Email</h3>
                                        <p class="mt-1 text-sm text-gray-900"><?= htmlspecialchars($order['email']) ?></p>
                                    </div>
                                <?php endif; ?>
                                <?php if ($order['order_type'] === 'delivery' && !empty($order['delivery_address'])): ?>
                                    <div>
                                        <h3 class="text-sm font-medium text-gray-500">Delivery Address</h3>
                                        <p class="mt-1 text-sm text-gray-900"><?= nl2br(htmlspecialchars($order['delivery_address'])) ?></p>
                                        <?php if (!empty($order['delivery_time'])): ?>
                                            <p class="mt-1 text-sm text-gray-900">
                                                <span class="font-medium">Delivery Time:</span> <?= htmlspecialchars($order['delivery_time']) ?>
                                            </p>
                                        <?php endif; ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>

                        <!-- Order Actions -->
                        <div class="bg-white shadow rounded-lg overflow-hidden mt-6">
                            <div class="px-6 py-4 border-b border-gray-200">
                                <h2 class="text-lg font-medium text-gray-900">Order Actions</h2>
                            </div>
                            <div class="px-6 py-4">
                                <form method="POST">
                                    <div class="mb-4">
                                        <label for="status" class="block text-sm font-medium text-gray-700 mb-1">Update Status</label>
                                        <select id="status" name="status" class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-amber-500 focus:border-amber-500 sm:text-sm rounded-md">
                                            <option value="pending" <?= $order['status'] === 'pending' ? 'selected' : '' ?>>Pending</option>
                                            <option value="preparing" <?= $order['status'] === 'preparing' ? 'selected' : '' ?>>Preparing</option>
                                            <option value="ready" <?= $order['status'] === 'ready' ? 'selected' : '' ?>>Ready</option>
                                            <option value="completed" <?= $order['status'] === 'completed' ? 'selected' : '' ?>>Completed</option>
                                            <option value="cancelled" <?= $order['status'] === 'cancelled' ? 'selected' : '' ?>>Cancelled</option>
                                        </select>
                                    </div>
                                    <button type="submit" name="update_status" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-amber-600 text-base font-medium text-white hover:bg-amber-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-amber-500 sm:text-sm">
                                        Update Order Status
                                    </button>
                                </form>
                            </div>
                        </div>

                        <!-- Payment Info -->
                        <div class="bg-white shadow rounded-lg overflow-hidden mt-6">
                            <div class="px-6 py-4 border-b border-gray-200">
                                <h2 class="text-lg font-medium text-gray-900">Payment Information</h2>
                            </div>
                            <div class="px-6 py-4">
                                <div class="mb-4">
                                    <h3 class="text-sm font-medium text-gray-500">Payment Method</h3>
                                    <p class="mt-1 text-sm text-gray-900"><?= ucfirst($order['payment_method']) ?></p>
                                </div>
                                <div>
                                    <h3 class="text-sm font-medium text-gray-500">Total Amount</h3>
                                    <p class="mt-1 text-lg font-medium text-gray-900">$<?= number_format($order['total_amount'], 2) ?></p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>
</body>
</html>
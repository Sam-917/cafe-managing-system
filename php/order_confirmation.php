<?php
session_start();
require '../includes/config.php';

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: index.php");
    exit();
}

$order_id = (int)$_GET['id'];

// Get order details
$stmt = $conn->prepare("SELECT o.*, l.name as location_name, l.address as location_address
                      FROM orders o
                      LEFT JOIN restaurant_locations l ON o.pickup_location_id = l.location_id
                      WHERE o.order_id = ?");
$stmt->execute([$order_id]);
$order = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$order) {
    header("Location: index.php");
    exit();
}

// Get order items
$stmt = $conn->prepare("SELECT oi.*, p.name as product_name
                      FROM order_items oi
                      JOIN products p ON oi.product_id = p.product_id
                      WHERE oi.order_id = ?");
$stmt->execute([$order_id]);
$items = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Confirmation | Cafe & Netic</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;700&family=Inter:wght@300;400;500;600&display=swap');
        
        .font-playfair { font-family: 'Playfair Display', serif; }
        .font-inter { font-family: 'Inter', sans-serif; }
    </style>
</head>
<body class="font-inter bg-gradient-to-br from-amber-50 to-orange-100 min-h-screen">
    <!-- Navigation Bar -->
    <nav class="bg-white/90 backdrop-blur-md shadow-lg sticky top-0 z-50 transition-all duration-300">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-16">
                <!-- Logo -->
                <div class="flex items-center space-x-2">
                    <div class="text-2xl">☕</div>
                    <span class="font-playfair text-2xl font-bold text-amber-800">Cafe & Netic</span>
                </div>
                
                <!-- Navigation Links --> 
                <div class="hidden md:flex space-x-8 items-center">
                    <a href="index.php" class="nav-item text-gray-700 hover:text-amber-700 font-medium px-3 py-2">Home</a>
                    <a href="menu.php" class="nav-item text-gray-700 hover:text-amber-700 font-medium px-3 py-2">Menu</a>
                    <?php if (isset($_SESSION['logged_in']) && $_SESSION['logged_in']): ?>
                        <!-- User Profile Dropdown -->
                        <div class="relative group">
                            <button class="flex items-center space-x-2 focus:outline-none">
                                <span class="font-medium text-gray-700"><?= htmlspecialchars($_SESSION['first_name'] ?? 'User') ?></span>
                                <div class="w-8 h-8 rounded-full bg-amber-600 flex items-center justify-center text-white font-semibold">
                                    <?= strtoupper(substr($_SESSION['first_name'] ?? 'U', 0, 1)) ?>
                                </div>
                            </button>
                        </div>
                    <?php else: ?>
                        <a href="login.php" class="nav-item text-gray-700 hover:text-amber-700 font-medium px-3 py-2">Sign In</a>
                        <a href="register.php" class="bg-gradient-to-r from-amber-600 to-orange-600 text-white px-6 py-2 rounded-full hover:from-amber-700 hover:to-orange-700 transform hover:scale-105 transition-all duration-300 shadow-lg">Join Us</a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </nav>

    <!-- Confirmation Content -->
    <div class="max-w-4xl mx-auto px-4 py-12">
        <div class="bg-white rounded-xl shadow-lg p-8 text-center mb-8">
            <div class="text-6xl mb-4">🎉</div>
            <h1 class="font-playfair text-4xl font-bold text-amber-800 mb-4">Order Confirmed!</h1>
            <p class="text-xl text-gray-600 mb-6">Thank you for your order, <?= htmlspecialchars($order['customer_name']) ?>!</p>
            <p class="text-lg">Your order #<?= $order_id ?> has been received and is being prepared.</p>
        </div>
        
        <div class="bg-white rounded-xl shadow-lg p-8 mb-8">
            <h2 class="font-playfair text-2xl font-bold text-amber-800 mb-6 border-b border-amber-200 pb-2">Order Summary</h2>
            
            <div class="grid md:grid-cols-2 gap-6 mb-8">
                <div>
                    <h3 class="font-semibold text-lg text-gray-800 mb-2">Customer Information</h3>
                    <p><span class="font-medium">Name:</span> <?= htmlspecialchars($order['customer_name']) ?></p>
                    <p><span class="font-medium">Phone:</span> <?= htmlspecialchars($order['phone']) ?></p>
                    <?php if ($order['email']): ?>
                        <p><span class="font-medium">Email:</span> <?= htmlspecialchars($order['email']) ?></p>
                    <?php endif; ?>
                </div>
                
                <div>
                    <h3 class="font-semibold text-lg text-gray-800 mb-2">Order Details</h3>
                    <p><span class="font-medium">Order Type:</span> <?= ucfirst(str_replace('_', ' ', $order['order_type'])) ?></p>
                    <p><span class="font-medium">Payment Method:</span> <?= ucfirst($order['payment_method']) ?></p>
                    <p><span class="font-medium">Order Date:</span> <?= date('F j, Y g:i A', strtotime($order['created_at'])) ?></p>
                    
                    <?php if ($order['order_type'] == 'dine_in'): ?>
                        <p><span class="font-medium">Table Number:</span> <?= $order['table_number'] ?></p>
                    <?php elseif ($order['order_type'] == 'takeaway'): ?>
                        <p><span class="font-medium">Pickup Location:</span> <?= htmlspecialchars($order['location_name']) ?></p>
                        <p><span class="font-medium">Pickup Address:</span> <?= htmlspecialchars($order['location_address']) ?></p>
                    <?php else: ?>
                        <p><span class="font-medium">Delivery Address:</span> <?= htmlspecialchars($order['delivery_address']) ?></p>
                    <?php endif; ?>
                </div>
            </div>
            
            <h3 class="font-semibold text-lg text-gray-800 mb-4">Order Items</h3>
            <div class="border border-gray-200 rounded-lg overflow-hidden">
                <table class="w-full">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-3 text-left text-sm font-medium text-gray-700">Item</th>
                            <th class="px-4 py-3 text-right text-sm font-medium text-gray-700">Qty</th>
                            <th class="px-4 py-3 text-right text-sm font-medium text-gray-700">Price</th>
                            <th class="px-4 py-3 text-right text-sm font-medium text-gray-700">Subtotal</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        <?php foreach ($items as $item): ?>
                        <tr>
                            <td class="px-4 py-3 text-sm text-gray-800">
                                <?= htmlspecialchars($item['product_name']) ?>
                                <?php if ($item['special_instructions']): ?>
                                    <div class="text-xs text-gray-500">Note: <?= htmlspecialchars($item['special_instructions']) ?></div>
                                <?php endif; ?>
                            </td>
                            <td class="px-4 py-3 text-sm text-gray-800 text-right"><?= $item['quantity'] ?></td>
                            <td class="px-4 py-3 text-sm text-gray-800 text-right">$<?= number_format($item['price'], 2) ?></td>
                            <td class="px-4 py-3 text-sm text-gray-800 text-right">$<?= number_format($item['price'] * $item['quantity'], 2) ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                    <tfoot class="bg-gray-50">
                        <tr>
                            <td colspan="3" class="px-4 py-3 text-right text-sm font-medium text-gray-700">Total</td>
                            <td class="px-4 py-3 text-right text-sm font-bold text-amber-700">$<?= number_format($order['total_amount'], 2) ?></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
            
            <?php if ($order['special_instructions']): ?>
                <div class="mt-6">
                    <h4 class="font-semibold text-gray-800 mb-2">Special Instructions</h4>
                    <p class="text-gray-600"><?= htmlspecialchars($order['special_instructions']) ?></p>
                </div>
            <?php endif; ?>
        </div>
        
        <div class="text-center">
            <a href="index.php" class="inline-block bg-gradient-to-r from-amber-600 to-orange-600 text-white px-8 py-3 rounded-full text-lg font-semibold hover:from-amber-700 hover:to-orange-700 transform hover:scale-105 transition-all duration-300 shadow-xl">
                Back to Home
            </a>
        </div>
    </div>

    <!-- Footer -->
    <footer class="bg-amber-900 text-white py-12 px-4">
        <div class="max-w-6xl mx-auto text-center">
            <div class="flex items-center justify-center space-x-2 mb-6">
                <div class="text-3xl">☕</div>
                <span class="font-playfair text-3xl font-bold">Cafe & Netic</span>
            </div>
            <p class="text-amber-200 mb-6">123 Coffee Street, Bean City | Open Daily 6AM - 10PM</p>
            <div class="flex justify-center space-x-6 text-2xl">
                <span class="hover:text-amber-300 cursor-pointer transition-colors">📧</span>
                <span class="hover:text-amber-300 cursor-pointer transition-colors">📱</span>
                <span class="hover:text-amber-300 cursor-pointer transition-colors">📍</span>
            </div>
        </div>
    </footer>
</body>
</html>
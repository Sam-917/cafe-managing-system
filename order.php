<?php
include 'includes/header.php';
require 'includes/config.php';

// Check order type
$valid_types = ['dine_in', 'takeaway', 'delivery'];
$order_type = isset($_GET['type']) && in_array($_GET['type'], $valid_types) ? $_GET['type'] : 'dine_in';

// Get all active locations
$stmt = $conn->query("SELECT * FROM restaurant_locations WHERE is_active = 1 ORDER BY name");
$locations = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Get available tables for dine-in if location is selected
$available_tables = [];
$selected_location_id = isset($_POST['location_id']) ? (int)$_POST['location_id'] : null;

if ($order_type == 'dine_in' && $selected_location_id) {
    $stmt = $conn->prepare("SELECT * FROM restaurant_tables WHERE location_id = ? AND is_active = 1 ORDER BY table_number");
    $stmt->execute([$selected_location_id]);
    $available_tables = $stmt->fetchAll(PDO::FETCH_ASSOC);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Place Your Order</title>
    <link href="assets/css/order.css" rel="stylesheet">
</head>
<body>
    <div class="max-w-4xl mx-auto px-4 py-12">
        <!-- Order Type Banner -->
        <div class="bg-gradient-to-r from-amber-600 to-orange-600 text-white rounded-xl p-8 mb-8 text-center">
            <h1 class="font-playfair text-3xl font-bold mb-2">Place Your <?= ucfirst(str_replace('_', ' ', $order_type)) ?> Order</h1>
            <p class="text-lg opacity-90">
                <?= $order_type == 'dine_in' ? 'Enjoy your meal in our restaurant' : 
                   ($order_type == 'takeaway' ? 'Pick up your order at your convenience' : 
                   'Get your order delivered to your doorstep') ?>
            </p>
            
            <!-- Order Type Switcher -->
            <div class="flex justify-center mt-6 space-x-4">
                <a href="order.php?type=dine_in" class="px-4 py-2 rounded-full <?= $order_type == 'dine_in' ? 'bg-white text-amber-700' : 'bg-white/20 text-white' ?>">
                    Dine In
                </a>
                <a href="order.php?type=takeaway" class="px-4 py-2 rounded-full <?= $order_type == 'takeaway' ? 'bg-white text-amber-700' : 'bg-white/20 text-white' ?>">
                    Takeaway
                </a>
                <a href="order.php?type=delivery" class="px-4 py-2 rounded-full <?= $order_type == 'delivery' ? 'bg-white text-amber-700' : 'bg-white/20 text-white' ?>">
                    Delivery
                </a>
            </div>
        </div>
        
        <form action="process_order.php" method="post" class="space-y-8">
            <input type="hidden" name="order_type" value="<?= $order_type ?>">
            
            <!-- Customer Information -->
            <div class="bg-white rounded-xl shadow-lg p-6">
                <h2 class="font-playfair text-2xl font-bold mb-6 pb-2 border-b border-amber-200">Customer Information</h2>
                
                <div class="grid md:grid-cols-2 gap-6">
                    <div>
                        <label for="customer_name" class="block text-gray-700 font-medium mb-2">Full Name</label>
                        <input type="text" id="customer_name" name="customer_name" 
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-amber-500" required>
                    </div>
                    
                    <div>
                        <label for="phone" class="block text-gray-700 font-medium mb-2">Phone Number</label>
                        <input type="tel" id="phone" name="phone" 
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-amber-500" required>
                    </div>
                    
                    <div>
                        <label for="email" class="block text-gray-700 font-medium mb-2">Email (optional)</label>
                        <input type="email" id="email" name="email" 
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-amber-500">
                    </div>
                    
                    <?php if ($order_type == 'delivery'): ?>
                    <div>
                        <label for="delivery_address" class="block text-gray-700 font-medium mb-2">Delivery Address</label>
                        <input type="text" id="delivery_address" name="delivery_address" 
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-amber-500" required>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
            
            <!-- Order Details Section -->
            <div class="bg-white rounded-xl shadow-lg p-6">
                <h2 class="font-playfair text-2xl font-bold mb-6 pb-2 border-b border-amber-200">Order Details</h2>
                
                <!-- Location Selection (for all order types) -->
                <div class="mb-6">
                    <h3 class="text-xl font-semibold mb-4">Location</h3>
                    <div class="grid md:grid-cols-2 gap-6">
                        <div>
                            <label for="location_id" class="block text-gray-700 font-medium mb-2">Select Location</label>
                            <select id="location_id" name="location_id" 
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-amber-500" required
                                    onchange="this.form.submit()">
                                <option value="">Select a location</option>
                                <?php foreach ($locations as $location): ?>
                                    <option value="<?= $location['location_id'] ?>" <?= $selected_location_id == $location['location_id'] ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($location['name']) ?> - <?= htmlspecialchars($location['city']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        
                        <div>
                            <label for="order_time" class="block text-gray-700 font-medium mb-2">
                                <?= $order_type == 'dine_in' ? 'Reservation Time' : ($order_type == 'takeaway' ? 'Pickup Time' : 'Delivery Time') ?>
                            </label>
                            <input type="datetime-local" id="order_time" name="order_time" 
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-amber-500" required
                                   min="<?= date('Y-m-d\TH:i') ?>">
                        </div>
                    </div>
                </div>
                
                <?php if ($order_type == 'dine_in' && $selected_location_id): ?>
                    <!-- Dine In Specific Options -->
                    <div class="mb-6">
                        <div class="grid md:grid-cols-2 gap-6">
                            <div>
                                <label for="guests" class="block text-gray-700 font-medium mb-2">Number of Guests</label>
                                <select id="guests" name="guests" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-amber-500" required>
                                    <option value="">Select guests</option>
                                    <?php for ($i = 1; $i <= 10; $i++): ?>
                                        <option value="<?= $i ?>"><?= $i ?> <?= $i == 1 ? 'person' : 'people' ?></option>
                                    <?php endfor; ?>
                                </select>
                            </div>
                            
                            <div>
                                <label for="table_number" class="block text-gray-700 font-medium mb-2">Select Table</label>
                                <select id="table_number" name="table_number" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-amber-500" required>
                                    <option value="">Select a table</option>
                                    <?php foreach ($available_tables as $table): ?>
                                        <option value="<?= $table['table_id'] ?>">
                                            Table <?= $table['table_number'] ?> (<?= $table['capacity'] ?> seats) - <?= ucfirst($table['type']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>
                
                <!-- Order Items --> 
                <h3 class="text-xl font-semibold mb-4">Your Order</h3>

                <!-- Category Filter -->
                <div class="mb-6">
                    <div class="flex flex-wrap gap-2" id="categoryFilters">
                        <button type="button" class="category-filter px-4 py-2 rounded-full bg-amber-100 text-amber-800 font-medium active" data-category="all">All Items</button>
                        <?php 
                        $categories = $conn->query("SELECT DISTINCT c.category_id, c.name 
                                                FROM categories c 
                                                JOIN products p ON c.category_id = p.category_id 
                                                WHERE p.is_active = TRUE 
                                                ORDER BY c.display_order");
                        foreach ($categories->fetchAll(PDO::FETCH_ASSOC) as $category): 
                        ?>
                        <button type="button" class="category-filter px-4 py-2 rounded-full bg-amber-100 text-amber-800 font-medium" 
                                data-category="<?= $category['category_id'] ?>">
                            <?= htmlspecialchars($category['name']) ?>
                        </button>
                        <?php endforeach; ?>
                    </div>
                </div>

                <div id="order_items" class="space-y-4">
                    <?php
                    $stmt = $conn->query("SELECT c.category_id, c.name AS category_name, 
                                        p.product_id, p.name, p.description, p.base_price, p.image_url
                                        FROM products p 
                                        JOIN categories c ON p.category_id = c.category_id 
                                        WHERE p.is_active = TRUE 
                                        ORDER BY c.display_order, p.display_order");
                    $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
                    
                    $current_category = null;
                    foreach ($products as $product):
                        if ($current_category != $product['category_id']):
                            $current_category = $product['category_id'];
                    ?>
                    <div class="pt-4 category-section" data-category="<?= $product['category_id'] ?>">
                        <h4 class="font-playfair text-lg font-semibold text-amber-800 border-b border-amber-200 pb-2 mb-3">
                            <?= htmlspecialchars($product['category_name']) ?>
                        </h4>
                    </div>
                    <?php endif; ?>
                    
                    <div class="flex items-start justify-between p-4 border border-gray-200 rounded-lg hover:shadow-md transition-shadow duration-200 category-item" 
                        data-category="<?= $product['category_id'] ?>">
                        <div class="flex items-start space-x-4 w-full">
                            <div class="flex-shrink-0">
                                <input type="checkbox" name="items[]" id="item_<?= $product['product_id'] ?>" 
                                    value="<?= $product['product_id'] ?>" class="mt-4">
                            </div>
                            
                            <div class="flex items-start space-x-4 flex-grow">
                                <?php if (!empty($product['image_url'])): ?>
                                <img src="assets/img/product/removebg/<?= htmlspecialchars($product['image_url']) ?>" 
                                    alt="<?= htmlspecialchars($product['name']) ?>" 
                                    class="w-24 h-24 object-cover rounded-lg">
                                <?php else: ?>
                                <div class="w-24 h-24 bg-gray-100 rounded-lg flex items-center justify-center text-gray-400">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                    </svg>
                                </div>
                                <?php endif; ?>
                                
                                <div class="flex-grow">
                                    <label for="item_<?= $product['product_id'] ?>" class="font-medium text-gray-800 cursor-pointer text-lg">
                                        <?= htmlspecialchars($product['name']) ?>
                                    </label>
                                    <p class="text-sm text-gray-600 mt-1"><?= htmlspecialchars($product['description']) ?></p>
                                    <p class="text-amber-700 font-semibold mt-2 text-lg">$<?= number_format($product['base_price'], 2) ?></p>
                                </div>
                            </div>
                            
                            <div class="flex space-x-3 flex-shrink-0">
                                <div>
                                    <label for="quantity_<?= $product['product_id'] ?>" class="sr-only">Quantity</label>
                                    <input type="number" name="quantity[<?= $product['product_id'] ?>]" 
                                        id="quantity_<?= $product['product_id'] ?>" min="1" value="1" 
                                        class="w-16 px-2 py-1 border border-gray-300 rounded" disabled>
                                </div>
                                
                                <div>
                                    <label for="instructions_<?= $product['product_id'] ?>" class="sr-only">Instructions</label>
                                    <input type="text" name="instructions[<?= $product['product_id'] ?>]" 
                                        id="instructions_<?= $product['product_id'] ?>" placeholder="Notes" 
                                        class="w-32 px-2 py-1 border border-gray-300 rounded" disabled>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
            
            <!-- Special Instructions -->
            <div class="bg-white rounded-xl shadow-lg p-6">
                <h2 class="font-playfair text-2xl font-bold mb-4 pb-2 border-b border-amber-200">Special Instructions</h2>
                <div class="mb-4">
                    <label for="special_instructions" class="block text-gray-700 font-medium mb-2">Additional Notes</label>
                    <textarea name="special_instructions" id="special_instructions" rows="4" 
                              class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-amber-500" 
                              placeholder="Any special requests or dietary restrictions..."></textarea>
                </div>
            </div>
            
            <!-- Payment Method -->
            <div class="bg-white rounded-xl shadow-lg p-6">
                <h2 class="font-playfair text-2xl font-bold mb-4 pb-2 border-b border-amber-200">Payment Method</h2>
                <div class="space-y-3">
                    <div class="flex items-center">
                        <input type="radio" id="cash" name="payment_method" value="cash" class="mr-2" checked>
                        <label for="cash" class="text-gray-700">Cash on <?= $order_type == 'delivery' ? 'Delivery' : ($order_type == 'takeaway' ? 'Pickup' : 'Arrival') ?></label>
                    </div>
                    <div class="flex items-center">
                        <input type="radio" id="card" name="payment_method" value="card" class="mr-2">
                        <label for="card" class="text-gray-700">Credit/Debit Card</label>
                    </div>
                    <?php if ($order_type == 'delivery'): ?>
                    <div class="flex items-center">
                        <input type="radio" id="online" name="payment_method" value="online" class="mr-2">
                        <label for="online" class="text-gray-700">Online Payment</label>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
            
            <!-- Submit Button -->
            <div class="text-center">
                <button type="submit" class="bg-gradient-to-r from-amber-600 to-orange-600 text-white px-12 py-4 rounded-full text-lg font-semibold hover:from-amber-700 hover:to-orange-700 transform hover:scale-105 transition-all duration-300 shadow-xl">
                    Place Order
                </button>
            </div>
        </form>
    </div>

<?php include 'includes/footer.php'; ?>
<script src='assets/js/order.js'></script>  
</body>
</html>
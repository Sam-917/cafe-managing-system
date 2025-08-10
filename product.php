<?php
include 'includes/header.php';
require 'includes/config.php';

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: menu.php");
    exit();
}

$product_id = (int)$_GET['id'];

// Get product details
$stmt = $conn->prepare("SELECT p.*, c.name AS category_name 
                      FROM products p
                      JOIN categories c ON p.category_id = c.category_id
                      WHERE p.product_id = ?");
$stmt->execute([$product_id]);
$product = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$product) {
    header("Location: menu.php");
    exit();
}

// Get nutritional info
$stmt = $conn->prepare("SELECT * FROM nutritional_info WHERE product_id = ?");
$stmt->execute([$product_id]);
$nutrition = $stmt->fetch(PDO::FETCH_ASSOC);

// Get product options
$stmt = $conn->prepare("SELECT * FROM product_options WHERE product_id = ? ORDER BY option_type, name");
$stmt->execute([$product_id]);
$options = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Check if product is seasonal
$stmt = $conn->prepare("SELECT * FROM seasonal_items 
                      WHERE product_id = ? AND CURDATE() BETWEEN start_date AND end_date");
$stmt->execute([$product_id]);
$is_seasonal = $stmt->fetch(PDO::FETCH_ASSOC);
?>
 

    <!-- Product Content -->
    <div class="max-w-6xl mx-auto px-4 py-12">
        <div class="bg-white rounded-xl shadow-lg overflow-hidden">
            <div class="md:flex">
                <!-- Product Image -->
                <div class="md:w-1/2">
                    <img src="assets/img/product/removebg/<?= htmlspecialchars($product['image_url'] ?? 'default.jpg') ?>" 
                         alt="<?= htmlspecialchars($product['name']) ?>" 
                         class="w-full h-full object-cover"
                         onerror="this.src='assets/img/default.jpg'">
                </div>
                
                <!-- Product Details -->
                <div class="md:w-1/2 p-8">
                    <div class="flex justify-between items-start mb-4">
                        <div>
                            <span class="text-sm text-gray-500"><?= htmlspecialchars($product['category_name']) ?></span>
                            <h1 class="font-playfair text-3xl font-bold text-gray-800"><?= htmlspecialchars($product['name']) ?></h1>
                        </div>
                        <span class="font-bold text-amber-700 text-2xl">$<?= number_format($product['base_price'], 2) ?></span>
                    </div>
                    
                    <?php if ($is_seasonal): ?>
                        <span class="inline-block bg-gradient-to-r from-amber-500 to-orange-500 text-white px-3 py-1 rounded-full text-xs font-bold mb-4">
                            Seasonal Special
                        </span>
                    <?php endif; ?>
                    
                    <?php if ($product['is_featured']): ?>
                        <span class="inline-block bg-gradient-to-r from-blue-500 to-green-500 text-white px-3 py-1 rounded-full text-xs font-bold mb-4">
                            Featured Item
                        </span>
                    <?php endif; ?>
                    
                    <p class="text-gray-600 mb-6"><?= htmlspecialchars($product['description']) ?></p>
                    
                    <?php if ($product['calories'] > 0): ?>
                        <div class="bg-gray-50 p-4 rounded-lg mb-6">
                            <h3 class="font-semibold text-gray-800 mb-2">Nutritional Information</h3>
                            <div class="grid grid-cols-2 gap-4">
                                <div class="nutrition-fact">
                                    <span class="text-sm text-gray-500">Calories</span>
                                    <p class="font-medium"><?= $product['calories'] ?></p>
                                </div>
                                <?php if ($nutrition): ?>
                                    <?php if ($nutrition['caffeine_mg'] > 0): ?>
                                    <div class="nutrition-fact">
                                        <span class="text-sm text-gray-500">Caffeine</span>
                                        <p class="font-medium"><?= $nutrition['caffeine_mg'] ?>mg</p>
                                    </div>
                                    <?php endif; ?>
                                    
                                    <?php if ($nutrition['sugar_g'] > 0): ?>
                                    <div class="nutrition-fact">
                                        <span class="text-sm text-gray-500">Sugar</span>
                                        <p class="font-medium"><?= $nutrition['sugar_g'] ?>g</p>
                                    </div>
                                    <?php endif; ?>
                                    
                                    <?php if ($nutrition['fat_g'] > 0): ?>
                                    <div class="nutrition-fact">
                                        <span class="text-sm text-gray-500">Total Fat</span>
                                        <p class="font-medium"><?= $nutrition['fat_g'] ?>g</p>
                                    </div>
                                    <?php endif; ?>
                                    
                                    <?php if ($nutrition['protein_g'] > 0): ?>
                                    <div class="nutrition-fact">
                                        <span class="text-sm text-gray-500">Protein</span>
                                        <p class="font-medium"><?= $nutrition['protein_g'] ?>g</p>
                                    </div>
                                    <?php endif; ?>
                                    
                                    <?php if (!empty($nutrition['allergens'])): ?>
                                    <div class="col-span-2 nutrition-fact">
                                        <span class="text-sm text-gray-500">Allergens</span>
                                        <p class="font-medium"><?= htmlspecialchars($nutrition['allergens']) ?></p>
                                    </div>
                                    <?php endif; ?>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endif; ?>
                    
                    <?php if (!empty($options)): ?>
                        <div class="mb-6">
                            <h3 class="font-semibold text-gray-800 mb-3">Customization Options</h3>
                            
                            <?php
                            $groupedOptions = [];
                            foreach ($options as $option) {
                                $groupedOptions[$option['option_type']][] = $option;
                            }
                            
                            foreach ($groupedOptions as $type => $typeOptions):
                            ?>
                            <div class="option-group">
                                <h4 class="font-medium text-gray-700 capitalize mb-2"><?= $type ?></h4>
                                <div class="space-y-2">
                                    <?php foreach ($typeOptions as $option): ?>
                                    <div class="flex justify-between items-center">
                                        <label class="flex items-center">
                                            <input type="radio" name="<?= $type ?>" value="<?= $option['option_id'] ?>" 
                                                   class="mr-2" <?= $option['additional_price'] == 0 ? 'checked' : '' ?>>
                                            <?= htmlspecialchars($option['name']) ?>
                                        </label>
                                        <span class="text-amber-700">
                                            <?php if ($option['additional_price'] > 0): ?>
                                                +$<?= number_format($option['additional_price'], 2) ?>
                                            <?php endif; ?>
                                        </span>
                                    </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                    
                    <div class="flex space-x-4">
                        <a href="order.php?type=dine_in&product=<?= $product['product_id'] ?>" 
                           class="flex-1 bg-gradient-to-r from-amber-600 to-orange-600 text-white py-3 px-6 rounded-lg font-medium text-center hover:from-amber-700 hover:to-orange-700 transition-all duration-300">
                            Order Now
                        </a>
                        <a href="menu.php" class="flex-1 border border-amber-600 text-amber-700 py-3 px-6 rounded-lg font-medium text-center hover:bg-amber-50 transition-all duration-300">
                            Back to Menu
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

<?php include 'includes/footer.php'; ?>
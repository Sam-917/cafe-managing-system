<?php 
include 'includes/header.php';
require 'includes/config.php';

function getCategories($conn) {
    $stmt = $conn->query("SELECT * FROM categories WHERE is_active = TRUE ORDER BY display_order");
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function getProductsByCategory($conn, $category_id) {
    $stmt = $conn->prepare("SELECT p.*, 
                          (SELECT COUNT(*) FROM seasonal_items si 
                           WHERE si.product_id = p.product_id 
                           AND CURDATE() BETWEEN si.start_date AND si.end_date) AS is_seasonal
                          FROM products p 
                          WHERE p.category_id = ? AND p.is_active = TRUE 
                          ORDER BY p.display_order");
    $stmt->execute([$category_id]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}
?>

 
    <!-- Menu Content -->
    <div class="max-w-7xl mx-auto px-4 py-12">
        <h1 class="font-playfair text-4xl font-bold text-center text-amber-900 mb-4">Our Menu</h1>
        <p class="text-center text-gray-600 mb-12 max-w-3xl mx-auto">
            Discover our handcrafted beverages and delicious food. Click on any item to view details.
        </p>
        
        <div class="space-y-16">
            <?php
            $categories = getCategories($conn);
            
            foreach ($categories as $category): 
                $products = getProductsByCategory($conn, $category['category_id']);
                if (!empty($products)):
            ?>
            <section id="<?= strtolower(str_replace(' ', '-', $category['name'])) ?>" class="scroll-mt-20">
                <div class="flex items-center mb-8">
                    <h2 class="font-playfair text-3xl font-bold text-amber-800"><?= htmlspecialchars($category['name']) ?></h2>
                    <div class="flex-1 border-b-2 border-amber-200 ml-4"></div>
                </div>
                <p class="text-gray-600 mb-8"><?= htmlspecialchars($category['description']) ?></p>
                
                <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-8">
                    <?php foreach ($products as $product): ?>
                    <a href="product.php?id=<?= $product['product_id'] ?>" class="product-card block bg-white rounded-xl shadow-md overflow-hidden hover:no-underline">
                        <div class="relative">
                            <img src="assets/img/product/removebg/<?= htmlspecialchars($product['image_url'] ?? 'default.jpg') ?>" 
                                 alt="<?= htmlspecialchars($product['name']) ?>" 
                                 class="w-full h-48 object-cover"
                                 onerror="this.src='assets/img/product/removebg/default.jpg'">
                            
                            <?php if ($product['is_featured']): ?>
                                <span class="featured-badge absolute top-4 left-4 px-3 py-1 rounded-full text-xs font-bold">
                                    Featured
                                </span>
                            <?php endif; ?>
                            
                            <?php if ($product['is_seasonal'] > 0): ?>
                                <span class="seasonal-badge absolute top-4 right-4 px-3 py-1 rounded-full text-xs font-bold">
                                    Seasonal
                                </span>
                            <?php endif; ?>
                        </div>
                        
                        <div class="p-6">
                            <div class="flex justify-between items-start mb-2">
                                <h3 class="font-bold text-xl text-gray-800"><?= htmlspecialchars($product['name']) ?></h3>
                                <span class="font-bold text-amber-700">$<?= number_format($product['base_price'], 2) ?></span>
                            </div>
                            
                            <p class="text-gray-600 mb-4"><?= htmlspecialchars($product['description']) ?></p>
                            
                            <?php if ($product['calories'] > 0): ?>
                                <span class="inline-block bg-gray-100 text-gray-700 text-xs px-2 py-1 rounded">
                                    <?= $product['calories'] ?> calories
                                </span>
                            <?php endif; ?>
                            
                            <div class="mt-4 text-amber-600 font-medium">
                                View details →
                            </div>
                        </div>
                    </a>
                    <?php endforeach; ?>
                </div>
            </section>
            <?php 
                endif;
            endforeach; 
            ?>
        </div>
    </div>

    <!-- Order Now Banner -->
    <div class="bg-gradient-to-r from-amber-600 to-orange-600 text-white py-12 px-4 text-center">
        <div class="max-w-4xl mx-auto">
            <h2 class="font-playfair text-3xl font-bold mb-4">Ready to Order?</h2>
            <p class="text-xl mb-6">Choose your preferred way to enjoy our delicious offerings.</p>
            <a href="order.php?type=dine_in" class="inline-block bg-white text-amber-700 px-8 py-3 rounded-full text-lg font-semibold hover:bg-gray-100 transition-all duration-300">
                Start Your Order
            </a>
        </div>
    </div>
 
<?php include 'includes/footer.php'; ?>
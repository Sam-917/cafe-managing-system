<?php
session_start();
require '../includes/config.php';

// Check if user is admin
if (!isset($_SESSION['logged_in']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit();
}

// Get product ID from URL
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: admin_products.php");
    exit();
}

$product_id = (int)$_GET['id'];

// Fetch product details
try {
    $product_stmt = $conn->prepare("
        SELECT p.*, c.name as category_name 
        FROM products p
        JOIN categories c ON p.category_id = c.category_id
        WHERE p.product_id = :product_id
    ");
    $product_stmt->bindParam(':product_id', $product_id);
    $product_stmt->execute();
    $product = $product_stmt->fetch(PDO::FETCH_ASSOC);

    if (!$product) {
        header("Location: admin_products.php");
        exit();
    }

    // Fetch all categories for dropdown
    $categories_stmt = $conn->query("SELECT * FROM categories WHERE is_active = 1 ORDER BY display_order");
    $categories = $categories_stmt->fetchAll(PDO::FETCH_ASSOC);

    // Fetch product options
    $options_stmt = $conn->prepare("SELECT * FROM product_options WHERE product_id = :product_id ORDER BY option_type, name");
    $options_stmt->bindParam(':product_id', $product_id);
    $options_stmt->execute();
    $product_options = $options_stmt->fetchAll(PDO::FETCH_ASSOC);

    // Fetch nutritional info
    $nutrition_stmt = $conn->prepare("SELECT * FROM nutritional_info WHERE product_id = :product_id");
    $nutrition_stmt->bindParam(':product_id', $product_id);
    $nutrition_stmt->execute();
    $nutrition_info = $nutrition_stmt->fetch(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    $error = "Failed to fetch product details: " . $e->getMessage();
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_product'])) {
    // Validate and sanitize input
    $name = trim($_POST['name']);
    $category_id = (int)$_POST['category_id'];
    $description = trim($_POST['description']);
    $base_price = (float)$_POST['base_price'];
    $calories = !empty($_POST['calories']) ? (int)$_POST['calories'] : null;
    $is_featured = isset($_POST['is_featured']) ? 1 : 0;
    $is_active = isset($_POST['is_active']) ? 1 : 0;
    $display_order = !empty($_POST['display_order']) ? (int)$_POST['display_order'] : 0;

    // Handle image upload
    $image_url = $product['image_url']; // Keep existing image by default
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $upload_dir = '../images/products/';
        $file_ext = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
        $new_filename = 'product_' . $product_id . '_' . time() . '.' . $file_ext;
        
        if (move_uploaded_file($_FILES['image']['tmp_name'], $upload_dir . $new_filename)) {
            // Delete old image if it exists
            if ($image_url && file_exists($upload_dir . $image_url)) {
                unlink($upload_dir . $image_url);
            }
            $image_url = $new_filename;
        }
    }

    try {
        // Update product
        $update_stmt = $conn->prepare("
            UPDATE products 
            SET 
                category_id = :category_id,
                name = :name,
                description = :description,
                base_price = :base_price,
                calories = :calories,
                image_url = :image_url,
                is_featured = :is_featured,
                is_active = :is_active,
                display_order = :display_order
            WHERE product_id = :product_id
        ");
        
        $update_stmt->bindParam(':category_id', $category_id);
        $update_stmt->bindParam(':name', $name);
        $update_stmt->bindParam(':description', $description);
        $update_stmt->bindParam(':base_price', $base_price);
        $update_stmt->bindParam(':calories', $calories);
        $update_stmt->bindParam(':image_url', $image_url);
        $update_stmt->bindParam(':is_featured', $is_featured);
        $update_stmt->bindParam(':is_active', $is_active);
        $update_stmt->bindParam(':display_order', $display_order);
        $update_stmt->bindParam(':product_id', $product_id);
        $update_stmt->execute();

        // Update nutritional info if exists
        if ($nutrition_info) {
            $nutrition_update_stmt = $conn->prepare("
                UPDATE nutritional_info 
                SET 
                    serving_size = :serving_size,
                    caffeine_mg = :caffeine_mg,
                    sugar_g = :sugar_g,
                    fat_g = :fat_g,
                    protein_g = :protein_g,
                    allergens = :allergens
                WHERE product_id = :product_id
            ");
            
            $nutrition_update_stmt->bindParam(':serving_size', $_POST['serving_size']);
            $nutrition_update_stmt->bindParam(':caffeine_mg', $_POST['caffeine_mg']);
            $nutrition_update_stmt->bindParam(':sugar_g', $_POST['sugar_g']);
            $nutrition_update_stmt->bindParam(':fat_g', $_POST['fat_g']);
            $nutrition_update_stmt->bindParam(':protein_g', $_POST['protein_g']);
            $nutrition_update_stmt->bindParam(':allergens', $_POST['allergens']);
            $nutrition_update_stmt->bindParam(':product_id', $product_id);
            $nutrition_update_stmt->execute();
        }

        $success = "Product updated successfully!";
        
        // Refresh product data
        $product_stmt->execute();
        $product = $product_stmt->fetch(PDO::FETCH_ASSOC);
        
    } catch (PDOException $e) {
        $error = "Failed to update product: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Product | Cafe & Netic</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.ckeditor.com/4.16.2/standard/ckeditor.js"></script>
</head>
<body class="font-inter bg-gray-50">
    <!-- Admin Layout -->
    <div class="flex h-screen overflow-hidden"> 
        <?php include '../includes/admin_sidebar.php'; ?>
        
        <!-- Main Content -->
        <div class="flex-1 overflow-auto">
            <?php 
                $page_title = "Edit Product";
                include '../includes/admin_topnav.php'; 
            ?>
            
            <!-- Product Edit Content -->
            <main class="p-6">
                <div class="flex justify-between items-center mb-6">
                    <div>
                        <h1 class="text-2xl font-bold text-gray-800">Edit Product</h1>
                        <nav class="flex" aria-label="Breadcrumb">
                            <ol class="inline-flex items-center space-x-1 md:space-x-2">
                                <li class="inline-flex items-center">
                                    <a href="admin_products.php" class="inline-flex items-center text-sm font-medium text-gray-700 hover:text-amber-600">
                                        Products
                                    </a>
                                </li>
                                <li aria-current="page">
                                    <div class="flex items-center">
                                        <svg class="w-6 h-6 text-gray-400" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path></svg>
                                        <span class="ml-1 text-sm font-medium text-gray-500 md:ml-2"><?= htmlspecialchars($product['name']) ?></span>
                                    </div>
                                </li>
                            </ol>
                        </nav>
                    </div>
                    <a href="admin_products.php" class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md shadow-sm text-white bg-amber-600 hover:bg-amber-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-amber-500">
                        Back to Products
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

                <div class="bg-white shadow rounded-lg overflow-hidden">
                    <form method="POST" enctype="multipart/form-data">
                        <div class="px-6 py-4 border-b border-gray-200">
                            <h2 class="text-lg font-medium text-gray-900">Product Information</h2>
                        </div>
                        <div class="px-6 py-4 grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Basic Info -->
                            <div class="space-y-4">
                                <div>
                                    <label for="name" class="block text-sm font-medium text-gray-700">Product Name *</label>
                                    <input type="text" id="name" name="name" required 
                                           value="<?= htmlspecialchars($product['name']) ?>" 
                                           class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-amber-500 focus:border-amber-500 sm:text-sm">
                                </div>

                                <div>
                                    <label for="category_id" class="block text-sm font-medium text-gray-700">Category *</label>
                                    <select id="category_id" name="category_id" required 
                                            class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-amber-500 focus:border-amber-500 sm:text-sm">
                                        <?php foreach ($categories as $category): ?>
                                            <option value="<?= $category['category_id'] ?>" <?= $category['category_id'] == $product['category_id'] ? 'selected' : '' ?>>
                                                <?= htmlspecialchars($category['name']) ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>

                                <div>
                                    <label for="base_price" class="block text-sm font-medium text-gray-700">Base Price *</label>
                                    <input type="number" id="base_price" name="base_price" step="0.01" min="0" required 
                                           value="<?= number_format($product['base_price'], 2) ?>" 
                                           class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-amber-500 focus:border-amber-500 sm:text-sm">
                                </div>

                                <div>
                                    <label for="calories" class="block text-sm font-medium text-gray-700">Calories</label>
                                    <input type="number" id="calories" name="calories" min="0" 
                                           value="<?= $product['calories'] ?? '' ?>" 
                                           class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-amber-500 focus:border-amber-500 sm:text-sm">
                                </div>

                                <div>
                                    <label for="display_order" class="block text-sm font-medium text-gray-700">Display Order</label>
                                    <input type="number" id="display_order" name="display_order" min="0" 
                                           value="<?= $product['display_order'] ?? 0 ?>" 
                                           class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-amber-500 focus:border-amber-500 sm:text-sm">
                                </div>

                                <div class="flex items-center space-x-4">
                                    <div class="flex items-center">
                                        <input id="is_featured" name="is_featured" type="checkbox" 
                                               <?= $product['is_featured'] ? 'checked' : '' ?> 
                                               class="h-4 w-4 text-amber-600 focus:ring-amber-500 border-gray-300 rounded">
                                        <label for="is_featured" class="ml-2 block text-sm text-gray-700">Featured Product</label>
                                    </div>

                                    <div class="flex items-center">
                                        <input id="is_active" name="is_active" type="checkbox" 
                                               <?= $product['is_active'] ? 'checked' : '' ?> 
                                               class="h-4 w-4 text-amber-600 focus:ring-amber-500 border-gray-300 rounded">
                                        <label for="is_active" class="ml-2 block text-sm text-gray-700">Active</label>
                                    </div>
                                </div>
                            </div>

                            <!-- Image Upload -->
                            <div class="space-y-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Current Image</label>
                                    <?php if ($product['image_url']): ?>
                                        <img src="../images/products/<?= htmlspecialchars($product['image_url']) ?>" alt="<?= htmlspecialchars($product['name']) ?>" class="mt-1 h-32 w-32 object-cover rounded-md">
                                    <?php else: ?>
                                        <div class="mt-1 h-32 w-32 bg-gray-200 rounded-md flex items-center justify-center text-gray-500">
                                            No image
                                        </div>
                                    <?php endif; ?>
                                </div>

                                <div>
                                    <label for="image" class="block text-sm font-medium text-gray-700">Update Image</label>
                                    <input type="file" id="image" name="image" 
                                           class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-amber-50 file:text-amber-700 hover:file:bg-amber-100">
                                    <p class="mt-1 text-xs text-gray-500">JPEG, PNG or GIF (Max 2MB)</p>
                                </div>
                            </div>

                            <!-- Description -->
                            <div class="md:col-span-2">
                                <label for="description" class="block text-sm font-medium text-gray-700">Description</label>
                                <textarea id="description" name="description" rows="4" 
                                          class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-amber-500 focus:border-amber-500 sm:text-sm"><?= htmlspecialchars($product['description']) ?></textarea>
                            </div>
                        </div>

                        <!-- Nutritional Information -->
                        <div class="px-6 py-4 border-t border-gray-200">
                            <h2 class="text-lg font-medium text-gray-900">Nutritional Information</h2>
                        </div>
                        <div class="px-6 py-4 grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label for="serving_size" class="block text-sm font-medium text-gray-700">Serving Size</label>
                                <input type="text" id="serving_size" name="serving_size" 
                                       value="<?= $nutrition_info['serving_size'] ?? '' ?>" 
                                       class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-amber-500 focus:border-amber-500 sm:text-sm">
                            </div>

                            <div>
                                <label for="caffeine_mg" class="block text-sm font-medium text-gray-700">Caffeine (mg)</label>
                                <input type="number" id="caffeine_mg" name="caffeine_mg" min="0" 
                                       value="<?= $nutrition_info['caffeine_mg'] ?? '' ?>" 
                                       class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-amber-500 focus:border-amber-500 sm:text-sm">
                            </div>

                            <div>
                                <label for="sugar_g" class="block text-sm font-medium text-gray-700">Sugar (g)</label>
                                <input type="number" id="sugar_g" name="sugar_g" min="0" 
                                       value="<?= $nutrition_info['sugar_g'] ?? '' ?>" 
                                       class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-amber-500 focus:border-amber-500 sm:text-sm">
                            </div>

                            <div>
                                <label for="fat_g" class="block text-sm font-medium text-gray-700">Fat (g)</label>
                                <input type="number" id="fat_g" name="fat_g" min="0" 
                                       value="<?= $nutrition_info['fat_g'] ?? '' ?>" 
                                       class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-amber-500 focus:border-amber-500 sm:text-sm">
                            </div>

                            <div>
                                <label for="protein_g" class="block text-sm font-medium text-gray-700">Protein (g)</label>
                                <input type="number" id="protein_g" name="protein_g" min="0" 
                                       value="<?= $nutrition_info['protein_g'] ?? '' ?>" 
                                       class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-amber-500 focus:border-amber-500 sm:text-sm">
                            </div>

                            <div class="md:col-span-2">
                                <label for="allergens" class="block text-sm font-medium text-gray-700">Allergens</label>
                                <input type="text" id="allergens" name="allergens" 
                                       value="<?= $nutrition_info['allergens'] ?? '' ?>" 
                                       class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-amber-500 focus:border-amber-500 sm:text-sm">
                                <p class="mt-1 text-xs text-gray-500">Comma-separated list of allergens</p>
                            </div>
                        </div>

                        <!-- Product Options -->
                        <div class="px-6 py-4 border-t border-gray-200">
                            <div class="flex justify-between items-center">
                                <h2 class="text-lg font-medium text-gray-900">Product Options</h2>
                                <button type="button" class="inline-flex items-center px-3 py-1 border border-transparent text-xs font-medium rounded shadow-sm text-white bg-amber-600 hover:bg-amber-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-amber-500">
                                    Add Option
                                </button>
                            </div>
                        </div>
                        <div class="px-6 py-4">
                            <?php if (empty($product_options)): ?>
                                <p class="text-sm text-gray-500">No options configured for this product.</p>
                            <?php else: ?>
                                <div class="overflow-x-auto">
                                    <table class="min-w-full divide-y divide-gray-200">
                                        <thead class="bg-gray-50">
                                            <tr>
                                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th>
                                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
                                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Additional Price</th>
                                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Calories</th>
                                                <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody class="bg-white divide-y divide-gray-200">
                                            <?php foreach ($product_options as $option): ?>
                                                <tr>
                                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"><?= ucfirst($option['option_type']) ?></td>
                                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900"><?= htmlspecialchars($option['name']) ?></td>
                                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">$<?= number_format($option['additional_price'], 2) ?></td>
                                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900"><?= $option['calories_addition'] ?? 'N/A' ?></td>
                                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                                        <a href="#" class="text-amber-600 hover:text-amber-900 mr-3">Edit</a>
                                                        <a href="#" class="text-red-600 hover:text-red-900">Delete</a>
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                            <?php endif; ?>
                        </div>

                        <!-- Form Actions -->
                        <div class="px-6 py-4 border-t border-gray-200 bg-gray-50 text-right">
                            <button type="submit" name="update_product" class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-amber-600 hover:bg-amber-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-amber-500">
                                Save Changes
                            </button>
                        </div>
                    </form>
                </div>
            </main>
        </div>
    </div>

    <script>
        // Initialize CKEditor for description field
        CKEDITOR.replace('description', {
            toolbar: [
                { name: 'basicstyles', items: ['Bold', 'Italic', 'Underline'] },
                { name: 'paragraph', items: ['NumberedList', 'BulletedList'] },
                { name: 'links', items: ['Link', 'Unlink'] },
                { name: 'tools', items: ['Maximize'] }
            ],
            height: 150
        });
    </script>
</body>
</html>
<?php
session_start();
require_once 'includes/config.php';

// Process the order
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Validate and sanitize input
    $customer_name = htmlspecialchars(trim($_POST['customer_name']));
    $phone = htmlspecialchars(trim($_POST['phone']));
    $email = isset($_POST['email']) ? htmlspecialchars(trim($_POST['email'])) : null;
    $order_type = htmlspecialchars($_POST['order_type']);
    $special_instructions = isset($_POST['special_instructions']) ? htmlspecialchars(trim($_POST['special_instructions'])) : null;
    $payment_method = htmlspecialchars($_POST['payment_method']);
    
    // Additional fields based on order type
    $table_number = null;
    $pickup_location_id = null;
    $delivery_address = null;
    $delivery_time = null;
    
    if ($order_type == 'dine_in') {
        $table_number = (int)$_POST['table_number'];
        $reservation_date = htmlspecialchars($_POST['reservation_date']);
        $reservation_time = htmlspecialchars($_POST['reservation_time']);
        
        // Mark table as unavailable
        $stmt = $conn->prepare("UPDATE restaurant_tables SET is_active = 0 WHERE table_id = ?");
        $stmt->execute([$table_number]);
    } 
    elseif ($order_type == 'takeaway') {
        $pickup_location_id = (int)$_POST['pickup_location_id'];
        $pickup_time = htmlspecialchars($_POST['pickup_time']);
    } 
    else {
        $delivery_location_id = (int)$_POST['delivery_location_id'];
        $delivery_address = htmlspecialchars(trim($_POST['delivery_address']));
        $delivery_time_option = htmlspecialchars($_POST['delivery_time']);
        
        if ($delivery_time_option == 'specific') {
            $delivery_time = htmlspecialchars($_POST['specific_delivery_time']);
        } else {
            $delivery_time = $delivery_time_option;
        }
    }
    
    // Start transaction
    $conn->beginTransaction();
    
    try {
        // Calculate total amount and prepare order items
        $total_amount = 0;
        $items = [];
        
        if (isset($_POST['items']) && is_array($_POST['items'])) {
            foreach ($_POST['items'] as $item_id) {
                $item_id = (int)$item_id;
                $quantity = isset($_POST['quantity'][$item_id]) ? (int)$_POST['quantity'][$item_id] : 1;
                $instructions = isset($_POST['instructions'][$item_id]) ? htmlspecialchars(trim($_POST['instructions'][$item_id])) : null;
                
                // Get item price
                $stmt = $conn->prepare("SELECT base_price FROM products WHERE product_id = ?");
                $stmt->execute([$item_id]);
                $product = $stmt->fetch(PDO::FETCH_ASSOC);
                
                if ($product) {
                    $item_price = $product['base_price'];
                    $total_amount += $item_price * $quantity;
                    
                    $items[] = [
                        'product_id' => $item_id,
                        'quantity' => $quantity,
                        'price' => $item_price,
                        'instructions' => $instructions
                    ];
                }
            }
        }
        
        if (empty($items)) {
            throw new Exception("No items selected for the order.");
        }
        
        // Insert conn
        $stmt = $conn->prepare("INSERT INTO orders (customer_name, phone, email, order_type, table_number, 
                              pickup_location_id, delivery_address, delivery_time, payment_method, 
                              total_amount, special_instructions, status) 
                              VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'pending')");
        $stmt->execute([
            $customer_name, $phone, $email, $order_type, $table_number, 
            $pickup_location_id ?? null, $delivery_address ?? null, $delivery_time ?? null, 
            $payment_method, $total_amount, $special_instructions
        ]);
        $order_id = $conn->lastInsertId();
        
        // Insert order items
        foreach ($items as $item) {
            $stmt = $conn->prepare("INSERT INTO order_items (order_id, product_id, quantity, price, special_instructions) 
                                  VALUES (?, ?, ?, ?, ?)");
            $stmt->execute([
                $order_id, $item['product_id'], $item['quantity'], 
                $item['price'], $item['instructions']
            ]);
        }
        
        // Commit transaction
        $conn->commit();
        
        // Redirect to confirmation page
        header("Location: order_confirmation.php?id=$order_id");
        exit();
        
    } catch (Exception $e) {
        // Rollback transaction on error
        $conn->rollBack();
        
        // Store error in session and redirect back
        $_SESSION['order_error'] = "There was an error processing your order: " . $e->getMessage();
        header("Location: order.php?type=$order_type");
        exit();
    }
} else {
    // Redirect if accessed directly
    header("Location: index.php");
    exit();
}
?>
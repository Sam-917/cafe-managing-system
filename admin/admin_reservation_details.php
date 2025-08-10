<?php
session_start();
require '../includes/config.php';

// Check if user is admin
if (!isset($_SESSION['logged_in']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit();
}

// Get reservation ID from URL
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: admin_reservations.php");
    exit();
}

$reservation_id = (int)$_GET['id'];

// Fetch reservation details
try {
    $reservation_stmt = $conn->prepare("
        SELECT r.*, t.table_number, t.capacity, t.type as table_type, 
               l.name as location_name, l.address as location_address, l.phone as location_phone
        FROM reservations r
        JOIN restaurant_tables t ON r.table_id = t.table_id
        JOIN restaurant_locations l ON t.location_id = l.location_id
        WHERE r.reservation_id = :reservation_id
    ");
    $reservation_stmt->bindParam(':reservation_id', $reservation_id);
    $reservation_stmt->execute();
    $reservation = $reservation_stmt->fetch(PDO::FETCH_ASSOC);

    if (!$reservation) {
        header("Location: admin_reservations.php");
        exit();
    }

} catch (PDOException $e) {
    $error = "Failed to fetch reservation details: " . $e->getMessage();
}

// Handle status update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_status'])) {
    $new_status = $_POST['status'];
    $valid_statuses = ['confirmed', 'pending', 'cancelled', 'completed', 'no-show'];

    if (in_array($new_status, $valid_statuses)) {
        try {
            $update_stmt = $conn->prepare("
                UPDATE reservations 
                SET status = :status 
                WHERE reservation_id = :reservation_id
            ");
            $update_stmt->bindParam(':status', $new_status);
            $update_stmt->bindParam(':reservation_id', $reservation_id);
            $update_stmt->execute();

            // Refresh reservation data
            $reservation_stmt->execute();
            $reservation = $reservation_stmt->fetch(PDO::FETCH_ASSOC);

            $success = "Reservation status updated successfully!";
        } catch (PDOException $e) {
            $error = "Failed to update reservation status: " . $e->getMessage();
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
    <title>Reservation Details | Cafe & Netic</title>
    <script src="https://cdn.tailwindcss.com"></script>
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
    </style>
</head>
<body class="font-inter bg-gray-50">
    <!-- Admin Layout -->
    <div class="flex h-screen overflow-hidden"> 
        <?php include '../includes/admin_sidebar.php'; ?>
        
        <!-- Main Content -->
        <div class="flex-1 overflow-auto">
            <?php include '../includes/admin_topnav.php'; ?>
            
            <!-- Reservation Details Content -->
            <main class="p-6">
                <div class="flex justify-between items-center mb-6">
                    <div>
                        <h1 class="text-2xl font-bold text-gray-800">Reservation Details</h1>
                        <nav class="flex" aria-label="Breadcrumb">
                            <ol class="inline-flex items-center space-x-1 md:space-x-2">
                                <li class="inline-flex items-center">
                                    <a href="admin_reservations.php" class="inline-flex items-center text-sm font-medium text-gray-700 hover:text-amber-600">
                                        Reservations
                                    </a>
                                </li>
                                <li aria-current="page">
                                    <div class="flex items-center">
                                        <svg class="w-6 h-6 text-gray-400" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path></svg>
                                        <span class="ml-1 text-sm font-medium text-gray-500 md:ml-2">#<?= $reservation['reservation_id'] ?></span>
                                    </div>
                                </li>
                            </ol>
                        </nav>
                    </div>
                    <a href="admin_reservations.php" class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md shadow-sm text-white bg-amber-600 hover:bg-amber-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-amber-500">
                        Back to Reservations
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
                    <!-- Reservation Summary -->
                    <div class="lg:col-span-2">
                        <div class="bg-white shadow rounded-lg overflow-hidden">
                            <div class="px-6 py-4 border-b border-gray-200">
                                <h2 class="text-lg font-medium text-gray-900">Reservation Summary</h2>
                            </div>
                            <div class="px-6 py-4">
                                <div class="flex justify-between mb-4">
                                    <div>
                                        <h3 class="text-sm font-medium text-gray-500">Reservation ID</h3>
                                        <p class="mt-1 text-sm text-gray-900">#<?= $reservation['reservation_id'] ?></p>
                                    </div>
                                    <div>
                                        <h3 class="text-sm font-medium text-gray-500">Date</h3>
                                        <p class="mt-1 text-sm text-gray-900"><?= date('M j, Y', strtotime($reservation['reservation_date'])) ?></p>
                                    </div>
                                    <div>
                                        <h3 class="text-sm font-medium text-gray-500">Status</h3>
                                        <p class="mt-1">
                                            <span class="px-2 py-1 text-xs rounded-full reservation-status-<?= $reservation['status'] ?>">
                                                <?= ucfirst($reservation['status']) ?>
                                            </span>
                                        </p>
                                    </div>
                                </div>

                                <div class="border-t border-gray-200 pt-4">
                                    <h3 class="text-sm font-medium text-gray-500">Time</h3>
                                    <p class="mt-1 text-sm text-gray-900">
                                        <?= date('g:i A', strtotime($reservation['start_time'])) ?> - <?= date('g:i A', strtotime($reservation['end_time'])) ?>
                                    </p>
                                </div>

                                <div class="border-t border-gray-200 pt-4">
                                    <h3 class="text-sm font-medium text-gray-500">Table Details</h3>
                                    <p class="mt-1 text-sm text-gray-900">
                                        <?= $reservation['table_number'] ?> (<?= ucfirst($reservation['table_type']) ?>)
                                        <span class="text-gray-500"> - Capacity: <?= $reservation['capacity'] ?></span>
                                    </p>
                                </div>

                                <div class="border-t border-gray-200 pt-4">
                                    <h3 class="text-sm font-medium text-gray-500">Guests</h3>
                                    <p class="mt-1 text-sm text-gray-900"><?= $reservation['guests'] ?></p>
                                </div>

                                <?php if (!empty($reservation['special_requests'])): ?>
                                    <div class="border-t border-gray-200 pt-4">
                                        <h3 class="text-sm font-medium text-gray-500">Special Requests</h3>
                                        <p class="mt-1 text-sm text-gray-900"><?= nl2br(htmlspecialchars($reservation['special_requests'])) ?></p>
                                    </div>
                                <?php endif; ?>

                                <?php if (!empty($reservation['confirmation_number'])): ?>
                                    <div class="border-t border-gray-200 pt-4">
                                        <h3 class="text-sm font-medium text-gray-500">Confirmation Number</h3>
                                        <p class="mt-1 text-sm text-gray-900"><?= $reservation['confirmation_number'] ?></p>
                                    </div>
                                <?php endif; ?>
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
                                    <p class="mt-1 text-sm text-gray-900"><?= htmlspecialchars($reservation['customer_name']) ?></p>
                                </div>
                                <div class="mb-4">
                                    <h3 class="text-sm font-medium text-gray-500">Phone</h3>
                                    <p class="mt-1 text-sm text-gray-900"><?= htmlspecialchars($reservation['phone']) ?></p>
                                </div>
                                <?php if (!empty($reservation['email'])): ?>
                                    <div class="mb-4">
                                        <h3 class="text-sm font-medium text-gray-500">Email</h3>
                                        <p class="mt-1 text-sm text-gray-900"><?= htmlspecialchars($reservation['email']) ?></p>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>

                        <!-- Location Info -->
                        <div class="bg-white shadow rounded-lg overflow-hidden mt-6">
                            <div class="px-6 py-4 border-b border-gray-200">
                                <h2 class="text-lg font-medium text-gray-900">Location Information</h2>
                            </div>
                            <div class="px-6 py-4">
                                <div class="mb-4">
                                    <h3 class="text-sm font-medium text-gray-500">Location</h3>
                                    <p class="mt-1 text-sm text-gray-900"><?= htmlspecialchars($reservation['location_name']) ?></p>
                                </div>
                                <div class="mb-4">
                                    <h3 class="text-sm font-medium text-gray-500">Address</h3>
                                    <p class="mt-1 text-sm text-gray-900"><?= htmlspecialchars($reservation['location_address']) ?></p>
                                </div>
                                <div>
                                    <h3 class="text-sm font-medium text-gray-500">Phone</h3>
                                    <p class="mt-1 text-sm text-gray-900"><?= htmlspecialchars($reservation['location_phone']) ?></p>
                                </div>
                            </div>
                        </div>

                        <!-- Reservation Actions -->
                        <div class="bg-white shadow rounded-lg overflow-hidden mt-6">
                            <div class="px-6 py-4 border-b border-gray-200">
                                <h2 class="text-lg font-medium text-gray-900">Reservation Actions</h2>
                            </div>
                            <div class="px-6 py-4">
                                <form method="POST">
                                    <div class="mb-4">
                                        <label for="status" class="block text-sm font-medium text-gray-700 mb-1">Update Status</label>
                                        <select id="status" name="status" class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-amber-500 focus:border-amber-500 sm:text-sm rounded-md">
                                            <option value="confirmed" <?= $reservation['status'] === 'confirmed' ? 'selected' : '' ?>>Confirmed</option>
                                            <option value="pending" <?= $reservation['status'] === 'pending' ? 'selected' : '' ?>>Pending</option>
                                            <option value="cancelled" <?= $reservation['status'] === 'cancelled' ? 'selected' : '' ?>>Cancelled</option>
                                            <option value="completed" <?= $reservation['status'] === 'completed' ? 'selected' : '' ?>>Completed</option>
                                            <option value="no-show" <?= $reservation['status'] === 'no-show' ? 'selected' : '' ?>>No Show</option>
                                        </select>
                                    </div>
                                    <button type="submit" name="update_status" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-amber-600 text-base font-medium text-white hover:bg-amber-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-amber-500 sm:text-sm">
                                        Update Reservation Status
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>
</body>
</html>
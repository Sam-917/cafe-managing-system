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
$valid_statuses = ['all', 'confirmed', 'pending', 'cancelled', 'completed', 'no-show'];

// Validate status filter
if (!in_array($status_filter, $valid_statuses)) {
    $status_filter = 'all';
}

// Build the SQL query based on filter
$sql = "SELECT r.*, t.table_number as table_number, l.name as location_name
        FROM reservations r
        JOIN restaurant_tables t ON r.table_id = t.table_id
        JOIN restaurant_locations l ON t.location_id = l.location_id";

if ($status_filter !== 'all') {
    $sql .= " WHERE r.status = :status";
}

$sql .= " ORDER BY r.reservation_date DESC, r.start_time DESC";

try {
    $stmt = $conn->prepare($sql);
    
    if ($status_filter !== 'all') {
        $stmt->bindParam(':status', $status_filter);
    }
    
    $stmt->execute();
    $reservations = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $error = "Failed to fetch reservations: " . $e->getMessage();
}

// Count reservations by status for the status filter tabs
try {
    $status_counts_stmt = $conn->query("
        SELECT 
            status, 
            COUNT(*) as count 
        FROM reservations 
        GROUP BY status
    ");
    $status_counts = $status_counts_stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Initialize counts
    $counts = [
        'all' => 0,
        'confirmed' => 0,
        'pending' => 0,
        'cancelled' => 0,
        'completed' => 0,
        'no-show' => 0
    ];
    
    foreach ($status_counts as $sc) {
        $counts[$sc['status']] = $sc['count'];
        $counts['all'] += $sc['count'];
    }
    
} catch (PDOException $e) {
    $error = "Failed to fetch reservation counts: " . $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Reservations | Cafe & Netic</title>
    <script src="https://cdn.tailwindcss.com"></script>
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
    </style>
</head>
<body class="font-inter bg-gray-50">
    <!-- Admin Layout -->
    <div class="flex h-screen overflow-hidden"> 
        <?php include '../includes/admin_sidebar.php'; ?>
        
        <!-- Main Content -->
        <div class="flex-1 overflow-auto">
            <?php include '../includes/admin_topnav.php'; ?>
            
            <!-- Reservations Content -->
            <main class="p-6">
                <div class="flex justify-between items-center mb-6">
                    <h1 class="text-2xl font-bold text-gray-800">Manage Reservations</h1>
                    <a href="admin_reservations_add.php" class="bg-amber-600 hover:bg-amber-700 text-white px-4 py-2 rounded-md text-sm font-medium">
                        Add New Reservation
                    </a>
                </div>

                <?php if (isset($error)): ?>
                    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-6">
                        <?= htmlspecialchars($error) ?>
                    </div>
                <?php endif; ?>

                <!-- Status Filter Tabs -->
                <div class="mb-6 border-b border-gray-200">
                    <nav class="-mb-px flex space-x-8">
                        <a href="admin_reservations.php" 
                           class="<?= $status_filter === 'all' ? 'border-amber-500 text-amber-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' ?> whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
                            All
                            <span class="bg-gray-100 text-gray-600 ml-1 py-0.5 px-2 rounded-full text-xs">
                                <?= $counts['all'] ?>
                            </span>
                        </a>
                        
                        <a href="admin_reservations.php?status=confirmed" 
                           class="<?= $status_filter === 'confirmed' ? 'border-amber-500 text-amber-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' ?> whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
                            Confirmed
                            <span class="bg-gray-100 text-gray-600 ml-1 py-0.5 px-2 rounded-full text-xs">
                                <?= $counts['confirmed'] ?>
                            </span>
                        </a>
                        
                        <a href="admin_reservations.php?status=pending" 
                           class="<?= $status_filter === 'pending' ? 'border-amber-500 text-amber-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' ?> whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
                            Pending
                            <span class="bg-gray-100 text-gray-600 ml-1 py-0.5 px-2 rounded-full text-xs">
                                <?= $counts['pending'] ?>
                            </span>
                        </a>
                        
                        <a href="admin_reservations.php?status=cancelled" 
                           class="<?= $status_filter === 'cancelled' ? 'border-amber-500 text-amber-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' ?> whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
                            Cancelled
                            <span class="bg-gray-100 text-gray-600 ml-1 py-0.5 px-2 rounded-full text-xs">
                                <?= $counts['cancelled'] ?>
                            </span>
                        </a>
                        
                        <a href="admin_reservations.php?status=completed" 
                           class="<?= $status_filter === 'completed' ? 'border-amber-500 text-amber-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' ?> whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
                            Completed
                            <span class="bg-gray-100 text-gray-600 ml-1 py-0.5 px-2 rounded-full text-xs">
                                <?= $counts['completed'] ?>
                            </span>
                        </a>
                        
                        <a href="admin_reservations.php?status=no-show" 
                           class="<?= $status_filter === 'no-show' ? 'border-amber-500 text-amber-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' ?> whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
                            No Show
                            <span class="bg-gray-100 text-gray-600 ml-1 py-0.5 px-2 rounded-full text-xs">
                                <?= $counts['no-show'] ?>
                            </span>
                        </a>
                    </nav>
                </div>

                <!-- Reservations Table -->
                <div class="bg-white shadow rounded-lg overflow-hidden">
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Reservation ID</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Customer</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date & Time</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Table</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Guests</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Location</th>
                                    <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                <?php if (empty($reservations)): ?>
                                    <tr>
                                        <td colspan="8" class="px-6 py-4 text-center text-sm text-gray-500">No reservations found</td>
                                    </tr>
                                <?php else: ?>
                                    <?php foreach ($reservations as $reservation): ?>
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">#<?= $reservation['reservation_id'] ?></td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm text-gray-900"><?= htmlspecialchars($reservation['customer_name']) ?></div>
                                            <div class="text-sm text-gray-500"><?= htmlspecialchars($reservation['phone']) ?></div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm text-gray-900"><?= date('M j, Y', strtotime($reservation['reservation_date'])) ?></div>
                                            <div class="text-sm text-gray-500">
                                                <?= date('g:i A', strtotime($reservation['start_time'])) ?> - <?= date('g:i A', strtotime($reservation['end_time'])) ?>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            <?= htmlspecialchars($reservation['table_number']) ?>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            <?= $reservation['guests'] ?>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="px-2 py-1 text-xs rounded-full reservation-status-<?= $reservation['status'] ?>">
                                                <?= ucfirst($reservation['status']) ?>
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            <?= htmlspecialchars($reservation['location_name']) ?>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                            <a href="admin_reservation_details.php?id=<?= $reservation['reservation_id'] ?>" class="text-amber-600 hover:text-amber-900 mr-3">View</a>
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
        reservationId: null, 
        currentStatus: '',
        newStatus: '',
        openStatusModal(id, status) {
            this.reservationId = id;
            this.currentStatus = status;
            this.newStatus = status;
            this.showModal = true;
        },
        async updateStatus() {
            if (!this.reservationId || !this.newStatus) return;
            
            try {
                const response = await fetch('update_reservation_status.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({
                        reservation_id: this.reservationId,
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
                    <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">Update Reservation Status</h3>
                    
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Current Status</label>
                        <div class="px-2 py-1 inline-block rounded-full reservation-status-<%= currentStatus %>">
                            <%= currentStatus.charAt(0).toUpperCase() + currentStatus.slice(1) %>
                        </div>
                    </div>
                    
                    <div class="mb-4">
                        <label for="newStatus" class="block text-sm font-medium text-gray-700 mb-1">New Status</label>
                        <select id="newStatus" x-model="newStatus" class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-amber-500 focus:border-amber-500 sm:text-sm rounded-md">
                            <option value="confirmed">Confirmed</option>
                            <option value="pending">Pending</option>
                            <option value="cancelled">Cancelled</option>
                            <option value="completed">Completed</option>
                            <option value="no-show">No Show</option>
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
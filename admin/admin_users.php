<?php
session_start();
require '../includes/config.php';

// Check if user is admin
if (!isset($_SESSION['logged_in']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit();
}

// Get role filter from URL
$role_filter = isset($_GET['role']) ? $_GET['role'] : 'all';
$valid_roles = ['all', 'admin', 'customer'];

// Validate role filter
if (!in_array($role_filter, $valid_roles)) {
    $role_filter = 'all';
}

// Get status filter from URL
$status_filter = isset($_GET['status']) ? $_GET['status'] : 'active';
$valid_statuses = ['all', 'active', 'inactive'];

// Validate status filter
if (!in_array($status_filter, $valid_statuses)) {
    $status_filter = 'active';
}

// Build the SQL query based on filters
$sql = "SELECT * FROM users WHERE 1=1";
$params = [];

if ($role_filter !== 'all') {
    $sql .= " AND role = :role";
    $params[':role'] = $role_filter;
}

if ($status_filter !== 'all') {
    $sql .= " AND is_active = :is_active";
    $params[':is_active'] = ($status_filter === 'active') ? 1 : 0;
}

$sql .= " ORDER BY created_at DESC";

try {
    $stmt = $conn->prepare($sql);
    
    foreach ($params as $key => $value) {
        $stmt->bindValue($key, $value);
    }
    
    $stmt->execute();
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $error = "Failed to fetch users: " . $e->getMessage();
}

// Count users by role and status for the filter tabs
try {
    $counts_stmt = $conn->query("
        SELECT 
            role,
            is_active,
            COUNT(*) as count 
        FROM users 
        GROUP BY role, is_active
    ");
    $counts_data = $counts_stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Initialize counts
    $counts = [
        'all' => 0,
        'admin' => ['all' => 0, 'active' => 0, 'inactive' => 0],
        'customer' => ['all' => 0, 'active' => 0, 'inactive' => 0],
        'active' => 0,
        'inactive' => 0
    ];
    
    foreach ($counts_data as $cd) {
        $counts['all'] += $cd['count'];
        $counts[$cd['role']]['all'] += $cd['count'];
        $counts[$cd['role']][$cd['is_active'] ? 'active' : 'inactive'] += $cd['count'];
        $counts[$cd['is_active'] ? 'active' : 'inactive'] += $cd['count'];
    }
    
} catch (PDOException $e) {
    $error = "Failed to fetch user counts: " . $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Users | Cafe & Netic</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <style>
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
            <?php 
                // Update the page title in topnav
                $page_title = "Manage Users";
                include '../includes/admin_topnav.php'; 
            ?>
            
            <!-- Users Content -->
            <main class="p-6">
                <div class="flex justify-between items-center mb-6">
                    <h1 class="text-2xl font-bold text-gray-800">User Management</h1>
                    <a href="admin_users_add.php" class="bg-amber-600 hover:bg-amber-700 text-white px-4 py-2 rounded-md text-sm font-medium">
                        Add New User
                    </a>
                </div>

                <?php if (isset($error)): ?>
                    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-6">
                        <?= htmlspecialchars($error) ?>
                    </div>
                <?php endif; ?>

                <!-- Filter Tabs -->
                <div class="mb-6 space-y-4">
                    <!-- Role Filter -->
                    <div class="border-b border-gray-200">
                        <nav class="-mb-px flex space-x-8">
                            <a href="admin_users.php" 
                               class="<?= ($role_filter === 'all' && $status_filter === 'active') ? 'border-amber-500 text-amber-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' ?> whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
                                All Users
                                <span class="bg-gray-100 text-gray-600 ml-1 py-0.5 px-2 rounded-full text-xs">
                                    <?= $counts['all'] ?>
                                </span>
                            </a>
                            
                            <a href="admin_users.php?role=admin" 
                               class="<?= $role_filter === 'admin' ? 'border-amber-500 text-amber-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' ?> whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
                                Admins
                                <span class="bg-gray-100 text-gray-600 ml-1 py-0.5 px-2 rounded-full text-xs">
                                    <?= $counts['admin']['all'] ?>
                                </span>
                            </a>
                            
                            <a href="admin_users.php?role=customer" 
                               class="<?= $role_filter === 'customer' ? 'border-amber-500 text-amber-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' ?> whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
                                Customers
                                <span class="bg-gray-100 text-gray-600 ml-1 py-0.5 px-2 rounded-full text-xs">
                                    <?= $counts['customer']['all'] ?>
                                </span>
                            </a>
                        </nav>
                    </div>
                    
                    <!-- Status Filter -->
                    <div class="border-b border-gray-200">
                        <nav class="-mb-px flex space-x-8">
                            <a href="admin_users.php?status=active<?= $role_filter !== 'all' ? '&role='.$role_filter : '' ?>" 
                               class="<?= $status_filter === 'active' ? 'border-amber-500 text-amber-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' ?> whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
                                Active
                                <span class="bg-gray-100 text-gray-600 ml-1 py-0.5 px-2 rounded-full text-xs">
                                    <?= $counts['active'] ?>
                                </span>
                            </a>
                            
                            <a href="admin_users.php?status=inactive<?= $role_filter !== 'all' ? '&role='.$role_filter : '' ?>" 
                               class="<?= $status_filter === 'inactive' ? 'border-amber-500 text-amber-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' ?> whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
                                Inactive
                                <span class="bg-gray-100 text-gray-600 ml-1 py-0.5 px-2 rounded-full text-xs">
                                    <?= $counts['inactive'] ?>
                                </span>
                            </a>
                            
                            <a href="admin_users.php?status=all<?= $role_filter !== 'all' ? '&role='.$role_filter : '' ?>" 
                               class="<?= $status_filter === 'all' ? 'border-amber-500 text-amber-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' ?> whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
                                All Statuses
                                <span class="bg-gray-100 text-gray-600 ml-1 py-0.5 px-2 rounded-full text-xs">
                                    <?= $counts['all'] ?>
                                </span>
                            </a>
                        </nav>
                    </div>
                </div>

                <!-- Users Table -->
                <div class="bg-white shadow rounded-lg overflow-hidden">
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">User ID</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Contact</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Username</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Role</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Joined</th>
                                    <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                <?php if (empty($users)): ?>
                                    <tr>
                                        <td colspan="8" class="px-6 py-4 text-center text-sm text-gray-500">No users found</td>
                                    </tr>
                                <?php else: ?>
                                    <?php foreach ($users as $user): ?>
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">#<?= $user['user_id'] ?></td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm font-medium text-gray-900"><?= htmlspecialchars($user['first_name'] . ' ' . $user['last_name']) ?></div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm text-gray-900"><?= htmlspecialchars($user['email']) ?></div>
                                            <div class="text-sm text-gray-500"><?= htmlspecialchars($user['phone'] ?? 'N/A') ?></div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            <?= htmlspecialchars($user['username']) ?>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="px-2 py-1 text-xs rounded-full user-role-<?= $user['role'] ?>">
                                                <?= ucfirst($user['role']) ?>
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="px-2 py-1 text-xs rounded-full user-status-<?= $user['is_active'] ? 'active' : 'inactive' ?>">
                                                <?= $user['is_active'] ? 'Active' : 'Inactive' ?>
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            <?= date('M j, Y', strtotime($user['created_at'])) ?>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                            <a href="admin_users_edit.php?id=<?= $user['user_id'] ?>" class="text-amber-600 hover:text-amber-900 mr-3">Edit</a>
                                            <a href="#" class="text-blue-600 hover:text-blue-900" 
                                               @click.prevent="confirmToggleStatus(<?= $user['user_id'] ?>, <?= $user['is_active'] ?>)">
                                                <?= $user['is_active'] ? 'Deactivate' : 'Activate' ?>
                                            </a>
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

    <!-- Status Toggle Modal -->
    <div x-data="{ 
        showModal: false, 
        userId: null, 
        currentStatus: false,
        openToggleModal(id, status) {
            this.userId = id;
            this.currentStatus = status;
            this.showModal = true;
        },
        async toggleStatus() {
            if (!this.userId) return;
            
            try {
                const response = await fetch('toggle_user_status.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({
                        user_id: this.userId,
                        new_status: !this.currentStatus
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
                    <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">
                        <?= $user['is_active'] ? 'Deactivate User' : 'Activate User' ?>
                    </h3>
                    
                    <div class="mb-4">
                        <p class="text-sm text-gray-500">
                            Are you sure you want to <?= $user['is_active'] ? 'deactivate' : 'activate' ?> this user?
                            <?= $user['is_active'] ? 'They will no longer be able to access the system.' : 'They will regain access to the system.' ?>
                        </p>
                    </div>
                </div>
                <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                    <button type="button" @click="toggleStatus()" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-amber-600 text-base font-medium text-white hover:bg-amber-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-amber-500 sm:ml-3 sm:w-auto sm:text-sm">
                        Confirm
                    </button>
                    <button type="button" @click="showModal = false" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-amber-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                        Cancel
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Function to confirm status toggle
        function confirmToggleStatus(id, currentStatus) {
            const modal = document.querySelector('[x-data]').__x.$data;
            modal.openToggleModal(id, currentStatus);
        }
    </script>
</body>
</html>
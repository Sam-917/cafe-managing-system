<!-- Sidebar -->
<script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

<div class="sidebar w-64 bg-white shadow-lg z-10 transition-all duration-300 ease-in-out" :class="{'-ml-64': !sidebarOpen}" x-data="{ sidebarOpen: true }">
    <!-- Toggle Button (outside sidebar, fixed position) -->
    <button @click="sidebarOpen = !sidebarOpen" class="fixed z-20 left-64 top-4 ml-1 p-2 rounded-md bg-amber-100 text-amber-800 transition-all duration-300 ease-in-out" :class="{ 'left-0': !sidebarOpen }">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
            <path x-show="sidebarOpen" fill-rule="evenodd" d="M15.707 15.707a1 1 0 01-1.414 0l-5-5a1 1 0 010-1.414l5-5a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 010 1.414zm-6 0a1 1 0 01-1.414 0l-5-5a1 1 0 010-1.414l5-5a1 1 0 011.414 1.414L5.414 10l4.293 4.293a1 1 0 010 1.414z" clip-rule="evenodd" />
            <path x-show="!sidebarOpen" fill-rule="evenodd" d="M4.293 15.707a1 1 0 010-1.414L8.586 10 4.293 5.707a1 1 0 011.414-1.414l5 5a1 1 0 010 1.414l-5 5a1 1 0 01-1.414 0zm6 0a1 1 0 010-1.414L14.586 10l-4.293-4.293a1 1 0 011.414-1.414l5 5a1 1 0 010 1.414l-5 5a1 1 0 01-1.414 0z" clip-rule="evenodd" />
        </svg>
    </button>
    
    <a href="../index.php">
        <div class="p-4 border-b border-gray-200">
            <div class="flex items-center space-x-2">
                <div class="text-2xl">☕</div>
                <span class="font-playfair text-xl font-bold text-amber-800">Cafe & Netic</span>
                <span class="text-xs bg-amber-100 text-amber-800 px-2 py-1 rounded-full">Admin</span>
            </div>
        </div>
    </a>
    
    <div class="p-4">
        <div class="flex items-center space-x-3 mb-6">
            <div class="w-10 h-10 rounded-full bg-amber-600 flex items-center justify-center text-white font-semibold">
                <?= strtoupper(substr($_SESSION['first_name'] ?? 'A', 0, 1)) ?>
            </div>
            <div>
                <p class="font-medium"><?= htmlspecialchars($_SESSION['first_name'] ?? 'Admin') ?></p>
                <p class="text-xs text-gray-500">Administrator</p>
            </div>
        </div>
        
        <nav class="space-y-1">
            <?php
            // Get current page filename
            $current_page = basename($_SERVER['PHP_SELF']);
            ?>
            
            <!-- Dashboard -->
            <a href="dashboard.php" class="sidebar-item flex items-center space-x-3 px-3 py-2 rounded-lg <?= ($current_page == 'dashboard.php') ? 'bg-amber-50 text-amber-700' : '' ?>">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                    <path d="M10 2a8 8 0 100 16 8 8 0 000-16zm0 14a6 6 0 110-12 6 6 0 010 12z" />
                    <path d="M10 12a2 2 0 100-4 2 2 0 000 4z" />
                </svg>
                <span>Dashboard</span>
            </a>
            
            <!-- Orders Dropdown -->
            <div x-data="{ open: false }" class="relative">
                <button @click="open = !open" class="sidebar-item flex items-center justify-between w-full px-3 py-2 rounded-lg <?= (in_array($current_page, ['admin_orders.php', 'admin_order_details.php'])) ? 'bg-amber-50 text-amber-700' : '' ?>">
                    <div class="flex items-center space-x-3">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 <?= (in_array($current_page, ['admin_orders.php', 'admin_order_details.php'])) ? 'text-amber-700' : 'text-gray-500' ?>" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M10 2a4 4 0 00-4 4v1H5a1 1 0 00-.994.89l-1 9A1 1 0 004 18h12a1 1 0 00.994-1.11l-1-9A1 1 0 0015 7h-1V6a4 4 0 00-4-4zm2 5V6a2 2 0 10-4 0v1h4zm-6 3a1 1 0 112 0 1 1 0 01-2 0zm7-1a1 1 0 100 2 1 1 0 000-2z" clip-rule="evenodd" />
                        </svg>
                        <span>Orders</span>
                    </div>
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 transition-transform duration-200" :class="{'transform rotate-90': open}" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" />
                    </svg>
                </button>
                <div x-show="open" x-transition:enter="transition ease-out duration-100" 
                    x-transition:enter-start="opacity-0 scale-95" 
                    x-transition:enter-end="opacity-100 scale-100" 
                    x-transition:leave="transition ease-in duration-75" 
                    x-transition:leave-start="opacity-100 scale-100" 
                    x-transition:leave-end="opacity-0 scale-95" 
                    class="ml-8 mt-1 space-y-1">
                    <a href="admin_orders.php" class="block px-3 py-2 text-sm rounded-lg <?= ($current_page == 'admin_orders.php') ? 'bg-amber-50 text-amber-700' : 'hover:bg-gray-100' ?>">All Orders</a>
                    <a href="admin_orders.php?status=pending" class="block px-3 py-2 text-sm rounded-lg <?= ($current_page == 'admin_orders.php' && isset($_GET['status'])) && $_GET['status'] == 'pending' ? 'bg-amber-50 text-amber-700' : 'hover:bg-gray-100' ?>">Pending</a>
                    <a href="admin_orders.php?status=completed" class="block px-3 py-2 text-sm rounded-lg <?= ($current_page == 'admin_orders.php' && isset($_GET['status'])) && $_GET['status'] == 'completed' ? 'bg-amber-50 text-amber-700' : 'hover:bg-gray-100' ?>">Completed</a>
                    <a href="admin_orders.php?status=cancelled" class="block px-3 py-2 text-sm rounded-lg <?= ($current_page == 'admin_orders.php' && isset($_GET['status'])) && $_GET['status'] == 'cancelled' ? 'bg-amber-50 text-amber-700' : 'hover:bg-gray-100' ?>">Cancelled</a>
                </div>
            </div>
            
            <!-- Locations Dropdown -->
            <div x-data="{ open: false }" class="relative">
                <button @click="open = !open" class="sidebar-item flex items-center justify-between w-full px-3 py-2 rounded-lg <?= (in_array($current_page, ['admin_locations.php', 'admin_tables.php'])) ? 'bg-amber-50 text-amber-700' : '' ?>">
                    <div class="flex items-center space-x-3">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 <?= (in_array($current_page, ['admin_locations.php', 'admin_tables.php'])) ? 'text-amber-700' : 'text-gray-500' ?>" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M5.05 4.05a7 7 0 119.9 9.9L10 18.9l-4.95-4.95a7 7 0 010-9.9zM10 11a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd" />
                        </svg>
                        <span>Locations</span>
                    </div>
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 transition-transform duration-200" :class="{'transform rotate-90': open}" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" />
                    </svg>
                </button>
                <div x-show="open" x-transition class="ml-8 mt-1 space-y-1">
                    <a href="admin_locations.php" class="block px-3 py-2 text-sm rounded-lg <?= ($current_page == 'admin_locations.php') ? 'bg-amber-50 text-amber-700' : 'hover:bg-gray-100' ?>">Manage Locations</a>
                    <a href="admin_tables.php" class="block px-3 py-2 text-sm rounded-lg <?= ($current_page == 'admin_tables.php') ? 'bg-amber-50 text-amber-700' : 'hover:bg-gray-100' ?>">Manage Tables</a>
                </div>
            </div>
            
            <!-- Reservations Dropdown -->
            <div x-data="{ open: false }" class="relative">
                <button @click="open = !open" class="sidebar-item flex items-center justify-between w-full px-3 py-2 rounded-lg <?= (in_array($current_page, ['admin_reservations.php', 'admin_reservation_details.php'])) ? 'bg-amber-50 text-amber-700' : '' ?>">
                    <div class="flex items-center space-x-3">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 <?= (in_array($current_page, ['admin_reservations.php', 'admin_reservation_details.php'])) ? 'text-amber-700' : 'text-gray-500' ?>" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z" clip-rule="evenodd" />
                        </svg>
                        <span>Reservations</span>
                    </div>
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 transition-transform duration-200" :class="{'transform rotate-90': open}" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" />
                    </svg>
                </button>
                <div x-show="open" x-transition class="ml-8 mt-1 space-y-1">
                    <a href="admin_reservations.php" class="block px-3 py-2 text-sm rounded-lg <?= ($current_page == 'admin_reservations.php' && !isset($_GET['status'])) ? 'bg-amber-50 text-amber-700' : 'hover:bg-gray-100' ?>">All Reservations</a>
                    <a href="admin_reservations.php?status=confirmed" class="block px-3 py-2 text-sm rounded-lg <?= ($current_page == 'admin_reservations.php' && isset($_GET['status'])) && $_GET['status'] == 'confirmed' ? 'bg-amber-50 text-amber-700' : 'hover:bg-gray-100' ?>">Confirmed</a>
                    <a href="admin_reservations.php?status=pending" class="block px-3 py-2 text-sm rounded-lg <?= ($current_page == 'admin_reservations.php' && isset($_GET['status'])) && $_GET['status'] == 'pending' ? 'bg-amber-50 text-amber-700' : 'hover:bg-gray-100' ?>">Pending</a>
                </div>
            </div>
            
            <!-- Products Dropdown -->
            <div x-data="{ open: false }" class="relative">
                <button @click="open = !open" class="sidebar-item flex items-center justify-between w-full px-3 py-2 rounded-lg <?= (in_array($current_page, ['admin_products.php', 'admin_products_add.php', 'admin_products_edit.php', 'admin_categories.php', 'admin_seasonal.php', 'admin_nutrition.php'])) ? 'bg-amber-50 text-amber-700' : '' ?>">
                    <div class="flex items-center space-x-3">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 <?= (in_array($current_page, ['admin_products.php', 'admin_products_add.php', 'admin_products_edit.php', 'admin_categories.php', 'admin_seasonal.php', 'admin_nutrition.php'])) ? 'text-amber-700' : 'text-gray-500' ?>" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M10 2a4 4 0 00-4 4v1H5a1 1 0 00-.994.89l-1 9A1 1 0 004 18h12a1 1 0 00.994-1.11l-1-9A1 1 0 0015 7h-1V6a4 4 0 00-4-4zm2 5V6a2 2 0 10-4 0v1h4zm-6 3a1 1 0 112 0 1 1 0 01-2 0zm7-1a1 1 0 100 2 1 1 0 000-2z" clip-rule="evenodd" />
                        </svg>
                        <span>Products</span>
                    </div>
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 transition-transform duration-200" :class="{'transform rotate-90': open}" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" />
                    </svg>
                </button>
                <div x-show="open" x-transition class="ml-8 mt-1 space-y-1">
                    <a href="admin_products.php" class="block px-3 py-2 text-sm rounded-lg <?= ($current_page == 'admin_products.php') ? 'bg-amber-50 text-amber-700' : 'hover:bg-gray-100' ?>">All Products</a>
                    <a href="admin_categories.php" class="block px-3 py-2 text-sm rounded-lg <?= ($current_page == 'admin_categories.php') ? 'bg-amber-50 text-amber-700' : 'hover:bg-gray-100' ?>">Categories</a>
                    <a href="admin_seasonal.php" class="block px-3 py-2 text-sm rounded-lg <?= ($current_page == 'admin_seasonal.php') ? 'bg-amber-50 text-amber-700' : 'hover:bg-gray-100' ?>">Seasonal Items</a>
                    <a href="admin_nutrition.php" class="block px-3 py-2 text-sm rounded-lg <?= ($current_page == 'admin_nutrition.php') ? 'bg-amber-50 text-amber-700' : 'hover:bg-gray-100' ?>">Nutritional Info</a>
                </div>
            </div>
            
            <!-- Users -->
            <a href="admin_users.php" class="sidebar-item flex items-center space-x-3 px-3 py-2 rounded-lg <?= ($current_page == 'admin_users.php') ? 'bg-amber-50 text-amber-700' : '' ?>">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 <?= ($current_page == 'admin_users.php') ? 'text-amber-700' : 'text-gray-500' ?>" viewBox="0 0 20 20" fill="currentColor">
                    <path d="M9 6a3 3 0 11-6 0 3 3 0 016 0zM17 6a3 3 0 11-6 0 3 3 0 016 0zM12.93 17c.046-.327.07-.66.07-1a6.97 6.97 0 00-1.5-4.33A5 5 0 0119 16v1h-6.07zM6 11a5 5 0 015 5v1H1v-1a5 5 0 015-5z" />
                </svg>
                <span>Users</span>
            </a>
            
            <!-- Inventory -->
            <a href="admin_inventory.php" class="sidebar-item flex items-center space-x-3 px-3 py-2 rounded-lg <?= ($current_page == 'admin_inventory.php') ? 'bg-amber-50 text-amber-700' : '' ?>">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 <?= ($current_page == 'admin_inventory.php') ? 'text-amber-700' : 'text-gray-500' ?>" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M3 17a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm3.293-7.707a1 1 0 011.414 0L9 10.586V3a1 1 0 112 0v7.586l1.293-1.293a1 1 0 111.414 1.414l-3 3a1 1 0 01-1.414 0l-3-3a1 1 0 010-1.414z" clip-rule="evenodd" />
                </svg>
                <span>Inventory</span>
            </a>
            
            <!-- Reports Dropdown -->
            <div x-data="{ open: false }" class="relative">
                <button @click="open = !open" class="sidebar-item flex items-center justify-between w-full px-3 py-2 rounded-lg <?= (in_array($current_page, ['admin_reports_sales.php', 'admin_reports_reservations.php', 'admin_reports_inventory.php', 'admin_reports_users.php'])) ? 'bg-amber-50 text-amber-700' : '' ?>">
                    <div class="flex items-center space-x-3">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 <?= (in_array($current_page, ['admin_reports_sales.php', 'admin_reports_reservations.php', 'admin_reports_inventory.php', 'admin_reports_users.php'])) ? 'text-amber-700' : 'text-gray-500' ?>" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M6 2a2 2 0 00-2 2v12a2 2 0 002 2h8a2 2 0 002-2V7.414A2 2 0 0015.414 6L12 2.586A2 2 0 0010.586 2H6zm2 10a1 1 0 10-2 0v3a1 1 0 102 0v-3zm2-3a1 1 0 011 1v5a1 1 0 11-2 0v-5a1 1 0 011-1zm4-1a1 1 0 10-2 0v7a1 1 0 102 0V8z" clip-rule="evenodd" />
                        </svg>
                        <span>Reports</span>
                    </div>
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 transition-transform duration-200" :class="{'transform rotate-90': open}" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" />
                    </svg>
                </button>
                <div x-show="open" x-transition class="ml-8 mt-1 space-y-1">
                    <a href="admin_reports_sales.php" class="block px-3 py-2 text-sm rounded-lg <?= ($current_page == 'admin_reports_sales.php') ? 'bg-amber-50 text-amber-700' : 'hover:bg-gray-100' ?>">Sales Reports</a>
                    <a href="admin_reports_reservations.php" class="block px-3 py-2 text-sm rounded-lg <?= ($current_page == 'admin_reports_reservations.php') ? 'bg-amber-50 text-amber-700' : 'hover:bg-gray-100' ?>">Reservation Reports</a>
                    <a href="admin_reports_inventory.php" class="block px-3 py-2 text-sm rounded-lg <?= ($current_page == 'admin_reports_inventory.php') ? 'bg-amber-50 text-amber-700' : 'hover:bg-gray-100' ?>">Inventory Reports</a>
                    <a href="admin_reports_users.php" class="block px-3 py-2 text-sm rounded-lg <?= ($current_page == 'admin_reports_users.php') ? 'bg-amber-50 text-amber-700' : 'hover:bg-gray-100' ?>">User Activity</a>
                </div>
            </div>
            
            <!-- Settings -->
            <a href="admin_settings.php" class="sidebar-item flex items-center space-x-3 px-3 py-2 rounded-lg <?= ($current_page == 'admin_settings.php') ? 'bg-amber-50 text-amber-700' : '' ?>">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 <?= ($current_page == 'admin_settings.php') ? 'text-amber-700' : 'text-gray-500' ?>" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M11.49 3.17c-.38-1.56-2.6-1.56-2.98 0a1.532 1.532 0 01-2.286.948c-1.372-.836-2.942.734-2.106 2.106.54.886.061 2.042-.947 2.287-1.561.379-1.561 2.6 0 2.978a1.532 1.532 0 01.947 2.287c-.836 1.372.734 2.942 2.106 2.106a1.532 1.532 0 012.287.947c.379 1.561 2.6 1.561 2.978 0a1.533 1.533 0 012.287-.947c1.372.836 2.942-.734 2.106-2.106a1.533 1.533 0 01.947-2.287c1.561-.379 1.561-2.6 0-2.978a1.532 1.532 0 01-.947-2.287c.836-1.372-.734-2.942-2.106-2.106a1.532 1.532 0 01-2.287-.947zM10 13a3 3 0 100-6 3 3 0 000 6z" clip-rule="evenodd" />
                </svg>
                <span>Settings</span>
            </a>
        </nav>
    </div>
    
    <div class="mt-auto p-4 border-t border-gray-200">
        <a href="../logout.php" class="sidebar-item flex items-center space-x-3 px-3 py-2 rounded-lg text-gray-600 hover:bg-gray-100">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                <path fill-rule="evenodd" d="M3 3a1 1 0 00-1 1v12a1 1 0 102 0V4a1 1 0 00-1-1zm10.293 9.293a1 1 0 001.414 1.414l3-3a1 1 0 000-1.414l-3-3a1 1 0 10-1.414 1.414L14.586 9H7a1 1 0 100 2h7.586l-1.293 1.293z" clip-rule="evenodd" />
            </svg>
            <span>Logout</span>
        </a>
    </div>
</div>
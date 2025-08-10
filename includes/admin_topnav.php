<!-- Top Navigation -->
<header class="bg-white shadow-sm">
    <div class="px-6 py-4 flex justify-between items-center">
        <h1 class="text-2xl font-bold text-gray-800 pl-10">Dashboard Overview</h1>
        <div class="flex items-center space-x-4">
            <div class="text-sm bg-amber-100 text-amber-800 px-3 py-1 rounded-full">
                <?= date('l, F j, Y') ?>
            </div>
            <button class="p-2 rounded-full bg-gray-100 text-gray-600 hover:bg-gray-200">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                    <path d="M10 2a6 6 0 00-6 6v3.586l-.707.707A1 1 0 004 14h12a1 1 0 00.707-1.707L16 11.586V8a6 6 0 00-6-6zM10 18a3 3 0 01-3-3h6a3 3 0 01-3 3z" />
                </svg>
            </button>
            <div class="relative" x-data="{ open: false }" 
                @mouseenter="setTimeout(() => open = true, 100)" 
                @mouseleave="setTimeout(() => open = false, 300)">
                <button class="flex items-center space-x-2 focus:outline-none" @click="open = !open">
                    <span class="font-medium text-gray-700"><?= htmlspecialchars($_SESSION['first_name'] ?? 'Admin') ?></span>
                    <div class="w-8 h-8 rounded-full bg-amber-600 flex items-center justify-center text-white font-semibold">
                        <?= strtoupper(substr($_SESSION['first_name'] ?? 'A', 0, 1)) ?>
                    </div>
                </button>
                
                <!-- Dropdown Menu -->
                <div class="absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg py-1 z-50 transition-all duration-300 ease-in-out"
                    x-show="open"
                    x-transition:enter="transition ease-out duration-200"
                    x-transition:enter-start="opacity-0 transform scale-95"
                    x-transition:enter-end="opacity-100 transform scale-100"
                    x-transition:leave="transition ease-in duration-150"
                    x-transition:leave-start="opacity-100 transform scale-100"
                    x-transition:leave-end="opacity-0 transform scale-95"
                    @mouseenter="open = true"
                    @mouseleave="open = false"
                    style="display: none;">
                    <a href="admin_settings.php" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 transition-colors duration-200">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 inline mr-2" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M11.49 3.17c-.38-1.56-2.6-1.56-2.98 0a1.532 1.532 0 01-2.286.948c-1.372-.836-2.942.734-2.106 2.106.54.886.061 2.042-.947 2.287-1.561.379-1.561 2.6 0 2.978a1.532 1.532 0 01.947 2.287c-.836 1.372.734 2.942 2.106 2.106a1.532 1.532 0 012.287.947c.379 1.561 2.6 1.561 2.978 0a1.533 1.533 0 012.287-.947c1.372.836 2.942-.734 2.106-2.106a1.533 1.533 0 01.947-2.287c1.561-.379 1.561-2.6 0-2.978a1.532 1.532 0 01-.947-2.287c.836-1.372-.734-2.942-2.106-2.106a1.532 1.532 0 01-2.287-.947zM10 13a3 3 0 100-6 3 3 0 000 6z" clip-rule="evenodd" />
                        </svg>
                        Settings
                    </a>
                    <a href="../logout.php" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 transition-colors duration-200">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 inline mr-2" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M3 3a1 1 0 00-1 1v12a1 1 0 102 0V4a1 1 0 00-1-1zm10.293 9.293a1 1 0 001.414 1.414l3-3a1 1 0 000-1.414l-3-3a1 1 0 10-1.414 1.414L14.586 9H7a1 1 0 100 2h7.586l-1.293 1.293z" clip-rule="evenodd" />
                        </svg>
                        Logout
                    </a>
                </div>
            </div>
        </div>
    </div>
</header>
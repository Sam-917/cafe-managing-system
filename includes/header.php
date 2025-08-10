<?php
session_start();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cafe & Netic</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;700&family=Inter:wght@300;400;500;600&display=swap');
        
        .font-playfair { font-family: 'Playfair Display', serif; }
        .font-inter { font-family: 'Inter', sans-serif; }
        
        /* Custom animations */
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        @keyframes float {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-10px); }
        }
        
        .animate-fadeInUp {
            animation: fadeInUp 0.8s ease-out forwards;
        }
        
        .animate-float {
            animation: float 3s ease-in-out infinite;
        }
        
        .hover-lift {
            transition: all 0.3s ease;
        }
        
        .hover-lift:hover {
            transform: translateY(-5px);
            box-shadow: 0 20px 40px rgba(0,0,0,0.1);
        }
        
        .nav-item {
            position: relative;
            transition: all 0.3s cubic-bezier(0.25, 0.8, 0.25, 1);
            transform-origin: center;
        }

        .nav-item:hover {
            color: #92400e; /* amber-800 */
            transform: translateY(-2px);
            text-shadow: 0 2px 4px rgba(146, 64, 14, 0.1);
        }

        .nav-item::after {
            content: '';
            position: absolute;
            width: 0;
            height: 2px;
            bottom: -5px;
            left: 0; /* Changed from left:50% to left:0 for consistent alignment */
            background: linear-gradient(90deg, #8B4513, #D2691E);
            transition: all 0.3s ease;
            transform: none; /* Removed translateX(-50%) */
        }

        .nav-item:hover::after {
            width: 100%;
        }
        
        .coffee-steam {
            position: relative;
        }
        
        .coffee-steam {
            position: relative;
        }
        
        .coffee-steam::before {
            content: '☁️';
            position: absolute;
            top: -20px;
            left: 50%;
            transform: translateX(-50%);
            animation: float 2s ease-in-out infinite;
            opacity: 0.7;
        }

        /* Add to your existing styles */
        .profile-dropdown {
            transition: all 0.3s ease;
        }

        .profile-dropdown:hover .dropdown-menu {
            opacity: 1;
            visibility: visible;
            transform: translateY(0);
        }

        .dropdown-menu {
            opacity: 0;
            visibility: hidden;
            transform: translateY(-10px);
            transition: all 0.3s ease;
        }

        /* Enhanced Hover Animations */
        .nav-item {
            position: relative;
            transition: all 0.3s cubic-bezier(0.25, 0.8, 0.25, 1);
            transform-origin: center;
        }

        .nav-item:hover {
            color: #92400e; /* amber-800 */
            transform: translateY(-2px) scale(1.05);
            text-shadow: 0 2px 4px rgba(146, 64, 14, 0.1);
        }

        .nav-item::after {
            content: '';
            position: absolute;
            width: 0;
            height: 2px;
            bottom: -2px;
            left: 0;
            background: linear-gradient(90deg, #92400e, #c2410c);
            transition: all 0.4s cubic-bezier(0.25, 0.8, 0.25, 1);
        }

        .nav-item:hover::after {
            width: 100%;
        }

        /* Enhanced Join Us Button */
        .join-btn {
            background: linear-gradient(135deg, #92400e, #c2410c);
            box-shadow: 0 4px 6px rgba(146, 64, 14, 0.1), 
                        0 1px 3px rgba(0, 0, 0, 0.08);
            transition: all 0.3s cubic-bezier(0.25, 0.8, 0.25, 1);
        }

        .join-btn:hover {
            transform: translateY(-2px) scale(1.05);
            box-shadow: 0 10px 15px rgba(146, 64, 14, 0.1), 
                        0 4px 6px rgba(0, 0, 0, 0.05);
        }

        /* Logo Animation */
        .logo-container:hover .coffee-icon {
            animation: jiggle 0.4s ease-in-out;
        }

        @keyframes jiggle {
            0%, 100% { transform: rotate(0deg); }
            25% { transform: rotate(-5deg); }
            75% { transform: rotate(5deg); }
        }

    </style>
</head>
<body class="font-inter bg-gradient-to-br from-amber-50 to-orange-100 min-h-screen">
    <!-- Navigation Bar -->
    <nav class="bg-white/90 backdrop-blur-md shadow-lg sticky top-0 z-50 transition-all duration-300">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-16">
                <!-- Logo --> 
                <a href="index.php" class="flex items-center space-x-2">
                    <div class="text-2xl">☕</div>
                    <span class="font-playfair text-2xl font-bold text-amber-800">Cafe & Netic</span>
                </a>
                
                <!-- Navigation Links --> 
                <div class="hidden md:flex space-x-8 items-center">
                    <a href="about_us.php" class="nav-item text-gray-700 hover:text-amber-700 font-medium px-3 py-2">About Us</a>
                    <a href="menu.php" class="nav-item text-gray-700 hover:text-amber-700 font-medium px-3 py-2">Menu</a>
                    <a href="order.php" class="nav-item text-gray-700 hover:text-amber-700 font-medium px-3 py-2">Order Now</a>
                    <a href="reservation.php" class="nav-item text-gray-700 hover:text-amber-700 font-medium px-3 py-2">Reservation</a>
                    <?php if (isset($_SESSION['logged_in']) && $_SESSION['logged_in']): ?>
                        <!-- User Profile Dropdown -->
                        <div class="relative" x-data="{ open: false }" 
                            @mouseenter="setTimeout(() => open = true, 100)" 
                            @mouseleave="setTimeout(() => open = false, 300)">
                            <button class="flex items-center space-x-2 focus:outline-none" @click="open = !open">
                                <span class="font-medium text-gray-700"><?= htmlspecialchars($_SESSION['first_name'] ?? 'User') ?></span>
                                <div class="w-8 h-8 rounded-full bg-amber-600 flex items-center justify-center text-white font-semibold">
                                    <?= strtoupper(substr($_SESSION['first_name'] ?? 'U', 0, 1)) ?>
                                </div>
                                <svg class="w-4 h-4 text-gray-600 transition-transform" :class="{'transform rotate-180': open}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                </svg>
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
                                <a href="profile.php" class="block px-4 py-2 text-sm text-gray-700 hover:bg-amber-50 transition-colors duration-200 flex items-center">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd" />
                                    </svg>
                                    Profile
                                </a>
                                <a href="settings.php" class="block px-4 py-2 text-sm text-gray-700 hover:bg-amber-50 transition-colors duration-200 flex items-center">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M11.49 3.17c-.38-1.56-2.6-1.56-2.98 0a1.532 1.532 0 01-2.286.948c-1.372-.836-2.942.734-2.106 2.106.54.886.061 2.042-.947 2.287-1.561.379-1.561 2.6 0 2.978a1.532 1.532 0 01.947 2.287c-.836 1.372.734 2.942 2.106 2.106a1.532 1.532 0 012.287.947c.379 1.561 2.6 1.561 2.978 0a1.533 1.533 0 012.287-.947c1.372.836 2.942-.734 2.106-2.106a1.533 1.533 0 01.947-2.287c1.561-.379 1.561-2.6 0-2.978a1.532 1.532 0 01-.947-2.287c.836-1.372-.734-2.942-2.106-2.106a1.532 1.532 0 01-2.287-.947zM10 13a3 3 0 100-6 3 3 0 000 6z" clip-rule="evenodd" />
                                    </svg>
                                    Settings
                                </a>
                                <?php if ($_SESSION['role'] === 'admin' || $_SESSION['role'] === 'staff'): ?>
                                    <div class="border-t border-gray-100"></div>
                                    <a href="admin/dashboard.php" class="block px-4 py-2 text-sm text-gray-700 hover:bg-amber-50 transition-colors duration-200 flex items-center">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" viewBox="0 0 20 20" fill="currentColor">
                                            <path d="M10 2a8 8 0 100 16 8 8 0 000-16zm0 14a6 6 0 110-12 6 6 0 010 12z" />
                                            <path d="M10 12a2 2 0 100-4 2 2 0 000 4z" />
                                        </svg>
                                        Dashboard
                                    </a>
                                <?php endif; ?>
                                <div class="border-t border-gray-100"></div>
                                <a href="logout.php" class="block px-4 py-2 text-sm text-gray-700 hover:bg-amber-50 transition-colors duration-200 flex items-center">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M3 3a1 1 0 00-1 1v12a1 1 0 102 0V4a1 1 0 00-1-1zm10.293 9.293a1 1 0 001.414 1.414l3-3a1 1 0 000-1.414l-3-3a1 1 0 10-1.414 1.414L14.586 9H7a1 1 0 100 2h7.586l-1.293 1.293z" clip-rule="evenodd" />
                                    </svg>
                                    Sign Out
                                </a>
                            </div>
                        </div>
                    <?php else: ?>
                        <a href="login.php" class="nav-item text-gray-700 hover:text-amber-700 font-medium px-3 py-2">Sign In</a>
                        <a href="register.php" class="bg-gradient-to-r from-amber-600 to-orange-600 text-white px-6 py-2 rounded-full hover:from-amber-700 hover:to-orange-700 transform hover:scale-105 transition-all duration-300 shadow-lg">Join Us</a>
                    <?php endif; ?>
                </div>

                <!-- Mobile menu button -->
                <button id="mobile-menu-btn" class="md:hidden text-gray-700 hover:text-amber-700 focus:outline-none">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                    </svg>
                </button>
            </div>
        </div>
        
        <!-- Mobile menu -->
        <div id="mobile-menu" class="md:hidden hidden bg-white/95 backdrop-blur-md border-t">
            <div class="px-2 pt-2 pb-3 space-y-1">
                <a href="about_us.php" class="block px-3 py-2 text-gray-700 hover:text-amber-700 hover:bg-amber-50 rounded-md transition-all">About Us</a>
                <a href="menu.php" class="block px-3 py-2 text-gray-700 hover:text-amber-700 hover:bg-amber-50 rounded-md transition-all">Menu</a>
                <a href="order.php" class="block px-3 py-2 text-gray-700 hover:text-amber-700 hover:bg-amber-50 rounded-md transition-all">Order Now</a>
                <a href="reservation.php" class="block px-3 py-2 text-gray-700 hover:text-amber-700 hover:bg-amber-50 rounded-md transition-all">Reservation</a>
                
                <?php if (isset($_SESSION['logged_in']) && $_SESSION['logged_in']): ?>
                    <a href="profile.php" class="block px-3 py-2 text-gray-700 hover:text-amber-700 hover:bg-amber-50 rounded-md transition-all">Profile</a>
                    <?php if ($_SESSION['role'] === 'admin' || $_SESSION['role'] === 'staff'): ?>
                        <a href="admin/dashboard.php" class="block px-3 py-2 text-gray-700 hover:text-amber-700 hover:bg-amber-50 rounded-md transition-all">Dashboard</a>
                    <?php endif; ?>
                    <a href="logout.php" class="block px-3 py-2 text-gray-700 hover:text-amber-700 hover:bg-amber-50 rounded-md transition-all">Sign Out</a>
                <?php else: ?>
                    <a href="login.php" class="block px-3 py-2 text-gray-700 hover:text-amber-700 hover:bg-amber-50 rounded-md transition-all">Sign In</a>
                    <a href="register.php" class="block px-3 py-2 bg-gradient-to-r from-amber-600 to-orange-600 text-white rounded-md hover:from-amber-700 hover:to-orange-700 transition-all">Join Us</a>
                <?php endif; ?>
            </div>
        </div>
    </nav>
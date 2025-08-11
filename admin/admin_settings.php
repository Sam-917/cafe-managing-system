<?php
session_start();
require '../includes/config.php';

// Check if user is admin
if (!isset($_SESSION['logged_in'])) {
    header("Location: ../login.php");
    exit();
}

if ($_SESSION['role'] !== 'admin') {
    header("Location: ../index.php");
    exit();
}

// Handle form submissions
$message = '';
$message_type = '';

// Update general settings
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_general'])) {
    try {
        $site_name = $_POST['site_name'];
        $contact_email = $_POST['contact_email'];
        $timezone = $_POST['timezone'];
        
        // Update settings in database
        $stmt = $conn->prepare("UPDATE site_settings SET 
                              site_name = ?, 
                              contact_email = ?, 
                              timezone = ? 
                              WHERE id = 1");
        $stmt->execute([$site_name, $contact_email, $timezone]);
        
        $message = 'General settings updated successfully!';
        $message_type = 'success';
    } catch (PDOException $e) {
        $message = 'Error updating settings: ' . $e->getMessage();
        $message_type = 'error';
    }
}

// Update appearance settings (including dark mode)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_appearance'])) {
    try {
        $dark_mode = isset($_POST['dark_mode']) ? 1 : 0;
        $primary_color = $_POST['primary_color'];
        $secondary_color = $_POST['secondary_color'];
        
        // Update settings in database
        $stmt = $conn->prepare("UPDATE site_settings SET 
                              dark_mode = ?, 
                              primary_color = ?, 
                              secondary_color = ? 
                              WHERE id = 1");
        $stmt->execute([$dark_mode, $primary_color, $secondary_color]);
        
        $message = 'Appearance settings updated successfully!';
        $message_type = 'success';
    } catch (PDOException $e) {
        $message = 'Error updating appearance settings: ' . $e->getMessage();
        $message_type = 'error';
    }
}

// Get current settings
try {
    $stmt = $conn->query("SELECT * FROM site_settings WHERE id = 1");
    $settings = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$settings) {
        // Initialize default settings if none exist
        $conn->query("INSERT INTO site_settings (site_name, contact_email, timezone, dark_mode, primary_color, secondary_color) 
                     VALUES ('Cafe & Netic', 'contact@cafenetic.com', 'UTC', 0, '#92400e', '#f59e0b')");
        $stmt = $conn->query("SELECT * FROM site_settings WHERE id = 1");
        $settings = $stmt->fetch(PDO::FETCH_ASSOC);
    }
} catch (PDOException $e) {
    $message = 'Error loading settings: ' . $e->getMessage();
    $message_type = 'error';
}

// Get available timezones
$timezones = DateTimeZone::listIdentifiers();
?>
<!DOCTYPE html>
<html lang="en" class="<?= $settings['dark_mode'] ? 'dark' : '' ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Settings | Cafe & Netic</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    colors: {
                        primary: {
                            DEFAULT: '<?= $settings['primary_color'] ?>',
                            light: '<?= adjustBrightness($settings['primary_color'], 0.8) ?>',
                            dark: '<?= adjustBrightness($settings['primary_color'], -0.2) ?>',
                        },
                        secondary: {
                            DEFAULT: '<?= $settings['secondary_color'] ?>',
                            light: '<?= adjustBrightness($settings['secondary_color'], 0.8) ?>',
                            dark: '<?= adjustBrightness($settings['secondary_color'], -0.2) ?>',
                        }
                    }
                }
            }
        }
        
        // Helper function to adjust color brightness
        function adjustBrightness(color, amount) {
            // This would be a PHP function in reality, simplified for JS example
            return color; // Actual implementation would adjust hex color
        }
    </script>
    <style>
        .color-preview {
            width: 24px;
            height: 24px;
            border-radius: 4px;
            display: inline-block;
            vertical-align: middle;
            margin-right: 8px;
            border: 1px solid #e5e7eb;
        }
    </style>
</head>
<body class="font-inter bg-gray-50 dark:bg-gray-900 text-gray-800 dark:text-gray-200">
    <!-- Admin Layout -->
    <div class="flex h-screen overflow-hidden"> 
        <?php include '../includes/admin_sidebar.php'; ?>
        
        <!-- Main Content -->
        <div class="flex-1 overflow-auto">
            <?php include '../includes/admin_topnav.php'; ?>
            
            <!-- Settings Content -->
            <main class="p-6">
                <?php if ($message): ?>
                    <div class="mb-6 p-4 rounded-lg <?= $message_type === 'success' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' ?>">
                        <?= htmlspecialchars($message) ?>
                    </div>
                <?php endif; ?>
                
                <div class="mb-6 flex justify-between items-center">
                    <h1 class="text-2xl font-bold">Settings</h1>
                    <button id="darkModeToggle" class="p-2 rounded-full bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-200">
                        <svg id="darkIcon" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                            <path d="M17.293 13.293A8 8 0 016.707 2.707a8.001 8.001 0 1010.586 10.586z" />
                        </svg>
                        <svg id="lightIcon" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 hidden" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M10 2a1 1 0 011 1v1a1 1 0 11-2 0V3a1 1 0 011-1zm4 8a4 4 0 11-8 0 4 4 0 018 0zm-.464 4.95l.707.707a1 1 0 001.414-1.414l-.707-.707a1 1 0 00-1.414 1.414zm2.12-10.607a1 1 0 010 1.414l-.706.707a1 1 0 11-1.414-1.414l.707-.707a1 1 0 011.414 0zM17 11a1 1 0 100-2h-1a1 1 0 100 2h1zm-7 4a1 1 0 011 1v1a1 1 0 11-2 0v-1a1 1 0 011-1zM5.05 6.464A1 1 0 106.465 5.05l-.708-.707a1 1 0 00-1.414 1.414l.707.707zm1.414 8.486l-.707.707a1 1 0 01-1.414-1.414l.707-.707a1 1 0 011.414 1.414zM4 11a1 1 0 100-2H3a1 1 0 000 2h1z" clip-rule="evenodd" />
                        </svg>
                    </button>
                </div>
                
                <!-- Settings Tabs -->
                <div x-data="{ activeTab: 'general' }" class="mb-8">
                    <div class="border-b border-gray-200 dark:border-gray-700">
                        <nav class="-mb-px flex space-x-8">
                            <button @click="activeTab = 'general'" 
                                    :class="activeTab === 'general' ? 'border-primary-500 text-primary-600 dark:text-primary-400' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 dark:hover:text-gray-300'" 
                                    class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
                                General Settings
                            </button>
                            <button @click="activeTab = 'appearance'" 
                                    :class="activeTab === 'appearance' ? 'border-primary-500 text-primary-600 dark:text-primary-400' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 dark:hover:text-gray-300'" 
                                    class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
                                Appearance
                            </button>
                            <button @click="activeTab = 'security'" 
                                    :class="activeTab === 'security' ? 'border-primary-500 text-primary-600 dark:text-primary-400' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 dark:hover:text-gray-300'" 
                                    class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
                                Security
                            </button>
                        </nav>
                    </div>
                    
                    <!-- General Settings Tab -->
                    <div x-show="activeTab === 'general'" class="pt-6">
                        <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-6 mb-6">
                            <h2 class="text-lg font-medium mb-4">General Settings</h2>
                            <form method="POST" action="">
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    <div>
                                        <label for="site_name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Site Name</label>
                                        <input type="text" id="site_name" name="site_name" value="<?= htmlspecialchars($settings['site_name']) ?>" 
                                               class="w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                                    </div>
                                    <div>
                                        <label for="contact_email" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Contact Email</label>
                                        <input type="email" id="contact_email" name="contact_email" value="<?= htmlspecialchars($settings['contact_email']) ?>" 
                                               class="w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                                    </div>
                                    <div>
                                        <label for="timezone" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Timezone</label>
                                        <select id="timezone" name="timezone" class="w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                                            <?php foreach ($timezones as $tz): ?>
                                                <option value="<?= htmlspecialchars($tz) ?>" <?= $tz === $settings['timezone'] ? 'selected' : '' ?>>
                                                    <?= htmlspecialchars($tz) ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="mt-6">
                                    <button type="submit" name="update_general" class="px-4 py-2 bg-primary-600 text-white rounded-md hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2">
                                        Save General Settings
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                    
                    <!-- Appearance Settings Tab -->
                    <div x-show="activeTab === 'appearance'" class="pt-6">
                        <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-6 mb-6">
                            <h2 class="text-lg font-medium mb-4">Appearance Settings</h2>
                            <form method="POST" action="">
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    <div>
                                        <label class="flex items-center">
                                            <input type="checkbox" name="dark_mode" class="rounded border-gray-300 text-primary-600 shadow-sm focus:border-primary-300 focus:ring focus:ring-primary-200 focus:ring-opacity-50 dark:bg-gray-700" <?= $settings['dark_mode'] ? 'checked' : '' ?>>
                                            <span class="ml-2 text-sm font-medium text-gray-700 dark:text-gray-300">Enable Dark Mode</span>
                                        </label>
                                    </div>
                                    <div>
                                        <label for="primary_color" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Primary Color</label>
                                        <div class="flex items-center">
                                            <span class="color-preview" style="background-color: <?= $settings['primary_color'] ?>"></span>
                                            <input type="color" id="primary_color" name="primary_color" value="<?= htmlspecialchars($settings['primary_color']) ?>" 
                                                   class="w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 dark:bg-gray-700 dark:border-gray-600">
                                        </div>
                                    </div>
                                    <div>
                                        <label for="secondary_color" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Secondary Color</label>
                                        <div class="flex items-center">
                                            <span class="color-preview" style="background-color: <?= $settings['secondary_color'] ?>"></span>
                                            <input type="color" id="secondary_color" name="secondary_color" value="<?= htmlspecialchars($settings['secondary_color']) ?>" 
                                                   class="w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 dark:bg-gray-700 dark:border-gray-600">
                                        </div>
                                    </div>
                                </div>
                                <div class="mt-6">
                                    <button type="submit" name="update_appearance" class="px-4 py-2 bg-primary-600 text-white rounded-md hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2">
                                        Save Appearance Settings
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                    
                    <!-- Security Settings Tab -->
                    <div x-show="activeTab === 'security'" class="pt-6">
                        <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-6 mb-6">
                            <h2 class="text-lg font-medium mb-4">Security Settings</h2>
                            <div class="space-y-6">
                                <div>
                                    <h3 class="text-md font-medium mb-2">Password Requirements</h3>
                                    <div class="space-y-2">
                                        <div class="flex items-center">
                                            <input type="checkbox" id="require_mixed_case" name="require_mixed_case" class="rounded border-gray-300 text-primary-600 shadow-sm focus:border-primary-300 focus:ring focus:ring-primary-200 focus:ring-opacity-50 dark:bg-gray-700" checked disabled>
                                            <label for="require_mixed_case" class="ml-2 text-sm font-medium text-gray-700 dark:text-gray-300">Require both upper and lower case letters</label>
                                        </div>
                                        <div class="flex items-center">
                                            <input type="checkbox" id="require_numbers" name="require_numbers" class="rounded border-gray-300 text-primary-600 shadow-sm focus:border-primary-300 focus:ring focus:ring-primary-200 focus:ring-opacity-50 dark:bg-gray-700" checked disabled>
                                            <label for="require_numbers" class="ml-2 text-sm font-medium text-gray-700 dark:text-gray-300">Require at least one number</label>
                                        </div>
                                        <div class="flex items-center">
                                            <input type="checkbox" id="require_special_chars" name="require_special_chars" class="rounded border-gray-300 text-primary-600 shadow-sm focus:border-primary-300 focus:ring focus:ring-primary-200 focus:ring-opacity-50 dark:bg-gray-700">
                                            <label for="require_special_chars" class="ml-2 text-sm font-medium text-gray-700 dark:text-gray-300">Require at least one special character</label>
                                        </div>
                                    </div>
                                </div>
                                
                                <div>
                                    <h3 class="text-md font-medium mb-2">Session Settings</h3>
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                        <div>
                                            <label for="session_timeout" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Session Timeout (minutes)</label>
                                            <input type="number" id="session_timeout" name="session_timeout" value="30" min="5" 
                                                   class="w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                                        </div>
                                        <div>
                                            <label for="max_login_attempts" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Max Login Attempts</label>
                                            <input type="number" id="max_login_attempts" name="max_login_attempts" value="5" min="1" 
                                                   class="w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="pt-4">
                                    <button type="button" class="px-4 py-2 bg-primary-600 text-white rounded-md hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2">
                                        Save Security Settings
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>
    
    <script>
        // Dark mode toggle functionality
        document.getElementById('darkModeToggle').addEventListener('click', function() {
            const html = document.documentElement;
            const isDark = html.classList.contains('dark');
            
            // Toggle dark class on html element
            html.classList.toggle('dark');
            
            // Toggle icons
            document.getElementById('darkIcon').classList.toggle('hidden');
            document.getElementById('lightIcon').classList.toggle('hidden');
            
            // Save preference to localStorage
            localStorage.setItem('darkMode', !isDark);
            
            // Update setting via AJAX
            fetch('update_dark_mode.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({ dark_mode: !isDark })
            });
        });
        
        // Check for saved dark mode preference
        if (localStorage.getItem('darkMode') === 'true') {
            document.documentElement.classList.add('dark');
            document.getElementById('darkIcon').classList.add('hidden');
            document.getElementById('lightIcon').classList.remove('hidden');
        } else if (localStorage.getItem('darkMode') === 'false') {
            document.documentElement.classList.remove('dark');
            document.getElementById('darkIcon').classList.remove('hidden');
            document.getElementById('lightIcon').classList.add('hidden');
        }
    </script>
</body>
</html>
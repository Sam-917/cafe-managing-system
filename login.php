<?php
session_start();

// Check if user is already logged in
if (isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true) {
    $redirect = 'index.php';
    if ($_SESSION['role'] === 'admin' || $_SESSION['role'] === 'staff') {
        $redirect = 'admin/dashboard.php';
    }
    header("Location: $redirect");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login System</title>
    <link href="assets/css/login.css" rel="stylesheet">
    <style>
        .home-btn {
            width: 100%;
            padding: 12px;
            background-color: #95a5a6;
            color: white;
            border: none;
            border-radius: 6px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            margin-top: 15px;
            text-decoration: none;
            display: inline-block;
            text-align: center;
        }
        
        .home-btn:hover {
            background-color: #7f8c8d;
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        }

        .submit-btn:hover {
            background-color: #2980b9;
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="login-header">
            <h1>Welcome Back</h1>
            <p>Please login to continue</p>
        </div>
        
        <?php if (isset($_GET['timeout'])): ?>
            <div class="error-message" style="display: block;">Your session has expired. Please login again.</div>
        <?php elseif (isset($_GET['error'])): ?>
            <div class="error-message" style="display: block;"><?= htmlspecialchars($_GET['error']) ?></div>
        <?php elseif (isset($_GET['registration'])): ?>
            <div class="success-message">Registration successful! Please login.</div>
        <?php endif; ?>
        
        <div id="errorMessage" class="error-message"></div>
        
        <form id="loginForm" method="POST">
            <div class="form-group">
                <label for="username">Username or Email</label>
                <input type="text" id="username" name="username" required>
            </div>
            
            <div class="form-group">
                <label for="password">Password</label>
                <div class="password-input-container">
                    <input type="password" id="password" name="password" required>
                    <button type="button" class="password-toggle" id="togglePassword" aria-label="Show password">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16" id="showIcon">
                            <path d="M16 8s-3-5.5-8-5.5S0 8 0 8s3 5.5 8 5.5S16 8 16 8zM1.173 8a13.133 13.133 0 0 1 1.66-2.043C4.12 4.668 5.88 3.5 8 3.5c2.12 0 3.879 1.168 5.168 2.457A13.133 13.133 0 0 1 14.828 8c-.058.087-.122.183-.195.288-.335.48-.83 1.12-1.465 1.755C11.879 11.332 10.119 12.5 8 12.5c-2.12 0-3.879-1.168-5.168-2.457A13.134 13.134 0 0 1 1.172 8z"/>
                            <path d="M8 5.5a2.5 2.5 0 1 0 0 5 2.5 2.5 0 0 0 0-5zM4.5 8a3.5 3.5 0 1 1 7 0 3.5 3.5 0 0 1-7 0z"/>
                        </svg>
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16" id="hideIcon" style="display: none;">
                            <path d="M13.359 11.238C15.06 9.72 16 8 16 8s-3-5.5-8-5.5a7.028 7.028 0 0 0-2.79.588l.77.771A5.944 5.944 0 0 1 8 3.5c2.12 0 3.879 1.168 5.168 2.457A13.134 13.134 0 0 1 14.828 8c-.058.087-.122.183-.195.288-.335.48-.83 1.12-1.465 1.755-.165.165-.337.328-.517.486l.708.709z"/>
                            <path d="M11.297 9.176a3.5 3.5 0 0 0-4.474-4.474l.823.823a2.5 2.5 0 0 1 2.829 2.829l.822.822zm-2.943 1.299.822.822a3.5 3.5 0 0 1-4.474-4.474l.823.823a2.5 2.5 0 0 0 2.829 2.829z"/>
                            <path d="M3.35 5.47c-.18.16-.353.322-.518.487A13.134 13.134 0 0 0 1.172 8l.195.288c.335.48.83 1.12 1.465 1.755C4.121 11.332 5.881 12.5 8 12.5c.716 0 1.39-.133 2.02-.36l.77.772A7.029 7.029 0 0 1 8 13.5C3 13.5 0 8 0 8s.939-1.721 2.641-3.238l.708.709zm10.296 8.884-12-12 .708-.708 12 12-.708.708z"/>
                        </svg>
                    </button>
                </div>
            </div>
            
            <div class="form-group" style="text-align: right; margin-top: -15px;">
                <a href="forgot_password.php" style="font-size: 13px; color: #3498db;">Forgot password?</a>
            </div>
            
            <button type="submit" class="submit-btn">Login</button>
            <a href="index.php" class="home-btn">Return Home</a>
        </form>
        
        <div class="register-link">
            Don't have an account? <a href="register.php">Register here</a>
        </div>

         
    </div>

    <script src="assets/js/login.js"></script>
    <script>
        document.getElementById('loginForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            const errorElement = document.getElementById('errorMessage');
            errorElement.style.display = 'none';
            
            fetch('php/authenticate.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Redirect based on role
                    let redirect = 'index.php';
                    if (data.role === 'admin' || data.role === 'staff') {
                        redirect = 'admin/dashboard.php';
                    }
                    window.location.href = redirect;
                } else {
                    errorElement.textContent = data.message || 'Login failed';
                    errorElement.style.display = 'block';
                }
            })
            .catch(error => {
                console.error('Error:', error);
                errorElement.textContent = 'An error occurred during login';
                errorElement.style.display = 'block';
            });
        });
    </script>
</body>
</html>
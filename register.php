<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Registration</title>

    <!-- Custom CSS -->
    <link href="assets/css/register.css" rel="stylesheet">
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
    <div class="registration-wrapper">
        <div class="registration-card">
            <div class="registration-header">
                <h2>Create Account</h2>
                <p>Join our community today</p>
            </div>
            
            <form id="registrationForm" action="php/process_registration.php" method="POST">
                <div id="errorMessage" class="error-message" style="display: none; margin-bottom: 15px;"></div>
                <div class="form-group">
                    <label for="first_name" class="required-field">First Name</label>
                    <input type="text" id="first_name" name="first_name" required>
                    <div id="firstNameError" class="error-message">Please enter your first name</div>
                </div>
                
                <div class="form-group">
                    <label for="last_name" class="required-field">Last Name</label>
                    <input type="text" id="last_name" name="last_name" required>
                    <div id="lastNameError" class="error-message">Please enter your last name</div>
                </div>
                
                <div class="form-group">
                    <label for="username" class="required-field">Username</label>
                    <input type="text" id="username" name="username" required minlength="4" pattern="[a-zA-Z0-9_]+" title="Only letters, numbers, and underscores allowed">
                    <div id="usernameError" class="error-message">Username must be at least 4 characters (letters, numbers, or underscores)</div>
                </div>

                <div class="form-group">
                    <label for="email" class="required-field">Email</label>
                    <input type="email" id="email" name="email" required>
                    <div id="emailError" class="error-message">Please enter a valid email address</div>
                </div>
                
                <div class="form-group">
                    <label for="phone">Phone Number</label>
                    <input type="tel" id="phone" name="phone">
                </div>
                
                <div class="form-group">
                    <label for="password" class="required-field">Password</label>
                    <div class="password-input-container">
                        <input type="password" id="password" name="password" required minlength="8">
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
                    <div class="password-strength-container">
                        <div id="passwordStrengthBar" class="password-strength-bar"></div>
                    </div>
                    <div id="passwordError" class="error-message">Password must be at least 8 characters</div>
                </div>
                
                <div class="form-group">
                    <label for="confirmPassword" class="required-field">Confirm Password</label>
                    <div class="password-input-container">
                        <input type="password" id="confirmPassword" name="confirmPassword" required>
                        <button type="button" class="password-toggle" id="toggleConfirmPassword" aria-label="Show password">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16" id="showConfirmIcon">
                                <path d="M16 8s-3-5.5-8-5.5S0 8 0 8s3 5.5 8 5.5S16 8 16 8zM1.173 8a13.133 13.133 0 0 1 1.66-2.043C4.12 4.668 5.88 3.5 8 3.5c2.12 0 3.879 1.168 5.168 2.457A13.133 13.133 0 0 1 14.828 8c-.058.087-.122.183-.195.288-.335.48-.83 1.12-1.465 1.755C11.879 11.332 10.119 12.5 8 12.5c-2.12 0-3.879-1.168-5.168-2.457A13.134 13.134 0 0 1 1.172 8z"/>
                                <path d="M8 5.5a2.5 2.5 0 1 0 0 5 2.5 2.5 0 0 0 0-5zM4.5 8a3.5 3.5 0 1 1 7 0 3.5 3.5 0 0 1-7 0z"/>
                            </svg>
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16" id="hideConfirmIcon" style="display: none;">
                                <path d="M13.359 11.238C15.06 9.72 16 8 16 8s-3-5.5-8-5.5a7.028 7.028 0 0 0-2.79.588l.77.771A5.944 5.944 0 0 1 8 3.5c2.12 0 3.879 1.168 5.168 2.457A13.134 13.134 0 0 1 14.828 8c-.058.087-.122.183-.195.288-.335.48-.83 1.12-1.465 1.755-.165.165-.337.328-.517.486l.708.709z"/>
                                <path d="M11.297 9.176a3.5 3.5 0 0 0-4.474-4.474l.823.823a2.5 2.5 0 0 1 2.829 2.829l.822.822zm-2.943 1.299.822.822a3.5 3.5 0 0 1-4.474-4.474l.823.823a2.5 2.5 0 0 0 2.829 2.829z"/>
                                <path d="M3.35 5.47c-.18.16-.353.322-.518.487A13.134 13.134 0 0 0 1.172 8l.195.288c.335.48.83 1.12 1.465 1.755C4.121 11.332 5.881 12.5 8 12.5c.716 0 1.39-.133 2.02-.36l.77.772A7.029 7.029 0 0 1 8 13.5C3 13.5 0 8 0 8s.939-1.721 2.641-3.238l.708.709zm10.296 8.884-12-12 .708-.708 12 12-.708.708z"/>
                            </svg>
                        </button>
                    </div>
                    <div id="confirmPasswordError" class="error-message">Passwords do not match</div>
                </div>
                
                <div class="form-group">
                    <label for="profile_image">Profile Image (Optional)</label>
                    <input type="file" id="profile_image" name="profile_image" accept="image/*">
                </div>
                
                <button type="submit" class="submit-btn" name="register">Register</button>
                <a href="index.php" class="home-btn">Return Home</a>
                
                <div class="login-link">
                    Already have an account? <a href="login.php">Login Here</a>
                </div>
 
                </button>
            </form>
        </div>
    </div>

    <!-- Custom JS -->
    <script src="assets/js/register.js"></script>
    
    <script>
        // Replace your current form submission code with this:
        document.getElementById('registrationForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            const errorElement = document.getElementById('errorMessage');
            errorElement.style.display = 'none';
            
            fetch(this.action, {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Use the redirect URL from the response
                    window.location.href = data.redirect || 'index.php';
                } else {
                    errorElement.textContent = data.message || 'Registration failed';
                    errorElement.style.display = 'block';
                }
            })
            .catch(error => {
                console.error('Error:', error);
                errorElement.textContent = 'An error occurred during registration';
                errorElement.style.display = 'block';
            });
        });
    </script>
</body>
</html>
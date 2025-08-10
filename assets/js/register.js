        // Password strength indicator
        const passwordInput = document.getElementById('password');
        const strengthBar = document.getElementById('passwordStrengthBar');
        
        passwordInput.addEventListener('input', function() {
            const password = this.value;
            let strength = 0;
            
            // Length check
            if (password.length >= 8) strength += 1;
            // Lowercase check
            if (password.match(/[a-z]/)) strength += 1;
            // Uppercase check
            if (password.match(/[A-Z]/)) strength += 1;
            // Number check
            if (password.match(/[0-9]/)) strength += 1;
            // Special char check
            if (password.match(/[^a-zA-Z0-9]/)) strength += 1;
            
            // Update strength bar
            const width = strength * 20;
            let color;
            
            if (strength <= 1) color = '#e74c3c'; // Weak (red)
            else if (strength <= 3) color = '#f39c12'; // Medium (orange)
            else color = '#2ecc71'; // Strong (green)
            
            strengthBar.style.width = width + '%';
            strengthBar.style.backgroundColor = color;
        });

        // Form validation
        document.getElementById('registrationForm').addEventListener('submit', function(e) {
            let isValid = true;
            
            // Validate name
            const name = document.getElementById('fullname').value.trim();
            if (name === '') {
                document.getElementById('nameError').style.display = 'block';
                isValid = false;
            } else {
                document.getElementById('nameError').style.display = 'none';
            }
            
            // Validate email
            const email = document.getElementById('email').value.trim();
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (!emailRegex.test(email)) {
                document.getElementById('emailError').style.display = 'block';
                isValid = false;
            } else {
                document.getElementById('emailError').style.display = 'none';
            }
            
            // Validate password
            const password = document.getElementById('password').value;
            if (password.length < 8) {
                document.getElementById('passwordError').style.display = 'block';
                isValid = false;
            } else {
                document.getElementById('passwordError').style.display = 'none';
            }
            
            // Validate password confirmation
            const confirmPassword = document.getElementById('confirmPassword').value;
            if (password !== confirmPassword) {
                document.getElementById('confirmPasswordError').style.display = 'block';
                isValid = false;
            } else {
                document.getElementById('confirmPasswordError').style.display = 'none';
            }
            
            if (!isValid) {
                e.preventDefault();
            }
        });

        // Show/hide password functionality
        const togglePassword = document.getElementById('togglePassword');
        const toggleConfirmPassword = document.getElementById('toggleConfirmPassword');
        const password = document.getElementById('password');
        const confirmPassword = document.getElementById('confirmPassword');

        togglePassword.addEventListener('click', function() {
            const type = password.getAttribute('type') === 'password' ? 'text' : 'password';
            password.setAttribute('type', type);
            this.textContent = type === 'password' ? 'Show' : 'Hide';
        });

        toggleConfirmPassword.addEventListener('click', function() {
            const type = confirmPassword.getAttribute('type') === 'password' ? 'text' : 'password';
            confirmPassword.setAttribute('type', type);
            this.textContent = type === 'password' ? 'Show' : 'Hide';
        });
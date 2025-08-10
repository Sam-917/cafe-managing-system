        // Password toggle functionality
        const togglePassword = document.getElementById('togglePassword');
        const passwordInput = document.getElementById('password');
        const showIcon = document.getElementById('showIcon');
        const hideIcon = document.getElementById('hideIcon');

        togglePassword.addEventListener('click', function() {
            // Toggle the type attribute
            const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
            passwordInput.setAttribute('type', type);
            
            // Toggle the eye / eye-slash icons
            if (type === 'password') {
                showIcon.style.display = 'block';
                hideIcon.style.display = 'none';
                this.setAttribute('aria-label', 'Show password');
            } else {
                showIcon.style.display = 'none';
                hideIcon.style.display = 'block';
                this.setAttribute('aria-label', 'Hide password');
            }
        });

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
                    let redirect = 'index.php'; // Default for customers and staff
                    if (data.role === 'admin') {
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
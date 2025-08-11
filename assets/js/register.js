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

// Show/hide password functionality
const togglePassword = document.getElementById('togglePassword');
const toggleConfirmPassword = document.getElementById('toggleConfirmPassword');
const password = document.getElementById('password');
const confirmPassword = document.getElementById('confirmPassword');

togglePassword.addEventListener('click', function() {
    const type = password.getAttribute('type') === 'password' ? 'text' : 'password';
    password.setAttribute('type', type);
    
    // Toggle icon visibility
    document.getElementById('showIcon').style.display = type === 'password' ? 'block' : 'none';
    document.getElementById('hideIcon').style.display = type === 'password' ? 'none' : 'block';
});

toggleConfirmPassword.addEventListener('click', function() {
    const type = confirmPassword.getAttribute('type') === 'password' ? 'text' : 'password';
    confirmPassword.setAttribute('type', type);
    
    // Toggle icon visibility
    document.getElementById('showConfirmIcon').style.display = type === 'password' ? 'block' : 'none';
    document.getElementById('hideConfirmIcon').style.display = type === 'password' ? 'none' : 'block';
});
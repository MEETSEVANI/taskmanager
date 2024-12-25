function validatePasswords() {
    const passwordInput = document.querySelector('input[name="password"]');
    const confirmPasswordInput = document.querySelector('input[name="confirmPassword"]');
    const errorMessage = document.getElementById('errorMessage');
    const registerButton = document.getElementById('registerButton');

    if (passwordInput.value === confirmPasswordInput.value) {
        errorMessage.classList.add('hidden'); // Hide error message
        registerButton.disabled = false; // Enable button
        registerButton.classList.remove('opacity-50', 'cursor-not-allowed'); // Style button as active
    } else {
        errorMessage.classList.remove('hidden'); // Show error message
        registerButton.disabled = true; // Disable button
        registerButton.classList.add('opacity-50', 'cursor-not-allowed'); // Style button as inactive
    }
}

const togglePassword = document.querySelector('#togglePassword');
const togglePassword2 = document.querySelector('#togglePassword2');
const password = document.querySelector('#password');
const passwordConfirmation = document.querySelector('#password_confirmation');

togglePassword.addEventListener('click', function () {
    togglePasswordVisibility(password);
});

togglePassword2.addEventListener('click', function () {
    togglePasswordVisibility(passwordConfirmation);
});

function togglePasswordVisibility(inputField) {
    const type = inputField.getAttribute('type') === 'password' ? 'text' : 'password';
    inputField.setAttribute('type', type);
    togglePassword.classList.toggle('fa-eye-slash');
    togglePassword2.classList.toggle('fa-eye-slash');
}
